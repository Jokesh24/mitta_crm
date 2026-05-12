<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\AdminLoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    /**
     * Show login page
     */
    public function showLoginForm()
    {
        return view("login");
    }

    /**
     * Login submit
     */
    public function loginold(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:4',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check admin user
        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            return back()->with('error', 'Invalid email or password.');
        }

        if (!Hash::check($request->password, $admin->password)) {
            return back()->with('error', 'Invalid email or password.');
        }

        if ($admin->status == 0) {
            return back()->with('error', 'Your account is inactive.');
        }

        // Create Session
        Session::put('admin_id', $admin->id);
        Session::put('admin_name', $admin->name);

        // Save login logs
        $this->saveLoginLog($admin->id, $request);

        // Redirect to dashboard
        return redirect()->route('dashboard')->with('success', 'Login successful.');
    }

    public function login(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:4',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        /* ==============================================================
            1️⃣ CHECK ADMIN TABLE FIRST
        ============================================================== */
        $admin = Admin::where('email', $request->email)->first();

        if ($admin) {

            if (!Hash::check($request->password, $admin->password)) {
                return back()->with('error', 'Invalid email or password.');
            }

            if ($admin->status == 0) {
                return back()->with('error', 'Your admin account is inactive.');
            }

            // Save session
            Session::put('admin_name', $admin->name);
            Session::put('user_type', 'admin'); // NEW
            Session::put('team_role', 1);
            Session::put('admin_id', $admin->id);
            Session::put('user_id', $admin->id);




            // Save logs
            $this->saveLoginLog($admin->id, $request);

            return redirect()->route('dashboard')->with('success', 'Login successful.');
        }

        /* ==============================================================
            2️⃣ CHECK TEAM TABLE (role 1 = Admin, role 2 = Lab Tech)
        ============================================================== */
        $team = \App\Models\Team::where('email', $request->email)->first();

        if ($team) {

            if (!Hash::check($request->password, $team->password)) {
                return back()->with('error', 'Invalid email or password.');
            }

            if ($team->status == 0) {
                return back()->with('error', 'Your account is inactive.');
            }

            if (!in_array($team->role, [1, 2])) {
                return back()->with('error', 'You are not allowed to access the system.');
            }

            // Save Team Session

            Session::put('admin_name', $team->first_name . ' ' . $team->last_name);
            Session::put('user_type', 'team'); // NEW
            Session::put('team_role', $team->role);
            Session::put('admin_id', $team->id);
            Session::put('user_id', $team->id);




            // Optional: Save logs
            // $this->saveLoginLog($team->id, $request);

            return redirect()->route('dashboard')->with('success', 'Login successful.');
        }

        /* ==============================================================
           3️⃣ IF NOT FOUND IN BOTH TABLES
        ============================================================== */
        return back()->with('error', 'Invalid email or password.');
    }


    /**
     * Logout
     */
    public function logout()
    {
        Session::flush();
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    /**
     * Save login logs
     */
    private function saveLoginLog($adminId, Request $request)
    {
        $ip = $request->ip();
        $userAgent = $request->header('User-Agent');

        // Fetch location (free API)
        $location = null;
        try {
            $location = Http::timeout(5)->get("https://ipapi.co/{$ip}/json/")->json();
        } catch (\Exception $e) {
        }

        AdminLoginLog::create([
            'admin_id' => $adminId,
            'ip_address' => $ip,
            'city' => $location['city'] ?? null,
            'region' => $location['region'] ?? null,
            'country' => $location['country_name'] ?? null,
            'browser' => $userAgent,
            'device' => $userAgent,
            'logged_in_at' => now(),
        ]);
    }

    public function changePassword()
    {
        return view('change_password');
    }

    public function updatePasswordold(Request $request)
    {
        $adminId = Session::get('admin_id');
        $admin = Admin::find($adminId);

        // Validate Inputs
        $request->validate([
            'current_password' => 'required',
            'new_password' => [
                'required',
                'min:8',
                'regex:/[a-z]/',      // must contain lowercase
                'regex:/[A-Z]/',      // must contain uppercase
                'regex:/[0-9]/',      // must contain number
                'regex:/[@$!%*#?&]/', // must contain symbol
            ],
            'confirm_password' => 'required|same:new_password',
        ]);

        // Check current password
        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.'
            ]);
        }

        // Update the new password
        $admin->password = Hash::make($request->new_password);
        $admin->save();

        return back()->with('success', 'Password updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $role = Session::get('team_role');   // 1 = Admin, 2 = Lab Tech
        $id = Session::get('admin_id');    // Used for BOTH tables

        // Identify correct model using role
        if ($role == 1) {
            // Admin
            $user = \App\Models\Admin::find($id);
        } else {
            // Lab Tech (Team)
            $user = \App\Models\Team::find($id);
        }

        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        // Validate Inputs
        $request->validate([
            'current_password' => 'required',
            'new_password' => [
                'required',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
            'confirm_password' => 'required|same:new_password',
        ]);

        // Check old password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.'
            ]);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password updated successfully!');
    }


}

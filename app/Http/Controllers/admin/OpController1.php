<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Op;
use App\Models\Company;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Exception;

class OpController extends Controller
{
    /**
     * Show Add OP Page
     */
    public function add_op()
    {
        $companies = Company::where('status', 1)
            ->orderBy('company_name')
            ->get();

        $locations = Op::select('location')
            ->whereNotNull('location')
            ->groupBy('location')
            ->orderBy('location')
            ->get();

        return view('add_op', compact('companies', 'locations'));
    }

    /**
     * AJAX: Fetch patient(s) by mobile
     */
    public function fetch_patient(Request $request)
    {
        $mobile = $request->mobile;

        $patients = Patient::where('mobile_number', $mobile)->get();

        if ($patients->count() > 0) {
            return response()->json([
                'exists' => true,
                'patients' => $patients,
            ]);
        }

        return response()->json(['exists' => false]);
    }

    /**
     * Store OP Record
     */
    public function store_op(Request $request)
    {
        try {
            // Normalise the "Add New Patient" case safely
            if ($request->input('existing_patient_id') === 'new_patient') {
                $request->merge([
                    'patient_type' => 'new',
                    'existing_patient_id' => null,
                ]);
            }

            $patientType = $request->input('patient_type', 'new'); // default new

            // Base rules
            $rules = [
                'mobile_number' => 'required|digits_between:8,15',
                'patient_type' => 'required|in:new,existing',

                // 'patient_name' => 'required_if:patient_type,new|string|max:255',
                'patient_name' => 'nullable|string|max:255|required_if:patient_type,new',


                'dob' => 'nullable|date',
                'age' => 'required|integer|min:1|max:120',
                'gender' => 'required|string',
                'location' => 'required|string|max:255',

                // 'status' => 'required|in:1,0',
                // 'company' => 'required|exists:companies,id',
                // 'payment_ref' => 'required|string|max:255',
            ];

            // Only add this rule when patient_type = existing
            if ($patientType === 'existing') {
                $rules['existing_patient_id'] = 'required|integer|exists:patients,id';
            }

            $messages = [
                'existing_patient_id.required' => 'Please select a patient from the list.',
                'existing_patient_id.exists' => 'The selected patient does not exist.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            /* ---------------------------------------------------------
               PATIENT HANDLING
               --------------------------------------------------------- */

            if ($patientType === 'existing') {
                // Use selected existing patient
                $patient_id = (int) $request->existing_patient_id;

            } else {
                // NEW PATIENT

                $userRole = Session::get('team_role');   // 1 = Admin, 2 = Lab Tech
                $userId = Session::get('admin_id');    // always logged-in user id

                $patient = Patient::create([
                    'mobile_number' => $request->mobile_number,
                    'patient_name' => $request->patient_name,
                    'dob' => $request->dob ? date('Y-m-d', strtotime($request->dob)) : null,
                    'age' => $request->age,
                    'gender' => $request->gender,
                    'added_by_role' => $userRole,
                    'added_by' => $userId,
                ]);

                $patient_id = $patient->id;
            }

            /* ---------------------------------------------------------
               CREATE OP RECORD
               --------------------------------------------------------- */
            Op::create([
                'patient_id' => $patient_id,
                'company_id' => $request->company,
                'location' => $request->location,
                'status' => 1,
                'payment_ref' => $request->payment_ref,
            ]);

            return redirect()
                ->route('manage-op')
                ->with('success', 'OP Registered Successfully');

        } catch (Exception $e) {
            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Manage OP list
     */
    public function manage_op()
    {
        $userRole = Session::get('team_role'); // 1 = Admin, 2 = Lab Tech
        $userId = Session::get('admin_id');  // logged in user

        $query = Op::with(['patient', 'company'])
            ->orderBy('id', 'DESC');

        // If Lab Technician → show only their records
        if ($userRole == 2) {
            $query->whereHas('patient', function ($q) use ($userId) {
                $q->where('added_by', $userId)
                    ->where('added_by_role', 2);

            });
        }

        $ops = $query->paginate(10);

        return view('manage_op', compact('ops'));
    }


    /**
     * AJAX: Search OP Records
     */
    public function ajax_op(Request $request)
    {
        $userRole = Session::get('team_role');
        $userId = Session::get('admin_id');

        $query = Op::with(['patient', 'company']);

        // ROLE-BASED FILTERING
        if ($userRole == 2) {
            $query->whereHas('patient', function ($q) use ($userId) {
                $q->where('added_by', $userId)
                    ->where('added_by_role', 2);

            });
        }

        // TEXT SEARCH
        if ($request->searchText) {
            $s = $request->searchText;

            $query->where(function ($q) use ($s) {

                // Patient name & mobile
                $q->whereHas('patient', function ($sub) use ($s) {
                    $sub->where('patient_name', 'LIKE', "%$s%")
                        ->orWhere('mobile_number', 'LIKE', "%$s%");
                })

                    // Company search
                    ->orWhereHas('company', function ($sub) use ($s) {
                        $sub->where('company_name', 'LIKE', "%$s%");
                    })

                    // Payment ref search
                    ->orWhere('payment_ref', 'LIKE', "%$s%")

                    // 🔥 LOCATION SEARCH (NEW)
                    ->orWhere('location', 'LIKE', "%$s%");
            });

        }

        // DATE FILTERS
        if ($request->dateFrom) {
            $query->whereDate('created_at', '>=', date('Y-m-d', strtotime($request->dateFrom)));
        }

        if ($request->dateTo) {
            $query->whereDate('created_at', '<=', date('Y-m-d', strtotime($request->dateTo)));
        }

        $ops = $query->orderBy('id', 'DESC')->paginate(10);

        return response()->json([
            'ops' => $ops,
            'pagination' => $ops->links()->render(),
        ]);
    }


    /**
     * Edit OP Page
     */
    public function edit_op($id)
    {
        $op = Op::with('patient')->findOrFail($id);

        $companies = Company::where('status', 1)
            ->orderBy('company_name')
            ->get();

        $locations = Op::select('location')
            ->whereNotNull('location')
            ->groupBy('location')
            ->orderBy('location')
            ->get();

        $patients = Patient::where('mobile_number', $op->patient->mobile_number)->get();

        return view('edit_op', compact('op', 'companies', 'patients', 'locations'));
    }

    /**
     * Update OP Record
     */
    public function update_op(Request $request, $id)
    {
        try {
            $patientType = $request->input('patient_type', 'new');

            $rules = [
                'mobile_number' => 'required|digits_between:8,15',
                'patient_type' => 'required|in:new,existing',

                'patient_name' => 'required_if:patient_type,new|string|max:255',

                'dob' => 'nullable|date',
                'age' => 'required|integer|min:1|max:120',
                'gender' => 'required|string',

                // 'company' => 'required|exists:companies,id',
                'location' => 'required|string|max:255',
                // 'status' => 'required|in:1,0',
                // 'payment_ref' => 'required|string|max:255',
            ];

            if ($patientType === 'existing') {
                $rules['existing_patient_id'] = 'required|integer|exists:patients,id';
            }

            $messages = [
                'existing_patient_id.required' => 'Please select a patient from the list.',
                'existing_patient_id.exists' => 'The selected patient does not exist.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $op = Op::findOrFail($id);

            // Logged-in user
            // $userRole = Session::get('team_role');
            // $userId = Session::get('user_id');

            $userRole = Session::get('team_role');   // 1 = Admin, 2 = Lab Tech
            $userId = Session::get('admin_id');

            if ($patientType === 'existing') {

                $patient_id = (int) $request->existing_patient_id;

            } else {

                $patient = Patient::updateOrCreate(
                    ['id' => $op->patient_id],
                    [
                        'mobile_number' => $request->mobile_number,
                        'patient_name' => $request->patient_name,
                        'dob' => $request->dob ? date('Y-m-d', strtotime($request->dob)) : null,
                        'age' => $request->age,
                        'gender' => $request->gender,
                        'added_by_role' => $userRole,
                        'added_by' => $userId,
                    ]
                );

                $patient_id = $patient->id;
            }

            $op->update([
                'patient_id' => $patient_id,
                'company_id' => $request->company,
                'location' => $request->location,
                'status' => 1,
                'payment_ref' => $request->payment_ref,
            ]);

            return redirect()
                ->route('manage-op')
                ->with('success', 'OP updated successfully');

        } catch (Exception $e) {
            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
}

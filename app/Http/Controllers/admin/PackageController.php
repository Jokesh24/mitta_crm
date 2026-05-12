<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Package;
use Illuminate\Support\Facades\Validator;
use Exception;

class PackageController extends Controller
{
    /* ---------------- ADD PACKAGE ---------------- */

    public function add_package()
    {
        $parameters = Test::where('status', 1)
            ->orderBy('test_name')
            ->get();

        return view('add_package', compact('parameters'));
    }

    /* ---------------- STORE PACKAGE ---------------- */

    public function store_package(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'package_name' => 'required|string|max:255',
                'parameters_order' => 'required',
                'package_price' => 'required|numeric|min:1',
            ]);

            // dd($request->all());

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            // Package::create([
            //     'package_name' => $request->package_name,
            //     // 'parameters' => $request->parameters_order,
            //     'parameters' => json_decode($request->parameters_order, true),
            //     'package_price' => $request->package_price,
            //     'status' => 1,
            // ]);

            Package::create([
                'package_name' => $request->package_name,
                'tests' => [], // temporary fix
                'parameters' => json_decode($request->parameters_order, true),
                'package_price' => $request->package_price,
                'status' => 1,
            ]);

            return redirect()->route('add-package')
                ->with('success', 'Package created successfully.');

        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /* ---------------- MANAGE PACKAGE ---------------- */

    public function manage_package()
    {
        $packages = Package::orderBy('id', 'DESC')->paginate(10);
        return view('manage_package', compact('packages'));
    }

    /* ---------------- PACKAGE DETAILS (VIEW MODAL) ---------------- */

    public function package_details($id)
    {
        $package = Package::findOrFail($id);

        $allTestIds = [];

        // NEW STRUCTURE
        if (!empty($package->parameters)) {

            $params = $package->parameters;

            $allTestIds = $params['global'] ?? [];

            foreach ($params['groups'] ?? [] as $group) {
                $allTestIds = array_merge($allTestIds, $group['params'] ?? []);
            }

        }
        // LEGACY STRUCTURE
        elseif (!empty($package->tests)) {

            $decoded = is_array($package->tests)
                ? $package->tests
                : json_decode($package->tests, true);

            if (is_array($decoded)) {
                $allTestIds = $decoded;
            }
        }

        $tests = Test::whereIn('id', $allTestIds)
            ->pluck('test_name', 'id');

        return response()->json([
            'package' => $package,
            'parameters' => $package->parameters,
            'tests' => $tests
        ]);
    }

    /**
     * AJAX Search for Packages
     */
    public function ajax_packages(Request $request)
    {
        $query = Package::query();

        // SEARCH TEXT (package name, price, test ids)
        if ($request->searchText) {
            $s = $request->searchText;

            $query->where(function ($q) use ($s) {
                $q->where('package_name', 'LIKE', "%$s%")
                    ->orWhere('package_price', 'LIKE', "%$s%");
            });
        }

        // MINIMUM TEST FILTER
        if ($request->minTests) {
            $query->whereRaw('JSON_LENGTH(tests) >= ?', [$request->minTests]);
        }

        // STATUS FILTER
        if ($request->statusFilter == "Active") {
            $query->where('status', 1);
        } elseif ($request->statusFilter == "Inactive") {
            $query->where('status', 0);
        }

        $packages = $query->orderBy('id', 'DESC')->paginate(10);

        return response()->json([
            'packages' => $packages,
            'pagination' => $packages->links()->render(),
        ]);
    }

    public function edit_package($id)
    {
        $package = Package::findOrFail($id);
        $parameters = Test::where('status', 1)
            ->orderBy('test_name')
            ->get();

        // Normalize parameters
        if (empty($package->parameters)) {

            // If old package with tests column
            if (!empty($package->tests)) {

                $decoded = is_array($package->tests)
                    ? $package->tests
                    : json_decode($package->tests, true);

                $package->parameters = [
                    'global' => $decoded ?? [],
                    'groups' => []
                ];
            } else {
                $package->parameters = [
                    'global' => [],
                    'groups' => []
                ];
            }
        }

        return view('edit_package', compact('package', 'parameters'));
    }

    public function update_package(Request $request, $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                'package_name' => 'required|string|max:255',
                'parameters_order' => 'required',
                'package_price' => 'required|numeric|min:1',
                'status' => 'required|in:0,1'
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $package = Package::findOrFail($id);

            $package->update([
                'package_name' => $request->package_name,
                'parameters' => json_decode($request->parameters_order, true),
                'tests' => [], // keep empty for compatibility
                'package_price' => $request->package_price,
                'status' => $request->status,
            ]);

            return redirect()->route('manage-package')
                ->with('success', 'Package updated successfully.');

        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }



}

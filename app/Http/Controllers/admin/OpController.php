<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Op;
use App\Models\Company;
use App\Models\Package;
use App\Models\TestNew; // Added Model
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
     * UPDATED: Store OP Record
     */
    public function store_op(Request $request)
    {
        try {
            /* ------------------ VALIDATION ------------------ */
            $rules = [
                'mobile_number' => 'required|digits:10',
                'patient_type' => 'required|in:new,existing',
                'age' => 'required|integer|min:1',
                'gender' => 'required|string',
                'location' => 'required|string',
                'amount' => 'required|numeric|min:0',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            /* ------------------ PATIENT HANDLING ------------------ */
            if ($request->patient_type == "existing") {
                $patient_id = (int) $request->existing_patient_id;
            } else {
                $patient = Patient::create([
                    'mobile_number' => $request->mobile_number,
                    'patient_name' => $request->patient_name,
                    'dob' => $request->dob ? date('Y-m-d', strtotime($request->dob)) : null,
                    'age' => $request->age,
                    'gender' => $request->gender,
                    'added_by_role' => Session::get('team_role'),
                    'added_by' => Session::get('admin_id'),
                ]);
                $patient_id = $patient->id;
            }

            /* ------------------ UNIQUE CODES ------------------ */
            $patientCode = "PID" . $patient_id;
            $nextOpId = (Op::max('id') ?? 0) + 1;
            $opRegNo = "OPR" . $nextOpId;

            /* ------------------ BUILD JSON DATA ------------------ */
            $testsJson = $request->input('tests_json') ? json_decode($request->input('tests_json'), true) : [];
            $packagesJson = $request->input('packages_json') ? json_decode($request->input('packages_json'), true) : [];
            $testsNewJson = $request->input('tests_new_json') ? json_decode($request->input('tests_new_json'), true) : [];

            // We store profiles (tests_new) in their own column or merge as per your requirement
            // To maintain the sub-test structure, we keep testsNewJson as is.

            Op::create([
                'patient_id' => $patient_id,
                'patient_code' => $patientCode,
                'op_reg_no' => $opRegNo,
                'company_id' => $request->company,
                'location' => $request->location,
                'status' => 1,
                'tests_json' => $testsJson,
                'packages_json' => $packagesJson,
                'tests_new_json' => $testsNewJson, // This holds the profile IDs and their specific prices
                'grand_total' => $request->amount,
                'payment_type' => $request->payment_type,
                'payment_amount' => $request->amount,
                'payment_ref' => $request->payment_ref,
            ]);

            return redirect()->route('manage-op')->with('success', 'OP Registered Successfully');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
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

    public function ajax_op(Request $request)
    {
        $userRole = Session::get('team_role');
        $userId = Session::get('admin_id');

        $query = Op::with(['patient', 'company']);

        // ROLE FILTER
        if ($userRole == 2) {
            $query->whereHas('patient', function ($q) use ($userId) {
                $q->where('added_by', $userId)
                    ->where('added_by_role', 2);
            });
        }

        // SEARCH FILTER
        if ($request->searchText) {
            $s = $request->searchText;

            $query->where(function ($q) use ($s) {
                $q->whereHas('patient', function ($sub) use ($s) {
                    $sub->where('patient_name', 'LIKE', "%$s%")
                        ->orWhere('mobile_number', 'LIKE', "%$s%");
                })
                    ->orWhereHas('company', function ($sub) use ($s) {
                        $sub->where('company_name', 'LIKE', "%$s%");
                    })
                    ->orWhere('payment_ref', 'LIKE', "%$s%")
                    ->orWhere('location', 'LIKE', "%$s%");
            });
        }

        // DATE FILTER
        if ($request->dateFrom) {
            $query->whereDate('created_at', '>=', date('Y-m-d', strtotime($request->dateFrom)));
        }

        if ($request->dateTo) {
            $query->whereDate('created_at', '<=', date('Y-m-d', strtotime($request->dateTo)));
        }

        $ops = $query->orderBy('id', 'DESC')->paginate(10);

        // Append edit URL + date format
        $ops->getCollection()->transform(function ($op) {
            $op->edit_url = route('edit-op', $op->id);
            $op->created_at_format = $op->created_at->format('d-m-Y');
            return $op;
        });

        return response()->json([
            'ops' => $ops,
            'pagination' => $ops->links()->render(),
        ]);
    }

    public function getLabTests()
    {
        $tests = \App\Models\Test::where('status', 1)
            ->orderBy('test_name')
            ->get(['id', 'test_name', 'price']);

        return response()->json($tests);
    }

    public function getPackagesold()
    {
        $packages = Package::select('id', 'package_name', 'package_price', 'tests')
            ->get()
            ->map(function ($p) {

                // convert JSON string to array
                $testIds = is_array($p->tests) ? $p->tests : json_decode($p->tests, true);

                // Fetch test names
                $testNames = \App\Models\Test::whereIn('id', $testIds)
                    ->pluck('test_name')
                    ->toArray();

                return [
                    'id' => $p->id,
                    'package_name' => $p->package_name,
                    'package_price' => $p->package_price,
                    'tests' => $testNames,
                ];
            });

        return response()->json($packages);
    }

    public function getPackages()
    {
        $packages = Package::select('id', 'package_name', 'package_price', 'tests', 'parameters')
            ->where('status', 1)
            ->get()
            ->map(function ($p) {

                $testIds = [];

                /* ---------------- NEW STRUCTURE (parameters) ---------------- */
                if (!empty($p->parameters)) {

                    $params = is_array($p->parameters)
                        ? $p->parameters
                        : json_decode($p->parameters, true);

                    $testIds = $params['global'] ?? [];

                    foreach ($params['groups'] ?? [] as $group) {
                        $testIds = array_merge($testIds, $group['params'] ?? []);
                    }
                }

                /* ---------------- OLD STRUCTURE (tests) ---------------- */ elseif (!empty($p->tests)) {

                    $decoded = is_array($p->tests)
                        ? $p->tests
                        : json_decode($p->tests, true);

                    if (is_array($decoded)) {
                        $testIds = $decoded;
                    }
                }

                /* ---------------- FETCH TEST NAMES SAFELY ---------------- */
                $testNames = [];

                if (!empty($testIds)) {
                    $testNames = \App\Models\Test::whereIn('id', $testIds)
                        ->pluck('test_name')
                        ->toArray();
                }

                return [
                    'id' => $p->id,
                    'package_name' => $p->package_name,
                    'package_price' => $p->package_price,
                    'tests' => $testNames,
                ];
            });

        return response()->json($packages);
    }

    /**
     * UPDATED: Get Tests from tests_new (Profiles)
     * Now returns the full nested structure for the preview
     */
    public function getTestsNew()
    {
        $testsNew = TestNew::where('status', 1)
            ->orderBy('test_name')
            ->get()
            ->map(function ($t) {
                $data = json_decode($t->parameters, true);

                // Collect all IDs to fetch names for the preview string
                $allIds = $data['global'] ?? [];
                foreach ($data['groups'] ?? [] as $group) {
                    $allIds = array_merge($allIds, $group['params']);
                }

                $testNames = \App\Models\Test::whereIn('id', $allIds)
                    ->pluck('test_name')
                    ->toArray();

                return [
                    'id' => $t->id,
                    'test_name' => $t->test_name,
                    'price' => $t->price,
                    'tests' => $testNames, // Flattened names for the "Includes" preview
                    'structure' => $data    // Full JSON for report generation logic
                ];
            });

        return response()->json($testsNew);
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
            $op = Op::findOrFail($id);

            /* ------------------ VALIDATION ------------------ */
            $rules = [
                'mobile_number' => 'required|digits_between:8,15',
                'patient_type' => 'required|in:new,existing',
                'dob' => 'nullable|date',
                'age' => 'required|integer|min:1|max:120',
                'gender' => 'required|string',
                'location' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
            ];

            if ($request->patient_type == "new") {
                $rules['patient_name'] = 'required|string|max:255';
            } else {
                $rules['existing_patient_id'] = 'required|integer|exists:patients,id';
            }

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            /* ------------------ PATIENT UPDATE ------------------ */
            if ($request->patient_type == "existing") {
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
                    ]
                );
                $patient_id = $patient->id;
            }

            /* ------------------ BUILD JSON DATA ------------------ */
            $testsJson = $request->input('tests_json') ? json_decode($request->input('tests_json'), true) : [];
            $packagesJson = $request->input('packages_json') ? json_decode($request->input('packages_json'), true) : [];
            $testsNewJson = $request->input('tests_new_json') ? json_decode($request->input('tests_new_json'), true) : [];

            /* ------------------ UPDATE OP RECORD ------------------ */
            $op->update([
                'patient_id' => $patient_id,
                'company_id' => $request->company,
                'location' => $request->location,
                'tests_json' => $testsJson,
                'packages_json' => $packagesJson,
                'tests_new_json' => $testsNewJson, // Now saving to the correct column
                'grand_total' => $request->amount,
                'payment_type' => $request->payment_type,
                'payment_amount' => $request->amount,
                'payment_ref' => $request->payment_ref,
            ]);

            return redirect()->route('manage-op')->with('success', 'OP updated successfully');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function pending_ops()
    {
        // ... (No changes needed, code same as before) ...
        $userRole = Session::get('team_role');
        $userId = Session::get('admin_id');

        $query = Op::with(['patient'])->where('op_status', 1)->orderBy('id', 'DESC');

        if ($userRole == 2) {
            $query->whereHas('patient', function ($q) use ($userId) {
                $q->where('added_by', $userId)->where('added_by_role', 2);
            });
        }
        $ops = $query->paginate(10);
        return view('pending_ops', compact('ops'));
    }

    public function ajax_pending_ops(Request $request)
    {
        // ... (No changes needed, code same as before) ...
        $search = $request->searchText ?? '';
        $userRole = Session::get('team_role');
        $userId = Session::get('admin_id');

        $query = Op::with(['patient'])->where('op_status', 1)->orderBy('id', 'DESC');

        if ($userRole == 2) {
            $query->whereHas('patient', function ($q) use ($userId) {
                $q->where('added_by', $userId)->where('added_by_role', 2);
            });
        }

        if (!empty($search)) {
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('patient_name', 'LIKE', "%$search%")
                    ->orWhere('mobile_number', 'LIKE', "%$search%");
            });
        }

        $ops = $query->paginate(10);
        return response()->json(['ops' => $ops, 'pagination' => $ops->links()->render()]);
    }
}
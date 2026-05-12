<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Test;
use App\Models\TestNew;
use App\Models\Team;
use App\Models\Op;
use App\Models\Package;
use App\Models\ResultHeader;
use App\Models\ResultDetail;
use Illuminate\Support\Facades\Validator;
use Exception;
use Carbon\Carbon;
use Session;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Traits\SendWhatsAppMessage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL; // Ensure this is at the top

class ResultController extends Controller
{

    use SendWhatsAppMessage;

    private function decodeTestsColumn($tests)
    {
        if (is_array($tests)) {
            return $tests;
        }

        if (empty($tests)) {
            return [];
        }

        $decoded = json_decode($tests, true);

        // Handle double-encoded JSON
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }

        return is_array($decoded) ? $decoded : [];
    }

    public function add_result_from_op($opId)
    {
        $role = Session::get('team_role');
        $userId = Session::get('admin_id');

        $op = Op::with(['patient', 'company'])->findOrFail($opId);

        // Security Check
        if ($role == 2) {
            if (!$op->patient || $op->patient->added_by != $userId || $op->patient->added_by_role != 2) {
                abort(403, 'Unauthorized access.');
            }
        }

        // Safely get JSON data
        $testsJson = is_array($op->tests_json) ? $op->tests_json : json_decode($op->tests_json ?? '[]', true);
        $packagesJson = is_array($op->packages_json) ? $op->packages_json : json_decode($op->packages_json ?? '[]', true);
        $testsNewJson = is_array($op->tests_new_json) ? $op->tests_new_json : json_decode($op->tests_new_json ?? '[]', true);

        $allGroups = [];
        $allUniqueTestIds = [];

        // 1. PROCESS STANDARD PACKAGES
        // Extract IDs regardless of whether the JSON is [1,2] or [{"id":1}, {"id":2}]
        // $pkgIds = collect($packagesJson)->map(function ($item) {
        //     return is_array($item) ? $item['id'] : $item;
        // })->filter()->all();

        // if (!empty($pkgIds)) {
        //     $packages = Package::whereIn('id', $pkgIds)->get();
        //     foreach ($packages as $pkg) {
        //         // Ensure tests are decoded correctly from the Package model
        //         $tIds = is_array($pkg->tests) ? $pkg->tests : json_decode($pkg->tests ?? '[]', true);

        //         if (!empty($tIds)) {
        //             $tests = Test::with('unit')->whereIn('id', $tIds)
        //                 ->orderByRaw("FIELD(id," . implode(',', $tIds) . ")")
        //                 ->get();

        //             $allGroups[] = [
        //                 'title' => $pkg->package_name,
        //                 'is_profile' => false,
        //                 'subgroups' => [['name' => null, 'tests' => $tests]]
        //             ];
        //             $allUniqueTestIds = array_merge($allUniqueTestIds, $tIds);
        //         }
        //     }
        // }

        // 1. PROCESS STANDARD PACKAGES
        $pkgIds = collect($packagesJson)->map(function ($item) {
            return is_array($item) ? ($item['id'] ?? null) : $item;
        })->filter()->all();

        if (!empty($pkgIds)) {
            $packages = Package::whereIn('id', $pkgIds)->get();

            foreach ($packages as $pkg) {

                /* ===============================
                   NEW PACKAGES (parameters JSON)
                =============================== */
                if (!empty($pkg->parameters)) {

                    $params = is_array($pkg->parameters)
                        ? $pkg->parameters
                        : json_decode($pkg->parameters, true);

                    $subGroups = [];

                    // Global parameters
                    if (!empty($params['global'])) {
                        $tests = Test::with('unit')
                            ->whereIn('id', $params['global'])
                            ->orderByRaw("FIELD(id," . implode(',', $params['global']) . ")")
                            ->get();

                        $subGroups[] = [
                            'name' => null,
                            'tests' => $tests
                        ];

                        $allUniqueTestIds = array_merge($allUniqueTestIds, $params['global']);
                    }

                    // Sub-groups
                    foreach ($params['groups'] ?? [] as $g) {
                        $tests = Test::with('unit')
                            ->whereIn('id', $g['params'])
                            ->orderByRaw("FIELD(id," . implode(',', $g['params']) . ")")
                            ->get();

                        $subGroups[] = [
                            'name' => $g['group_name'],
                            'tests' => $tests
                        ];

                        $allUniqueTestIds = array_merge($allUniqueTestIds, $g['params']);
                    }

                    $allGroups[] = [
                        'title' => $pkg->package_name,
                        'is_profile' => false,
                        'subgroups' => $subGroups
                    ];

                    continue;
                }

                /* ===============================
                   OLD PACKAGES (tests column)
                =============================== */
                $tIds = $this->decodeTestsColumn($pkg->tests);

                if (!empty($tIds)) {
                    $tests = Test::with('unit')
                        ->whereIn('id', $tIds)
                        ->orderByRaw("FIELD(id," . implode(',', $tIds) . ")")
                        ->get();

                    $allGroups[] = [
                        'title' => $pkg->package_name,
                        'is_profile' => false,
                        'subgroups' => [
                            ['name' => null, 'tests' => $tests]
                        ]
                    ];

                    $allUniqueTestIds = array_merge($allUniqueTestIds, $tIds);
                }
            }
        }

        // 2. PROCESS PROFILES (tests_new)
        $profileIds = array_unique(array_merge(
            collect($testsNewJson)->map(fn($i) => is_array($i) ? $i['id'] : $i)->toArray(),
            collect($packagesJson)->where('type', 'test_new')->pluck('id')->toArray() // Backward compatibility
        ));

        if (!empty($profileIds)) {
            $profiles = TestNew::whereIn('id', $profileIds)->get();
            foreach ($profiles as $profile) {
                $params = is_array($profile->parameters) ? $profile->parameters : json_decode($profile->parameters ?? '[]', true);
                $currentProfileSubgroups = [];

                $globalIds = isset($params['global']) ? $params['global'] : (isset($params[0]) ? $params : []);
                if (!empty($globalIds)) {
                    $tests = Test::with('unit')->whereIn('id', $globalIds)->orderByRaw("FIELD(id," . implode(',', $globalIds) . ")")->get();
                    $currentProfileSubgroups[] = ['name' => null, 'tests' => $tests];
                    $allUniqueTestIds = array_merge($allUniqueTestIds, $globalIds);
                }

                if (isset($params['groups'])) {
                    foreach ($params['groups'] as $g) {
                        $tests = Test::with('unit')->whereIn('id', $g['params'])->orderByRaw("FIELD(id," . implode(',', $g['params']) . ")")->get();
                        $currentProfileSubgroups[] = ['name' => $g['group_name'], 'tests' => $tests];
                        $allUniqueTestIds = array_merge($allUniqueTestIds, $g['params']);
                    }
                }

                $allGroups[] = [
                    'title' => $profile->test_name,
                    'is_profile' => true,
                    'subgroups' => $currentProfileSubgroups
                ];
            }
        }

        // 3. PROCESS INDIVIDUAL TESTS
        $indIds = collect($testsJson)->map(fn($i) => is_array($i) ? $i['id'] : $i)->all();
        $individualTests = collect();
        if (!empty($indIds)) {
            $individualTests = Test::with('unit')->whereIn('id', $indIds)->get();
            $allUniqueTestIds = array_merge($allUniqueTestIds, $indIds);
        }

        // 4. FINAL UNIQUE TEST LIST
        $allUniqueTestIds = array_values(array_unique(array_filter($allUniqueTestIds)));
        $uniqueTests = empty($allUniqueTestIds) ? collect() : Test::whereIn('id', $allUniqueTestIds)->get();

        return view('add_result_entry_from_op', compact('op', 'allGroups', 'individualTests', 'uniqueTests'));
    }

    /** Show Add Result Page */
    public function add_result()
    {
        $tests = Test::with('unit')
            ->where('status', 1)
            ->orderBy('test_name')
            ->get();

        return view('add_result_entry', compact('tests'));
    }

    /** AJAX: Fetch patient(s) by mobile (ROLE BASED) */
    public function fetch_patient_result(Request $request)
    {
        $mobile = $request->mobile;
        $role = Session::get('team_role');
        $userId = Session::get('admin_id');

        $query = Patient::where('mobile_number', $mobile);

        if ($role == 2) {
            $query->where('added_by', $userId)
                ->where('added_by_role', 2);
        }

        $patients = $query->get();

        return response()->json([
            'exists' => $patients->count() > 0,
            'patients' => $patients,
            'allowed' => $role == 1 ? true : ($patients->count() > 0)
        ]);
    }

    /** Store Result Entry */
    public function store_result(Request $request)
    {
        try {
            // Validation including op_id
            $validator = Validator::make($request->all(), [
                'op_id' => 'required|exists:ops,id',
                'patient_id' => 'required|exists:patients,id',
                'mobile_number' => 'required',
                'tests' => 'required|array',
                'tests.*.test_id' => 'required|exists:tests,id',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            // Role check
            if (Session::get('team_role') == 2) {
                $allowed = Patient::where('id', $request->patient_id)
                    ->where('added_by', Session::get('admin_id'))
                    ->where('added_by_role', 2)
                    ->exists();

                if (!$allowed) {
                    return back()->with('error', 'Unauthorized access to this patient.');
                }
            }

            /** STEP 1: Create Result Header */
            $header = ResultHeader::create([
                'patient_id' => $request->patient_id,
                'op_id' => $request->op_id,
                'mobile_number' => $request->mobile_number,
                'entered_by' => Session::get('admin_id'),
            ]);

            /** STEP 2: Store each test result */
            foreach ($request->tests as $t) {
                // Only save if a value was actually entered (optional, but cleaner)
                if (isset($t['result_value']) && $t['result_value'] !== null) {
                    $test = Test::find($t['test_id']);
                    ResultDetail::create([
                        'result_header_id' => $header->id,
                        'test_id' => $test->id,
                        'result_value' => $t['result_value'],
                        'is_abnormal' => $t['is_abnormal'] ?? 0,
                        'unit_id' => $test->unit_id,
                        'normal_range' => $test->normal_range,
                    ]);
                }
            }

            /** STEP 3: Update Specific OP status = 2 (Completed) */
            Op::where('id', $request->op_id)->update(['op_status' => 2]);

            return redirect()->route('manage-result')->with('success', 'Results saved successfully');

        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Common Filter Logic used by AJAX, Excel, and PDF Zip Exports
     */
    private function applyFiltersold($query, Request $request)
    {
        $role = Session::get('team_role');
        $userId = Session::get('admin_id');

        // Role based access
        if ($role == 2) {
            $query->whereHas('patient', function ($q) use ($userId) {
                $q->where('added_by', $userId)->where('added_by_role', 2);
            });
        }

        // 2. Filter by Package ID (Hybrid Logic for New and Old Records)
        if ($request->filled('package_id')) {
            $pkgId = (int) $request->package_id;

            $query->where(function ($mainQuery) use ($pkgId) {
                // SCENARIO A: Newer records linked via op_id
                $mainQuery->whereHas('op', function ($q) use ($pkgId) {
                    $q->where(function ($sq) use ($pkgId) {
                        $sq->whereJsonContains('packages_json', [['id' => $pkgId]])
                            ->orWhereJsonContains('packages_json', ['id' => $pkgId])
                            ->orWhereJsonContains('packages_json', [['id' => $pkgId, 'type' => 'package']])
                            ->orWhereJsonContains('packages_json', ['id' => $pkgId, 'type' => 'package']);
                    });
                })
                    // SCENARIO B: Old records where op_id is NULL
                    ->orWhere(function ($fallbackQuery) use ($pkgId) {
                        $fallbackQuery->whereNull('op_id');

                        // Get all patient IDs who have ever ordered this package in ANY of their OP records
                        $patientIdsWithPackage = Op::where(function ($q) use ($pkgId) {
                            $q->whereJsonContains('packages_json', [['id' => $pkgId]])
                                ->orWhereJsonContains('packages_json', ['id' => $pkgId])
                                ->orWhereJsonContains('packages_json', [['id' => $pkgId, 'type' => 'package']])
                                ->orWhereJsonContains('packages_json', ['id' => $pkgId, 'type' => 'package']);
                        })->pluck('patient_id')->unique()->toArray();

                        $fallbackQuery->whereIn('patient_id', $patientIdsWithPackage);
                    });
            });
        }

        // NEW: Filter by Team Member (added_by)
        if ($request->filled('team_id')) {
            $teamId = $request->team_id;
            $query->whereHas('patient', function ($q) use ($teamId) {
                $q->where('added_by', $teamId)->where('added_by_role', 2);
            });
        }

        // 3. Filter by Location (Hybrid Logic)
        if ($request->filled('location')) {
            $location = $request->location;
            $query->where(function ($q) use ($location) {
                // Check linked OP
                $q->whereHas('op', function ($sub) use ($location) {
                    $sub->where('location', $location);
                })
                    // OR check via patient fallback for old records
                    ->orWhere(function ($sub) use ($location) {
                        $sub->whereNull('op_id');
                        $patientIdsAtLocation = Op::where('location', $location)->pluck('patient_id')->toArray();
                        $sub->whereIn('patient_id', $patientIdsAtLocation);
                    });
            });
        }

        // Filter by Search Text (Name/Mobile)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($q2) use ($search) {
                    $q2->where('patient_name', 'LIKE', '%' . $search . '%');
                })->orWhere('mobile_number', 'LIKE', '%' . $search . '%');
            });
        }

        // Date Filters
        if ($request->filled('date_from')) {
            $from = Carbon::createFromFormat('d-m-Y', $request->date_from)->startOfDay();
            $query->where('created_at', '>=', $from);
        }

        if ($request->filled('date_to')) {
            $to = Carbon::createFromFormat('d-m-Y', $request->date_to)->endOfDay();
            $query->where('created_at', '<=', $to);
        }

        return $query;
    }

    private function applyFilters($query, Request $request)
    {
        $role = Session::get('team_role');
        $userId = Session::get('admin_id');

        // 1. SELECT only ResultHeader columns to prevent 'id' collision with joined tables
        $query->select('result_headers.*');

        // 2. JOIN tables required for filtering
        // We use leftJoin so we don't exclude records with missing OP or Patient data
        $query->leftJoin('ops', 'result_headers.op_id', '=', 'ops.id')
            ->leftJoin('patients', 'result_headers.patient_id', '=', 'patients.id');

        // 3. ROLE BASED ACCESS
        if ($role == 2) {
            $query->where('patients.added_by', $userId)
                ->where('patients.added_by_role', 2);
        }

        // 4. FILTER BY TEAM MEMBER (Joins already handled above)
        if ($request->filled('team_id')) {
            $teamId = $request->team_id;
            $query->where('patients.added_by', $teamId)
                ->where('patients.added_by_role', 2);
        }

        // 5. FILTER BY PACKAGE ID (Hybrid Logic)
        if ($request->filled('package_id')) {
            $pkgId = (int) $request->package_id;

            $query->where(function ($mainQuery) use ($pkgId) {
                // Scenario A: Check the joined OP table
                $mainQuery->where(function ($q) use ($pkgId) {
                    $q->whereJsonContains('ops.packages_json', [['id' => $pkgId]])
                        ->orWhereJsonContains('ops.packages_json', ['id' => $pkgId])
                        ->orWhereJsonContains('ops.packages_json', [['id' => $pkgId, 'type' => 'package']])
                        ->orWhereJsonContains('ops.packages_json', ['id' => $pkgId, 'type' => 'package']);
                })
                    // Scenario B: Fallback for records where op_id is NULL (legacy support)
                    ->orWhere(function ($fallbackQuery) use ($pkgId) {
                        $fallbackQuery->whereNull('result_headers.op_id');
                        // We perform a sub-pluck only for legacy fallback to maintain accuracy
                        $patientIds = Op::whereJsonContains('packages_json', [['id' => $pkgId]])
                            ->orWhereJsonContains('packages_json', ['id' => $pkgId])
                            ->pluck('patient_id')->unique()->toArray();
                        $fallbackQuery->whereIn('result_headers.patient_id', $patientIds);
                    });
            });
        }

        // 6. FILTER BY LOCATION (Hybrid Logic)
        if ($request->filled('location')) {
            $location = $request->location;
            $query->where(function ($q) use ($location) {
                $q->where('ops.location', $location)
                    ->orWhere(function ($sub) use ($location) {
                        $sub->whereNull('result_headers.op_id');
                        $pIds = Op::where('location', $location)->pluck('patient_id')->unique()->toArray();
                        $sub->whereIn('result_headers.patient_id', $pIds);
                    });
            });
        }

        // 7. FILTER BY SEARCH TEXT (Name/Mobile)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('patients.patient_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('result_headers.mobile_number', 'LIKE', '%' . $search . '%');
            });
        }

        // 8. DATE FILTERS (Explicit table reference prevents 500 errors)
        if ($request->filled('date_from')) {
            $from = Carbon::createFromFormat('d-m-Y', $request->date_from)->startOfDay();
            $query->where('result_headers.created_at', '>=', $from);
        }

        if ($request->filled('date_to')) {
            $to = Carbon::createFromFormat('d-m-Y', $request->date_to)->endOfDay();
            $query->where('result_headers.created_at', '<=', $to);
        }

        return $query;
    }

    public function manage_result()
    {
        $locations = Op::select('location')->whereNotNull('location')->distinct()->pluck('location');
        $packages = Package::where('status', 1)->orderBy('package_name')->get();

        $teams = Team::where('status', 1)->orderBy('first_name')->get();

        $query = ResultHeader::with(['patient']);
        // Initialize with default filters (role check)
        $query = $this->applyFilters($query, new Request());

        $results = $query->orderBy('id', 'DESC')->paginate(30);

        return view('manage_result_entry', compact('results', 'locations', 'packages', 'teams'));
    }

    public function edit_result_from_op($id)
    {
        $role = Session::get('team_role');
        $userId = Session::get('admin_id');

        $header = ResultHeader::with(['patient', 'results.test.unit'])->findOrFail($id);

        // Access Control
        if ($role == 2) {
            if ($header->patient->added_by != $userId || $header->patient->added_by_role != 2) {
                abort(403, "Unauthorized access.");
            }
        }

        // Latest OP for this patient
        $op = Op::with(['patient', 'company'])
            ->where('patient_id', $header->patient_id)
            ->latest('id')
            ->first();

        // DECODE JSON DATA
        $testsJson = is_array($op->tests_json) ? $op->tests_json : json_decode($op->tests_json ?? '[]', true);
        $packagesJson = is_array($op->packages_json) ? $op->packages_json : json_decode($op->packages_json ?? '[]', true);
        $testsNewJson = is_array($op->tests_new_json) ? $op->tests_new_json : json_decode($op->tests_new_json ?? '[]', true);

        $allGroups = [];
        $allUniqueTestIds = [];

        // // 1. PROCESS STANDARD PACKAGES
        // $pkgIds = collect($packagesJson)->map(function ($item) {
        //     return is_array($item) ? $item['id'] : $item;
        // })->filter()->all();

        // if (!empty($pkgIds)) {
        //     $packages = Package::whereIn('id', $pkgIds)->get();
        //     foreach ($packages as $pkg) {
        //         $tIds = is_array($pkg->tests) ? $pkg->tests : json_decode($pkg->tests ?? '[]', true);

        //         if (!empty($tIds)) {
        //             $tests = Test::with('unit')->whereIn('id', $tIds)->orderByRaw("FIELD(id," . implode(',', $tIds) . ")")->get();

        //             $allGroups[] = [
        //                 'title' => $pkg->package_name,
        //                 'is_profile' => false,
        //                 'subgroups' => [['name' => null, 'tests' => $tests]]
        //             ];
        //             $allUniqueTestIds = array_merge($allUniqueTestIds, $tIds);
        //         }
        //     }
        // }

        // 1. PROCESS STANDARD PACKAGES
        $pkgIds = collect($packagesJson)->map(function ($item) {
            return is_array($item) ? ($item['id'] ?? null) : $item;
        })->filter()->all();

        if (!empty($pkgIds)) {
            $packages = Package::whereIn('id', $pkgIds)->get();

            foreach ($packages as $pkg) {

                /* NEW STRUCTURE (parameters JSON) */
                if (!empty($pkg->parameters)) {

                    $params = is_array($pkg->parameters)
                        ? $pkg->parameters
                        : json_decode($pkg->parameters, true);

                    $subGroups = [];

                    if (!empty($params['global'])) {
                        $tests = Test::with('unit')
                            ->whereIn('id', $params['global'])
                            ->orderByRaw("FIELD(id," . implode(',', $params['global']) . ")")
                            ->get();

                        $subGroups[] = ['name' => null, 'tests' => $tests];
                        $allUniqueTestIds = array_merge($allUniqueTestIds, $params['global']);
                    }

                    foreach ($params['groups'] ?? [] as $g) {
                        $tests = Test::with('unit')
                            ->whereIn('id', $g['params'])
                            ->orderByRaw("FIELD(id," . implode(',', $g['params']) . ")")
                            ->get();

                        $subGroups[] = [
                            'name' => $g['group_name'],
                            'tests' => $tests
                        ];

                        $allUniqueTestIds = array_merge($allUniqueTestIds, $g['params']);
                    }

                    $allGroups[] = [
                        'title' => $pkg->package_name,
                        'is_profile' => false,
                        'subgroups' => $subGroups
                    ];

                    continue;
                }

                /* OLD STRUCTURE */
                $tIds = $this->decodeTestsColumn($pkg->tests);

                if (!empty($tIds)) {
                    $tests = Test::with('unit')
                        ->whereIn('id', $tIds)
                        ->orderByRaw("FIELD(id," . implode(',', $tIds) . ")")
                        ->get();

                    $allGroups[] = [
                        'title' => $pkg->package_name,
                        'is_profile' => false,
                        'subgroups' => [['name' => null, 'tests' => $tests]]
                    ];

                    $allUniqueTestIds = array_merge($allUniqueTestIds, $tIds);
                }
            }
        }

        // 2. PROCESS PROFILES (tests_new)
        $profileIds = array_unique(array_merge(
            collect($testsNewJson)->map(fn($i) => is_array($i) ? $i['id'] : $i)->toArray(),
            collect($packagesJson)->where('type', 'test_new')->pluck('id')->toArray()
        ));

        if (!empty($profileIds)) {
            $profiles = TestNew::whereIn('id', $profileIds)->get();
            foreach ($profiles as $profile) {
                $params = is_array($profile->parameters) ? $profile->parameters : json_decode($profile->parameters ?? '[]', true);
                $currentProfileSubgroups = [];

                $globalIds = $params['global'] ?? (isset($params[0]) ? $params : []);
                if (!empty($globalIds)) {
                    $tests = Test::with('unit')->whereIn('id', $globalIds)->orderByRaw("FIELD(id," . implode(',', $globalIds) . ")")->get();
                    $currentProfileSubgroups[] = ['name' => null, 'tests' => $tests];
                    $allUniqueTestIds = array_merge($allUniqueTestIds, $globalIds);
                }

                if (isset($params['groups'])) {
                    foreach ($params['groups'] as $g) {
                        $tests = Test::with('unit')->whereIn('id', $g['params'])->orderByRaw("FIELD(id," . implode(',', $g['params']) . ")")->get();
                        $currentProfileSubgroups[] = ['name' => $g['group_name'], 'tests' => $tests];
                        $allUniqueTestIds = array_merge($allUniqueTestIds, $g['params']);
                    }
                }

                $allGroups[] = [
                    'title' => $profile->test_name,
                    'is_profile' => true,
                    'subgroups' => $currentProfileSubgroups
                ];
            }
        }

        // 3. PROCESS INDIVIDUAL TESTS
        $indIds = collect($testsJson)->map(fn($i) => is_array($i) ? $i['id'] : $i)->all();
        $individualTests = collect();
        if (!empty($indIds)) {
            $individualTests = Test::with('unit')->whereIn('id', $indIds)->get();
            $allUniqueTestIds = array_merge($allUniqueTestIds, $indIds);
        }

        // 4. MAP EXISTING SAVED VALUES
        $existing = [];
        foreach ($header->results as $r) {
            $existing[$r->test_id] = [
                'value' => $r->result_value,
                'abnormal' => $r->is_abnormal,
            ];
        }

        $allUniqueTestIds = array_values(array_unique(array_filter($allUniqueTestIds)));
        $uniqueTests = empty($allUniqueTestIds) ? collect() : Test::whereIn('id', $allUniqueTestIds)->get();

        return view('edit_result_entry_from_op', compact('op', 'header', 'allGroups', 'individualTests', 'uniqueTests', 'existing'));
    }

    /* ============================================================
     | UPDATE RESULT
     | URL: /admin/update_result/{id}
     ============================================================ */
    public function update_result_from_op(Request $request, $id)
    {
        try {
            $header = ResultHeader::findOrFail($id);

            // Role restriction
            if (Session::get('team_role') == 2) {
                $allowed = Patient::where('id', $header->patient_id)
                    ->where('added_by', Session::get('admin_id'))
                    ->where('added_by_role', 2)
                    ->exists();

                if (!$allowed) {
                    return back()->with('error', "You are not allowed to update this result.");
                }
            }

            // Validate
            $validator = Validator::make($request->all(), [
                'tests' => 'required|array',
                'tests.*.test_id' => 'required|exists:tests,id',
                'tests.*.result_value' => 'nullable|string|max:255',
                'tests.*.is_abnormal' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            /* ---------------------------------------------------
             * DELETE OLD & INSERT NEW (simple & reliable)
             --------------------------------------------------- */
            ResultDetail::where('result_header_id', $header->id)->delete();

            foreach ($request->tests as $row) {
                $test = Test::find($row['test_id']);

                ResultDetail::create([
                    'result_header_id' => $header->id,
                    'test_id' => $test->id,
                    'unit_id' => $test->unit_id,
                    'normal_range' => $test->normal_range,
                    'result_value' => $row['result_value'],
                    'is_abnormal' => $row['is_abnormal'] ?? 0,
                ]);
            }

            return redirect()->route('manage-result')
                ->with('success', "Result updated successfully!");

        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
    /** VIEW RESULT */

/**
 * The PHP function `view_result` retrieves and organizes test result data based on user roles and
 * package structures for display in a view.
 * 
 * @param id The `id` parameter is used to identify a specific result header record in the database. It
 * is typically an integer value that uniquely identifies a result header entry. This parameter is
 * crucial for fetching the relevant data associated with a specific result header when processing
 * requests or generating reports.
 * 
 * @return The `view_result` function returns a view named 'result_view' with the variables ``,
 * ``, and `` compacted.
 */
    public function view_result($id)
    {
        $role = Session::get('team_role');
        $userId = Session::get('admin_id');

        $header = ResultHeader::with(['patient', 'results.test.unit'])->findOrFail($id);

        $op = null;
        if (!empty($header->op_id)) {
            $op = Op::find($header->op_id);
        }

        // Fallback for old records
        if (!$op) {
            $op = Op::where('patient_id', $header->patient_id)
                ->latest('id')
                ->first();
        }

        // Role restriction
        if ($role == 2) {
            if ($header->patient->added_by != $userId || $header->patient->added_by_role != 2) {
                abort(403, "You are not allowed to view this result.");
            }
        }

        $resultGroups = [];
        $processedTestIds = [];

        $packagesJson = $op
            ? (is_array($op->packages_json)
                ? $op->packages_json
                : json_decode($op->packages_json ?? '[]', true))
            : [];

        $testsNewJson = is_array($op->tests_new_json ?? null)
            ? $op->tests_new_json
            : json_decode($op->tests_new_json ?? '[]', true);

        /* =====================================================
           1. PROCESS PACKAGES (NEW + OLD STRUCTURE)
        ===================================================== */
        $packageIds = collect($packagesJson)
            ->map(fn($p) => is_array($p) ? ($p['id'] ?? null) : null)
            ->filter()
            ->unique()
            ->toArray();

        if (!empty($packageIds)) {
            $packages = Package::whereIn('id', $packageIds)->get();

            foreach ($packages as $pkg) {

                /* ========== NEW PACKAGE (parameters JSON) ========== */
                if (!empty($pkg->parameters)) {

                    $params = is_array($pkg->parameters)
                        ? $pkg->parameters
                        : json_decode($pkg->parameters, true);

                    $subgroups = [];

                    // Global parameters
                    if (!empty($params['global'])) {
                        $res = $header->results->whereIn('test_id', $params['global']);
                        if ($res->isNotEmpty()) {
                            $subgroups[] = [
                                'group_name' => null,
                                'results' => $this->sortByPackageOrder($res, $params['global'])
                            ];
                            $processedTestIds = array_merge($processedTestIds, $params['global']);
                        }
                    }

                    // Sub-groups
                    foreach ($params['groups'] ?? [] as $g) {
                        $res = $header->results->whereIn('test_id', $g['params']);
                        if ($res->isNotEmpty()) {
                            $subgroups[] = [
                                'group_name' => $g['group_name'],
                                'results' => $this->sortByPackageOrder($res, $g['params'])
                            ];
                            $processedTestIds = array_merge($processedTestIds, $g['params']);
                        }
                    }

                    if (!empty($subgroups)) {
                        $resultGroups[] = [
                            'title' => $pkg->package_name,
                            'subgroups' => $subgroups
                        ];
                    }

                }
                /* ========== OLD PACKAGE (tests column) ========== */ else {

                    $pkgTestIds = $this->decodeTestsColumn($pkg->tests);
                    if (empty($pkgTestIds)) {
                        continue;
                    }

                    $groupResults = $header->results->whereIn('test_id', $pkgTestIds);

                    if ($groupResults->isNotEmpty()) {
                        $resultGroups[] = [
                            'title' => $pkg->package_name,
                            'subgroups' => [
                                [
                                    'group_name' => null,
                                    'results' => $this->sortByPackageOrder($groupResults, $pkgTestIds)
                                ]
                            ]
                        ];

                        $processedTestIds = array_merge(
                            $processedTestIds,
                            $groupResults->pluck('test_id')->toArray()
                        );
                    }
                }
            }
        }

        /* =====================================================
           2. PROCESS PROFILES (TestNew)
        ===================================================== */
        $profileIds = array_unique(array_merge(
            collect($testsNewJson)->pluck('id')->toArray(),
            collect($packagesJson)->where('type', 'test_new')->pluck('id')->toArray()
        ));

        if (!empty($profileIds)) {
            $profiles = TestNew::whereIn('id', $profileIds)->get();

            foreach ($profiles as $profile) {
                $params = is_array($profile->parameters)
                    ? $profile->parameters
                    : json_decode($profile->parameters ?? '[]', true);

                $profileSubGroups = [];

                $globalIds = $params['global'] ?? (isset($params[0]) ? $params : []);
                if (!empty($globalIds)) {
                    $res = $header->results
                        ->whereIn('test_id', $globalIds)
                        ->whereNotIn('test_id', $processedTestIds);

                    if ($res->isNotEmpty()) {
                        $profileSubGroups[] = [
                            'group_name' => null,
                            'results' => $this->sortByPackageOrder($res, $globalIds)
                        ];
                        $processedTestIds = array_merge($processedTestIds, $globalIds);
                    }
                }

                foreach ($params['groups'] ?? [] as $g) {
                    $res = $header->results
                        ->whereIn('test_id', $g['params'])
                        ->whereNotIn('test_id', $processedTestIds);

                    if ($res->isNotEmpty()) {
                        $profileSubGroups[] = [
                            'group_name' => $g['group_name'],
                            'results' => $this->sortByPackageOrder($res, $g['params'])
                        ];
                        $processedTestIds = array_merge($processedTestIds, $g['params']);
                    }
                }

                if (!empty($profileSubGroups)) {
                    $resultGroups[] = [
                        'title' => $profile->test_name,
                        'subgroups' => $profileSubGroups
                    ];
                }
            }
        }

        /* =====================================================
           3. LEFTOVER INDIVIDUAL TESTS
        ===================================================== */
        $individualResults = $header->results->whereNotIn('test_id', $processedTestIds);

        if ($individualResults->isNotEmpty()) {
            $resultGroups[] = [
                'title' => 'Individual Tests',
                'subgroups' => [
                    [
                        'group_name' => null,
                        'results' => $individualResults->sortBy(
                            fn($r) => $r->test->display_order ?? 999
                        )
                    ]
                ]
            ];
        }

        return view('result_view', compact('header', 'op', 'resultGroups'));
    }

    private function extractUniqueTestsFromOp(Op $op)
    {
        $unique = [];

        // From individual tests_json
        $testsJson = is_array($op->tests_json) ? $op->tests_json : json_decode($op->tests_json ?? '[]', true);
        foreach ($testsJson as $t) {
            $id = is_array($t) ? ($t['id'] ?? null) : $t;
            if ($id)
                $unique[$id] = (int) $id;
        }

        // From packages_json
        $packagesJson = is_array($op->packages_json) ? $op->packages_json : json_decode($op->packages_json ?? '[]', true);
        foreach ($packagesJson as $p) {
            $type = is_array($p) ? ($p['type'] ?? 'package') : 'package';
            $id = is_array($p) ? ($p['id'] ?? null) : $p;

            if ($type === 'package' && $id) {
                $package = Package::find($id);
                if ($package) {
                    $testIds = is_array($package->tests) ? $package->tests : json_decode($package->tests, true);
                    if (is_array($testIds)) {
                        foreach ($testIds as $tid) {
                            $unique[(int) $tid] = (int) $tid;
                        }
                    }
                }
            }
        }

        return array_values($unique);
    }

    private function buildResultGroups($op, $header)
    {
        $resultGroups = [];
        $processedTestIds = [];

        if (!$op) {
            return [];
        }

        $packagesJson = is_array($op->packages_json)
            ? $op->packages_json
            : json_decode($op->packages_json ?? '[]', true);

        $testsNewJson = is_array($op->tests_new_json)
            ? $op->tests_new_json
            : json_decode($op->tests_new_json ?? '[]', true);

        /* =====================================================
           1. PROCESS PACKAGES (NEW + OLD)
        ===================================================== */
        $packageIds = collect($packagesJson)
            ->map(fn($p) => is_array($p) ? ($p['id'] ?? null) : null)
            ->filter()
            ->unique()
            ->toArray();

        if (!empty($packageIds)) {
            $packages = Package::whereIn('id', $packageIds)->get();

            foreach ($packages as $pkg) {

                /* ---------- NEW PACKAGE (parameters JSON) ---------- */
                if (!empty($pkg->parameters)) {

                    $params = is_array($pkg->parameters)
                        ? $pkg->parameters
                        : json_decode($pkg->parameters, true);

                    $subgroups = [];

                    // Global parameters
                    if (!empty($params['global'])) {
                        $res = $header->results->whereIn('test_id', $params['global']);
                        if ($res->isNotEmpty()) {
                            $subgroups[] = [
                                'group_name' => null,
                                'results' => $this->sortByPackageOrder($res, $params['global'])
                            ];
                            $processedTestIds = array_merge($processedTestIds, $params['global']);
                        }
                    }

                    // Sub-groups
                    foreach ($params['groups'] ?? [] as $g) {
                        $res = $header->results->whereIn('test_id', $g['params']);
                        if ($res->isNotEmpty()) {
                            $subgroups[] = [
                                'group_name' => $g['group_name'],
                                'results' => $this->sortByPackageOrder($res, $g['params'])
                            ];
                            $processedTestIds = array_merge($processedTestIds, $g['params']);
                        }
                    }

                    if (!empty($subgroups)) {
                        $resultGroups[] = [
                            'title' => $pkg->package_name,
                            'subgroups' => $subgroups
                        ];
                    }

                }
                /* ---------- OLD PACKAGE (tests column) ---------- */ else {

                    $pkgTestIds = $this->decodeTestsColumn($pkg->tests);
                    if (empty($pkgTestIds)) {
                        continue;
                    }

                    $groupResults = $header->results->whereIn('test_id', $pkgTestIds);

                    if ($groupResults->isNotEmpty()) {
                        $resultGroups[] = [
                            'title' => $pkg->package_name,
                            'subgroups' => [
                                [
                                    'group_name' => null,
                                    'results' => $this->sortByPackageOrder($groupResults, $pkgTestIds)
                                ]
                            ]
                        ];

                        $processedTestIds = array_merge(
                            $processedTestIds,
                            $groupResults->pluck('test_id')->toArray()
                        );
                    }
                }
            }
        }

        /* =====================================================
           2. PROCESS PROFILES (TestNew)
        ===================================================== */
        $profileIds = array_unique(array_merge(
            collect($testsNewJson)->pluck('id')->toArray(),
            collect($packagesJson)->where('type', 'test_new')->pluck('id')->toArray()
        ));

        if (!empty($profileIds)) {
            $profiles = TestNew::whereIn('id', $profileIds)->get();

            foreach ($profiles as $profile) {
                $params = is_array($profile->parameters)
                    ? $profile->parameters
                    : json_decode($profile->parameters ?? '[]', true);

                $profileSubGroups = [];

                $globalIds = $params['global'] ?? (isset($params[0]) ? $params : []);
                if (!empty($globalIds)) {
                    $res = $header->results
                        ->whereIn('test_id', $globalIds)
                        ->whereNotIn('test_id', $processedTestIds);

                    if ($res->isNotEmpty()) {
                        $profileSubGroups[] = [
                            'group_name' => null,
                            'results' => $this->sortByPackageOrder($res, $globalIds)
                        ];
                        $processedTestIds = array_merge($processedTestIds, $globalIds);
                    }
                }

                foreach ($params['groups'] ?? [] as $g) {
                    $res = $header->results
                        ->whereIn('test_id', $g['params'])
                        ->whereNotIn('test_id', $processedTestIds);

                    if ($res->isNotEmpty()) {
                        $profileSubGroups[] = [
                            'group_name' => $g['group_name'],
                            'results' => $this->sortByPackageOrder($res, $g['params'])
                        ];
                        $processedTestIds = array_merge($processedTestIds, $g['params']);
                    }
                }

                if (!empty($profileSubGroups)) {
                    $resultGroups[] = [
                        'title' => $profile->test_name,
                        'subgroups' => $profileSubGroups
                    ];
                }
            }
        }

        /* =====================================================
           3. LEFTOVER INDIVIDUAL TESTS
        ===================================================== */
        $individualResults = $header->results->whereNotIn('test_id', $processedTestIds);

        if ($individualResults->isNotEmpty()) {
            $resultGroups[] = [
                'title' => 'Individual Tests',
                'subgroups' => [
                    [
                        'group_name' => null,
                        'results' => $individualResults->sortBy(
                            fn($r) => $r->test->display_order ?? 999
                        )
                    ]
                ]
            ];
        }

        return $resultGroups;
    }

    /** AJAX RESULTS FOR FILTERS (ROLE BASED) */
    public function ajax_results(Request $request)
    {
        $query = ResultHeader::with('patient')->orderBy('id', 'DESC');
        $query = $this->applyFilters($query, $request);

        $results = $query->paginate(30);

        return response()->json([
            'results' => $results,
            'pagination' => (string) $results->links()
        ]);
    }

    public function export_results(Request $request)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        $query = ResultHeader::with(['patient', 'results.test.unit'])->orderBy('id', 'DESC');
        $data = $this->applyFilters($query, $request)->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Test_Results_Matrix_" . date('d-m-Y_H-i') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        // Identify dynamic columns based on filtered data
        $uniqueTestColumns = [];
        foreach ($data as $row) {
            foreach ($row->results as $detail) {
                if ($detail->test) {
                    $uniqueTestColumns[$detail->test_id] = $detail->test->test_name;
                }
            }
        }

        $callback = function () use ($data, $uniqueTestColumns) {
            $file = fopen('php://output', 'w');
            $csvHeader = ['Name', 'Age', 'Gender', 'Phno', 'OP No', 'Date'];

            foreach ($uniqueTestColumns as $testName) {
                $csvHeader[] = $testName;
            }
            fputcsv($file, $csvHeader);

            foreach ($data as $row) {
                $op = Op::where('patient_id', $row->patient_id)->latest('id')->first();
                $csvRow = [
                    $row->patient->patient_name ?? '',
                    $row->patient->age ?? '',
                    $row->patient->gender ?? '',
                    $row->mobile_number ?? '',
                    $op->op_reg_no ?? '',
                    $row->created_at ? $row->created_at->format('d-m-Y') : ''
                ];

                $currentRowResults = $row->results->pluck('result_value', 'test_id')->toArray();

                foreach ($uniqueTestColumns as $testId => $testName) {
                    $csvRow[] = $currentRowResults[$testId] ?? '';
                }
                fputcsv($file, $csvRow);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function export_results_pdf_zip(Request $request)
    {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '1024M');

        $query = ResultHeader::with(['patient', 'results.test.unit'])->orderBy('id', 'DESC');
        $results = $this->applyFilters($query, $request)->get();

        if ($results->isEmpty())
            return back()->with('error', 'No results found.');

        $zipFolder = storage_path('app/temp_reports_' . time());
        mkdir($zipFolder, 0777, true);

        foreach ($results as $header) {
            $op = Op::where('patient_id', $header->patient_id)->latest('id')->first();
            $resultGroups = $this->buildResultGroups($op, $header);

            $name = preg_replace('/[^A-Za-z0-9]/', '', $header->patient->patient_name);
            $fileName = "{$name}_{$header->id}.pdf";

            $pdf = Pdf::loadView('result_pdf', compact('header', 'op', 'resultGroups'))->setPaper('a4', 'portrait');
            $pdf->save($zipFolder . '/' . $fileName);
        }

        $zipPath = storage_path('app/TestResults_' . date('d-m-Y_H-i') . '.zip');
        $zip = new \ZipArchive;
        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            foreach (glob($zipFolder . "/*.pdf") as $pdfFile) {
                $zip->addFile($pdfFile, basename($pdfFile));
            }
            $zip->close();
        }

        foreach (glob($zipFolder . "/*") as $file)
            unlink($file);
        rmdir($zipFolder);

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    private function sortByPackageOrder($results, $packageTestIds)
    {
        $ordered = collect();
        foreach ($packageTestIds as $testId) {
            $match = $results->firstWhere('test_id', $testId);
            if ($match)
                $ordered->push($match);
        }
        $leftovers = $results->reject(fn($r) => in_array($r->test_id, $packageTestIds));
        return $ordered->merge($leftovers);
    }

    /* ============================================================
       PDF GENERATION METHODS (UPDATED TO USE GROUPS)
       ============================================================ */

    public function download_result($id)
    {
        $role = Session::get('team_role');
        $userId = Session::get('admin_id');

        $header = ResultHeader::with(['patient', 'results.test.unit'])->findOrFail($id);

        if ($role == 2) {
            if ($header->patient->added_by != $userId || $header->patient->added_by_role != 2) {
                abort(403, "You are not allowed to download this PDF.");
            }
        }

        $op = Op::where('patient_id', $header->patient_id)->latest('id')->first();

        // ⭐ USE HELPER TO BUILD GROUPS
        $resultGroups = $this->buildResultGroups($op, $header);

        $patientName = preg_replace('/[^A-Za-z0-9]/', '', $header->patient->patient_name);
        $dob = $header->patient->dob ? date('d-m-Y', strtotime($header->patient->dob)) : 'DOB';
        $fileName = "{$patientName}_{$dob}.pdf";

        // Pass 'resultGroups' instead of 'packageName'
        $pdf = Pdf::loadView('result_pdf', compact('header', 'op', 'resultGroups'))
            ->setPaper('a4', 'portrait');

        return $pdf->download($fileName);
    }

    /**
     * SEND BULK WHATSAPP REPORTS
     * Fixed the 'Unknown column results' error
     */
    public function send_bulk_whatsapp(Request $request)
    {
        // ... (Time limits & validations same as before) ...
        set_time_limit(300);

        $location = $request->location;
        if (empty($location))
            return response()->json(['status' => 'error', 'message' => 'Location is required.']);

        $patientIdsInLocation = Op::where('location', $location)->pluck('patient_id')->toArray();
        $results = ResultHeader::with(['patient', 'results.test.unit'])
            ->whereIn('patient_id', $patientIdsInLocation)
            ->where(function ($q) {
                $q->where('whatsapp_sent', 0)->orWhereNull('whatsapp_sent');
            })
            ->orderBy('id', 'DESC')->get();

        if ($results->isEmpty())
            return response()->json(['status' => 'error', 'message' => 'No pending reports.']);

        $successCount = 0;
        $failCount = 0;
        $path = public_path('uploads/temp_reports');
        if (!File::exists($path))
            File::makeDirectory($path, 0777, true, true);

        foreach ($results as $header) {
            $op = Op::where('patient_id', $header->patient_id)->latest('id')->first();
            if (!$op)
                continue;

            // ⭐ USE HELPER
            $resultGroups = $this->buildResultGroups($op, $header);

            $cleanName = preg_replace('/[^A-Za-z0-9]/', '', $header->patient->patient_name);
            $pdfFileName = "Report_{$cleanName}_{$header->id}.pdf";
            $pdfFullPath = $path . '/' . $pdfFileName;
            $pdfPublicUrl = asset('public/uploads/temp_reports/' . $pdfFileName);

            $pdf = Pdf::loadView('result_pdf', compact('header', 'op', 'resultGroups'))->setPaper('a4', 'portrait');
            $pdf->save($pdfFullPath);

            // ... (Message Sending Logic Same as Before) ...
            $mobile = preg_replace('/[^0-9]/', '', $header->mobile_number);
            if (strlen($mobile) == 10)
                $mobile = '91' . $mobile;

            $payload = [
                "from_phone_number_id" => "",
                "phone_number" => $mobile,
                "template_name" => "patient_report",
                "template_language" => "en",
                "header_document" => $pdfPublicUrl,
                "header_document_name" => "Test report",
                "header_field_1" => "",
                "location_latitude" => "22.22",
                "location_longitude" => "22.22",
                "location_name" => "Lab Name",
                "location_address" => "Address",
                "field_1" => "Test result",
                "field_2" => "",
                "field_3" => "",
                "field_4" => "MMCC",
                "field_5" => "",
                "button_0" => "",
                "button_1" => "",
                "copy_code" => ""
            ];

            $status = $this->sendMessage($payload);
            if ($status === 'success') {
                ResultHeader::where('id', $header->id)->update(['whatsapp_sent' => 1]);
                $successCount++;
            } else {
                $failCount++;
            }
        }

        return response()->json(['status' => 'success', 'message' => "Sent: $successCount, Failed: $failCount"]);
    }


    /** * SEND SINGLE WHATSAPP REPORT 
     * Handles both initial send and resend functionality
     */
    public function send_single_whatsapp(Request $request)
    {
        $id = $request->id;
        $header = ResultHeader::with(['patient', 'results.test.unit'])->find($id);
        if (!$header)
            return response()->json(['status' => 'error', 'message' => 'Result not found.']);

        $op = Op::where('patient_id', $header->patient_id)->latest('id')->first();

        // ⭐ USE HELPER
        $resultGroups = $this->buildResultGroups($op, $header);

        $path = public_path('uploads/temp_reports');
        if (!File::exists($path))
            File::makeDirectory($path, 0777, true, true);

        $cleanName = preg_replace('/[^A-Za-z0-9]/', '', $header->patient->patient_name);
        $pdfFileName = "Report_{$cleanName}_{$header->id}.pdf";
        $pdfFullPath = $path . '/' . $pdfFileName;
        $pdfPublicUrl = asset('public/uploads/temp_reports/' . $pdfFileName);

        $pdf = Pdf::loadView('result_pdf', compact('header', 'op', 'resultGroups'))->setPaper('a4', 'portrait');
        $pdf->save($pdfFullPath);

        // ... (Send Logic Same as Bulk) ...
        $mobile = preg_replace('/[^0-9]/', '', $header->mobile_number);
        if (strlen($mobile) == 10)
            $mobile = '91' . $mobile;

        $payload = [
            "from_phone_number_id" => "",
            "phone_number" => $mobile,
            "template_name" => "patient_report",
            "template_language" => "en",
            "header_document" => $pdfPublicUrl,
            "header_document_name" => "Test report",
            "header_field_1" => "",
            "location_latitude" => "22.22",
            "location_longitude" => "22.22",
            "location_name" => "Lab",
            "location_address" => "Addr",
            "field_1" => "Test result",
            "field_2" => "",
            "field_3" => "",
            "field_4" => "MMCC",
            "field_5" => "",
            "button_0" => "",
            "button_1" => "",
            "copy_code" => ""
        ];

        $status = $this->sendMessage($payload);

        if ($status === 'success') {
            ResultHeader::where('id', $header->id)->update(['whatsapp_sent' => 1]);
            return response()->json(['status' => 'success', 'message' => 'Report sent successfully.']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Failed to send.']);
        }
    }


    /**
     * Handle Admin Request to send SMS
     */
    public function send_sms_report(Request $request)
    {
        $id = $request->id;
        $header = ResultHeader::with(['patient'])->find($id);

        if (!$header) {
            return response()->json(['status' => 'error', 'message' => 'Result not found.']);
        }

        // 1. Generate Secure Signed URL (valid for 7 days)
        // $downloadLink = URL::temporarySignedRoute(
        //     'public-report-download',
        //     now()->addDays(7),
        //     ['id' => $header->id]
        // );

        $id = $header->id;
        $expires = now()->addDays(7)->timestamp;

        // IMPORTANT: must match EXACTLY same format as validation
        $baseUrl = url('/download-report-secure') . "?id=$id&expires=$expires";

        $signature = sha1($baseUrl . config('app.key'));

        $downloadLink = $baseUrl . "&signature=" . $signature;

        // $downloadLink = route('public-report-download', ['id' => $header->id]);

        // 2. Prepare Data
        $mobile = preg_replace('/[^0-9]/', '', $header->mobile_number);
        $patientName = $header->patient->patient_name ?? 'Patient';

        // 3. Static SMS Logic Placeholder
        $smsContent = "Hello $patientName, your lab report is ready. Download here: $downloadLink - Rich Labz.";

        $smsStatus = $this->sendStaticSMS($mobile, $smsContent);

        if ($smsStatus) {
            return response()->json([
                'status' => 'success',
                'message' => 'SMS sent successfully!',
                'preview_link' => $downloadLink // Sent back for verification
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'SMS gateway error.']);
    }

    public function public_report_download(Request $request)
    {
        $id = $request->id;
        $expires = $request->expires;
        $signature = $request->signature;

        if (!$id || !$expires || !$signature) {
            abort(403, 'Invalid request');
        }

        // 1. Expiry check
        if (now()->timestamp > $expires) {
            abort(403, 'Link expired');
        }

        // 2. MUST match EXACT same format used while generating
        $baseUrl = url('/download-report-secure') . "?id=$id&expires=$expires";

        $expectedSignature = sha1($baseUrl . config('app.key'));

        // 3. Compare
        if (!hash_equals($expectedSignature, $signature)) {
            abort(403, 'Invalid signature');
        }

        // 4. Continue
        $header = ResultHeader::with(['patient', 'results.test.unit'])->findOrFail($id);

        $op = Op::where('patient_id', $header->patient_id)->latest('id')->first();
        $resultGroups = $this->buildResultGroups($op, $header);

        $pdf = Pdf::loadView('result_pdf', compact('header', 'op', 'resultGroups'))
            ->setPaper('a4', 'portrait');

        $fileName = preg_replace('/[^A-Za-z0-9]/', '', $header->patient->patient_name) . "_Report.pdf";

        return $pdf->stream($fileName);
    }

    /**
     * SMS Gateway Integration Point (Static)
     */
    private function sendStaticSMS($mobile, $message)
    {
        // Log for verification since you don't have gateway code yet
        \Log::info("SMS to $mobile: $message");

        /* GATEWAY CODE EXAMPLE:
           $url = "https://api.gateway.com/send?to=$mobile&msg=".urlencode($message);
           $response = file_get_contents($url);
           return ($response == 'OK');
        */

        return true; // Keep this true for now to simulate success
    }

}

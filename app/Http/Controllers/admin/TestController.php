<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Test;     // The Parameters (Source)
use App\Models\TestNew;  // The New Table (Destination)
use Illuminate\Support\Facades\Validator;
use Exception;

class TestController extends Controller
{
    public function add_test_new()
    {
        $parameters = Test::where('status', 1)->orderBy('test_name')->get();
        return view('add_test_new', compact('parameters'));
    }

    public function store_test_new(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'test_name' => 'required|string|max:255',
                'parameters_order' => 'required',
                'price' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            // The hidden input sends a JSON string
            TestNew::create([
                'test_name' => $request->test_name,
                'parameters' => $request->parameters_order,
                'price' => $request->price,
                'status' => 1,
            ]);

            return redirect()->route('manage-test-new')->with('success', 'Test created successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function manage_test_new()
    {
        $tests = TestNew::orderBy('id', 'DESC')->paginate(10);
        return view('manage_test_new', compact('tests'));
    }

    // --- AJAX SEARCH ---
    public function ajax_tests_new(Request $request)
    {
        $query = TestNew::query();

        if ($request->searchText) {
            $s = $request->searchText;
            $query->where('test_name', 'LIKE', "%$s%");
        }

        if ($request->statusFilter == "Active") {
            $query->where('status', 1);
        } elseif ($request->statusFilter == "Inactive") {
            $query->where('status', 0);
        }

        $tests = $query->orderBy('id', 'DESC')->paginate(10);

        return response()->json([
            'tests' => $tests,
            'pagination' => $tests->links()->render(),
        ]);
    }

    public function test_new_details($id)
    {
        $testNew = TestNew::findOrFail($id);
        $data = json_decode($testNew->parameters, true);

        // Collect all IDs to fetch names from 'tests' table
        $allIds = $data['global'] ?? [];
        if (!empty($data['groups'])) {
            foreach ($data['groups'] as $group) {
                $allIds = array_merge($allIds, $group['params']);
            }
        }

        $allParams = Test::whereIn('id', $allIds)->get()->keyBy('id');

        // Build the ordered output for the detail view
        $output = [];

        // 1. Add Ungrouped
        foreach (($data['global'] ?? []) as $pid) {
            if (isset($allParams[$pid]))
                $output[] = $allParams[$pid]->test_name;
        }

        // 2. Add Groups
        foreach (($data['groups'] ?? []) as $group) {
            $output[] = "--- " . $group['group_name'] . " ---";
            foreach ($group['params'] as $pid) {
                if (isset($allParams[$pid]))
                    $output[] = $allParams[$pid]->test_name;
            }
        }

        return response()->json([
            'test' => $testNew,
            'parameters' => $output
        ]);
    }

    public function update_test_new(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:tests_new,id',
                'test_name' => 'required|string|max:255',
                'parameters_order' => 'required',
                'price' => 'required|numeric|min:0',
                'status' => 'required|in:0,1'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
            }

            $test = TestNew::findOrFail($request->id);
            $test->update([
                'test_name' => $request->test_name,
                'parameters' => $request->parameters_order,
                'price' => $request->price,
                'status' => $request->status,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Test updated successfully.']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
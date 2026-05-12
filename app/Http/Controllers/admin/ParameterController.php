<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Unit;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

class ParameterController extends Controller
{
    /**
     * Show Add Test Page
     */
    public function add_test()
    {
        $units = Unit::where('status', 1)->orderBy('unit_name')->get();
        return view('add_test', compact('units'));
    }

    /**
     * Show Manage Tests Page
     */
    public function manage_test()
    {
        $tests = Test::with('unit')->orderBy('id', 'DESC')->paginate(10);
        return view('manage_tests', compact('tests'));
    }

    public function ajax_tests(Request $request)
    {
        $query = Test::with('unit');

        if ($request->searchText) {
            $query->where('test_name', 'LIKE', '%' . $request->searchText . '%');
        }

        $tests = $query->orderBy('id', 'DESC')->paginate(10);

        return response()->json([
            'tests' => $tests,
            'pagination' => (string) $tests->links()
        ]);
    }


    /**
     * Store Test
     */
    public function store_test(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'test_name' => 'required|string|max:255',
                'unit_id' => 'nullable|exists:units,id',
                'normal_range' => 'nullable|string|max:255',
                'price' => 'required|string|max:100',
                // 'status'        => 'required|in:1,0',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            Test::create([
                'test_name' => $request->test_name,
                'unit_id' => $request->unit_id,
                'normal_range' => $request->normal_range,
                'price' => $request->price,
                'status' => 1,
            ]);

            return redirect()->route('manage-tests')
                ->with('success', 'Test added successfully.');

        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Edit Test Page
     */
    public function edit_test($id)
    {
        $test = Test::findOrFail($id);
        $units = Unit::where('status', 1)->orderBy('unit_name')->get();
        return view('edit_test', compact('test', 'units'));
    }

    /**
     * Update Test
     */
    public function update_test(Request $request, $id)
    {
        try {
            $test = Test::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'test_name' => 'required|string|max:255',
                'unit_id' => 'nullable|exists:units,id',
                'normal_range' => 'nullable|string|max:255',
                'price' => 'required|string|max:100',
                // 'status'        => 'required|in:1,0',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $test->update([
                'test_name' => $request->test_name,
                'unit_id' => $request->unit_id,
                'normal_range' => $request->normal_range,
                'price' => $request->price,
                'status' => 1,
            ]);

            return redirect()->route('manage-tests')
                ->with('success', 'Test updated successfully.');

        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete Test
     */
    public function delete_test($id)
    {
        try {
            $test = Test::findOrFail($id);
            $test->delete();

            return redirect()->back()
                ->with('success', 'Test deleted successfully.');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    public function orderTests()
    {
        $tests = Test::orderBy('display_order')->orderBy('test_name')->get();

        return view('test_order', compact('tests'));
    }

    public function orderTestsSave(Request $request)
    {
        $order = $request->order; // array of test IDs in new order

        foreach ($order as $index => $id) {
            Test::where('id', $id)->update(['display_order' => $index + 1]);
        }

        return response()->json(['status' => 'success']);
    }

}

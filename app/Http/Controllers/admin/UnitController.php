<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

class UnitController extends Controller
{
    /**
     * Show Add Unit Page
     */
    public function add_unit()
    {
        return view('add_units');
    }

    /**
     * Show Manage Units Page
     */
    public function manage_unit()
    {
        $units = Unit::orderBy('id', 'DESC')->paginate(10); // pagination
        return view('manage_units', compact('units'));
    }


    /**
     * Store Multiple Units
     */
    public function store_units(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'unit.*' => 'required|string|max:255',
                'status.*' => 'required|in:1,0',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();

            foreach ($request->unit as $index => $unitName) {
                Unit::create([
                    'unit_name' => $unitName,
                    'status' => $request->status[$index],
                ]);
            }

            DB::commit();

            return redirect()->route('manage-units')->with('success', 'Units added successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Fetch units for listing (AJAX filters)
     */
    public function get_units(Request $request)
    {
        $query = Unit::query();

        if ($request->searchText) {
            $query->where('unit_name', 'LIKE', '%' . $request->searchText . '%');
        }

        if ($request->statusFilter !== null && $request->statusFilter !== '') {
            $query->where('status', $request->statusFilter);
        }

        $units = $query->orderBy('id', 'DESC')->paginate(10);

        return response()->json([
            'units' => $units,
            'pagination' => (string) $units->links()
        ]);
    }


    /**
     * Edit Unit Page
     */
    public function edit_unit($id)
    {
        $unit = Unit::findOrFail($id);
        return view('edit_unit', compact('unit'));
    }

    /**
     * Update Unit
     */
    public function update_unit(Request $request, $id)
    {
        try {
            $unit = Unit::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'unit_name' => 'required|string|max:255',
                'status' => 'required|in:1,0',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $unit->update([
                'unit_name' => $request->unit_name,
                'status' => $request->status,
            ]);

            return redirect()->route('manage-units')->with('success', 'Unit updated successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete Unit
     */
    public function delete_unit($id)
    {
        try {
            $unit = Unit::findOrFail($id);
            $unit->delete();

            return redirect()->back()->with('success', 'Unit deleted successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}

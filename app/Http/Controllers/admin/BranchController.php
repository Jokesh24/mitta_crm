<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Location;

use Exception;

class BranchController extends Controller
{
    public function add_branch()
    {
        $locations = Location::where('status', 1)->orderBy('location_name')->get();
        return view('add_branch', compact('locations'));
    }

    /**
     * Store Test
     */

    public function store_branch(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'branch_name' => 'required|string|max:255',
                'location_id' => 'required|exists:locations,id',
                'status' => 'required|in:1,0',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            Branch::create([
                'branch_name' => $request->branch_name,
                'location_id' => $request->location_id,
                'status' => $request->status,
            ]);

            return redirect()->route('manage-branch')
                ->with('success', 'Branch added successfully.');

        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function manage_branch()
    {
        $branches = Branch::with('location')->orderBy('id', 'DESC')->paginate(10);

        return view('manage_branch', compact(var_name: 'branches'));

    }

    public function ajax_branches(Request $request)
    {
        $query = Branch::with('location');

        if ($request->searchText) {
            $query->where('branch_name', 'LIKE', '%' . $request->searchText . '%')
                ->orWhereHas('location', function ($q) use ($request) {
                    $q->where('location_name', 'LIKE', '%' . $request->searchText . '%');
                });
        }

        if ($request->statusFilter !== null && $request->statusFilter !== "") {
            $query->where('status', $request->statusFilter);
        }

        $branches = $query->orderBy('id', 'DESC')->paginate(10);

        return response()->json([
            'branches' => $branches,
            'pagination' => (string) $branches->links()
        ]);
    }


    /**
     * Edit Test Page
     */
    public function edit_branch($id)
    {
        $branch = Branch::findOrFail($id);
        $locations = Location::where('status', 1)->orderBy('location_name')->get();
        return view('edit_branch', compact('branch', 'locations'));
    }

    /**
     * Update Test
     */
    public function update_branch(Request $request, $id)
    {
        try {
            $branch = Branch::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'branch_name' => 'required|string|max:255',
                'location_id' => 'nullable|exists:locations,id',
                // 'normal_range' => 'nullable|string|max:255',
                // 'sample_type'   => 'nullable|string|max:100',
                'status'        => 'required|in:1,0',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $branch->update([
                'branch_name' => $request->branch_name,
                'location_id' => $request->location_id,
                // 'normal_range' => $request->normal_range,
                // 'sample_type'   => $request->sample_type,
                'status' => $request->status,
            ]);

            return redirect()->route('manage-branch')
                ->with('success', 'Test updated successfully.');

        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete Test
     */
    public function delete_branch($id)
    {
        try {
            $branch = Branch::findOrFail($id);
            $branch->delete();

            return redirect()->back()
                ->with('success', 'Test deleted successfully.');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }



}
<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
use Illuminate\Support\Facades\Validator;
use Exception;

class LocationController extends Controller
{
    /**
     * Show Add Location Page
     */
    public function add_location()
    {
        return view('location');
    }

    /**
     * Store Location
     */
    public function store_location(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'location_name' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'pincode' => 'nullable|string|max:20',
                'status' => 'required|in:1,0',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            Location::create([
                'location_name' => $request->location_name,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'status' => $request->status,
            ]);

            return redirect()->route('manage-locations')
                ->with('success', 'Location added successfully.');

        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Manage Locations Page
     */
    public function manage_location()
    {
        $locations = Location::orderBy('id','DESC')->paginate(10);
        return view('manage_locations', compact('locations'));
    }

    /**
     * AJAX Search / Filter Locations
     */
    public function ajax_locations(Request $request)
    {
        $query = Location::query();

        if ($request->searchText) {
            $query->where('location_name', 'LIKE', '%' . $request->searchText . '%')
                  ->orWhere('state', 'LIKE', '%' . $request->searchText . '%')
                  ->orWhere('pincode', 'LIKE', '%' . $request->searchText . '%');
        }

        if ($request->statusFilter !== null && $request->statusFilter !== '') {
            $query->where('status', $request->statusFilter);
        }

        $locations = $query->orderBy('id','DESC')->paginate(10);

        return response()->json([
            'locations' => $locations,
            'pagination' => (string) $locations->links(),
        ]);
    }

    /**
     * Edit Location Page
     */
    public function edit_location($id)
    {
        $location = Location::findOrFail($id);
        return view('edit_location', compact('location'));
    }

    /**
     * Update Location
     */
    public function update_location(Request $request, $id)
    {
        try {
            $location = Location::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'location_name' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'pincode' => 'nullable|string|max:20',
                'status' => 'required|in:1,0',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $location->update([
                'location_name' => $request->location_name,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'status' => $request->status,
            ]);

            return redirect()->route('manage-locations')
                ->with('success', 'Location updated successfully.');

        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete Location
     */
    public function delete_location($id)
    {
        try {
            $location = Location::findOrFail($id);
            $location->delete();

            return redirect()->back()->with('success', 'Location deleted successfully.');

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}

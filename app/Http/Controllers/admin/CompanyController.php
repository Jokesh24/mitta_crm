<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Validator;
use Exception;

class CompanyController extends Controller
{
    public function add_company()
    {
        return view('company');
    }

    /**
     * Store Company
     */
    public function store_company(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'company_name' => 'required|string|max:255',
                'contact_person' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'status' => 'required|in:1,0',
                'address' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            Company::create([
                'company_name' => $request->company_name,
                'contact_person' => $request->contact_person,
                'phone' => $request->phone,
                'email' => $request->email,
                'status' => $request->status,
                'address' => $request->address,
            ]);

            return redirect()->route('manage-company')->with('success', 'Company added successfully.');

        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function manage_company()
    {
        $companies = Company::orderBy('id', 'DESC')->paginate(10);
        return view('manage_companies', compact('companies'));
    }

    // AJAX LISTING
    public function ajax_companies(Request $request)
    {
        $query = Company::query();

        if ($request->searchText) {
            $query->where(function ($q) use ($request) {
                $q->where('company_name', 'LIKE', '%' . $request->searchText . '%')
                    ->orWhere('contact_person', 'LIKE', '%' . $request->searchText . '%')
                    ->orWhere('phone', 'LIKE', '%' . $request->searchText . '%');
            });
        }

        if ($request->statusFilter !== "" && $request->statusFilter !== null) {
            $query->where('status', $request->statusFilter == "Active" ? 1 : 0);
        }

        // Pagination
        $companies = $query->orderBy('id', 'DESC')->paginate(10);

        return response()->json([
            'companies' => $companies,
            'pagination' => (string) $companies->links()
        ]);
    }


    /**
     * Edit Page
     */
    public function edit_company($id)
    {
        $company = Company::findOrFail($id);
        return view('edit_company', compact('company'));
    }

    /**
     * Update Company
     */
    public function update_company(Request $request, $id)
    {
        try {
            $company = Company::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'company_name' => 'required|string|max:255',
                'contact_person' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'status' => 'required|in:1,0',
                'address' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $company->update([
                'company_name' => $request->company_name,
                'contact_person' => $request->contact_person,
                'phone' => $request->phone,
                'email' => $request->email,
                'status' => $request->status,
                'address' => $request->address,
            ]);

            return redirect()->route('manage-company')->with('success', 'Company updated successfully.');

        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete Company
     */
    public function delete_company($id)
    {
        try {
            $company = Company::findOrFail($id);
            $company->delete();

            return redirect()->back()->with('success', 'Company deleted successfully.');

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}

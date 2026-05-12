<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;


class TeamController extends Controller
{
    /** Show Add Team Page */
    public function add_team()
    {
        $branches = Branch::where('status', 1)->get();
        return view('add_team', compact('branches'));
    }

    /** Store Team Member */
    public function store_team(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'emp_code' => 'nullable|string|max:50',
            'email' => 'required|email|unique:team,email',
            'mobile' => 'required|digits:10',
            'role' => 'required|in:1,2',
            'branch_id' => 'required|exists:branches,id',
            'date_of_join' => 'required|date_format:d-m-Y',
            'status' => 'required|in:0,1',
            'address' => 'nullable|string'
        ]);

        Team::create($request->only([
            'first_name',
            'last_name',
            'emp_code',
            'email',
            'mobile',
            'role',
            'branch_id',
            'status',
            'address'
        ]) + [
            'date_of_join' => Carbon::createFromFormat('d-m-Y', $request->date_of_join)->format('Y-m-d'),
            'password' => Hash::make('123123') // 🔥 Default password added
        ]);

        return redirect()->route('manage-team')->with('success', 'Team Member Added Successfully!');
    }

    /** Show Manage Team Page */
    public function manage_team()
    {
        $branches = Branch::all();
        return view('manage_team', compact('branches'));
    }

    public function team_list_ajax(Request $request)
    {
        $query = Team::with('branch');

        // 🔎 SEARCH
        if (!empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                    ->orWhere('last_name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('mobile', 'like', "%{$request->search}%");
            });
        }

        // 🧑 ROLE FILTER
        if (!empty($request->role)) {
            $query->where('role', $request->role);
        }

        // 🏢 BRANCH FILTER
        if (!empty($request->branch)) {
            $query->where('branch_id', $request->branch);
        }

        // 📅 DATE RANGE – handle from only, to only, or both
        $from = $request->joined_from;
        $to = $request->joined_to;

        if (!empty($from)) {
            $fromDate = Carbon::createFromFormat('d-m-Y', $from)->format('Y-m-d');
            $query->whereDate('date_of_join', '>=', $fromDate);
        }

        if (!empty($to)) {
            $toDate = Carbon::createFromFormat('d-m-Y', $to)->format('Y-m-d');
            $query->whereDate('date_of_join', '<=', $toDate);
        }

        $team = $query->orderBy('id', 'DESC')->paginate(10);

        return response()->json([
            'html' => $this->renderTeamRows($team),
            'pagination' => (string) $team->links()
        ]);
    }


    private function renderTeamRows($team)
    {
        $html = '';
        $i = $team->firstItem();

        foreach ($team as $t) {
            $role = $t->role == 1 ? "Admin" : "Lab Technician";
            $status = $t->status == 1
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-danger">Inactive</span>';

            $html .= "
        <tr>
            <td>{$i}</td>
            <td>{$t->first_name} {$t->last_name}</td>
            <td>{$role}</td>
            <td>{$t->branch->branch_name}</td>
            <td>{$t->email}</td>
            <td>{$t->mobile}</td>
            <td>" . date('d-m-Y', strtotime($t->date_of_join)) . "</td>
            <td>{$status}</td>
            <td>
                <a href='edit_team/{$t->id}' class='action-btn edit'><i class='bi bi-pencil-fill'></i></a>
                <a href='javascript:void(0)' onclick='deleteTeam({$t->id})' class='action-btn delete'><i class='bi bi-trash3-fill'></i></a>
            </td>
        </tr>";

            $i++;
        }

        return $html;
    }


    /** Edit Page */
    public function edit_team($id)
    {
        // echo "hi"; exit;
        $team = Team::findOrFail($id);
        $branches = Branch::where('status', 1)->get();
        return view('edit_team', compact('team', 'branches'));
    }

    /** Update Team Member */
    public function update_team(Request $request, $id)
    {
        $team = Team::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:team,email,' . $id,
            'mobile' => 'required|digits:10',
            'role' => 'required|in:1,2',
            'branch_id' => 'required|exists:branches,id',
            'date_of_join' => 'required|date_format:d-m-Y',
            'status' => 'required|in:0,1'
        ]);

        $team->update($request->only([
            'first_name',
            'last_name',
            'emp_code',
            'email',
            'mobile',
            'role',
            'branch_id',
            'status',
            'address'
        ]) + [
            'date_of_join' => Carbon::createFromFormat('d-m-Y', $request->date_of_join)->format('Y-m-d')
        ]);

        return redirect()->route('manage-team')->with('success', 'Team Member Updated Successfully!');
    }

    /** Delete Team Member */
    public function delete_team($id)
    {
        Team::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}

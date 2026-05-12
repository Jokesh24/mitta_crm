<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Team;
use App\Models\Op;
use App\Models\ResultHeader;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        $role = Session::get('team_role');
        $userId = Session::get('admin_id');

        /* ============================================================
           1️⃣ COMPANIES (Admin only)
        ============================================================ */
        $companies = Company::count();


        /* ============================================================
           2️⃣ EMPLOYEES (Admin only)
        ============================================================ */
        if ($role == 1) {
            $employees_total = Team::count();
            $employees_active = Team::where('status', 1)->count();
            $employees_inactive = Team::where('status', 0)->count();
        } else {
            // Lab Technician should NOT see overall employee data
            $employees_total = 0;
            $employees_active = 0;
            $employees_inactive = 0;
        }


        /* ============================================================
           3️⃣ OP REGISTRATIONS (Role based)
        ============================================================ */

        if ($role == 2) {
            // LAB TECH — only OPs of their own patients
            $op_today = Op::whereDate('created_at', today())
                ->whereHas('patient', function ($q) use ($userId) {
                    $q->where('added_by', $userId)
                        ->where('added_by_role', 2);
                })->count();

            $op_this_month = Op::whereMonth('created_at', now()->month)
                ->whereHas('patient', function ($q) use ($userId) {
                    $q->where('added_by', $userId)
                        ->where('added_by_role', 2);
                })->count();

        } else {
            // ADMIN — sees everything
            $op_today = Op::whereDate('created_at', today())->count();
            $op_this_month = Op::whereMonth('created_at', now()->month)->count();
        }


        /* ============================================================
           4️⃣ RESULT ENTRY (Role based)
        ============================================================ */

        if ($role == 2) {

            $results_completed = ResultHeader::where("whatsapp_sent", 1)
                ->whereHas('patient', function ($q) use ($userId) {
                    $q->where('added_by', $userId)
                        ->where('added_by_role', 2);
                })->count();

            $results_pending = ResultHeader::where("whatsapp_sent", 0)
                ->whereHas('patient', function ($q) use ($userId) {
                    $q->where('added_by', $userId)
                        ->where('added_by_role', 2);
                })->count();

        } else {

            $results_completed = ResultHeader::where("whatsapp_sent", 1)->count();
            $results_pending = ResultHeader::where("whatsapp_sent", 0)->count();
        }

        $results_total = $results_completed + $results_pending;


        /* ============================================================
           5️⃣ WHATSAPP DELIVERY (use same filtered result info)
        ============================================================ */
        $whatsapp_delivered = $results_completed;
        $whatsapp_failed = $results_pending;


        /* ============================================================
           6️⃣ TEST COUNT
        ============================================================ */
        $tests_total = Test::count();


        /* ============================================================
           7️⃣ MONTHLY OP REGISTRATION CHART (Role based)
        ============================================================ */

        if ($role == 2) {

            $monthly_op = Op::selectRaw("COUNT(ops.id) as count, MONTH(ops.created_at) as month")
                ->whereHas('patient', function ($q) use ($userId) {
                    $q->where('added_by', $userId)
                        ->where('added_by_role', 2);
                })
                ->groupBy("month")
                ->pluck("count", "month")
                ->toArray();

        } else {

            $monthly_op = Op::selectRaw("COUNT(*) as count, MONTH(created_at) as month")
                ->groupBy("month")
                ->pluck("count", "month")
                ->toArray();
        }

        $op_chart_data = [];
        for ($m = 1; $m <= 12; $m++) {
            $op_chart_data[] = $monthly_op[$m] ?? 0;
        }


        /* ============================================================
           8️⃣ MONTHLY RESULT COMPLETION CHART
        ============================================================ */

        if ($role == 2) {

            $monthly_completed = ResultHeader::selectRaw("COUNT(result_headers.id) as count, MONTH(result_headers.created_at) as month")
                ->where("whatsapp_sent", 1)
                ->whereHas('patient', function ($q) use ($userId) {
                    $q->where('added_by', $userId)
                        ->where('added_by_role', 2);
                })
                ->groupBy("month")
                ->pluck("count", "month")
                ->toArray();

            $monthly_pending = ResultHeader::selectRaw("COUNT(result_headers.id) as count, MONTH(result_headers.created_at) as month")
                ->where("whatsapp_sent", 0)
                ->whereHas('patient', function ($q) use ($userId) {
                    $q->where('added_by', $userId)
                        ->where('added_by_role', 2);
                })
                ->groupBy("month")
                ->pluck("count", "month")
                ->toArray();

        } else {

            $monthly_completed = ResultHeader::selectRaw("COUNT(*) as count, MONTH(created_at) as month")
                ->where("whatsapp_sent", 1)
                ->groupBy("month")
                ->pluck("count", "month")
                ->toArray();

            $monthly_pending = ResultHeader::selectRaw("COUNT(*) as count, MONTH(created_at) as month")
                ->where("whatsapp_sent", 0)
                ->groupBy("month")
                ->pluck("count", "month")
                ->toArray();
        }

        $completed_chart_data = [];
        $pending_chart_data = [];

        for ($m = 1; $m <= 12; $m++) {
            $completed_chart_data[] = $monthly_completed[$m] ?? 0;
            $pending_chart_data[] = $monthly_pending[$m] ?? 0;
        }


        /* ============================================================
           RETURN VIEW
        ============================================================ */
        return view('index', compact(
            'companies',
            'employees_total',
            'employees_active',
            'employees_inactive',
            'op_today',
            'op_this_month',
            'results_total',
            'results_completed',
            'results_pending',
            'whatsapp_delivered',
            'whatsapp_failed',
            'tests_total',
            'op_chart_data',
            'completed_chart_data',
            'pending_chart_data'
        ));
    }
}

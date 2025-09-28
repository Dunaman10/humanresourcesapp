<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Presences;
use App\Models\Task;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
  public function index()
  {
    $employee = Employee::count();
    $department = Department::count();
    $payroll = Payroll::count();
    $presence = Presences::count();
    $tasks = Task::all();

    return view('dashboard.index', compact('employee', 'department', 'payroll', 'presence', 'tasks'));
  }

  public function presence()
  {
    $data = Presences::where('status', 'present')
      ->selectRaw('MONTH(date) as month, YEAR(date) as year, COUNT(*) as total_present')
      ->groupBy('year', 'month')
      ->orderBy('month', 'asc')
      ->get();

    $temp = [];
    $i = 0;

    // Contoh yang di inginkan : [5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60]
    foreach ($data as $item) {
      $temp[$i] = $item->total_present;
      $i++;
    }

    return response()->json($temp);
  }
}

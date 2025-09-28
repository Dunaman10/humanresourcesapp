<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\Request;

class PayrollsController extends Controller
{
  public function index()
  {

    if (session('role') == 'HR') {
      $payrolls = Payroll::all();
    } else {
      $payrolls = Payroll::where('employee_id', session('employee_id'))->get();
    }

    return view('payrolls.index', compact('payrolls'));
  }

  public function create()
  {
    $employees = Employee::all();
    return view('payrolls.create', compact('employees'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'employee_id' => 'required|exists:employees,id',
      'salary' => 'required|numeric',
      'bonuses' => 'required|numeric',
      'deductions' => 'required|numeric',
      'net_salary' => 'required|numeric',
      'pay_date' => 'required|date',
    ]);

    Payroll::create($request->all());

    return redirect()->route('payrolls.index')->with('success', 'Payroll record created successfully.');
  }

  public function edit($id)
  {
    $payroll = Payroll::findOrFail($id);
    $employees = Employee::all();
    return view('payrolls.edit', compact('payroll', 'employees'));
  }

  public function update(Request $request, $id)
  {
    $request->validate([
      'employee_id' => 'required|exists:employees,id',
      'salary' => 'required|numeric',
      'bonuses' => 'required|numeric',
      'deductions' => 'required|numeric',
      'net_salary' => 'required|numeric',
      'pay_date' => 'required|date',
    ]);

    $payroll = Payroll::findOrFail($id);
    $payroll->update($request->all());

    return redirect()->route('payrolls.index')->with('success', 'Payroll record updated successfully.');
  }

  public function show(Payroll $payroll)
  {
    return view('payrolls.show', compact('payroll'));
  }

  public function destroy($id)
  {
    $payroll = Payroll::findOrFail($id);
    $payroll->delete();

    return redirect()->route('payrolls.index')->with('success', 'Payroll record deleted successfully.');
  }
}

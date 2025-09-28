<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
  public function index()
  {
    if (session('role') == 'HR') {
      $leaveRequests = LeaveRequest::all();
    } else {
      $leaveRequests = LeaveRequest::where('employee_id', session('employee_id'))->get();
    }

    return view('leave-request.index', compact('leaveRequests'));
  }

  public function create()
  {
    $employees = Employee::all();
    return view('leave-request.create', compact('employees'));
  }

  public function store(Request $request)
  {

    if (session('role') == 'HR') {
      $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'leave_type' => 'required|string|max:255',
        'start_date' => 'required|date',
        'end_date' => 'required|date',
      ]);

      $request->merge(['status' => 'pending']);
      LeaveRequest::create($request->all());
    } else {
      LeaveRequest::create([
        'employee_id' => session('employee_id'),
        'leave_type' => $request->leave_type,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'status' => 'pending'
      ]);
    }

    return redirect()->route('leave-requests.index')->with('success', 'Leave request created successfully.');
  }

  public function edit(LeaveRequest $leaveRequest)
  {
    $employees = Employee::all();
    return view('leave-request.edit', compact('leaveRequest', 'employees'));
  }

  public function update(Request $request, LeaveRequest $leaveRequest)
  {
    $request->validate([
      'employee_id' => 'required|exists:employees,id',
      'leave_type' => 'required|string|max:255',
      'start_date' => 'required|date',
      'end_date' => 'required|date',
    ]);

    $request->merge(['status' => 'Pending']);

    $leaveRequest->update($request->all());
    return redirect()->route('leave-requests.index')->with('success', 'Leave request updated successfully.');
  }

  public function confirm(int $id)
  {
    $leaveRequest = LeaveRequest::findOrFail($id);
    $leaveRequest->update([
      'status' => 'confirm'
    ]);

    return redirect()->route('leave-requests.index')->with('success', 'Leave request confirmed successfully.');
  }

  public function reject(int $id)
  {
    $leaveRequest = LeaveRequest::findOrFail($id);
    $leaveRequest->update([
      'status' => 'reject'
    ]);

    return redirect()->route('leave-requests.index')->with('success', 'Leave request rejected successfully.');
  }

  public function destroy(LeaveRequest $leave_request)
  {
    $leave_request->delete();
    return redirect()->route('leave-requests.index')->with('success', 'Leave request deleted successfully.');
  }
}

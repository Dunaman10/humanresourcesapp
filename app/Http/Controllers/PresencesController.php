<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Presences;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PresencesController extends Controller
{
  public function index()
  {
    if (session('role') == 'HR') {
      $presences = Presences::all();
    } else {
      $presences = Presences::where('employee_id', session('employee_id'))->get();
    }

    return view('presences.index', compact('presences'));
  }

  public function create()
  {
    $employees = Employee::all();
    return view('presences.create', compact('employees'));
  }

  public function store(Request $request)
  {

    if (session('role') == 'HR') {
      $request->validate([
        'employee_id' => 'required',
        'date' => 'required|date',
        'check_in' => 'required',
        'check_out' => 'nullable',
        'status' => 'required|string',
      ]);

      Presences::create($request->all());
    } else {
      Presences::create([
        'employee_id' => session('employee_id'),
        'check_in' => Carbon::now()->format('Y-m-d H:i:s'),
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'date' => Carbon::now()->format('Y-m-d'),
        'status' => 'present',
      ]);
    }

    return redirect()->route('presences.index')
      ->with('success', 'Presence recorded successfully.');
  }

  public function edit(Presences $presence)
  {
    $employees = Employee::all();
    return view('presences.edit', compact('presence', 'employees'));
  }

  public function update(Request $request, Presences $presence)
  {
    $request->validate([
      'employee_id' => 'required',
      'date' => 'required|date',
      'check_in' => 'required',
      'check_out' => 'nullable',
      'status' => 'required|string',
    ]);

    $presence->update($request->all());

    return redirect()->route('presences.index')
      ->with('success', 'Presence updated successfully.');
  }

  public function destroy(Presences $presence)
  {
    $presence->delete();

    return redirect()->route('presences.index')
      ->with('success', 'Presence deleted successfully.');
  }
}

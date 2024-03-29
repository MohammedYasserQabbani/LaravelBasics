<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     *  Retrieve all employees.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $employees = Employee::all();

        if ($employees->isEmpty()) {
            return response()->json([], Response::HTTP_NO_CONTENT); // رمز الحالة 204
        }
        $employees->each(function ($employee) {
            $employee->image_path = asset($employee->image);
        });
        return response()->json([
            "message" => "Employees retrieved successfully",
            "data" => $employees,
        ], Response::HTTP_OK); // رمز الحالة 200
    }
    /**
     *  Retrieve aspecific employee by Id
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                "message" => "Employee not found",
            ], Response::HTTP_NOT_FOUND); // رمز الحالة 404
        }
        $employee->image_path = asset($employee->image);
        return response()->json([
            "message" => "Employee retrieved successfully",
            "data" => $employee,
        ], Response::HTTP_OK); // رمز الحالة 200
    }
    /**
     * Store a new employee
     *
     * @param \App\Http\Request\EmployeeRequest $employeeRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(EmployeeRequest $employeeRequest)
    {
        $validatedData = $employeeRequest->validated();

        if($employeeRequest->hasFile('image')) {
            $image_path = $employeeRequest->file('image')->store('images', 'public');
            $validatedData['image'] = $image_path;
        }


        $employee = new Employee();
        $employee->fill($validatedData);
        $employee->save();
        return response()->json([
            "message" => "Data stored successfully",
        ], Response::HTTP_CREATED); // رمز الحالة 201
    }
    /**
     * Update an existing employee.
     *
     *  @param \App\Http\Request\EmployeeRequest $employeeRequest
     *  @param int $id
     *  @return \Illuminate\Http\JsonResponse
     */
    public function update(EmployeeRequest $employeeRequest, $id)
    {
        $validatedData = $employeeRequest->validated();
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                "message" => "Employee not found",
            ], Response::HTTP_NOT_FOUND); // رمز الحالة 404
        }
        if($employeeRequest->hasFile('image')) {
            if($employee->image) {
                Storage::disk('public')->delete($employee->image);
            }
            $image_path = $employeeRequest->file('image')->store('images', 'public');
            $validatedData['image'] = $image_path;
        }

        $employee->fill($validatedData);
        $employee->save();

        return response()->json([
            "message" => "Data updated successfully",
        ], Response::HTTP_OK); // رمز الحالة 200
    }
    /**
     *  Delete an existing employee.
     *
     * @param int $id.
     * @return  \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                "message" => "Employee not found",
            ], Response::HTTP_NOT_FOUND); // رمز الحالة 404
        }

        if($employee->image) {
            Storage::disk('public')->delete($employee->image);
        }
        $employee->delete();

        return response()->json([
            "message" => "Employee deleted successfully",
        ], Response::HTTP_OK); // رمز الحالة 200
    }
}

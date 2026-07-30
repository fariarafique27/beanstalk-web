<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AttendanceService;

class AttendanceController extends Controller
{
    protected $attendanceService;
    
    public function __construct(AttendanceService $attendanceService){
        $this->attendanceService = $attendanceService;
    }

    public function index(Request $request)
    {
        try {
            $response = $this->attendanceService->getAttendances($request->all());
            
            $attendances = $response['data'] ?? [];
            $totalPresent = $response['total_present'] ?? 0;
            $totalAbsent = $response['total_absent'] ?? 0;

            return view('attendances.index', compact('attendances', 'totalPresent', 'totalAbsent'));
        } catch (\Throwable $e) {
            return $this->getException($e);
        }
    }

   

    // public function show(Request $request, $id)
    // {
    //     try {
    //         // Delegate search, filter, and pagination logic to the service
    //         $attendances = $this->attendanceService->getFilteredAttendanceDetails($id, $request);

    //         return view('attendances.showDetail', compact('attendances', 'id'));
    //     } catch (\Throwable $e) {
    //         return $this->getException($e);
    //     }
    // }

    public function show(Request $request, $id)
    {
        try {
            $attendances = $this->attendanceService->getFilteredAttendanceDetails($id, $request);

            logger('[CONTROLLER] attendances passed to view', ['attendances' => $attendances]);

            return view('attendances.showDetail', compact('attendances', 'id'));
        } catch (\Throwable $e) {
            logger()->error('[CONTROLLER] Exception: ' . $e->getMessage());
            return $this->getException($e);
        }
    }
}
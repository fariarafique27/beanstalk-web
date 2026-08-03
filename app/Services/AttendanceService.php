<?php

namespace App\Services;
use Illuminate\Pagination\LengthAwarePaginator;

class AttendanceService extends GuzzleApiService
{

    public function getAttendances($params = [])
    {

        $response = $this->get('attendances', $params);

        logger('This is inside getAttendances of AttendanceService ' , $response );
        //dd( $response);
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        if (!$response['success']) {
            return redirect()->back()
                ->withErrors($response['errors'] ?? ['error' => $response['message']])
                ->withInput();
        }

        $attendances = $response['data'] ?? [];
        $totalPresent = $response['total_present'] ?? 0;
        $totalAbsent = $response['total_absent'] ?? 0;

        return view('attendances.index', compact('attendances', 'totalPresent', 'totalAbsent'));
    }


    public function getFilteredAttendanceDetails($id, $request)
    {
        try {
            $response = $this->get("attendances/{$id}", $request->all());

            logger('[GUZZLE] raw response from backend API', ['response' => $response]);

            if (isset($response['success']) && $response['success'] && isset($response['data'])) {
                logger('[GUZZLE] returning data key', ['data' => $response['data']]);
                return $response['data'];
            }

            logger('[GUZZLE] success/data missing, returning empty array', ['response' => $response]);
            return [];
        } catch (\Exception $e) {
            logger()->error('[GUZZLE] Attendance Service Error: ' . $e->getMessage());
            return [];
        }
    }
    
}
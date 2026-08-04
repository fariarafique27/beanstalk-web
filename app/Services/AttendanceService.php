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

        // FIX: total_present/total_absent/data are now nested one level
        // deeper under 'data', since AttendanceResponse wraps them via
        // successResponse() same as every other endpoint in the app.
        $attendances = $response['data']['data'] ?? [];
        $totalPresent = $response['data']['total_present'] ?? 0;
        $totalAbsent = $response['data']['total_absent'] ?? 0;

        return view('attendances.index', compact('attendances', 'totalPresent', 'totalAbsent'));
    }


    public function getFilteredAttendanceDetails($id, $request)
    {
        try {
            $response = $this->get("attendances/{$id}", $request->all());

            logger('[GUZZLE] raw response from backend API', ['response' => $response]);

            // Unchanged: AttendanceResponse::history() passes the paginator
            // straight through as 'data' with no extra nesting, so this
            // still reads correctly under the new response shape.
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

// class AttendanceService extends GuzzleApiService
// {

//     public function getAttendances($params = [])
//     {

//         $response = $this->get('attendances', $params);

//         logger('This is inside getAttendances of AttendanceService ' , $response );
//         //dd( $response);
//         if ($response instanceof \Illuminate\Http\RedirectResponse) {
//             return $response;
//         }

//         if (!$response['success']) {
//             return redirect()->back()
//                 ->withErrors($response['errors'] ?? ['error' => $response['message']])
//                 ->withInput();
//         }

//         $attendances = $response['data'] ?? [];
//         $totalPresent = $response['total_present'] ?? 0;
//         $totalAbsent = $response['total_absent'] ?? 0;

//         return view('attendances.index', compact('attendances', 'totalPresent', 'totalAbsent'));
//     }


//     public function getFilteredAttendanceDetails($id, $request)
//     {
//         try {
//             $response = $this->get("attendances/{$id}", $request->all());

//             logger('[GUZZLE] raw response from backend API', ['response' => $response]);

//             if (isset($response['success']) && $response['success'] && isset($response['data'])) {
//                 logger('[GUZZLE] returning data key', ['data' => $response['data']]);
//                 return $response['data'];
//             }

//             logger('[GUZZLE] success/data missing, returning empty array', ['response' => $response]);
//             return [];
//         } catch (\Exception $e) {
//             logger()->error('[GUZZLE] Attendance Service Error: ' . $e->getMessage());
//             return [];
//         }
//     }
    
// }
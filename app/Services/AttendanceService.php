<?php

namespace App\Services;
use Illuminate\Pagination\LengthAwarePaginator;

class AttendanceService extends GuzzleApiService
{

    public function getAttendances($params = [])
    {
        // Calls the $this->get() method inherited from GuzzleApiService
        // This automatically handles the base_uri, Bearer token, and apikey headers.
        return $this->get('attendances', $params);
    }

    // public function getFilteredAttendanceDetails($id, $request)
    // {
    //     // Fetch JSON from backend API with query filters
    //     $response = $this->get("attendances/{$id}", ['query' => $request->all()]);

    //     //dd('Raw Guzzle Return Value:', $response);
    //     // Return the decoded body or array data directly
    //     return $response['data'] ?? $response;
    // }

    // public function getFilteredAttendanceDetails($id, $request)
    // {
    //     try {
    //         $response = $this->get("attendances/{$id}", $request->all());
    //         //dd('Raw Guzzle Return Value:', $response);
    //         if (isset($response['success']) && $response['success'] && isset($response['data'])) {
    //             return $response['data']; // Returns the paginator array (containing "data", "links", etc.)
    //         }

    //         return [];
    //     } catch (\Exception $e) {
    //         logger()->error('Attendance Service Error: ' . $e->getMessage());
    //         return [];
    //     }
    // }

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
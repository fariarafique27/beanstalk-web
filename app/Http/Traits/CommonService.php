<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

trait CommonService
{
    public function errorResponse($message, $data = [], $code = 400)
    {
        return [
            'success' => false,
            'message' => $message,
            'errors'  => $data,
            'status_code' => $code,
        ];
    }

    public function successResponse($message, $data = [], $code = 200)
    {
        return [
            'success' => true,
            'message' => $message,
            'data'    => $data,
            'status_code' => $code,
        ];
    }

    public function getException($e)
    {
        $response = [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'code' => $e->getCode(),
        ];

        Log::error('Exception caught in API request', $response);

        if (app()->environment('local')) {
            $fullMessage = "Exception: {$response['message']}<br>" .
                           "File: {$response['file']}<br>" .
                           "Line: {$response['line']}<br>" .
                           "Code: {$response['code']}";
        } else {
            $fullMessage = 'An unexpected error occurred. Please try again later.';
        }

        return redirect()->back()
            ->withErrors(['error' => $fullMessage])
            ->withInput();
    }


}
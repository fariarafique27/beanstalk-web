<?php
namespace App\Http\Controllers;

use App\Services\DeviceSettingsService;
use Illuminate\Http\Request;

class DeviceSettingsController extends Controller
{
    public function __construct(protected DeviceSettingsService $deviceSettingsService) {}

    public function edit()
    {
        logger('--- ROUTE HIT: GET /settings/device (edit) ---');
        try {
            return $this->deviceSettingsService->getDevice();
        } catch (\Throwable $e) {
            return $this->getException($e);
        }
    }

    public function update(Request $request)
    {
        logger('--- ROUTE HIT: POST /settings/device (update) ---', $request->all());
        try {
            return $this->deviceSettingsService->saveDevice($request);
        } catch (\Throwable $e) {
            return $this->getException($e);
        }
    }

    public function sync(Request $request)
    {
        logger('--- ROUTE HIT: POST /settings/device/sync (sync) ---');
        try {
            $response = $this->deviceSettingsService->syncNow();
            return response()->json($response);
        } catch (\Throwable $e) {
            return $this->getException($e);
        }
    }
}
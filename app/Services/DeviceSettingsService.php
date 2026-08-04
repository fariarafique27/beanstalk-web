<?php
namespace App\Services;

class DeviceSettingsService extends GuzzleApiService
{
    
    public function getDevice()
    {
        $response = $this->get('device');

        logger('--- BACKEND API RESPONSE RECEIVED ---', ['response' => $response]);
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        if (!$response['success']) {   // <-- was 'success', now 'status'
            return redirect()->back()
                ->withErrors($response['errors'] ?? ['error' => $response['message']]);
        }

        $device = $response['data'] ?? null;
        return view('settings.device', compact('device'));
    }

    public function saveDevice($request)
    {
        $response = $this->post('device', $request->only(['ip', 'port', 'name']));

        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        if (!$response['success']) {
            return redirect()->back()
                ->withErrors($response['errors'] ?? ['error' => $response['message']])
                ->withInput();
        }

        return redirect()->route('settings.device.edit')->with('success', 'Device settings saved.');
    }

    public function syncNow()
    {
        return $this->post('device/sync');
    }
}
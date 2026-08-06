<?php

namespace App\Services;

class UserInviteService extends GuzzleApiService
{
    /**
     * Create/invite a new user via backend API
     * 
     * @param array $data
     * @return array
     */
    // public function inviteUser(array $data)
    // {
    //     // Call backend API endpoint
    //     $response = $this->post('users', $data);

    //     logger('UserInviteService response:', $response);

    //     // Handle error response
    //     if (!isset($response['success']) || !$response['success']) {
    //         throw new \Exception($response['message'] ?? 'Failed to create user');
    //     }

    //     // Return user data
    //     return $response['data'] ?? [];
    // }

    public function inviteUser(array $data)
{
    logger()->info('UserInviteService->inviteUser called with data:', $data);

    // Call backend API endpoint
    $response = $this->post('users', $data);

    logger()->info('UserInviteService raw response from Guzzle:', ['response' => $response]);

    // Handle error response check
    if (!isset($response['success']) || !$response['success']) {
        $errorMsg = $response['message'] ?? 'Failed to create user';
        logger()->warning('UserInviteService detected API failure flag:', ['message' => $errorMsg, 'response' => $response]);
        throw new \Exception($errorMsg);
    }

    return $response['data'] ?? [];
}
}
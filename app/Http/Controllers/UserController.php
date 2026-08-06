<?php

namespace App\Http\Controllers;

use App\Services\UserInviteService;
use App\Http\Requests\CreateUserRequest;

class UserController extends Controller
{
    protected UserInviteService $userInviteService;

    public function __construct(UserInviteService $userInviteService)
    {
        $this->userInviteService = $userInviteService;
    }

    /**
     * Store new user via backend API
     * 
     * @param CreateUserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    // public function store(CreateUserRequest $request)
    // {
    //     try {
    //         // Validation happens automatically via FormRequest
    //         // Call backend API via service
    //         $response = $this->userInviteService->inviteUser($request->validated());

    //         // Success
    //         return redirect()->back()->with('success', 'Employee added successfully!');

    //     } catch (\Exception $e) {
    //         logger()->error('Error creating user', ['error' => $e->getMessage()]);
    //         return redirect()->back()->withErrors(['error' => $e->getMessage()]);
    //     }
    // }

    public function store(CreateUserRequest $request)
{
    // 1. Log that the frontend hit the controller successfully
    logger()->info('UserController@store triggered from frontend', [
        'all_input' => $request->all(),
        'session_org_id' => session('organization_id')
    ]);

    try {
        $validatedData = $request->validated();
        
        logger()->info('FormRequest validation passed', $validatedData);

        // Call backend API via service
        $response = $this->userInviteService->inviteUser($validatedData);

        logger()->info('UserInviteService completed successfully', ['response' => $response]);

        return redirect()->back()->with('success', 'Employee added successfully!');

    } catch (\Exception $e) {
        // 2. Log any failure / exception caught in the controller
        logger()->error('UserController@store failed with exception: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        
        return redirect()->back()->withErrors(['error' => $e->getMessage()]);
    }
}
}
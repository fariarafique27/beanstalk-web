<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProfileService;

class CompanyController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService ){
        $this->profileService = $profileService;
    }

    public function dashboard(){
        logger('dashboard - CompanyController ');
        logger('All Session Data:', session()->all());
    try{
        return $this->profileService->dashboard();
    }
    catch(\Throwable $e){
        return $this->getException($e);
    }
}
}

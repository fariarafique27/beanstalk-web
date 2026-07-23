@extends('layouts.app')

@section('title', 'Set Your Password')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="col-md-5 col-lg-4">
        <div class="card border-0 shadow-lg p-4 rounded-4">
            <div class="card-body">
                <div class="text-center mb-4">
                    <h4 class="fw-bold">Welcome to HRMS!</h4>
                    <p class="text-muted fs-7">Set up your account password to complete registration.</p>
                </div>

                <form action="/set-password" method="POST">
                    @csrf
                    <input type="hidden" name="token" value="{{ request('token') }}">
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">New Password</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm fw-semibold">
                        Activate Account & Login
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
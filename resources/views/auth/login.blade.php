@extends('layouts.guest')

@section('title', 'Sign In')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card border-0 shadow-lg rounded-4 p-4">
                <div class="card-body">
                    
                    {{-- Logo / Header --}}
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mb-3" style="width: 54px; height: 54px;">
                            <i class="ti ti-building-community fs-1"></i>
                        </div>
                        <h4 class="fw-bold mb-1">Welcome Back</h4>
                        <p class="text-muted fs-7">Sign in to your HRMS Admin Console</p>
                    </div>

                    {{-- Success Alert --}}
                    @if(session('success'))
                        <div class="alert alert-success py-2 fs-7 mb-3">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Error Alerts --}}
                    @if($errors->any())
                        <div class="alert alert-danger py-2 fs-7 mb-3">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    {{-- Login Form --}}
                    <!-- <form action="{{ route('login') }}" method="POST"> -->
                        <form action="{{ url('/login') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-medium">Email Address</label>
                            <input type="email" name="email" class="form-control py-2" placeholder="admin@hrms.com" value="{{ old('email') }}" required autofocus>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium">Password</label>
                            <input type="password" name="password" class="form-control py-2" placeholder="••••••••" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm fw-semibold">
                            <i class="ti ti-login me-1"></i> Sign In
                        </button>
                    </form>

                </div>
            </div>
            
            <p class="text-center text-muted fs-7 mt-4">&copy; {{ date('Y') }} HRMS System. All rights reserved.</p>
        </div>
    </div>
</div>
@endsection
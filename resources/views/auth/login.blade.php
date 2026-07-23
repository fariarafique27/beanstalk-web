@extends('layouts.app')

@section('title', 'Super Admin Login')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="col-md-5 col-lg-4">
        <div class="card border-0 shadow-lg p-4 rounded-4">
            <div class="card-body">
                <div class="text-center mb-4">
                    <h4 class="fw-bold">Super Admin Portal</h4>
                    <p class="text-muted fs-7">Sign in to manage organizations</p>
                </div>

                {{-- Show Error Alerts --}}
                @if($errors->any())
                    <div class="alert alert-danger py-2 fs-7">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-medium">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="admin@system.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm fw-semibold">
                        Sign In
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
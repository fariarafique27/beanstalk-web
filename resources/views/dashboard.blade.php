@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 font-weight-bold text-dark mb-0">Dashboard</h1>
            <p class="text-muted mb-0">Overview and system statistics</p>
        </div>
        <div>
            <button class="btn btn-primary shadow-sm">
                <i class="ti ti-plus me-1"></i> New Action
            </button>
        </div>
    </div>
@endsection

@section('content')
    <div class="row g-3">
        <!-- Stat Card Example -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded p-3 me-3">
                        <i class="ti ti-users fs-3"></i>
                    </div>
                    <div>
                        <span class="text-muted text-uppercase fs-7 fw-bold">Users</span>
                        <h3 class="mb-0 fw-bold">1,240</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
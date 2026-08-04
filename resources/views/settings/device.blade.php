@extends('layouts.app')
@section('content')
<div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="mb-0">Device Settings</h2>

        @if($device)
            <div class="text-end">
                <button id="sync-now-btn" class="btn btn-outline-secondary">
                    <i class="ti ti-refresh"></i> Sync Now
                </button>
                <p class="text-muted small mb-0 mt-1">
                    Last synced: {{ $device['last_synced_at'] ?? 'Never' }}
                </p>
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('settings.device.update') }}" id="device-form">
        @csrf
        <fieldset id="device-fields" @if($device) disabled @endif>
            <div class="mb-3">
                <label>Device IP</label>
                <input type="text" name="ip" class="form-control" value="{{ old('ip', $device['ip'] ?? '') }}" required>
            </div>
            <div class="mb-3">
                <label>Port</label>
                <input type="number" name="port" class="form-control" value="{{ old('port', $device['port'] ?? 4370) }}" required>
            </div>
            <div class="mb-3">
                <label>Device Name (optional)</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $device['name'] ?? '') }}">
            </div>

            <div class="mb-3">
                <label>Device Password (if required)</label>
                <input type="password" name="password" class="form-control"
                       placeholder="{{ $device && !empty($device['has_password']) ? '••••••••  (leave blank to keep current)' : 'Leave blank if device has no password' }}"
                       autocomplete="new-password">
            </div>
        </fieldset>

        <button type="submit" class="btn btn-primary" id="save-btn">
            {{ $device ? 'Update' : 'Save' }}
        </button>

        @if($device)
            <button type="button" class="btn btn-link" id="edit-toggle-btn">Edit</button>
        @endif
    </form>
</div>

<script>
// FIX #1/#2: unlock fields for editing
document.getElementById('edit-toggle-btn')?.addEventListener('click', function () {
    const fieldset = document.getElementById('device-fields');
    fieldset.disabled = false;
    fieldset.querySelector('input[name="ip"]').focus();
    this.style.display = 'none';
});

// Sync Now (unchanged behavior, just relocated in the markup above)
document.getElementById('sync-now-btn')?.addEventListener('click', function () {
    this.disabled = true;
    this.textContent = 'Syncing...';
    fetch("{{ route('settings.device.sync') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(res => {
        alert(res.message);
        this.disabled = false;
        this.innerHTML = '<i class="ti ti-refresh"></i> Sync Now';
    });
});
</script>
@endsection


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Your Password - HRMS</title>
    
<script src="https://cdn.tailwindcss.com"></script> 

    <!-- Option B: CDN for quick clean styling if app.css isn't set up -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Set Your Password</h2>

        <form action="{{ route('password.submit') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ request('token') }}">
            <input type="hidden" name="email" value="{{ request('email') }}">

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                <input type="email" value="{{ request('email') }}" readonlyPasswordSetupController class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100 text-gray-600 cursor-not-allowed">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">New Password</label>
                <input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-2 rounded hover:bg-indigo-700 transition">
                Save Password & Continue
            </button>
        </form>
    </div>

</body>
</html>
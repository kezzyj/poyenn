<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — Poyenn Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center py-12 px-4">

    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white">Poyenn</h1>
            <p class="text-gray-400 mt-2 text-sm tracking-wider uppercase">Admin Portal</p>
        </div>

        <div class="bg-white rounded-lg shadow-xl p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Forgot your password?</h2>
            <p class="text-sm text-gray-500 mb-6">Enter your email and we'll send you a reset link.</p>

            @if (session('status'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                    <p class="text-sm">{{ $errors->first() }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.password.email') }}">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-md transition duration-200">
                    Email Password Reset Link
                </button>
            </form>

            <p class="text-center text-sm mt-6">
                <a href="{{ route('admin.login') }}" class="text-indigo-600 hover:text-indigo-800">Back to login</a>
            </p>
        </div>
    </div>

</body>
</html>
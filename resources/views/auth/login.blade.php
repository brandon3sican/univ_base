<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Built Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>

<body class="min-h-screen flex items-center justify-center p-4 bg-gradient-animated">
    <!-- Background overlay -->
    <div class="fixed inset-0 -z-10">
        <!-- Animated Shapes Background -->
        <div class="shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
            <div class="shape shape-5"></div>
        </div>
        <!-- Pattern Overlay -->
        <div class="bg-pattern"></div>
    </div>

    <div class="w-full max-w-md px-4">
        <div
            class="login-container relative overflow-hidden border border-opacity-20 border-white rounded-2xl backdrop-blur-md bg-white/10 shadow-2xl shadow-green-900/20 before:absolute before:inset-0 before:bg-gradient-to-br before:from-white/20 before:to-transparent before:border-t before:border-l before:border-white/30 before:rounded-2xl before:pointer-events-none">
            <!-- Header -->
            <div class="login-header bg-gradient-to-r from-green-600 to-emerald-600 p-8 text-center">
                <div class="inline-block p-3 bg-white bg-opacity-10 rounded-full mb-4">
                    <img src="img/denr-logo.png" alt="Logo" class="w-20 h-20">
                </div>
                <h1 class="text-3xl font-bold text-white">Universe - Baseline</h1>
                <p class="text-green-100 mt-2">Sign in to your account</p>
            </div>

            <!-- Login Form -->
            <form class="login-form p-8 space-y-6 hover:shadow-lg" method="POST" action="{{ route('login.post') }}">
                @csrf
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        @foreach ($errors->all() as $error)
                            <span>{{ $error }}</span>
                        @endforeach
                    </div>
                @endif

                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <div class="form-group">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email"
                            class="form-input block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none @error('email') border-red-500 @enderror"
                            placeholder="Enter your email" value="{{ old('email') }}" required>
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="flex justify-between items-center mb-1">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <a href="#" class="text-sm text-green-200 hover:text-green-400 transition-colors duration-200">
                            Forgot password?
                        </a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password"
                            class="form-input block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none @error('password') border-red-500 @enderror"
                            placeholder="Enter your password" minlength="6" required>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group flex items-center">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox"
                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>
                </div>

                <button type="submit"
                    class="btn-login w-full flex justify-center items-center py-3 px-4 rounded-lg text-white font-medium focus:outline-none hover:shadow-xl focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Sign In
                </button>

                <!-- Footer -->
                <div class="mt-6 text-center text-sm text-white">
                    <p>
                        &copy; 2026 DENR CAR. All rights reserved.</p>
                </div>
            </form>
        </div>
    </div>
    <!-- JavaScript -->
    <script src="js/script.js"></script>
</body>

</html>
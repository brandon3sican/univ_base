<!-- Top Navigation -->
<header class="bg-gradient-to-r from-white to-gray-50 border-b border-gray-200 shadow-lg">
    <div class="flex items-center justify-between px-6 py-4">
        <!-- Page Title Section -->
        <div class="flex items-center space-x-4">
            <div class="w-1 h-8 bg-gradient-to-b from-green-500 to-green-600 rounded-full"></div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">{{ $pageTitle }}</h1>
                <p class="text-xs text-gray-500 mt-1 uppercase tracking-wider">Universe | Baseline</p>
            </div>
        </div>
        
        <!-- Right Section -->
        <div class="flex items-center space-x-6">
            <!-- Notifications -->
            <div class="relative">
                <button class="relative p-2.5 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition-all duration-200 group">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                </button>
            </div>
            
            <!-- Divider -->
            <div class="h-10 w-px bg-gradient-to-b from-gray-200 to-gray-300"></div>
            
            <!-- User Profile -->
            <div class="flex items-center space-x-3 group cursor-pointer">
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-800">{{ $userName ?? 'Admin User' }}</p>
                    <p class="text-xs text-gray-500">Administrator</p>
                </div>
                <div class="relative">
                    <img src="{{ $userAvatar ?? 'https://ui-avatars.com/api/?name=Admin+User&background=059669&color=fff' }}" 
                         alt="User" 
                         class="w-10 h-10 rounded-full border-2 border-gray-200 group-hover:border-green-500 transition-all duration-200 shadow-sm">
                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400 group-hover:text-gray-600 transition-colors"></i>
            </div>
        </div>
    </div>
</header>
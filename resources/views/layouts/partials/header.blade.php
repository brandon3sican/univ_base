<!-- Top Navigation -->
<header class="bg-white/80 backdrop-blur-xl border-b border-gray-200/60 shadow-sm sticky top-0 z-40">
    <div class="flex items-center justify-between px-4 md:px-6 py-3">
        <!-- Page Title Section -->
        <div class="flex items-center space-x-3 md:space-x-4">
            <!-- Sidebar Toggle Button -->
            <button onclick="toggleSidebar()" id="sidebarToggleBtn"
                class="p-2.5 text-gray-600 hover:text-emerald-600 hover:bg-emerald-50/80 rounded-xl transition-all duration-300 group hover:shadow-md">
                <i id="sidebarToggleIcon" class="fas fa-bars text-lg transition-transform duration-300 group-hover:rotate-180"></i>
            </button>

            <div class="hidden md:block w-1 h-10 bg-gradient-to-b from-emerald-500 via-green-500 to-teal-500 rounded-full shadow-lg shadow-emerald-500/30"></div>
            <div>
                <h1 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent tracking-tight">{{ $pageTitle }}</h1>
                <p class="text-xs text-gray-500 mt-0.5 uppercase tracking-widest font-medium">Universe | Baseline</p>
            </div>
        </div>

        <!-- Right Section -->
        <div class="flex items-center space-x-2 md:space-x-4">

            <!-- User Profile -->
            <div class="relative">
                <button onclick="toggleUserDropdown()"
                    class="flex items-center space-x-2 md:space-x-3 group cursor-pointer focus:outline-none p-1.5 rounded-xl hover:bg-gray-100/80 transition-all duration-300">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-gray-800 group-hover:text-emerald-600 transition-colors">{{ $userName ?? 'Admin User' }}</p>
                        <p class="text-xs text-gray-500">Administrator</p>
                    </div>
                    <div class="relative">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-green-600 p-0.5 shadow-lg shadow-emerald-500/30 group-hover:shadow-emerald-500/50 transition-all duration-300">
                            <img src="{{ $userAvatar ?? 'https://ui-avatars.com/api/?name=Admin+User&background=059669&color=fff' }}"
                                alt="User"
                                class="w-full h-full rounded-full border-2 border-white">
                        </div>
                        <div class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-emerald-500 border-2 border-white rounded-full shadow-sm"></div>
                    </div>
                    <i id="userDropdownIcon"
                        class="fas fa-chevron-down text-xs text-gray-400 group-hover:text-emerald-600 transition-colors transition-transform duration-300 hidden sm:block"></i>
                </button>

                <!-- Dropdown Menu -->
                <div id="userDropdown"
                    class="absolute right-0 mt-3 w-56 bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl shadow-gray-200/50 border border-gray-200/60 py-2 hidden z-50 transform origin-top-right transition-all duration-200">
                    <!-- User Info Header -->
                    <div class="px-4 py-3 border-b border-gray-100/80 bg-gradient-to-r from-emerald-50/50 to-green-50/50">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-green-600 p-0.5">
                                <img src="{{ $userAvatar ?? 'https://ui-avatars.com/api/?name=Admin+User&background=059669&color=fff' }}"
                                    alt="User"
                                    class="w-full h-full rounded-full border-2 border-white">
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800">{{ $userName ?? 'Admin User' }}</p>
                                <p class="text-xs text-emerald-600 font-medium">Administrator</p>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-100/80"></div>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit"
                            class="flex items-center w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-all duration-200 group">
                            <i class="fas fa-sign-out-alt w-5 text-gray-400 group-hover:text-red-500 transition-colors"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    function toggleUserDropdown() {
        const dropdown = document.getElementById('userDropdown');
        const icon = document.getElementById('userDropdownIcon');

        dropdown.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('userDropdown');
        const button = event.target.closest('button[onclick="toggleUserDropdown()"]');

        if (!button && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
            document.getElementById('userDropdownIcon').classList.remove('rotate-180');
        }
    });
</script>

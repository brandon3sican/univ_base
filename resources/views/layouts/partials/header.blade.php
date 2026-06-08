<!-- Top Navigation -->
<header class="bg-gradient-to-r from-white to-gray-50 border-b border-gray-200 shadow-lg">
    <div class="flex items-center justify-between px-6 py-4">
        <!-- Page Title Section -->
        <div class="flex items-center space-x-4">
            <!-- Sidebar Toggle Button -->
            <button onclick="toggleSidebar()" id="sidebarToggleBtn"
                class="p-2.5 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition-all duration-200 group">
                <i id="sidebarToggleIcon" class="fas fa-bars text-lg transition-transform duration-200"></i>
            </button>

            <div class="w-1 h-8 bg-gradient-to-b from-green-500 to-green-600 rounded-full"></div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">{{ $pageTitle }}</h1>
                <p class="text-xs text-gray-500 mt-1 uppercase tracking-wider">Universe | Baseline</p>
            </div>
        </div>

        <!-- Right Section -->
        <div class="flex items-center space-x-6">

            <!-- User Profile -->
            <div class="relative">
                <button onclick="toggleUserDropdown()"
                    class="flex items-center space-x-3 group cursor-pointer focus:outline-none">
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-800">{{ $userName ?? 'Admin User' }}</p>
                        <p class="text-xs text-gray-500">Administrator</p>
                    </div>
                    <div class="relative">
                        <img src="{{ $userAvatar ?? 'https://ui-avatars.com/api/?name=Admin+User&background=059669&color=fff' }}"
                            alt="User"
                            class="w-10 h-10 rounded-full border-2 border-gray-200 group-hover:border-green-500 transition-all duration-200 shadow-sm">
                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full">
                        </div>
                    </div>
                    <i id="userDropdownIcon"
                        class="fas fa-chevron-down text-xs text-gray-400 group-hover:text-gray-600 transition-colors transition-transform duration-200"></i>
                </button>

                <!-- Dropdown Menu -->
                <div id="userDropdown"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 hidden z-50">
                    <div class="px-4 py-2 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-800">{{ $userName ?? 'Admin User' }}</p>
                        <p class="text-xs text-gray-500">Administrator</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit"
                            class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <i class="fas fa-sign-out-alt w-5"></i>
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

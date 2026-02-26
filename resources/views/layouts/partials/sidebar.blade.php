<!-- Sidebar -->
<div
    class="sidebar bg-gradient-to-b from-green-700 to-emerald-800 text-white w-64 flex-shrink-0 h-screen fixed overflow-hidden">
    <div class="p-3 flex items-center justify-between border-b border-green-600">
        <div class="flex items-center space-x-2">
            <img src="{{ asset('img/denr-logo.png') }}" alt="Logo" class="w-8 h-8">
            <span class="text-sm font-bold">UNIVERSE | BASELINE</span>
        </div>
    </div>
    <nav class="mt-4 h-[calc(100vh-120px)] overflow-y-auto">
        <div class="px-3">
            <!-- Main Actions -->
            <div class="mb-4">
                <div class="text-xs uppercase text-white/60 font-semibold tracking-wider mb-2 px-1">
                    Main
                </div>
                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-3 py-2 text-white rounded-lg mb-2 hover:bg-green-500 hover:shadow-lg hover:scale-105 transition-all duration-200 transform {{ request()->routeIs('dashboard') ? 'bg-green-600' : '' }}">
                    <i class="fas fa-tachometer-alt w-5 text-center"></i>
                    <span class="ml-3">Dashboard</span>
                </a>
            </div>

            <!-- Programs Dropdown -->
            <div class="mb-4">
                <div class="text-xs uppercase text-white/60 font-semibold tracking-wider mb-2 px-1">
                    Reports
                </div>
                <div class="relative">
                    <button onclick="toggleProgramsDropdown()" 
                            class="w-full flex items-center justify-between px-3 py-2 text-white rounded-lg hover:bg-green-500 hover:shadow-lg hover:scale-105 transition-all duration-200 transform {{ request()->routeIs('gass.*') ? 'bg-green-600' : '' }}">
                        <div class="flex items-center">
                            <i class="fas fa-file w-5 text-center"></i>
                            <span class="ml-3">Reports</span>
                        </div>
                        <i id="programs-dropdown-icon" class="fas fa-chevron-down text-xs transition-transform duration-200"></i>
                    </button>
                    
                    <div id="programs-dropdown" class="hidden mt-1 space-y-1">
                        <a href="{{ route('gass.index') }}"
                            class="flex items-center px-3 py-2 text-white/90 rounded-lg hover:bg-green-500 hover:shadow hover:scale-105 transition-all duration-200 transform pl-11 {{ request()->routeIs('gass.index') ? 'bg-green-600' : '' }}">
                            <span class="text-sm">GASS</span>
                        </a>

                        <a href="{{ route('sto.index') }}"
                            class="flex items-center px-3 py-2 text-white/90 rounded-lg hover:bg-green-500 hover:shadow hover:scale-105 transition-all duration-200 transform pl-11">
                            <span class="text-sm">STO</span>
                        </a>

                        <a href="#"
                            class="flex items-center px-3 py-2 text-white/90 rounded-lg hover:bg-green-500 hover:shadow hover:scale-105 transition-all duration-200 transform pl-11">
                            <span class="text-sm">ENF</span>
                        </a>

                        <a href="#"
                            class="flex items-center px-3 py-2 text-white/90 rounded-lg hover:bg-green-500 hover:shadow hover:scale-105 transition-all duration-200 transform pl-11">
                            <span class="text-sm">Biodiversity</span>
                        </a>

                        <a href="#"
                            class="flex items-center px-3 py-2 text-white/90 rounded-lg hover:bg-green-500 hover:shadow hover:scale-105 transition-all duration-200 transform pl-11">
                            <span class="text-sm">Lands</span>
                        </a>

                        <a href="#"
                            class="flex items-center px-3 py-2 text-white/90 rounded-lg hover:bg-green-500 hover:shadow hover:scale-105 transition-all duration-200 transform pl-11">
                            <span class="text-sm">Soilcon</span>
                        </a>

                        <a href="{{ route('nra.index') }}"
                            class="flex items-center px-3 py-2 text-white/90 rounded-lg hover:bg-green-500 hover:shadow hover:scale-105 transition-all duration-200 transform pl-11">
                            <span class="text-sm">NRA</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Administration -->
            <div class="mb-4">
                <div class="text-xs uppercase text-white/60 font-semibold tracking-wider mb-2 px-1">
                    System Administration
                </div>
                <a href="#"
                    class="flex items-center px-3 py-2 text-white rounded-lg mb-2 hover:bg-green-500 hover:shadow-lg hover:scale-105 transition-all duration-200 transform">
                    <i class="fas fa-edit w-5 text-center"></i>
                    <span class="ml-3">Edit History</span>
                </a>
                <a href="#"
                    class="flex items-center px-3 py-2 text-white rounded-lg mb-2 hover:bg-green-500 hover:shadow-lg hover:scale-105 transition-all duration-200 transform">
                    <i class="fas fa-users w-5 text-center"></i>
                    <span class="ml-3">Manage Users</span>
                </a>
                <a href="#"
                    class="flex items-center px-3 py-2 text-white rounded-lg mb-2 hover:bg-green-500 hover:shadow-lg hover:scale-105 transition-all duration-200 transform">
                    <i class="fas fa-cog w-5 text-center"></i>
                    <span class="ml-3">Settings</span>
                </a>
            </div>
        </div>
    </nav>

    <script>
    function toggleProgramsDropdown() {
        const dropdown = document.getElementById('programs-dropdown');
        const icon = document.getElementById('programs-dropdown-icon');
        
        if (dropdown.classList.contains('hidden')) {
            dropdown.classList.remove('hidden');
            icon.classList.add('rotate-180');
        } else {
            dropdown.classList.add('hidden');
            icon.classList.remove('rotate-180');
        }
    }

    function toggleDataCreationDropdown() {
        const dropdown = document.getElementById('data-creation-dropdown');
        const icon = document.getElementById('data-creation-dropdown-icon');
        
        if (dropdown.classList.contains('hidden')) {
            dropdown.classList.remove('hidden');
            icon.classList.add('rotate-180');
        } else {
            dropdown.classList.add('hidden');
            icon.classList.remove('rotate-180');
        }
    }
    </script>

    <div
        class="absolute bottom-0 w-full p-3 border-t border-green-600 bg-gradient-to-b from-transparent to-green-800/50">
        <div class="flex items-center">
            <img src="https://ui-avatars.com/api/?name=Admin+User&background=059669&color=fff" alt="User"
                class="w-8 h-8 rounded-full">
            <div class="ml-2 nav-text">
                <div class="font-medium text-sm">Admin User</div>
                <div class="text-xs text-green-200">Administrator</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="ml-auto inline">
                @csrf
                <button type="submit" class="text-red-400 hover:text-white text-sm">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</div>
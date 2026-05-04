<!-- Sidebar – collapsible -->
<div id="sidebar"
    class="sidebar bg-gradient-to-b from-green-700 to-emerald-800 text-white w-64 flex-shrink-0 h-screen fixed top-0 left-0 overflow-y-auto z-30 transition-all duration-300 ease-in-out transform">

    <!-- Expanded Header -->
    <div class="sidebar-expanded-header p-3 flex items-center justify-between border-b border-green-600">
        <div class="flex items-center space-x-2">
            <img src="{{ asset('img/denr-logo.png') }}" alt="Logo" class="w-8 h-8 flex-shrink-0">
            <span class="sidebar-text text-sm font-bold transition-all duration-300 ease-in-out">UNIVERSE |
                BASELINE</span>
        </div>
    </div>

    <!-- Collapsed Header -->
    <div class="sidebar-collapsed-header hidden p-3 flex justify-center border-b border-green-600">
        <img src="{{ asset('img/denr-logo.png') }}" alt="Logo" class="w-8 h-8 flex-shrink-0">
    </div>

    <!-- Expanded Navigation -->
    <nav class="sidebar-expanded-nav mt-4 px-3">
        <div class="space-y-6">

            <!-- Main -->
            <div>
                <div
                    class="text-xs uppercase text-white/60 font-semibold tracking-wider mb-2 px-1 sidebar-text transition-all duration-300 ease-in-out">
                    Main
                </div>
                <a href="{{ route('dashboard') }}"
                    class="sidebar-nav-item group relative flex items-center px-3 py-2.5 rounded-lg text-white/90 transition-all duration-200 hover:bg-emerald-700/20 hover:translate-x-1 hover:shadow-sm active:scale-[0.98] {{ request()->routeIs('dashboard') ? 'active bg-emerald-600/40' : '' }}">
                    <i
                        class="fas fa-tachometer-alt w-5 text-center transition-transform duration-200 group-hover:scale-110 flex-shrink-0"></i>
                    <span class="sidebar-text ml-3 transition-all duration-300 ease-in-out">Dashboard</span>
                    <div
                        class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-emerald-300 rounded-r opacity-0 group-[.active]:opacity-100 transition-opacity">
                    </div>
                </a>
            </div>

            <!-- UBs Dropdown -->
            <div>
                <div
                    class="text-xs uppercase text-white/60 font-semibold tracking-wider mb-2 px-1 sidebar-text transition-all duration-300 ease-in-out">
                    UBs
                </div>
                <div class="relative">
                    <button onclick="toggleProgramsDropdown()"
                        class="sidebar-nav-item group relative w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-white/90 transition-all duration-200 hover:bg-emerald-700/20 hover:translate-x-1 hover:shadow-sm active:scale-[0.98] {{ request()->routeIs('gass.*') || request()->routeIs('sto.*') ? 'active bg-emerald-600/40' : '' }}">
                        <div class="flex items-center">
                            <i
                                class="fas fa-file w-5 text-center transition-transform duration-200 group-hover:scale-110 flex-shrink-0"></i>
                            <span class="sidebar-text ml-3 transition-all duration-300 ease-in-out">UBs</span>
                        </div>
                        <i id="programs-dropdown-icon"
                            class="fas fa-chevron-down text-xs transition-transform duration-200 sidebar-text"></i>
                        <div
                            class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-emerald-300 rounded-r opacity-0 group-[.active]:opacity-100 transition-opacity">
                        </div>
                    </button>

                    <div id="programs-dropdown" class="mt-1 space-y-1 pl-4">
                        <a href="{{ route('gass.index') }}"
                            class="sidebar-nav-item group relative flex items-center px-4 py-2 text-white/85 rounded-lg transition-all duration-200 hover:bg-emerald-700/15 hover:translate-x-1.5 hover:shadow-sm active:scale-[0.98] {{ request()->routeIs('gass.index') ? 'active bg-emerald-600/30' : '' }}">
                            <i class="fas fa-circle text-xs mr-2 flex-shrink-0"></i>
                            <span class="sidebar-text transition-all duration-300 ease-in-out">GASS</span>
                            <div
                                class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-emerald-300 rounded-r opacity-0 group-[.active]:opacity-100 transition-opacity">
                            </div>
                        </a>

                        <a href="{{ route('sto.index') }}"
                            class="sidebar-nav-item group relative flex items-center px-4 py-2 text-white/85 rounded-lg transition-all duration-200 hover:bg-emerald-700/15 hover:translate-x-1.5 hover:shadow-sm active:scale-[0.98] {{ request()->routeIs('sto.index') ? 'active bg-emerald-600/30' : '' }}">
                            <i class="fas fa-circle text-xs mr-2 flex-shrink-0"></i>
                            <span class="sidebar-text transition-all duration-300 ease-in-out">STO</span>
                            <div
                                class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-emerald-300 rounded-r opacity-0 group-[.active]:opacity-100 transition-opacity">
                            </div>
                        </a>

                        <a href="{{ route('enf.index') }}"
                            class="sidebar-nav-item group relative flex items-center px-4 py-2 text-white/85 rounded-lg transition-all duration-200 hover:bg-emerald-700/15 hover:translate-x-1.5 hover:shadow-sm active:scale-[0.98] {{ request()->routeIs('enf.index') ? 'active bg-emerald-600/30' : '' }}">
                            <i class="fas fa-circle text-xs mr-2 flex-shrink-0"></i>
                            <span class="sidebar-text transition-all duration-300 ease-in-out">ENF</span>
                            <div
                                class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-emerald-300 rounded-r opacity-0 group-[.active]:opacity-100 transition-opacity">
                            </div>
                        </a>

                        <a href="{{ route('biodiversity.index') }}"
                            class="sidebar-nav-item group relative flex items-center px-4 py-2 text-white/85 rounded-lg transition-all duration-200 hover:bg-emerald-700/15 hover:translate-x-1.5 hover:shadow-sm active:scale-[0.98]">
                            <i class="fas fa-circle text-xs mr-2 flex-shrink-0"></i>
                            <span class="sidebar-text transition-all duration-300 ease-in-out">Biodiversity</span>
                        </a>

                        <a href="{{ route('lands.index') }}"
                            class="sidebar-nav-item group relative flex items-center px-4 py-2 text-white/85 rounded-lg transition-all duration-200 hover:bg-emerald-700/15 hover:translate-x-1.5 hover:shadow-sm active:scale-[0.98]">
                            <i class="fas fa-circle text-xs mr-2 flex-shrink-0"></i>
                            <span class="sidebar-text transition-all duration-300 ease-in-out">Lands</span>
                        </a>

                        <a href="{{ route('soilcon.index') }}"
                            class="sidebar-nav-item group relative flex items-center px-4 py-2 text-white/85 rounded-lg transition-all duration-200 hover:bg-emerald-700/15 hover:translate-x-1.5 hover:shadow-sm active:scale-[0.98] {{ request()->routeIs('soilcon.index') ? 'bg-emerald-700/25' : '' }}">
                            <i class="fas fa-circle text-xs mr-2 flex-shrink-0"></i>
                            <span class="sidebar-text transition-all duration-300 ease-in-out">Soilcon</span>
                        </a>

                        <a href="{{ route('nra.index') }}"
                            class="sidebar-nav-item group relative flex items-center px-4 py-2 text-white/85 rounded-lg transition-all duration-200 hover:bg-emerald-700/15 hover:translate-x-1.5 hover:shadow-sm active:scale-[0.98] {{ request()->routeIs('nra.index') ? 'active' : '' }}">
                            <i class="fas fa-circle text-xs mr-2 flex-shrink-0"></i>
                            <span class="sidebar-text transition-all duration-300 ease-in-out">NRA</span>
                            <div
                                class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-5 bg-emerald-300 rounded-r opacity-0 group-[.active]:opacity-100 transition-opacity">
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Administration -->
            <div>
                <div
                    class="text-xs uppercase text-white/60 font-semibold tracking-wider mb-2 px-1 sidebar-text transition-all duration-300 ease-in-out">
                    System Administration
                </div>
                <a href="#"
                    class="sidebar-nav-item group relative flex items-center px-3 py-2.5 rounded-lg text-white/90 transition-all duration-200 hover:bg-emerald-700/20 hover:translate-x-1 hover:shadow-sm active:scale-[0.98]">
                    <i
                        class="fas fa-cog w-5 text-center transition-transform duration-200 group-hover:scale-110 flex-shrink-0"></i>
                    <span class="sidebar-text ml-3 transition-all duration-300 ease-in-out">Settings</span>
                </a>
            </div>

        </div>
    </nav>

    <!-- Collapsed Navigation (Simplified) -->
    <nav class="sidebar-collapsed-nav hidden mt-4 px-2">
        <div class="space-y-2">
            <!-- Essential Navigation Items Only -->
            <a href="{{ route('dashboard') }}"
                class="group flex justify-center p-3 rounded-lg text-white/90 transition-all duration-200 hover:bg-emerald-700/20 active:scale-[0.98] {{ request()->routeIs('dashboard') ? 'bg-emerald-600/40' : '' }}"
                title="Dashboard">
                <i
                    class="fas fa-tachometer-alt w-5 text-center transition-transform duration-200 group-hover:scale-110"></i>
            </a>

            <!-- UBs Dropdown with Click -->
            <div class="dropdown-container" style="position: relative;">
                <button onclick="toggleDropdown('ubs-dropdown')"
                    class="flex justify-center p-3 rounded-lg text-white/90 transition-all duration-200 hover:bg-emerald-700/20 active:scale-[0.98] {{ request()->routeIs('gass.*') || request()->routeIs('sto.*') ? 'bg-emerald-600/40' : '' }}"
                    title="UBs">
                    <i class="fas fa-file w-5 text-center transition-transform duration-200 hover:scale-110"></i>
                </button>

                <!-- Dropdown Menu -->
                <div id="ubs-dropdown" class="dropdown-menu"
                    style="position: fixed; left: 80px; top: auto; display: none; z-index: 1000;">
                    <div
                        class="bg-gradient-to-b from-green-700 to-emerald-800 rounded-lg shadow-xl border border-green-600 py-2 min-w-48">
                        <div
                            class="px-3 py-2 text-xs font-semibold text-white/80 uppercase tracking-wider border-b border-green-600">
                            UBs
                        </div>
                        <a href="{{ route('gass.index') }}"
                            class="flex items-center px-3 py-2 text-sm text-white/90 hover:bg-emerald-600/30 transition-colors {{ request()->routeIs('gass.index') ? 'bg-emerald-600/40' : '' }}">
                            <i class="fas fa-circle text-xs mr-3 text-green-300"></i>
                            GASS
                        </a>
                        <a href="{{ route('sto.index') }}"
                            class="flex items-center px-3 py-2 text-sm text-white/90 hover:bg-emerald-600/30 transition-colors {{ request()->routeIs('sto.index') ? 'bg-emerald-600/40' : '' }}">
                            <i class="fas fa-circle text-xs mr-3 text-green-300"></i>
                            STO
                        </a>
                        <a href="{{ route('enf.index') }}"
                            class="flex items-center px-3 py-2 text-sm text-white/90 hover:bg-emerald-600/30 transition-colors {{ request()->routeIs('enf.index') ? 'bg-emerald-600/40' : '' }}">
                            <i class="fas fa-circle text-xs mr-3 text-green-300"></i>
                            ENF
                        </a>
                        <a href="{{ route('biodiversity.index') }}"
                            class="flex items-center px-3 py-2 text-sm text-white/90 hover:bg-emerald-600/30 transition-colors">
                            <i class="fas fa-circle text-xs mr-3 text-green-300"></i>
                            Biodiversity
                        </a>
                        <a href="{{ route('lands.index') }}"
                            class="flex items-center px-3 py-2 text-sm text-white/90 hover:bg-emerald-600/30 transition-colors">
                            <i class="fas fa-circle text-xs mr-3 text-green-300"></i>
                            Lands
                        </a>
                        <a href="{{ route('soilcon.index') }}"
                            class="flex items-center px-3 py-2 text-sm text-white/90 hover:bg-emerald-600/30 transition-colors {{ request()->routeIs('soilcon.index') ? 'bg-emerald-600/40' : '' }}">
                            <i class="fas fa-circle text-xs mr-3 text-green-300"></i>
                            Soilcon
                        </a>
                        <a href="{{ route('nra.index') }}"
                            class="flex items-center px-3 py-2 text-sm text-white/90 hover:bg-emerald-600/30 transition-colors {{ request()->routeIs('nra.index') ? 'bg-emerald-600/40' : '' }}">
                            <i class="fas fa-circle text-xs mr-3 text-green-300"></i>
                            NRA
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-green-600 pt-2 mt-4">
                <a href="#"
                    class="group flex justify-center p-3 rounded-lg text-white/90 transition-all duration-200 hover:bg-emerald-700/20 active:scale-[0.98]"
                    title="Settings">
                    <i class="fas fa-cog w-5 text-center transition-transform duration-200 group-hover:scale-110"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Expanded User Info -->
    <div
        class="sidebar-expanded-footer absolute bottom-0 left-0 right-0 p-3 border-t border-green-600 bg-gradient-to-b from-transparent to-green-800/50">
        <div class="flex items-center">
            <img src="https://ui-avatars.com/api/?name=Admin+User&background=059669&color=fff" alt="User"
                class="w-8 h-8 rounded-full">
            <div class="sidebar-text ml-2 transition-all duration-300 ease-in-out">
                <div class="sidebar-text font-medium text-sm">Admin User</div>
                <div class="sidebar-text text-xs text-green-200">Administrator</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="ml-auto inline">
                @csrf
                <button type="submit" class="text-red-400 hover:text-white text-sm transition-colors">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Collapsed User Info -->
    <div
        class="sidebar-collapsed-footer hidden absolute bottom-0 left-0 right-0 p-2 border-t border-green-600 bg-gradient-to-b from-transparent to-green-800/50">
        <div class="flex justify-center">
            <!-- Logout Button Only -->
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-red-400 hover:text-white transition-colors p-1" title="Logout">
                    <i class="fas fa-sign-out-alt text-sm"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // Dropdown toggle
    function toggleProgramsDropdown() {
        const dropdown = document.getElementById('programs-dropdown');
        const icon = document.getElementById('programs-dropdown-icon');

        dropdown.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    }

    // Auto-open when child is active
    document.addEventListener('DOMContentLoaded', function() {
        const isUBsActive =
            {{ request()->routeIs('gass.*') ? 'true' : 'false' }} ||
            {{ request()->routeIs('sto.*') ? 'true' : 'false' }}

        if (isUBsActive) {
            document.getElementById('programs-dropdown')?.classList.remove('hidden');
            document.getElementById('programs-dropdown-icon')?.classList.add('rotate-180');
        }
    });
</script>

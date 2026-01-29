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
                    class="flex items-center px-3 py-2 text-white rounded-lg mb-2 hover:bg-green-600 transition-colors {{ request()->routeIs('dashboard') ? 'bg-green-600' : '' }}">
                    <i class="fas fa-tachometer-alt w-5 text-center"></i>
                    <span class="ml-3">Dashboard</span>
                </a>

                <a href="{{ route('dashboard.create') }}"
                    class="flex items-center px-3 py-2 text-white rounded-lg mb-2 hover:bg-green-600 transition-colors">
                    <i class="fas fa-plus w-5 text-center"></i>
                    <span class="ml-3">Add Entry</span>
                </a>
            </div>

            <!-- Programs -->
            <div class="mb-4">
                <div class="text-xs uppercase text-white/60 font-semibold tracking-wider mb-2 px-1">
                    Programs
                </div>
                <a href="{{ route('gass.index') }}"
                    class="flex items-center px-3 py-2 text-white rounded-lg mb-2 hover:bg-green-600 transition-colors {{ request()->routeIs('gass.index') ? 'bg-green-600' : '' }}">
                    <i class="fas fa-cube w-5 text-center"></i>
                    <span class="ml-3">GASS</span>
                </a>

                <a href="#"
                    class="flex items-center px-3 py-2 text-white rounded-lg mb-2 hover:bg-green-600 transition-colors">
                    <i class="fas fa-cube w-5 text-center"></i>
                    <span class="ml-3">STO</span>
                </a>

                <a href="#"
                    class="flex items-center px-3 py-2 text-white rounded-lg mb-2 hover:bg-green-600 transition-colors">
                    <i class="fas fa-cube w-5 text-center"></i>
                    <span class="ml-3">ENF</span>
                </a>

                <a href="#"
                    class="flex items-center px-3 py-2 text-white rounded-lg mb-2 hover:bg-green-600 transition-colors">
                    <i class="fas fa-cube w-5 text-center"></i>
                    <span class="ml-3">Biodiversity</span>
                </a>

                <a href="#"
                    class="flex items-center px-3 py-2 text-white rounded-lg mb-2 hover:bg-green-600 transition-colors">
                    <i class="fas fa-cube w-5 text-center"></i>
                    <span class="ml-3">Lands</span>
                </a>

                <a href="#"
                    class="flex items-center px-3 py-2 text-white rounded-lg mb-2 hover:bg-green-600 transition-colors">
                    <i class="fas fa-cube w-5 text-center"></i>
                    <span class="ml-3">NRA</span>
                </a>
            </div>

            <!-- Management -->
            <div class="mb-4">
                <div class="text-xs uppercase text-white/60 font-semibold tracking-wider mb-2 px-1">
                    Management
                </div>
                <a href="#"
                    class="flex items-center px-3 py-2 text-white rounded-lg mb-2 hover:bg-green-600 transition-colors">
                    <i class="fas fa-edit w-5 text-center"></i>
                    <span class="ml-3">Edit History</span>
                </a>
                <a href="#"
                    class="flex items-center px-3 py-2 text-white rounded-lg mb-2 hover:bg-green-600 transition-colors">
                    <i class="fas fa-users w-5 text-center"></i>
                    <span class="ml-3">Manage Users</span>
                </a>
            </div>
        </div>
    </nav>

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
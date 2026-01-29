@extends('layouts.app')

@php
    $pageTitle = 'GASS';
@endphp

@section('content')
    <!-- Simple Page Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">GASS Entries</h1>
            <p class="text-gray-600 mt-1">Comprehensive management system for GASS entries, performance indicators, and universe baseline tracking</p>
        </div>
    </div>
    
    <!-- Enhanced Action Buttons -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('gass.create') }}" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-lg transition-all duration-200 shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
                    <i class="fas fa-plus mr-2"></i>
                    Create New PPA
                </a>
                <a href="{{ route('gass.indicators') }}" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transition-all duration-200 shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
                    <i class="fas fa-chart-line mr-2"></i>
                    Add Indicators
                </a>
                <a href="#" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white rounded-lg transition-all duration-200 shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
                    <i class="fas fa-globe mr-2"></i>
                    Set Universe
                </a>
            </div>
            
            <!-- Search and Filter -->
            <div class="flex items-center space-x-3">
                <div class="relative">
                    <input type="text" placeholder="Quick search..." class="pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm w-64">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <button class="px-3 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-lg transition-colors border border-gray-200">
                    <i class="fas fa-filter"></i>
                </button>
                <button class="px-3 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-lg transition-colors border border-gray-200">
                    <i class="fas fa-download"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-300 overflow-hidden">
        <div class="px-6 py-4 border-b-2 border-gray-300 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">GASS Entries</h3>
        </div>

        <div class="overflow-x-auto" style="max-height: 70vh; overflow-y: auto;">
            <table class="w-full border-collapse">
                <thead class="bg-gray-200 border-b-2 border-gray-300 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border-r-2 border-gray-300">
                            Program/Activity/Project
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border-r-2 border-gray-300">
                            Indicators
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border-r-2 border-gray-300">
                            Office
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border-r-2 border-gray-300">
                            Universe
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border-r-2 border-gray-300">
                            Accomplishment
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border-r-2 border-gray-300">
                            Baseline
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-300">
                    <!-- Sample Data Row 1 -->
                    <tr class="hover:bg-gray-50 transition-colors border-b border-gray-200">
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            <div class="relative group">
                                <button class="flex items-center justify-between w-full px-3 py-2 text-sm text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
                                    <span class="font-medium">Environmental Management</span>
                                    <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                                </button>
                                <div class="absolute left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                    <div class="py-1">
                                        <div class="px-3 py-2 text-sm text-gray-900 font-medium border-b border-gray-100">Environmental Management</div>
                                        <div class="px-3 py-1 text-xs text-gray-500">PAP-001</div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 border-r border-gray-200">
                            <div class="text-sm text-gray-900">Waste Reduction Target</div>
                            <div class="text-xs text-gray-500">25% reduction by 2025</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            <div class="relative group">
                                <button class="flex items-center justify-between w-full px-3 py-2 text-sm text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
                                    <span class="font-medium">DENR CAR Regional Office</span>
                                    <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                                </button>
                                <div class="absolute left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                    <div class="py-1">
                                        <div class="px-3 py-2 text-sm text-gray-900 font-medium border-b border-gray-100">DENR CAR Regional Office</div>
                                        <div class="px-3 py-1 text-xs text-gray-500">Cordillera Administrative Region</div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center border-r border-gray-200">
                            <div class="text-sm text-gray-900">100</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center border-r border-gray-200">
                            <div class="text-sm text-gray-900">75</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center border-r border-gray-200">
                            <div class="text-sm text-gray-900">50</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <button class="text-blue-600 hover:text-blue-800 transition-colors" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="text-green-600 hover:text-green-800 transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-800 transition-colors" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Sample Data Row 2 -->
                    <tr class="hover:bg-gray-50 transition-colors border-b border-gray-200">
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            <div class="relative group">
                                <button class="flex items-center justify-between w-full px-3 py-2 text-sm text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
                                    <span class="font-medium">Biodiversity Conservation</span>
                                    <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                                </button>
                                <div class="absolute left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                    <div class="py-1">
                                        <div class="px-3 py-2 text-sm text-gray-900 font-medium border-b border-gray-100">Biodiversity Conservation</div>
                                        <div class="px-3 py-1 text-xs text-gray-500">PAP-002</div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 border-r border-gray-200">
                            <div class="text-sm text-gray-900">Forest Coverage</div>
                            <div class="text-xs text-gray-500">Maintain 60% coverage</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            <div class="relative group">
                                <button class="flex items-center justify-between w-full px-3 py-2 text-sm text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
                                    <span class="font-medium">PENRO - Benguet</span>
                                    <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                                </button>
                                <div class="absolute left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                    <div class="py-1">
                                        <div class="px-3 py-2 text-sm text-gray-900 font-medium border-b border-gray-100">PENRO - Benguet</div>
                                        <div class="px-3 py-1 text-xs text-gray-500">Provincial Environment Office</div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center border-r border-gray-200">
                            <div class="text-sm text-gray-900">5000</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center border-r border-gray-200">
                            <div class="text-sm text-gray-900">3000</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center border-r border-gray-200">
                            <div class="text-sm text-gray-900">3000</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <button class="text-blue-600 hover:text-blue-800 transition-colors" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="text-green-600 hover:text-green-800 transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-800 transition-colors" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Sample Data Row 3 -->
                    <tr class="hover:bg-gray-50 transition-colors border-b border-gray-200">
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            <div class="relative group">
                                <button class="flex items-center justify-between w-full px-3 py-2 text-sm text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
                                    <span class="font-medium">Water Resources</span>
                                    <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                                </button>
                                <div class="absolute left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                    <div class="py-1">
                                        <div class="px-3 py-2 text-sm text-gray-900 font-medium border-b border-gray-100">Water Resources</div>
                                        <div class="px-3 py-1 text-xs text-gray-500">PAP-003</div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 border-r border-gray-200">
                            <div class="text-sm text-gray-900">Water Quality Index</div>
                            <div class="text-xs text-gray-500">Target: Class A</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                            <div class="relative group">
                                <button class="flex items-center justify-between w-full px-3 py-2 text-sm text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
                                    <span class="font-medium">CENRO - Baguio</span>
                                    <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                                </button>
                                <div class="absolute left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                    <div class="py-1">
                                        <div class="px-3 py-2 text-sm text-gray-900 font-medium border-b border-gray-100">CENRO - Baguio</div>
                                        <div class="px-3 py-1 text-xs text-gray-500">City Environment Office</div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center border-r border-gray-200">
                            <div class="text-sm text-gray-900">85</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center border-r border-gray-200">
                            <div class="text-sm text-gray-900">38</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center border-r border-gray-200">
                            <div class="text-sm text-gray-900">70</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <button class="text-blue-600 hover:text-blue-800 transition-colors" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="text-green-600 hover:text-green-800 transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-800 transition-colors" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing <span class="font-medium">1</span> to <span class="font-medium">3</span> of <span class="font-medium">3</span> results
                </div>
                <div class="flex items-center space-x-2">
                    <button class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="px-3 py-1 text-sm bg-green-600 text-white rounded">1</button>
                    <button class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">2</button>
                    <button class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">3</button>
                    <button class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection
@extends('layouts.app')

@php
    $pageTitle = 'Dashboard';
@endphp

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-100 text-green-600">
                    <i class="fas fa-users"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Users</p>
                    <p class="text-2xl font-semibold text-gray-800">1,234</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-100 text-blue-600">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Records</p>
                    <p class="text-2xl font-semibold text-gray-800">5,678</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-yellow-100 text-yellow-600">
                    <i class="fas fa-edit"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Recent Edits</p>
                    <p class="text-2xl font-semibold text-gray-800">89</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-100 text-purple-600">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">This Month</p>
                    <p class="text-2xl font-semibold text-gray-800">+12.5%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Main Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 lg:col-span-2">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-800">Activity Overview</h2>
                <div class="flex space-x-2">
                    <button class="px-3 py-1 text-xs font-medium rounded-lg bg-green-50 text-green-600">Week</button>
                    <button class="px-3 py-1 text-xs font-medium rounded-lg text-gray-500 hover:bg-gray-50">Month</button>
                    <button class="px-3 py-1 text-xs font-medium rounded-lg text-gray-500 hover:bg-gray-50">Year</button>
                </div>
            </div>
            <div class="h-80">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-800">Recent Activity</h2>
                <button class="text-sm text-green-600 hover:text-green-700 font-medium">View All</button>
            </div>
            <div class="space-y-4">
                <div class="flex items-start pb-4 border-b border-gray-100">
                    <div class="p-2 bg-green-100 text-green-600 rounded-lg">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-700">
                            <span class="font-medium">John Doe</span>
                            added a new record
                        </p>
                        <p class="text-xs text-gray-500 mt-1">2 minutes ago</p>
                    </div>
                </div>
                <div class="flex items-start pb-4 border-b border-gray-100">
                    <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-700">
                            <span class="font-medium">Jane Smith</span>
                            updated a record
                        </p>
                        <p class="text-xs text-gray-500 mt-1">1 hour ago</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="p-2 bg-yellow-100 text-yellow-600 rounded-lg">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-700">
                            <span class="font-medium">System</span>
                            completed nightly backup
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Yesterday, 2:30 AM</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
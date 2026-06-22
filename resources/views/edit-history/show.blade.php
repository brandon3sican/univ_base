@extends('layouts.app')

@php
    $pageTitle = 'Edit History Details';
@endphp

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-slate-700 to-slate-800 text-white p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-history mr-3 text-xl"></i>
                        <h2 class="text-xl font-bold">Edit History Details</h2>
                    </div>
                    <a href="{{ route('edit-history.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </a>
                </div>
            </div>

            <!-- Details -->
            <div class="p-6 animate-fade-in animate-delay-initial">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- User Info -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">User Information</h3>
                        <div class="flex items-center">
                            <div class="h-12 w-12 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold text-lg">
                                {{ $editHistory->user ? strtoupper(substr($editHistory->user->name, 0, 1)) : '?' }}
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $editHistory->user ? $editHistory->user->name : 'Unknown' }}</div>
                                <div class="text-xs text-gray-500">{{ $editHistory->user ? $editHistory->user->email : '' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Info -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Action</h3>
                        @if ($editHistory->action === 'created')
                            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-plus mr-2"></i>Created
                            </span>
                        @elseif ($editHistory->action === 'updated')
                            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                <i class="fas fa-edit mr-2"></i>Updated
                            </span>
                        @elseif ($editHistory->action === 'deleted')
                            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-trash mr-2"></i>Deleted
                            </span>
                        @else
                            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ $editHistory->action }}
                            </span>
                        @endif
                    </div>

                    <!-- Sector Info -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Sector Information</h3>
                        <div class="text-sm text-gray-900 font-medium">{{ $editHistory->model_name }}</div>
                        <div class="text-xs text-gray-500">ID: {{ $editHistory->model_id }}</div>
                        <div class="text-xs text-gray-500 mt-1">Class: {{ $editHistory->model_type }}</div>
                    </div>

                    <!-- Timestamp -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Timestamp</h3>
                        <div class="text-sm text-gray-900">{{ $editHistory->created_at ? $editHistory->created_at->format('F d, Y - g:i A') : '-' }}</div>
                        <div class="text-xs text-gray-500">{{ $editHistory->created_at ? $editHistory->created_at->diffForHumans() : '' }}</div>
                    </div>
                </div>

                <!-- Description -->
                @if ($editHistory->description)
                    <div class="mt-6 bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Description</h3>
                        <div class="text-sm text-gray-900">{{ $editHistory->description }}</div>
                    </div>
                @endif

                <!-- Changes -->
                @if ($editHistory->changes && $editHistory->action === 'updated')
                    <div class="mt-6 bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Changed Fields</h3>
                        <div class="text-sm text-gray-900">
                            @php
                                $changedFields = array_keys($editHistory->changes['new'] ?? []);
                            @endphp
                            @foreach ($changedFields as $field)
                                <div class="py-1">
                                    <span class="font-medium">{{ $field }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

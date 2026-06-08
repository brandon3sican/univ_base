@extends('layouts.app')

@php
    $pageTitle = 'Dashboard';
@endphp

@section('content')
    <!-- UB Statistics Cards with Pie Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-3">
        <!-- Pie Chart Card -->
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-500 to-green-600 px-3 py-2">
                <h2 class="text-sm font-bold text-white flex items-center">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Records Distribution by Sector
                </h2>
            </div>
            <div class="p-3">
                <div class="relative h-40">
                    <canvas id="ubPieChart"></canvas>
                </div>
            </div>
        </div>

        <!-- UB Count Cards -->
        <div class="lg:col-span-2 grid grid-cols-2 md:grid-cols-4 gap-2">
            @php
                $ubColors = [
                    'GASS' => ['#10B981', '#059669'],
                    'STO' => ['#3B82F6', '#2563EB'],
                    'ENF' => ['#F59E0B', '#D97706'],
                    'BIODIVERSITY' => ['#EF4444', '#DC2626'],
                    'LANDS' => ['#8B5CF6', '#7C3AED'],
                    'SOILCON' => ['#EC4899', '#DB2777'],
                    'NRA' => ['#06B6D4', '#0891B2'],
                ];
                $ubTextColors = [
                    'GASS' => 'text-emerald-600',
                    'STO' => 'text-blue-600',
                    'ENF' => 'text-amber-600',
                    'BIODIVERSITY' => 'text-red-600',
                    'LANDS' => 'text-violet-600',
                    'SOILCON' => 'text-pink-600',
                    'NRA' => 'text-cyan-600',
                ];
                $totalRecords = array_sum($ubCounts);
            @endphp
            @foreach (['GASS', 'STO', 'ENF', 'BIODIVERSITY', 'LANDS', 'SOILCON', 'NRA'] as $ub)
                <div
                    class="bg-gradient-to-br from-white to-gray-50 rounded-lg shadow-md border border-gray-200 overflow-hidden hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                    <div class="px-2 py-1.5"
                        style="background: linear-gradient(to right, {{ $ubColors[$ub][0] }}, {{ $ubColors[$ub][1] }});">
                        <h3 class="text-xs font-bold text-white text-center">{{ $ub }}</h3>
                    </div>
                    <div class="p-2 text-center">
                        <div class="text-xl font-bold {{ $ubTextColors[$ub] }}">{{ $ubCounts[$ub] ?? 0 }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">Total Records</div>
                    </div>
                </div>
            @endforeach
            <!-- Total Records Card -->
            <div
                class="bg-gradient-to-br from-white to-gray-50 rounded-lg shadow-md border border-gray-200 overflow-hidden hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                <div class="px-2 py-1.5" style="background: linear-gradient(to right, #1F2937, #111827);">
                    <h3 class="text-xs font-bold text-white text-center">TOTAL</h3>
                </div>
                <div class="p-2 text-center">
                    <div class="text-xl font-bold text-gray-800">{{ $totalRecords }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">All Records</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ubCounts = @json($ubCounts);
            const colors = ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4'];

            const canvas = document.getElementById('ubPieChart');
            if (canvas) {
                new Chart(canvas, {
                    type: 'pie',
                    data: {
                        labels: Object.keys(ubCounts),
                        datasets: [{
                            data: Object.values(ubCounts),
                            backgroundColor: colors,
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    usePointStyle: true,
                                    padding: 8,
                                    font: {
                                        size: 9
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.raw / total) * 100).toFixed(1);
                                        return `${context.label}: ${context.raw} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>

    <!-- Search Module -->
    <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md border border-gray-200 mb-3 overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-500 to-green-600 px-3 py-2">
            <h2 class="text-sm font-bold text-white flex items-center">
                <i class="fas fa-search mr-2"></i>
                Search PPA Records
            </h2>
        </div>
        <form action="{{ route('dashboard.search') }}" method="GET" class="p-3 space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <!-- Keyword Input -->
                <div class="group">
                    <label for="keyword" class="block text-xs font-semibold text-gray-700 mb-1 flex items-center">
                        <i class="fas fa-key text-emerald-500 mr-1.5"></i>
                        PPA Name / Indicator
                    </label>
                    <div class="relative">
                        <input type="text" id="keyword" name="keyword" placeholder="Enter keyword..."
                            value="{{ old('keyword') }}"
                            class="w-full pl-8 pr-3 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all group-hover:border-gray-300 bg-white shadow-sm text-sm">
                        <i
                            class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 transition-colors text-xs"></i>
                    </div>
                </div>

                <!-- Search Location Dropdown -->
                <div class="group">
                    <label for="search_location" class="block text-xs font-semibold text-gray-700 mb-1 flex items-center">
                        <i class="fas fa-map-marker-alt text-emerald-500 mr-1.5"></i>
                        Search In
                    </label>
                    <div class="relative">
                        <select id="search_location" name="search_location"
                            class="w-full pl-8 pr-8 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all group-hover:border-gray-300 bg-white shadow-sm appearance-none cursor-pointer text-sm">
                            <option value="ALL"
                                {{ old('search_location') == 'ALL' || old('search_location') == '' ? 'selected' : '' }}>All
                                Sectors</option>
                            <option value="GASS" {{ old('search_location') == 'GASS' ? 'selected' : '' }}>GASS</option>
                            <option value="STO" {{ old('search_location') == 'STO' ? 'selected' : '' }}>STO</option>
                            <option value="ENF" {{ old('search_location') == 'ENF' ? 'selected' : '' }}>ENF</option>
                            <option value="BIODIVERSITY" {{ old('search_location') == 'BIODIVERSITY' ? 'selected' : '' }}>
                                Biodiversity</option>
                            <option value="LANDS" {{ old('search_location') == 'LANDS' ? 'selected' : '' }}>Lands</option>
                            <option value="SOILCON" {{ old('search_location') == 'SOILCON' ? 'selected' : '' }}>Soilcon
                            </option>
                            <option value="NRA" {{ old('search_location') == 'NRA' ? 'selected' : '' }}>NRA</option>
                        </select>
                        <i
                            class="fas fa-building absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 transition-colors text-xs"></i>
                        <i
                            class="fas fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
                    </div>
                </div>

                <!-- Office Dropdown -->
                <div class="group">
                    <label for="office" class="block text-xs font-semibold text-gray-700 mb-1 flex items-center">
                        <i class="fas fa-building text-emerald-500 mr-1.5"></i>
                        Office
                    </label>
                    <div class="relative">
                        <select id="office" name="office"
                            class="w-full pl-8 pr-8 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all group-hover:border-gray-300 bg-white shadow-sm appearance-none cursor-pointer text-sm">
                            <option value="ALL" {{ old('office') == 'ALL' || old('office') == '' ? 'selected' : '' }}>
                                All Offices</option>
                            <option value="RO" {{ old('office') == 'RO' ? 'selected' : '' }}>RO</option>
                            <option value="ABRA" {{ old('office') == 'ABRA' ? 'selected' : '' }}>Abra</option>
                            <option value="APAYAO" {{ old('office') == 'APAYAO' ? 'selected' : '' }}>Apayao</option>
                            <option value="BENGUET" {{ old('office') == 'BENGUET' ? 'selected' : '' }}>Benguet</option>
                            <option value="IFUGAO" {{ old('office') == 'IFUGAO' ? 'selected' : '' }}>Ifugao</option>
                            <option value="KALINGA" {{ old('office') == 'KALINGA' ? 'selected' : '' }}>Kalinga</option>
                            <option value="MOUNTAIN PROVINCE" {{ old('office') == 'MOUNTAIN PROVINCE' ? 'selected' : '' }}>
                                Mountain Province</option>
                        </select>
                        <i
                            class="fas fa-building absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 transition-colors text-xs"></i>
                        <i
                            class="fas fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- Search Button -->
            <div class="flex justify-end space-x-2 pt-1">
                <button type="reset" onclick="window.location.href='{{ route('dashboard') }}'"
                    class="px-4 py-2 bg-gradient-to-r from-gray-500 to-gray-600 text-white font-semibold rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center text-xs">
                    <i class="fas fa-undo mr-1.5"></i>
                    Reset
                </button>
                <button type="submit"
                    class="px-5 py-2 bg-gradient-to-r from-emerald-500 to-green-600 text-white font-semibold rounded-lg hover:from-emerald-600 hover:to-green-700 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center text-xs">
                    <i class="fas fa-search mr-1.5"></i>
                    Search
                </button>
            </div>
        </form>

        <!-- Search Results Table -->
        <div
            class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md border border-gray-200 overflow-hidden m-1">
            <div class="bg-gray-600 px-3 py-2 flex items-center justify-between">
                <h2 class="text-sm font-bold text-white flex items-center">
                    <i class="fas fa-table mr-2"></i>
                    Search Results
                </h2>
                @if (isset($results))
                    <span class="text-xs font-semibold text-white/90 bg-white/20 px-3 py-1 rounded-full backdrop-blur-sm">
                        <i class="fas fa-database mr-1.5"></i>
                        <strong>{{ $results->count() }}</strong> record{{ $results->count() != 1 ? 's' : '' }}
                    </span>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border-2 border-gray-400 shadow-md rounded-lg overflow-hidden text-xs">
                    <thead
                        class="bg-gradient-to-r from-gray-50 to-gray-100 text-gray-700 shadow-md border-b border-gray-300">
                        <tr class="border-b border-gray-300">
                            <th class="px-3 py-2 text-left text-xs font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200"
                                style="width: 20%;">PPA Name
                            </th>
                            <th class="px-3 py-2 text-left text-xs font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200"
                                style="width: 30%;">Indicator
                            </th>
                            <th class="px-3 py-2 text-left text-xs font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200"
                                style="width: 5%;">Location
                            </th>
                            <th class="px-3 py-2 text-left text-xs font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200"
                                style="width: 5%;">Office
                            </th>
                            <th class="px-3 py-2 text-left text-xs font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200"
                                style="width: 5%;">Universe
                            </th>
                            @php
                                $years = ['2022', '2023', '2024', '2025', '2026', '2027', '2028'];
                            @endphp
                            @foreach ($years as $year)
                                <th class="px-2 py-2 text-center text-xs font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200"
                                    style="width: 5%;">
                                    {{ $year }}<br><span class="text-gray-400 normal-case">Accomp.</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-gradient-to-b from-gray-50 to-gray-100 divide-y divide-gray-300">
                        @if (isset($results) && $results->count() > 0)
                            @foreach ($results as $result)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 text-xs text-gray-900"
                                        style="width: 20%; word-wrap: break-word;">
                                        {{ $result->ppa_name ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2 text-xs text-gray-600"
                                        style="width: 30%; word-wrap: break-word;">
                                        {{ $result->indicator ?? '-' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs" style="width: 5%;">
                                        <a href="{{ route(strtolower($result->location) . '.show', $result->id) }}"
                                            class="text-green-600 hover:text-green-800 font-medium">{{ $result->location ?? '-' }}</a>
                                    </td>
                                    <td class="px-3 py-2 text-xs text-gray-600" style="width: 5%;">{!! $result->office ?? '-' !!}
                                    </td>
                                    <td class="px-3 py-2 text-xs text-gray-600" style="width: 5%;">{!! $result->universe ?? '-' !!}
                                    </td>
                                    @php
                                        $years = ['2022', '2023', '2024', '2025', '2026', '2027', '2028'];
                                        $accomplishmentsByYear = is_array($result->accomplishments ?? [])
                                            ? $result->accomplishments
                                            : [];
                                    @endphp
                                    @foreach ($years as $year)
                                        <td class="px-2 py-2 text-center text-xs text-gray-600" style="width: 5%;">
                                            {{ $accomplishmentsByYear[$year] ?? '-' }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="12" class="px-3 py-6 text-center text-xs text-gray-500">
                                    <i class="fas fa-search text-gray-300 text-2xl mb-2"></i>
                                    <p>No results found. Use the search form above to find records.</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

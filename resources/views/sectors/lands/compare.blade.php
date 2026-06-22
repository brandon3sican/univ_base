@extends('layouts.app')

@php
    $pageTitle = 'Lands - Year Comparison';
@endphp

@section('content')
    <style>
        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
        }
        .table-container {
            max-height: 500px;
            overflow-y: auto;
        }
    </style>

    <!-- Comparison Page Header -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-amber-600 to-amber-700 text-white p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-chart-bar mr-3 text-xl"></i>
                    <h2 class="text-lg font-bold">Lands Year Comparison</h2>
                </div>
                <a href="{{ route('lands.index') }}" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-all duration-200 text-sm font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Year Selection Controls -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-6">
        <div class="p-4">
            <div class="flex items-center space-x-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Year</label>
                    <select id="year1" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        @foreach($allYears as $year)
                            <option value="{{ $year }}" {{ $year == $year1 ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center pt-6">
                    <i class="fas fa-exchange-alt text-gray-400"></i>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Second Year</label>
                    <select id="year2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        @foreach($allYears as $year)
                            <option value="{{ $year }}" {{ $year == $year2 ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pt-6">
                    <button onclick="updateComparison()" class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2 rounded-lg transition-all duration-200 text-sm font-medium">
                        <i class="fas fa-sync-alt mr-2"></i>Compare
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Toggle -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-6">
        <div class="p-4">
            <div class="flex items-center space-x-4">
                <button onclick="showChartView()" id="chartViewBtn" class="flex-1 bg-amber-600 text-white px-4 py-2 rounded-lg transition-all duration-200 text-sm font-medium">
                    <i class="fas fa-chart-bar mr-2"></i>Chart View
                </button>
                <button onclick="showTableView()" id="tableViewBtn" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition-all duration-200 text-sm font-medium">
                    <i class="fas fa-table mr-2"></i>Table View
                </button>
            </div>
        </div>
    </div>

    <!-- Chart View -->
    <div id="chartView" class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-6 {{ request()->has('year1') && request()->has('year2') ? '' : 'hidden' }}">
        <div class="p-4">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-chart-bar mr-2 text-amber-600"></i>
                Accomplishment Comparison: {{ $year1 }} vs {{ $year2 }}
            </h3>
            <div class="chart-container">
                <canvas id="comparisonChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Table View -->
    <div id="tableView" class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-6 hidden {{ request()->has('year1') && request()->has('year2') ? '' : 'hidden' }}">
        <div class="p-4">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-table mr-2 text-amber-600"></i>
                Accomplishment Comparison: {{ $year1 }} vs {{ $year2 }}
            </h3>
            <table class="w-full border-collapse border border-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-left text-sm font-bold text-gray-700">Category</th>
                        <th class="border border-gray-300 px-4 py-2 text-right text-sm font-bold text-gray-700">{{ $year1 }}</th>
                        <th class="border border-gray-300 px-4 py-2 text-right text-sm font-bold text-gray-700">{{ $year2 }}</th>
                        <th class="border border-gray-300 px-4 py-2 text-right text-sm font-bold text-gray-700">Difference</th>
                        <th class="border border-gray-300 px-4 py-2 text-right text-sm font-bold text-gray-700">Change %</th>
                        <th class="border border-gray-300 px-4 py-2 text-right text-sm font-bold text-gray-700">% of Total ({{ $year2 }})</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-amber-50">
                        <td class="border border-gray-300 px-4 py-2 text-sm font-bold">
                            <i class="fas fa-globe mr-2 text-amber-600"></i>With Universe
                            <div class="text-xs text-gray-500 font-normal mt-1">Accomplishments with defined universe targets</div>
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-sm text-right">{{ number_format($year1TotalWithUniverse) }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-sm text-right">{{ number_format($year2TotalWithUniverse) }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-sm text-right font-bold {{ $year2TotalWithUniverse >= $year1TotalWithUniverse ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($year2TotalWithUniverse - $year1TotalWithUniverse) }}
                            @if($year2TotalWithUniverse > $year1TotalWithUniverse)
                                <i class="fas fa-arrow-up ml-1"></i>
                            @elseif($year2TotalWithUniverse < $year1TotalWithUniverse)
                                <i class="fas fa-arrow-down ml-1"></i>
                            @endif
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-sm text-right font-bold {{ $year2TotalWithUniverse >= $year1TotalWithUniverse ? 'text-green-600' : 'text-red-600' }}">
                            @if($year1TotalWithUniverse > 0)
                                {{ number_format((($year2TotalWithUniverse - $year1TotalWithUniverse) / $year1TotalWithUniverse) * 100, 2) }}%
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-sm text-right font-bold text-amber-600">
                            @if($year2Total > 0)
                                {{ number_format(($year2TotalWithUniverse / $year2Total) * 100, 1) }}%
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="border border-gray-300 px-4 py-2 text-sm font-bold">
                            <i class="fas fa-minus-circle mr-2 text-gray-500"></i>Without Universe
                            <div class="text-xs text-gray-500 font-normal mt-1">Accomplishments without defined universe targets</div>
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-sm text-right">{{ number_format($year1TotalWithoutUniverse) }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-sm text-right">{{ number_format($year2TotalWithoutUniverse) }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-sm text-right font-bold {{ $year2TotalWithoutUniverse >= $year1TotalWithoutUniverse ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($year2TotalWithoutUniverse - $year1TotalWithoutUniverse) }}
                            @if($year2TotalWithoutUniverse > $year1TotalWithoutUniverse)
                                <i class="fas fa-arrow-up ml-1"></i>
                            @elseif($year2TotalWithoutUniverse < $year1TotalWithoutUniverse)
                                <i class="fas fa-arrow-down ml-1"></i>
                            @endif
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-sm text-right font-bold {{ $year2TotalWithoutUniverse >= $year1TotalWithoutUniverse ? 'text-green-600' : 'text-red-600' }}">
                            @if($year1TotalWithoutUniverse > 0)
                                {{ number_format((($year2TotalWithoutUniverse - $year1TotalWithoutUniverse) / $year1TotalWithoutUniverse) * 100, 2) }}%
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-sm text-right font-bold text-gray-600">
                            @if($year2Total > 0)
                                {{ number_format(($year2TotalWithoutUniverse / $year2Total) * 100, 1) }}%
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                    <tr class="bg-amber-50">
                        <td class="border border-gray-300 px-4 py-2 text-sm font-bold">Total</td>
                        <td class="border border-gray-300 px-4 py-2 text-sm text-right font-bold">{{ number_format($year1Total) }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-sm text-right font-bold">{{ number_format($year2Total) }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-sm text-right font-bold {{ $year2Total >= $year1Total ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($year2Total - $year1Total) }}
                            @if($year2Total > $year1Total)
                                <i class="fas fa-arrow-up ml-1"></i>
                            @elseif($year2Total < $year1Total)
                                <i class="fas fa-arrow-down ml-1"></i>
                            @endif
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-sm text-right font-bold {{ $year2Total >= $year1Total ? 'text-green-600' : 'text-red-600' }}">
                            @if($year1Total > 0)
                                {{ number_format((($year2Total - $year1Total) / $year1Total) * 100, 2) }}%
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-sm text-right font-bold text-amber-600">100%</td>
                    </tr>
                </tbody>
            </table>
            
            <!-- Summary Section -->
            <div class="mt-4 p-3 bg-gradient-to-r from-amber-50 to-yellow-50 rounded-lg border border-amber-200">
                <h4 class="text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-info-circle mr-2 text-amber-600"></i>Key Insights
                </h4>
                <ul class="text-xs text-gray-600 space-y-1">
                    <li>
                        <span class="font-semibold">With Universe:</span> 
                        {{ number_format($year2TotalWithUniverse) }} accomplishments ({{ $year2Total > 0 ? number_format(($year2TotalWithUniverse / $year2Total) * 100, 1) : 0 }}% of total)
                        @if($year2TotalWithoutUniverse > 0)
                            - {{ number_format($year2TotalWithoutUniverse) }} accomplishments lack universe targets
                        @endif
                    </li>
                    <li>
                        <span class="font-semibold">Overall Trend:</span>
                        @if($year2Total > $year1Total)
                            <span class="text-green-600 font-semibold">Increase of {{ number_format($year2Total - $year1Total) }} ({{ number_format((($year2Total - $year1Total) / $year1Total) * 100, 1) }}%)</span>
                        @elseif($year2Total < $year1Total)
                            <span class="text-red-600 font-semibold">Decrease of {{ number_format($year1Total - $year2Total) }} ({{ number_format((($year1Total - $year2Total) / $year1Total) * 100, 1) }}%)</span>
                        @else
                            <span class="text-gray-600 font-semibold">No change</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- No Selection Message -->
    <div id="noSelectionMessage" class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-6 {{ request()->has('year1') && request()->has('year2') ? 'hidden' : '' }}">
        <div class="p-8 text-center">
            <i class="fas fa-chart-bar text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-bold text-gray-700 mb-2">Select Years to Compare</h3>
            <p class="text-gray-500">Please select the first and second year from the dropdowns above, then click the "Compare" button to generate the comparison chart and table.</p>
        </div>
    </div>

    <script>
        let comparisonChart = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Only initialize chart if years are selected in query parameters
            @if(request()->has('year1') && request()->has('year2'))
                initChart();
            @endif
        });

        function initChart() {
            const ctx = document.getElementById('comparisonChart').getContext('2d');
            
            const year1 = {{ $year1 }};
            const year2 = {{ $year2 }};
            const year1TotalWithUniverse = {{ $year1TotalWithUniverse }};
            const year2TotalWithUniverse = {{ $year2TotalWithUniverse }};
            const year1TotalWithoutUniverse = {{ $year1TotalWithoutUniverse }};
            const year2TotalWithoutUniverse = {{ $year2TotalWithoutUniverse }};

            if (comparisonChart) {
                comparisonChart.destroy();
            }

            comparisonChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [year1.toString(), year2.toString()],
                    datasets: [
                        {
                            label: 'With Universe',
                            data: [year1TotalWithUniverse, year2TotalWithUniverse],
                            backgroundColor: 'rgba(245, 158, 11, 0.7)',
                            borderColor: 'rgba(245, 158, 11, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Without Universe',
                            data: [year1TotalWithoutUniverse, year2TotalWithoutUniverse],
                            backgroundColor: 'rgba(156, 163, 175, 0.7)',
                            borderColor: 'rgba(156, 163, 175, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.raw.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: false
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        function updateComparison() {
            const year1 = document.getElementById('year1').value;
            const year2 = document.getElementById('year2').value;
            window.location.href = '{{ route('lands.compare') }}?year1=' + year1 + '&year2=' + year2;
        }

        function showChartView() {
            document.getElementById('chartView').classList.remove('hidden');
            document.getElementById('tableView').classList.add('hidden');
            document.getElementById('chartViewBtn').classList.remove('bg-gray-200', 'text-gray-700');
            document.getElementById('chartViewBtn').classList.add('bg-amber-600', 'text-white');
            document.getElementById('tableViewBtn').classList.remove('bg-amber-600', 'text-white');
            document.getElementById('tableViewBtn').classList.add('bg-gray-200', 'text-gray-700');
        }

        function showTableView() {
            document.getElementById('chartView').classList.add('hidden');
            document.getElementById('tableView').classList.remove('hidden');
            document.getElementById('tableViewBtn').classList.remove('bg-gray-200', 'text-gray-700');
            document.getElementById('tableViewBtn').classList.add('bg-amber-600', 'text-white');
            document.getElementById('chartViewBtn').classList.remove('bg-amber-600', 'text-white');
            document.getElementById('chartViewBtn').classList.add('bg-gray-200', 'text-gray-700');
        }
    </script>
@endsection

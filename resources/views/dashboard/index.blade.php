@extends('layouts.app')

@php
    $pageTitle = 'Dashboard';
@endphp

@section('content')
    <!-- Summary KPI Cards -->
    @php
        $totalUniverse = array_sum($universeTotals);
        $totalBaseline = array_sum($baselineTotals);
        $totalAccomplishment = array_sum($accomplishmentTotals);
        $overallProgress = $totalUniverse > 0 ? min(100, round((($totalUniverse - $totalBaseline) / $totalUniverse) * 100, 1)) : 0;
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-blue-100 uppercase tracking-wide">Total Universe</p>
                    <p class="text-2xl font-bold mt-1">{{ number_format($totalUniverse) }}</p>
                </div>
                <div class="bg-white/20 rounded-xl p-3">
                    <i class="fas fa-database text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-emerald-100 uppercase tracking-wide">Total Baseline</p>
                    <p class="text-2xl font-bold mt-1">{{ number_format($totalBaseline) }}</p>
                </div>
                <div class="bg-white/20 rounded-xl p-3">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-amber-100 uppercase tracking-wide">Total Accomplishment</p>
                    <p class="text-2xl font-bold mt-1">{{ number_format($totalAccomplishment) }}</p>
                </div>
                <div class="bg-white/20 rounded-xl p-3">
                    <i class="fas fa-trophy text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-purple-100 uppercase tracking-wide">Overall Progress</p>
                    <p class="text-2xl font-bold mt-1">{{ $overallProgress }}%</p>
                </div>
                <div class="bg-white/20 rounded-xl p-3">
                    <i class="fas fa-percentage text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Combined Progress Chart -->
    <div class="bg-white rounded-2xl shadow-md border border-slate-200/50 p-4 mb-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-bold text-slate-800 flex items-center">
                <i class="fas fa-chart-bar mr-2 text-blue-600"></i>
                Sector Progress Overview
            </h2>
            <span class="text-xs text-slate-500">Comparison across all sectors</span>
        </div>
        <div class="h-64">
            <canvas id="combinedProgressChart"></canvas>
        </div>
    </div>

    <!-- Combined Charts and Cards Section -->
    <div id="sectors" class="grid grid-cols-7 gap-2 mb-3">
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
            $ubIcons = [
                'GASS' => 'fa-leaf',
                'STO' => 'fa-database',
                'ENF' => 'fa-tree',
                'BIODIVERSITY' => 'fa-paw',
                'LANDS' => 'fa-map',
                'SOILCON' => 'fa-mountain',
                'NRA' => 'fa-water',
            ];
        @endphp
        @foreach (['GASS', 'STO', 'ENF', 'BIODIVERSITY', 'LANDS', 'SOILCON', 'NRA'] as $ub)
            @php
                $universe = $universeTotals[$ub] ?? 0;
                $baseline = $baselineTotals[$ub] ?? 0;
                $progress = $universe > 0 ? min(100, round((($universe - $baseline) / $universe) * 100, 1)) : 0;
                $accomplishmentWithoutUniverse = $accomplishmentWithoutUniverseTotals[$ub] ?? 0;
            @endphp
            <a href="{{ route('dashboard.sector', $ub) }}" class="block">
                <div
                    class="bg-white rounded-xl shadow-sm border border-slate-200/50 overflow-hidden hover:shadow-2xl hover:border-slate-400 transition-all duration-300 transform hover:-translate-y-1 hover:scale-105 cursor-pointer group relative @if($accomplishmentWithoutUniverse > 0) border-amber-400 border-2 @endif">
                    <!-- Header with icon -->
                    <div class="px-3 py-2.5 flex items-center justify-between"
                        style="background: linear-gradient(135deg, {{ $ubColors[$ub][0] }}, {{ $ubColors[$ub][1] }});">
                        <div class="flex items-center gap-2">
                            <i class="fas {{ $ubIcons[$ub] }} text-white/90 text-xs"></i>
                            <h3 class="text-xs font-bold text-white tracking-wide">{{ $ub }}</h3>
                            @if($accomplishmentWithoutUniverse > 0)
                            <span class="bg-amber-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">
                                <i class="fas fa-exclamation-triangle mr-1"></i>{{ number_format($accomplishmentWithoutUniverse) }}
                            </span>
                            @endif
                        </div>
                        <i class="fas fa-chevron-right text-white/70 text-xs opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    </div>

                    <!-- Chart -->
                    <div class="p-3 pt-2.5 pb-2">
                        <div class="relative h-36">
                            <canvas id="{{ $ub }}Chart"></canvas>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="px-3 pb-3 pt-2">
                        <div class="grid grid-cols-2 gap-2">
                            <div
                                class="bg-gradient-to-br from-slate-50 to-slate-100/50 rounded-lg p-3 border border-slate-200/60">
                                <div class="text-lg font-bold {{ $ubTextColors[$ub] }}">{{ number_format($universe) }}
                                </div>
                                <div class="text-xs text-slate-500 font-medium mt-1 uppercase tracking-wide">Universe</div>
                            </div>
                            <div
                                class="bg-gradient-to-br from-slate-50 to-slate-100/50 rounded-lg p-3 border border-slate-200/60">
                                <div class="text-lg font-bold {{ $ubTextColors[$ub] }}">
                                    {{ number_format($baselineTotals[$ub] ?? 0) }}</div>
                                <div class="text-xs text-slate-500 font-medium mt-1 uppercase tracking-wide">Baseline</div>
                            </div>
                        </div>

                        <!-- Accomplishment (shows on hover) -->
                        <div class="mt-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div
                                class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-lg p-2.5 border border-amber-200/60">
                                <div class="text-base font-bold text-amber-600">{{ number_format($accomplishmentTotals[$ub] ?? 0) }}
                                </div>
                                <div class="text-xs text-amber-700 font-medium mt-1 uppercase tracking-wide">Accomplishment</div>
                            </div>
                        </div>

                        <!-- Progress -->
                        <div class="mt-3">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Progress</span>
                                <span class="text-sm font-bold {{ $ubTextColors[$ub] }}">{{ $progress }}%</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                                <div class="h-3 rounded-full transition-all duration-700 ease-out"
                                    style="width: {{ $progress }}%; background: linear-gradient(90deg, {{ $ubColors[$ub][0] }}, {{ $ubColors[$ub][1] }}); box-shadow: 0 0 10px {{ $ubColors[$ub][0] }}40;">
                                </div>
                            </div>
                        </div>

                        <!-- View Button -->
                        <div class="mt-3">
                            <button onclick="event.stopPropagation(); window.location.href='{{ route('dashboard.sector', $ub) }}'"
                                class="w-full text-xs font-semibold text-white bg-gradient-to-r from-slate-600 to-slate-700 px-3 py-2 rounded-lg hover:from-slate-700 hover:to-slate-800 transition-all shadow-sm hover:shadow-md">
                                <i class="fas fa-table mr-1"></i>View
                            </button>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    @if(isset($totalAccomplishmentWithoutUniverse) && $totalAccomplishmentWithoutUniverse > 0)
    <!-- Warning Banner for Accomplishments Without Universe -->
    <div class="bg-amber-50 border-l-4 border-amber-500 rounded-r-lg p-3 mb-3 shadow-sm">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-amber-500 text-sm"></i>
            </div>
            <div class="ml-2">
                <h3 class="text-xs font-bold text-amber-800">
                    Accomplishments Without Universe Detected
                </h3>
                <div class="mt-0.5 text-xs text-amber-700">
                    <p>
                        There are <strong>{{ number_format($totalAccomplishmentWithoutUniverse) }}</strong> total accomplishments across all sectors that do not have a corresponding universe value.
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Sector Records Modal -->
    <div id="sectorRecordsModal" class="fixed inset-0 bg-black/80 z-50 hidden items-center justify-center p-4">
        <div
            class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden border border-slate-200/60">
            <div
                class="bg-gradient-to-r from-emerald-600 to-green-600 px-5 py-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-white flex items-center tracking-wide">
                    <i class="fas fa-table mr-3"></i>
                    {{ $selectedSector ?? 'Sector' }} Records
                </h2>
                <div class="flex items-center gap-3">
                    <select id="yearFilter" onchange="updateAccomplishmentChart()"
                        class="bg-white/90 text-slate-800 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-white/50 backdrop-blur-sm">
                    </select>
                    <button onclick="closeSectorRecordsModal()"
                        class="text-white/90 hover:text-white transition-colors bg-white/20 px-3 py-2 rounded-lg hover:bg-white/30 backdrop-blur-sm">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            <div class="p-5 overflow-y-auto max-h-[calc(90vh-80px)]">
                <!-- Accomplishments per Month Chart -->
                <div class="bg-gradient-to-br from-slate-50 to-slate-100/50 rounded-xl p-5 mb-6 border border-slate-200 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center">
                        <i class="fas fa-chart-line mr-2 text-emerald-600"></i>
                        Accomplishments per Month
                    </h3>
                    <div class="h-64">
                        <canvas id="accomplishmentChart"></canvas>
                    </div>
                </div>

                <!-- Divider -->
                <div class="flex items-center gap-4 mb-5">
                    <div class="flex-1 h-px bg-gradient-to-r from-transparent via-slate-300 to-transparent"></div>
                    <div class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-50 to-green-50 rounded-full border border-emerald-200/50">
                        <i class="fas fa-table text-emerald-600 text-xs"></i>
                        <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wide">Sector Records</span>
                    </div>
                    <div class="flex-1 h-px bg-gradient-to-r from-transparent via-slate-300 to-transparent"></div>
                </div>

                <!-- Records Table -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <table class="w-full border-collapse text-xs">
                    <thead
                        class="bg-gradient-to-r from-slate-50 to-slate-100 text-slate-700 shadow-sm border-b border-slate-200">
                        <tr class="border-b border-slate-200">
                            <th class="px-3 py-2 text-left text-xs font-bold uppercase tracking-wider border-r border-slate-200 bg-gradient-to-b from-slate-100 to-slate-200"
                                style="width: 25%;">PPA Name
                            </th>
                            <th class="px-3 py-2 text-left text-xs font-bold uppercase tracking-wider border-r border-slate-200 bg-gradient-to-b from-slate-100 to-slate-200"
                                style="width: 25%;">Indicator
                            </th>
                            <th class="px-3 py-2 text-left text-xs font-bold uppercase tracking-wider border-r border-slate-200 bg-gradient-to-b from-slate-100 to-slate-200"
                                style="width: 10%;">Sector
                            </th>
                            <th class="px-3 py-2 text-left text-xs font-bold uppercase tracking-wider border-r border-slate-200 bg-gradient-to-b from-slate-100 to-slate-200"
                                style="width: 10%;">Universe
                            </th>
                            <th class="px-3 py-2 text-left text-xs font-bold uppercase tracking-wider border-r border-slate-200 bg-gradient-to-b from-slate-100 to-slate-200"
                                style="width: 10%;">Baseline
                            </th>
                            <th class="px-3 py-2 text-center text-xs font-bold uppercase tracking-wider bg-gradient-to-b from-slate-100 to-slate-200"
                                style="width: 20%;">Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @if (isset($sectorRecords) && $sectorRecords->count() > 0)
                            @foreach ($sectorRecords as $record)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-3 py-2 text-xs text-slate-900 font-medium" style="word-wrap: break-word;">
                                        {{ $record->ppa_name ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2 text-xs text-slate-600" style="word-wrap: break-word;">
                                        {{ $record->indicator ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2 text-xs text-slate-600">
                                        {{ $record->sector ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2 text-xs text-slate-600 font-semibold">
                                        @if(($record->universe ?? 0) == 0 && ($record->baseline ?? 0) == 0)
                                            NO DATA
                                        @else
                                            {{ number_format($record->universe ?? 0) }}
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-xs text-slate-600 font-semibold">
                                        @if(($record->universe ?? 0) == 0 && ($record->baseline ?? 0) == 0)
                                            NO DATA
                                        @else
                                            {{ number_format($record->baseline ?? 0) }}
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <a href="{{ route(strtolower($record->sector) . '.index', ['record_id' => $record->id]) }}"
                                            class="text-emerald-600 hover:text-emerald-800 font-semibold text-xs inline-flex items-center px-2 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 transition-all">
                                            <i class="fas fa-eye mr-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="px-3 py-4 text-center text-slate-500">
                                    No records found
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function zoomChart(direction, chartId) {
            // Extract sector name from chartId (e.g., 'GASSChart' -> 'GASS')
            const sector = chartId.replace('Chart', '');

            // Get the chart from window.sectorCharts
            const chart = window.sectorCharts ? window.sectorCharts[sector] : null;

            if (!chart) return;

            const xAxis = chart.scales.x;
            const yAxis = chart.scales.y;

            // Initialize min/max if not set
            if (xAxis.options.min === undefined) {
                xAxis.options.min = xAxis.min;
            }
            if (xAxis.options.max === undefined) {
                xAxis.options.max = xAxis.max;
            }

            if (direction === 'in') {
                const range = xAxis.options.max - xAxis.options.min;
                xAxis.options.min = xAxis.options.min + range * 0.1;
                xAxis.options.max = xAxis.options.max - range * 0.1;
            } else {
                const range = xAxis.options.max - xAxis.options.min;
                xAxis.options.min = Math.max(0, xAxis.options.min - range * 0.1);
                xAxis.options.max = xAxis.options.max + range * 0.1;
            }

            chart.update();
        }

        function resetZoom(chartId) {
            // Extract sector name from chartId (e.g., 'GASSChart' -> 'GASS')
            const sector = chartId.replace('Chart', '');

            // Get the chart from window.sectorCharts
            const chart = window.sectorCharts ? window.sectorCharts[sector] : null;

            if (!chart) return;
            chart.options.scales.x.min = undefined;
            chart.options.scales.x.max = undefined;
            chart.options.scales.y.min = undefined;
            chart.options.scales.y.max = undefined;
            chart.update();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const universeTotals = @json($universeTotals);
            const baselineTotals = @json($baselineTotals);
            const accomplishmentTotals = @json($accomplishmentTotals);
            const accomplishmentWithoutUniverseTotals = @json($accomplishmentWithoutUniverseTotals ?? []);
            const colors = ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4'];

            // Scroll to sector records if present
            const sectorRecords = document.getElementById('sectorRecords');
            if (sectorRecords) {
                setTimeout(() => {
                    sectorRecords.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 100);
            }

            const sectors = Object.keys(universeTotals);
            const universeData = sectors.map(s => universeTotals[s] || 0);
            const baselineData = sectors.map(s => baselineTotals[s] || 0);
            const accomplishmentData = sectors.map(s => accomplishmentTotals[s] || 0);

            // Create accomplishment colors - use red for sectors with accomplishments but no universe
            const accomplishmentColors = sectors.map(s => {
                const universe = universeTotals[s] || 0;
                const accomplishment = accomplishmentTotals[s] || 0;
                if (accomplishment > 0 && universe === 0) {
                    return 'rgba(239, 68, 68, 0.9)'; // Red for accomplishments without universe
                }
                return 'rgba(245, 158, 11, 0.8)'; // Orange for normal accomplishments
            });
            const accomplishmentBorders = sectors.map(s => {
                const universe = universeTotals[s] || 0;
                const accomplishment = accomplishmentTotals[s] || 0;
                if (accomplishment > 0 && universe === 0) {
                    return 'rgba(239, 68, 68, 1)'; // Red border
                }
                return 'rgba(245, 158, 11, 1)'; // Orange border
            });

            // Create individual charts for each sector
            const sectorCharts = {};
            sectors.forEach(sector => {
                const canvas = document.getElementById(sector + 'Chart');
                if (canvas) {
                    const universe = universeTotals[sector] || 0;
                    const baseline = baselineTotals[sector] || 0;
                    const accomplishment = accomplishmentTotals[sector] || 0;
                    const progress = universe > 0 ? Math.min(100, Math.round(((universe - baseline) /
                        universe) * 100, 1)) : 0;

                    // Determine accomplishment color
                    let accomplishmentColor = 'rgba(245, 158, 11, 0.8)';
                    let accomplishmentBorder = 'rgba(245, 158, 11, 1)';
                    if (accomplishment > 0 && universe === 0) {
                        accomplishmentColor = 'rgba(239, 68, 68, 0.9)';
                        accomplishmentBorder = 'rgba(239, 68, 68, 1)';
                    }

                    sectorCharts[sector] = new Chart(canvas, {
                        type: 'bar',
                        data: {
                            labels: ['Universe', 'Baseline', 'Accomplishment'],
                            datasets: [{
                                label: sector,
                                data: [universe, baseline, accomplishment],
                                backgroundColor: [
                                    'rgba(59, 130, 246, 0.8)',
                                    'rgba(16, 185, 129, 0.8)',
                                    accomplishmentColor
                                ],
                                borderColor: [
                                    'rgba(59, 130, 246, 1)',
                                    'rgba(16, 185, 129, 1)',
                                    accomplishmentBorder
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        font: {
                                            size: 11
                                        }
                                    },
                                    grid: {
                                        display: false
                                    }
                                },
                                x: {
                                    ticks: {
                                        font: {
                                            size: 10
                                        }
                                    },
                                    grid: {
                                        display: false
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.9)',
                                    titleFont: {
                                        size: 13,
                                        weight: 'bold'
                                    },
                                    bodyFont: {
                                        size: 11
                                    },
                                    padding: 10,
                                    cornerRadius: 8,
                                    displayColors: true,
                                    borderColor: 'rgba(255, 255, 255, 0.1)',
                                    borderWidth: 1,
                                    callbacks: {
                                        title: function(context) {
                                            return '🔍 ' + sector + ' Sector';
                                        },
                                        label: function(context) {
                                            const label = context.label;
                                            const value = context.raw;
                                            let displayLabel = label;
                                            if (label === 'Universe') {
                                                displayLabel = '📊 Universe';
                                            } else if (label === 'Baseline') {
                                                displayLabel = '📈 Baseline';
                                            } else if (label === 'Accomplishment') {
                                                displayLabel = '✅ Accomplishment';
                                            }
                                            return `${displayLabel}: ${value.toLocaleString()}`;
                                        },
                                        afterBody: function(context) {
                                            const universe = universeTotals[sector] || 0;
                                            const baseline = baselineTotals[sector] || 0;
                                            const accomplishment = accomplishmentTotals[
                                                sector] || 0;
                                            const accomplishmentWithoutUniverse = accomplishmentWithoutUniverseTotals[sector] || 0;
                                            const progress = universe > 0 ? Math.min(100, Math
                                                .round(((universe - baseline) / universe) *
                                                    100, 1)) : 0;

                                            const lines = [
                                                `━━━━━━━━━━━━━━━━`,
                                                `📊 Progress: ${progress}%`,
                                                `📈 Remaining: ${baseline.toLocaleString()}`
                                            ];
                                            if (accomplishmentWithoutUniverse > 0) {
                                                lines.push(
                                                    `⚠️ Without Universe: ${accomplishmentWithoutUniverse.toLocaleString()}`
                                                );
                                            }
                                            return lines;
                                        }
                                    }
                                }
                            },
                            onHover: function(event, elements) {
                                const canvas = event.native.target;
                                if (elements.length > 0) {
                                    canvas.style.cursor = 'zoom-in';
                                } else {
                                    canvas.style.cursor = 'default';
                                }
                            }
                        }
                    });
                }
            });

            // Store charts globally for zoom/modal functions
            window.sectorCharts = sectorCharts;

            // Create combined progress chart
            const combinedCtx = document.getElementById('combinedProgressChart');
            if (combinedCtx) {
                const progressData = sectors.map(s => {
                    const universe = universeTotals[s] || 0;
                    const baseline = baselineTotals[s] || 0;
                    return universe > 0 ? Math.min(100, Math.round(((universe - baseline) / universe) * 100, 1)) : 0;
                });

                new Chart(combinedCtx, {
                    type: 'bar',
                    data: {
                        labels: sectors,
                        datasets: [{
                            label: 'Progress %',
                            data: progressData,
                            backgroundColor: [
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(59, 130, 246, 0.8)',
                                'rgba(245, 158, 11, 0.8)',
                                'rgba(239, 68, 68, 0.8)',
                                'rgba(139, 92, 246, 0.8)',
                                'rgba(236, 72, 153, 0.8)',
                                'rgba(6, 182, 212, 0.8)'
                            ],
                            borderColor: [
                                'rgba(16, 185, 129, 1)',
                                'rgba(59, 130, 246, 1)',
                                'rgba(245, 158, 11, 1)',
                                'rgba(239, 68, 68, 1)',
                                'rgba(139, 92, 246, 1)',
                                'rgba(236, 72, 153, 1)',
                                'rgba(6, 182, 212, 1)'
                            ],
                            borderWidth: 2,
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    },
                                    font: {
                                        size: 12
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        size: 11,
                                        weight: 'bold'
                                    }
                                },
                                grid: {
                                    display: false
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.85)',
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        const sector = context.label;
                                        const progress = context.raw;
                                        const universe = universeTotals[sector] || 0;
                                        const baseline = baselineTotals[sector] || 0;
                                        const accomplishment = accomplishmentTotals[sector] || 0;
                                        return [
                                            `Progress: ${progress}%`,
                                            `Universe: ${universe.toLocaleString()}`,
                                            `Baseline: ${baseline.toLocaleString()}`,
                                            `Accomplishment: ${accomplishment.toLocaleString()}`
                                        ];
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Auto-open sector records modal if present
            const sectorRecordsModal = document.getElementById('sectorRecordsModal');
            const hasSectorRecords = @json(isset($sectorRecords) && $sectorRecords->count() > 0);
            if (sectorRecordsModal && hasSectorRecords) {
                sectorRecordsModal.classList.remove('hidden');
                sectorRecordsModal.classList.add('flex');
                populateYearFilter();
                updateAccomplishmentChart();
            }
        });

        function openSectorRecordsModal() {
            const modal = document.getElementById('sectorRecordsModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            populateYearFilter();
            updateAccomplishmentChart();
        }

        function closeSectorRecordsModal() {
            const modal = document.getElementById('sectorRecordsModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        let accomplishmentChart = null;
        const monthlyData = @json($monthlyAccomplishments ?? []);

        function populateYearFilter() {
            const yearFilter = document.getElementById('yearFilter');
            if (!yearFilter) return;

            const currentYear = new Date().getFullYear();
            const years = [2022, 2023, 2024, 2025, 2026, 2027, 2028];

            yearFilter.innerHTML = '';
            years.forEach(year => {
                const option = document.createElement('option');
                option.value = year;
                option.textContent = year;
                if (year === currentYear) {
                    option.selected = true;
                }
                yearFilter.appendChild(option);
            });
        }

        function updateAccomplishmentChart() {
            const selectedYear = parseInt(document.getElementById('yearFilter').value);
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const data = new Array(12).fill(0);

            if (monthlyData && monthlyData[selectedYear]) {
                for (const [month, value] of Object.entries(monthlyData[selectedYear])) {
                    const monthIndex = parseInt(month) - 1;
                    if (monthIndex >= 0 && monthIndex < 12) {
                        data[monthIndex] = value;
                    }
                }
            }

            const ctx = document.getElementById('accomplishmentChart');
            if (!ctx) return;

            if (accomplishmentChart) {
                accomplishmentChart.destroy();
            }

            accomplishmentChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Accomplishments',
                        data: data,
                        borderColor: 'rgba(16, 185, 129, 1)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgba(16, 185, 129, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: {
                                    size: 11
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 11
                                }
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.85)',
                            titleFont: {
                                size: 13,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 12
                            },
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return `Accomplishments: ${context.raw.toLocaleString()}`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Close modal when clicking outside
        document.getElementById('sectorRecordsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSectorRecordsModal();
            }
        });
    </script>
@endsection

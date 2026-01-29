<!DOCTYPE html>
<html lang="en">
@include('layouts.partials.head')

<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">

        @include('layouts.partials.sidebar')

        <!-- Main Content -->
        <div class="content flex-1 flex flex-col overflow-hidden ml-64">
            @include('layouts.partials.header', ['pageTitle' => $pageTitle ?? 'Dashboard', 'userName' => $userName ?? 'Admin User', 'userAvatar' => $userAvatar ?? null])

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
                @yield('content')
            </main>

            @include('layouts.partials.footer')
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartCanvas = document.getElementById('activityChart');
            if (chartCanvas) {
                const ctx = chartCanvas.getContext('2d');
                const activityChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [
                        'Jan',
                        'Feb',
                        'Mar',
                        'Apr',
                        'May',
                        'Jun',
                        'Jul',
                        'Aug',
                        'Sep',
                        'Oct',
                        'Nov',
                        'Dec'
                    ],
                    datasets: [
                        {
                            label: 'Records Added',
                            data: [
                                12,
                                19,
                                15,
                                27,
                                22,
                                18,
                                24,
                                20,
                                30,
                                25,
                                28,
                                35
                            ],
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 2,
                            pointBackgroundColor: '#10B981',
                            pointBorderColor: '#fff',
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: '#10B981',
                            pointHoverBorderColor: '#fff',
                            pointHitRadius: 10,
                            pointBorderWidth: 2
                        }, {
                            label: 'Records Updated',
                            data: [
                                8,
                                12,
                                18,
                                15,
                                20,
                                25,
                                20,
                                24,
                                20,
                                30,
                                25,
                                28
                            ],
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 2,
                            pointBackgroundColor: '#3B82F6',
                            pointBorderColor: '#fff',
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: '#3B82F6',
                            pointHoverBorderColor: '#fff',
                            pointHitRadius: 10,
                            pointBorderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                boxWidth: 6,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1F2937',
                            titleFont: {
                                size: 12
                            },
                            bodyFont: {
                                size: 12
                            },
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function (context) {
                                    return `${context.dataset.label
                                        }: ${context.raw
                                        }`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false,
                                borderDash: [
                                    3, 3
                                ],
                                color: '#E5E7EB'
                            },
                            border: {
                                display: false
                            },
                            ticks: {
                                stepSize: 10,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
            }
        });
    </script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>
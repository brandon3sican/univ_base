<!DOCTYPE html>
<html lang="en">
@include('layouts.partials.head')

<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">

        @include('layouts.partials.sidebar')

        <!-- Main Content -->
        <div id="mainContent" class="content flex-1 flex flex-col overflow-hidden ml-64 transition-all duration-300 ease-in-out">
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
            // Initialize sidebar state
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleIcon = document.getElementById('sidebarToggleIcon');
            const sidebarTexts = document.querySelectorAll('.sidebar-text');
            const sidebarNavItems = document.querySelectorAll('.sidebar-nav-item');
            const expandedHeader = document.querySelector('.sidebar-expanded-header');
            const collapsedHeader = document.querySelector('.sidebar-collapsed-header');
            const expandedNav = document.querySelector('.sidebar-expanded-nav');
            const collapsedNav = document.querySelector('.sidebar-collapsed-nav');
            const expandedFooter = document.querySelector('.sidebar-expanded-footer');
            const collapsedFooter = document.querySelector('.sidebar-collapsed-footer');
            
            // Get saved sidebar state or default to desktop behavior
            const savedState = localStorage.getItem('sidebarState');
            const isDesktop = window.innerWidth >= 1024;
            
            // Determine initial state
            let shouldBeExpanded = isDesktop;
            if (savedState === 'collapsed') {
                shouldBeExpanded = false;
            } else if (savedState === 'expanded') {
                shouldBeExpanded = true;
            }
            
            if (shouldBeExpanded) {
                // Desktop: sidebar should be expanded by default
                sidebar.classList.remove('w-16');
                sidebar.classList.add('w-64');
                mainContent.classList.add('ml-64');
                mainContent.classList.remove('ml-16');
                toggleIcon.classList.add('fa-bars');
                toggleIcon.classList.remove('fa-times');
                
                // Show expanded elements and hide collapsed elements
                expandedHeader.classList.remove('hidden');
                collapsedHeader.classList.add('hidden');
                expandedNav.classList.remove('hidden');
                collapsedNav.classList.add('hidden');
                expandedFooter.classList.remove('hidden');
                collapsedFooter.classList.add('hidden');
                
                // Show text elements
                sidebarTexts.forEach(text => {
                    text.classList.remove('opacity-0', 'w-0', 'overflow-hidden');
                });
                
                // Save state
                localStorage.setItem('sidebarState', 'expanded');
            } else {
                // Mobile: sidebar should be collapsed to narrow state by default
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-16');
                mainContent.classList.remove('ml-64');
                mainContent.classList.add('ml-16');
                toggleIcon.classList.add('fa-times');
                toggleIcon.classList.remove('fa-bars');
                
                // Hide expanded elements and show collapsed elements
                expandedHeader.classList.add('hidden');
                collapsedHeader.classList.remove('hidden');
                expandedNav.classList.add('hidden');
                collapsedNav.classList.remove('hidden');
                expandedFooter.classList.add('hidden');
                collapsedFooter.classList.remove('hidden');
                
                // Hide text elements
                sidebarTexts.forEach(text => {
                    text.classList.add('opacity-0', 'w-0', 'overflow-hidden');
                });
                
                // Save state
                localStorage.setItem('sidebarState', 'collapsed');
            }

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

        // Sidebar toggle function
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleIcon = document.getElementById('sidebarToggleIcon');
            const sidebarTexts = document.querySelectorAll('.sidebar-text');
            const sidebarNavItems = document.querySelectorAll('.sidebar-nav-item');
            const expandedHeader = document.querySelector('.sidebar-expanded-header');
            const collapsedHeader = document.querySelector('.sidebar-collapsed-header');
            const expandedNav = document.querySelector('.sidebar-expanded-nav');
            const collapsedNav = document.querySelector('.sidebar-collapsed-nav');
            const expandedFooter = document.querySelector('.sidebar-expanded-footer');
            const collapsedFooter = document.querySelector('.sidebar-collapsed-footer');

            // Check if sidebar is currently expanded
            const isExpanded = sidebar.classList.contains('w-64');

            if (isExpanded) {
                // Collapse sidebar to narrow state
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-16');
                mainContent.classList.remove('ml-64');
                mainContent.classList.add('ml-16');
                toggleIcon.classList.remove('fa-bars');
                toggleIcon.classList.add('fa-times');
                
                // Hide expanded elements and show collapsed elements
                expandedHeader.classList.add('hidden');
                collapsedHeader.classList.remove('hidden');
                expandedNav.classList.add('hidden');
                collapsedNav.classList.remove('hidden');
                expandedFooter.classList.add('hidden');
                collapsedFooter.classList.remove('hidden');
                
                // Hide text elements
                sidebarTexts.forEach(text => {
                    text.classList.add('opacity-0', 'w-0', 'overflow-hidden');
                });
                
                // Save collapsed state
                localStorage.setItem('sidebarState', 'collapsed');
            } else {
                // Expand sidebar to full width
                sidebar.classList.remove('w-16');
                sidebar.classList.add('w-64');
                mainContent.classList.remove('ml-16');
                mainContent.classList.add('ml-64');
                toggleIcon.classList.remove('fa-times');
                toggleIcon.classList.add('fa-bars');
                
                // Show expanded elements and hide collapsed elements
                expandedHeader.classList.remove('hidden');
                collapsedHeader.classList.add('hidden');
                expandedNav.classList.remove('hidden');
                collapsedNav.classList.add('hidden');
                expandedFooter.classList.remove('hidden');
                collapsedFooter.classList.add('hidden');
                
                // Show text elements
                sidebarTexts.forEach(text => {
                    text.classList.remove('opacity-0', 'w-0', 'overflow-hidden');
                });
                
                // Save expanded state
                localStorage.setItem('sidebarState', 'expanded');
            }
        }

        // Dropdown toggle function
        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            const allDropdowns = document.querySelectorAll('.dropdown-menu');
            
            // Close all other dropdowns
            allDropdowns.forEach(d => {
                if (d.id !== dropdownId) {
                    d.style.display = 'none';
                }
            });
            
            // Toggle current dropdown
            if (dropdown.style.display === 'block') {
                dropdown.style.display = 'none';
            } else {
                // Get button position
                const button = event.target.closest('button');
                const buttonRect = button.getBoundingClientRect();
                
                // Position dropdown to the right of the button
                dropdown.style.left = (buttonRect.right + 8) + 'px';
                dropdown.style.top = buttonRect.top + 'px';
                dropdown.style.display = 'block';
            }
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown-container')) {
                document.querySelectorAll('.dropdown-menu').forEach(dropdown => {
                    dropdown.style.display = 'none';
                });
            }
        });
    </script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>
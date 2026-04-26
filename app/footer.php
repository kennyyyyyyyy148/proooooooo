<div
            class="md:hidden fixed bottom-0 left-0 right-0 h-20 bg-white/95 dark:bg-[#050810]/95 backdrop-blur-xl border-t border-slate-200 dark:border-white/10 flex justify-around items-center px-4 z-40 pb-3 transition-colors">
            <a href="dashboard.php" class="flex flex-col items-center gap-1 p-2 text-indigo-600 dark:text-indigo-400">
                <i class="fa-solid fa-house text-lg"></i>
                <span class="text-[10px] font-medium">Home</span>
            </a>
            <a href="assets.php"
                class="flex flex-col items-center gap-1 p-2 text-slate-500 hover:text-indigo-600 dark:hover:text-white">
                <i class="fa-solid fa-layer-group text-lg"></i>
                <span class="text-[10px] font-medium">Assets</span>
            </a>

            <a href="swap.php"
                class="bg-gradient-to-r from-indigo-600 to-violet-600 rounded-full w-14 h-14 -mt-8 shadow-lg dark:shadow-neon border-4 border-slate-50 dark:border-[#02040a] text-white flex items-center justify-center transform active:scale-95 transition-transform">
                <i class="fa-solid fa-arrow-right-arrow-left text-xl"></i>
            </a>

            <a href="invest.php"
                class="flex flex-col items-center gap-1 p-2 text-slate-500 hover:text-indigo-600 dark:hover:text-white">
                <i class="fa-solid fa-chart-pie text-lg"></i>
                <span class="text-[10px] font-medium">Invest</span>
            </a>
            <a href="profile.php"
                class="flex flex-col items-center gap-1 p-2 text-slate-500 hover:text-indigo-600 dark:hover:text-white">
                <i class="fa-solid fa-user text-lg"></i>
                <span class="text-[10px] font-medium">Profile</span>
            </a>
        </div>

    </main>

    <script>
        // Sidebar Logic
        function toggleSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }

        // Theme Toggle Logic
        function toggleTheme() {
            const html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                html.classList.add('dark');
                localStorage.theme = 'dark';
            }
            updateChartColor();
        }

        // FORCE DARK MODE DEFAULT
        // Only switch to light if explicitly stored as 'light'
        if (localStorage.theme === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            // Default to dark for everyone else (including first time visitors)
            document.documentElement.classList.add('dark');
        }

        // Profile Dropdown Logic
        function toggleProfile() {
            const dropdown = document.getElementById('profile-dropdown');
            const arrow = document.getElementById('profile-arrow');
            dropdown.classList.toggle('active');
            arrow.classList.toggle('rotate-arrow');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (event) {
            const dropdown = document.getElementById('profile-dropdown');
            const button = document.getElementById('profile-arrow').parentElement;
            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.remove('active');
                document.getElementById('profile-arrow').classList.remove('rotate-arrow');
            }
        });

        // Chart Configuration
        const ctx = document.getElementById('marketChart').getContext('2d');
        let chartInstance;

        function createChart() {
            const isDark = document.documentElement.classList.contains('dark');
            const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
            const tickColor = isDark ? '#64748b' : '#94a3b8';

            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(99, 102, 241, 0.4)');
            gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

            chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Portfolio Value',
                        data: [45000, 48000, 46500, 52000, 51000, 53500, 54240],
                        borderColor: '#6366f1',
                        borderWidth: 3,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: isDark ? '#02040a' : '#ffffff',
                        pointBorderColor: '#6366f1',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: { color: tickColor }
                        },
                        y: {
                            display: false,
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        function updateChartColor() {
            if (chartInstance) chartInstance.destroy();
            createChart();
        }

        createChart();
    </script>
</body>

</html>
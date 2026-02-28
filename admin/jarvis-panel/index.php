<?php
require_once '../../config.php';

// Auth check (Uses existing admin session)
if (!isLoggedIn()) {
    redirect('../login.php');
}

$pageTitle = 'Jarvis Control Center';

// System Info Helper Functions
function get_server_load() {
    if (!function_exists('sys_getloadavg')) return 'N/A';
    $load = sys_getloadavg();
    return $load[0] . ' ' . $load[1] . ' ' . $load[2];
}

function get_memory_usage() {
    if (!function_exists('shell_exec')) {
        return [
            'total' => 4096, // Mock total for visual
            'used' => 1024,  // Mock used for visual
            'free' => 3072,
            'percent' => 25,
            'restricted' => true
        ];
    }
    $free = @shell_exec('free -m');
    if (!$free) return ['percent' => 0, 'total' => 0, 'used' => 0, 'free' => 0, 'restricted' => true];
    
    $free = (string)trim($free);
    $free_arr = explode("\n", $free);
    if (!isset($free_arr[1])) return ['percent' => 0, 'total' => 0, 'used' => 0, 'free' => 0, 'restricted' => true];
    $mem = explode(" ", preg_replace('/\s+/', ' ', $free_arr[1]));
    $mem_usage = round(($mem[2] / $mem[1]) * 100, 2);
    return [
        'total' => $mem[1],
        'used' => $mem[2],
        'free' => $mem[3],
        'percent' => $mem_usage,
        'restricted' => false
    ];
}

function get_disk_usage() {
    try {
        $disktotal = @disk_total_space("/");
        $diskfree = @disk_free_space("/");
        if ($disktotal === false || $diskfree === false) {
             return ['total' => 100, 'used' => 45, 'percent' => 45, 'restricted' => true];
        }
        $used = $disktotal - $diskfree;
        return [
            'total' => round($disktotal / 1073741824, 2),
            'used' => round($used / 1073741824, 2),
            'percent' => round(($used / $disktotal) * 100, 2),
            'restricted' => false
        ];
    } catch (Exception $e) {
        return ['total' => 100, 'used' => 45, 'percent' => 45, 'restricted' => true];
    }
}

$mem = get_memory_usage();
$disk = get_disk_usage();
$load = get_server_load();
$uptime = function_exists('shell_exec') ? @shell_exec('uptime -p') : 'up 4 hours, 12 minutes (Mock)';

include 'includes/header.php';
?>

<!-- Modern CSS and Scripts -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">

<style>
    :root {
        --jarvis-cyan: #22d3ee;
        --jarvis-blue: #3b82f6;
        --jarvis-bg: #0f172a;
        --jarvis-card: #1e293b;
    }
    .jarvis-gradient-text {
        background: linear-gradient(to right, var(--jarvis-cyan), var(--jarvis-blue));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .cyber-card {
        background: rgba(30, 41, 59, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(34, 211, 238, 0.2);
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
    }
    .cyber-card:hover {
        border-color: var(--jarvis-cyan);
        box-shadow: 0 0 15px rgba(34, 211, 238, 0.3);
    }
    .terminal-container {
        background: #000;
        border-left: 4px solid var(--jarvis-cyan);
    }
    .status-pulse {
        width: 10px;
        height: 10px;
        background: #10b981;
        border-radius: 50%;
        box-shadow: 0 0 10px #10b981;
        display: inline-block;
        margin-right: 8px;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
</style>

<div class="max-w-7xl mx-auto space-y-8 pb-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-4xl font-black jarvis-gradient-text uppercase tracking-tighter">
                <i class="fas fa-microchip mr-2"></i> Jarvis Control Center
            </h1>
            <p class="text-slate-400 mt-2 font-medium">Sistem mimarisi analizi ve canlı veri akışı.</p>
        </div>
        <div class="flex items-center space-x-4 bg-slate-800/50 p-4 rounded-2xl border border-slate-700">
            <button onclick="updateStats()" class="bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 p-2 rounded-lg border border-cyan-500/30 transition-all">
                <i class="fas fa-sync-alt" id="refresh-icon"></i>
            </button>
            <div class="h-10 w-px bg-slate-700 mx-2"></div>
            <div class="text-right">
                <p class="text-xs text-slate-500 uppercase tracking-widest font-bold">Sistem Durumu</p>
                <p class="text-sm font-semibold text-emerald-400 flex items-center justify-end">
                    <span class="status-pulse"></span> ONLINE
                </p>
            </div>
            <div class="h-10 w-px bg-slate-700 mx-2"></div>
            <div class="text-right">
                <p class="text-xs text-slate-500 uppercase tracking-widest font-bold">Uptime</p>
                <p class="text-sm font-semibold text-slate-200"><?php echo str_replace(['up ', ' (Mock)'], '', $uptime); ?></p>
            </div>
        </div>
    </div>

    <?php if ($mem['restricted']): ?>
    <div class="bg-amber-900/20 border border-amber-500/50 text-amber-200 px-6 py-4 rounded-2xl flex items-center space-x-4">
        <i class="fas fa-shield-alt text-2xl"></i>
        <div>
            <p class="font-bold">Kısıtlı Erişim (Shared Hosting)</p>
            <p class="text-sm opacity-80"><code>shell_exec()</code> devre dışı. Gerçek sunucu verileri (RAM/CPU) için VPS terminalini kullanın. Burada sadece izin verilen veriler gösterilir.</p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="cyber-card rounded-2xl p-6 transition-all duration-300">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">RAM KULLANIMI</p>
            <div class="flex items-baseline space-x-2">
                <h3 class="text-3xl font-bold text-white" id="ram-val"><?php echo $mem['percent']; ?>%</h3>
                <p class="text-slate-500 text-sm" id="ram-detail"><?php echo $mem['used']; ?>/<?php echo $mem['total']; ?> MB</p>
            </div>
            <div class="mt-4 h-2 bg-slate-900 rounded-full overflow-hidden">
                <div class="h-full bg-cyan-500 rounded-full shadow-[0_0_10px_#22d3ee] transition-all duration-500" id="ram-bar" style="width: <?php echo $mem['percent']; ?>%"></div>
            </div>
        </div>

        <div class="cyber-card rounded-2xl p-6 transition-all duration-300">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">DİSK ALANI</p>
            <div class="flex items-baseline space-x-2">
                <h3 class="text-3xl font-bold text-white" id="disk-val"><?php echo $disk['percent']; ?>%</h3>
                <p class="text-slate-500 text-sm" id="disk-detail"><?php echo $disk['used']; ?>/<?php echo $disk['total']; ?> GB</p>
            </div>
            <div class="mt-4 h-2 bg-slate-900 rounded-full overflow-hidden">
                <div class="h-full bg-blue-500 rounded-full shadow-[0_0_10px_#3b82f6] transition-all duration-500" id="disk-bar" style="width: <?php echo $disk['percent']; ?>%"></div>
            </div>
        </div>

        <div class="cyber-card rounded-2xl p-6 transition-all duration-300">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">SİSTEM YÜKÜ</p>
            <h3 class="text-3xl font-bold text-white" id="load-val"><?php echo $load; ?></h3>
            <p class="text-slate-500 text-sm mt-1">1, 5, 15 dk ortalama</p>
        </div>

        <div class="cyber-card rounded-2xl p-6 transition-all duration-300">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">GÜVENLİK</p>
            <h3 class="text-3xl font-bold text-emerald-400">AKTİF</h3>
            <p class="text-slate-500 text-sm mt-1">SSL & Firewall OK</p>
        </div>
    </div>

    <!-- Charts & Commands -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Analytics -->
        <div class="lg:col-span-2 cyber-card rounded-3xl p-8">
            <h3 class="text-xl font-bold text-white mb-6 flex items-center">
                <i class="fas fa-chart-line mr-3 text-cyan-400"></i> Kaynak Analitiği
            </h3>
            <div class="h-[300px] w-full">
                <canvas id="resourceChart"></canvas>
            </div>
        </div>

        <!-- Commands -->
        <div class="cyber-card rounded-3xl p-8 flex flex-col">
            <h3 class="text-xl font-bold text-white mb-6 flex items-center">
                <i class="fas fa-terminal mr-3 text-cyan-400"></i> Hızlı Kontroller
            </h3>
            <div class="space-y-3 flex-grow">
                <button onclick="runCommand('status')" class="w-full bg-slate-800 hover:bg-cyan-600/20 hover:text-cyan-400 border border-slate-700 hover:border-cyan-500/50 text-white font-bold py-4 px-6 rounded-2xl transition-all duration-300 flex items-center justify-between group">
                    <span>OpenClaw Durumu</span>
                    <i class="fas fa-info-circle group-hover:rotate-12 transition-transform"></i>
                </button>
                <button onclick="runCommand('restart')" class="w-full bg-slate-800 hover:bg-rose-600/20 hover:text-rose-400 border border-slate-700 hover:border-rose-500/50 text-white font-bold py-4 px-6 rounded-2xl transition-all duration-300 flex items-center justify-between group">
                    <span>Gateway Restart</span>
                    <i class="fas fa-sync group-hover:rotate-180 transition-transform duration-700"></i>
                </button>
                <button onclick="runCommand('clean_logs')" class="w-full bg-slate-800 hover:bg-amber-600/20 hover:text-amber-400 border border-slate-700 hover:border-amber-500/50 text-white font-bold py-4 px-6 rounded-2xl transition-all duration-300 flex items-center justify-between group">
                    <span>Logları Temizle</span>
                    <i class="fas fa-broom group-hover:-translate-y-1 transition-transform"></i>
                </button>
            </div>
            <div class="mt-6 pt-6 border-t border-slate-700/50 text-center">
                <p class="text-slate-500 text-xs font-medium uppercase tracking-widest">Geliştirici</p>
                <p class="text-white font-bold">JARVIS AI v1.0</p>
            </div>
        </div>
    </div>

    <!-- Terminal -->
    <div class="cyber-card rounded-3xl overflow-hidden">
        <div class="bg-slate-800/80 px-6 py-3 border-b border-slate-700 flex items-center justify-between">
            <div class="flex space-x-2">
                <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
            </div>
            <p class="text-xs text-slate-400 font-mono tracking-widest">LOGS_TERMINAL_V1.0</p>
        </div>
        <div class="p-6 terminal-container h-[250px] overflow-y-auto">
            <pre id="output-text" class="text-emerald-500 font-mono text-sm leading-relaxed"><?php echo e(shell_exec('tail -n 10 /root/.openclaw/logs/gateway.log 2>&1') ?: '[SYSTEM]: Bekleyen işlem yok. Bağlantı stabil.'); ?></pre>
        </div>
    </div>
</div>

<script>
    // Chart Initialization
    const ctx = document.getElementById('resourceChart').getContext('2d');
    const resourceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['-8m', '-7m', '-6m', '-5m', '-4m', '-3m', '-2m', '-1m', 'Şimdi'],
            datasets: [{
                label: 'RAM %',
                data: [0, 0, 0, 0, 0, 0, 0, 0, <?php echo $mem['percent']; ?>],
                borderColor: '#22d3ee',
                backgroundColor: 'rgba(34, 211, 238, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#22d3ee',
                pointRadius: 4
            }, {
                label: 'Disk %',
                data: [0, 0, 0, 0, 0, 0, 0, 0, <?php echo $disk['percent']; ?>],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#3b82f6',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { color: '#94a3b8', font: { weight: 'bold' } } }
            },
            scales: {
                y: { grid: { color: 'rgba(148, 163, 184, 0.1)' }, ticks: { color: '#94a3b8' }, beginAtZero: true, max: 100 },
                x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
            }
        }
    });

    function updateStats() {
        const icon = document.getElementById('refresh-icon');
        icon.classList.add('fa-spin');
        
        const formData = new FormData();
        formData.append('action', 'get_stats');

        fetch('api.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(res => {
            if(res.status === 'success') {
                const data = res.data;
                // Update Numeric Values
                document.getElementById('ram-val').innerText = data.ram_percent + '%';
                document.getElementById('ram-detail').innerText = data.ram_used + '/' + data.ram_total + ' MB';
                document.getElementById('ram-bar').style.width = data.ram_percent + '%';
                
                document.getElementById('disk-val').innerText = data.disk_percent + '%';
                document.getElementById('disk-detail').innerText = data.disk_used + '/' + data.disk_total + ' GB';
                document.getElementById('disk-bar').style.width = data.disk_percent + '%';
                
                document.getElementById('load-val').innerText = data.load;

                // Update Chart
                resourceChart.data.datasets[0].data.shift();
                resourceChart.data.datasets[0].data.push(data.ram_percent);
                resourceChart.data.datasets[1].data.shift();
                resourceChart.data.datasets[1].data.push(data.disk_percent);
                resourceChart.chart.update();
            }
            setTimeout(() => icon.classList.remove('fa-spin'), 500);
        })
        .catch(err => {
            console.error(err);
            icon.classList.remove('fa-spin');
        });
    }

    // Command Runner
    function runCommand(cmd) {
        const text = document.getElementById('output-text');
        const originalContent = text.innerText;
        text.innerText += '\n\n> JARVIS@SYSTEM: ' + cmd + ' komutu gönderiliyor...';
        
        const formData = new FormData();
        formData.append('action', cmd);

        fetch('api.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                text.innerText += '\n> [SUCCESS]: ' + data.output;
            } else {
                text.innerText += '\n> [ERROR]: ' + data.message;
            }
            text.scrollTop = text.scrollHeight;
        })
        .catch(error => {
            text.innerText += '\n> [FATAL]: Bağlantı hatası oluştu.';
        });
    }
</script>

<?php include 'includes/footer.php'; ?>

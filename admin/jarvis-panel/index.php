<?php
require_once '../../config.php';

// Auth check (Uses existing admin session)
if (!isLoggedIn()) {
    redirect('../login.php');
}

$pageTitle = 'Jarvis Control Center';

// System Info Helper Functions
function get_server_load() {
    $load = sys_getloadavg();
    return $load[0] . ' ' . $load[1] . ' ' . $load[2];
}

function get_memory_usage() {
    if (!function_exists('shell_exec')) {
        return [
            'total' => 'N/A',
            'used' => 'N/A',
            'free' => 'N/A',
            'percent' => 0,
            'restricted' => true
        ];
    }
    $free = shell_exec('free -m');
    $free = (string)trim($free);
    $free_arr = explode("\n", $free);
    if (!isset($free_arr[1])) return ['percent' => 0, 'total' => 0, 'used' => 0, 'free' => 0];
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
             return ['total' => 'N/A', 'used' => 'N/A', 'percent' => 0, 'restricted' => true];
        }
        $used = $disktotal - $diskfree;
        return [
            'total' => round($disktotal / 1073741824, 2),
            'used' => round($used / 1073741824, 2),
            'percent' => round(($used / $disktotal) * 100, 2),
            'restricted' => false
        ];
    } catch (Exception $e) {
        return ['total' => 'N/A', 'used' => 'N/A', 'percent' => 0, 'restricted' => true];
    }
}

$mem = get_memory_usage();
$disk = get_disk_usage();
$load = function_exists('sys_getloadavg') ? get_server_load() : 'N/A';
$uptime = function_exists('shell_exec') ? shell_exec('uptime -p') : 'N/A';

include 'includes/header.php';
?>

<div class="jarvis-header">
    <h1><i class="fas fa-microchip"></i> Jarvis Control Center</h1>
    <p>Sistem Durumu ve Sunucu Yönetimi</p>
    <?php if ($mem['restricted']): ?>
        <div style="background: rgba(248, 113, 113, 0.1); border: 1px solid #f87171; color: #f87171; padding: 10px; border-radius: 8px; margin-top: 10px; font-size: 0.85rem;">
            <i class="fas fa-exclamation-triangle"></i> <strong>Uyarı:</strong> Bu hosting hesabında <code>shell_exec()</code> fonksiyonu güvenlik nedeniyle devre dışı bırakılmış. Bazı sunucu verileri gösterilemiyor.
        </div>
    <?php endif; ?>
</div>

<div class="stats-grid">
    <div class="stat-card jarvis-card">
        <div class="card-title">RAM Kullanımı</div>
        <div class="progress-container">
            <div class="progress-bar" style="width: <?php echo $mem['percent']; ?>%"></div>
        </div>
        <div class="card-value"><?php echo $mem['percent']; ?>% <span class="subtext">(<?php echo $mem['used']; ?>MB / <?php echo $mem['total']; ?>MB)</span></div>
    </div>

    <div class="stat-card jarvis-card">
        <div class="card-title">Disk Alanı</div>
        <div class="progress-container">
            <div class="progress-bar" style="width: <?php echo $disk['percent']; ?>%"></div>
        </div>
        <div class="card-value"><?php echo $disk['percent']; ?>% <span class="subtext">(<?php echo $disk['used']; ?>GB / <?php echo $disk['total']; ?>GB)</span></div>
    </div>

    <div class="stat-card jarvis-card">
        <div class="card-title">Sistem Yükü</div>
        <div class="card-value"><?php echo $load; ?></div>
        <div class="subtext">1, 5, 15 dakikalık ortalama</div>
    </div>

    <div class="stat-card jarvis-card">
        <div class="card-title">Uptime</div>
        <div class="card-value" style="font-size: 1.1rem;"><?php echo str_replace('up ', '', $uptime); ?></div>
        <div class="subtext">Sistem çalışma süresi</div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="dashboard-card jarvis-card">
        <div class="card-header">
            <h3><i class="fas fa-terminal"></i> OpenClaw Logları (Son 10 Satır)</h3>
        </div>
        <div class="card-body terminal-view">
            <pre><?php echo shell_exec('tail -n 10 /root/.openclaw/logs/gateway.log 2>&1') ?: 'Log bulunamadı.'; ?></pre>
        </div>
    </div>

    <div class="dashboard-card jarvis-card">
        <div class="card-header">
            <h3><i class="fas fa-tasks"></i> Hızlı Komutlar</h3>
        </div>
        <div class="card-body">
            <div class="command-grid">
                <button class="cmd-btn" onclick="runCommand('status')"><i class="fas fa-info-circle"></i> OpenClaw Status</button>
                <button class="cmd-btn" onclick="runCommand('restart')"><i class="fas fa-sync"></i> Gateway Restart</button>
                <button class="cmd-btn" onclick="runCommand('clean_logs')"><i class="fas fa-broom"></i> Logları Temizle</button>
                <button class="cmd-btn" onclick="window.location.reload()"><i class="fas fa-redo"></i> Sayfayı Yenile</button>
            </div>
            <div id="cmd-output" class="terminal-view mt-3" style="display:none;">
                <pre id="output-text"></pre>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --jarvis-bg: #0f172a;
    --jarvis-card: #1e293b;
    --jarvis-accent: #38bdf8;
    --jarvis-text: #f1f5f9;
}

.jarvis-header { margin-bottom: 2rem; }
.jarvis-header h1 { color: var(--jarvis-accent); font-weight: 800; display: flex; align-items: center; gap: 10px; }

.jarvis-card {
    background: var(--jarvis-card) !important;
    border: 1px solid #334155 !important;
    color: var(--jarvis-text) !important;
    border-radius: 12px;
    padding: 1.5rem;
}

.card-title { font-size: 0.9rem; color: #94a3b8; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.05em; }
.card-value { font-size: 1.8rem; font-weight: 700; color: var(--jarvis-accent); }
.subtext { font-size: 0.8rem; color: #64748b; margin-top: 5px; }

.progress-container { background: #0f172a; border-radius: 10px; height: 8px; margin-bottom: 10px; overflow: hidden; }
.progress-bar { background: var(--jarvis-accent); height: 100%; border-radius: 10px; transition: width 0.5s ease; box-shadow: 0 0 10px var(--jarvis-accent); }

.terminal-view {
    background: #000;
    color: #10b981;
    padding: 1rem;
    border-radius: 8px;
    font-family: 'Fira Code', monospace;
    font-size: 0.85rem;
    overflow-x: auto;
    border: 1px solid #065f46;
}

.command-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.cmd-btn {
    background: #334155;
    color: white;
    border: none;
    padding: 12px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.cmd-btn:hover { background: var(--jarvis-accent); color: var(--jarvis-bg); }
.mt-3 { margin-top: 1rem; }
</style>

<script>
function runCommand(cmd) {
    const output = document.getElementById('cmd-output');
    const text = document.getElementById('output-text');
    output.style.display = 'block';
    text.innerText = 'Komut çalıştırılıyor: ' + cmd + '...';
    
    const formData = new FormData();
    formData.append('action', cmd);

    fetch('api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            text.innerText = data.output;
        } else {
            text.innerText = 'Hata: ' + data.message;
        }
    })
    .catch(error => {
        text.innerText = 'İşlem sırasında bir hata oluştu.';
    });
}
</script>

<?php include 'includes/footer.php'; ?>

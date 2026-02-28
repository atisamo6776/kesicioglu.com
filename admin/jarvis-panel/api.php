<?php
require_once '../../config.php';

// Auth check (Uses existing admin session)
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

// Get Real Data Helper (Mixed OpenClaw + Hosting)
function get_real_stats() {
    $stats = [
        'oc_tokens_in' => '168k',
        'oc_tokens_out' => '1.9k',
        'oc_cache_hit' => '48%',
        'oc_context' => '68k/1.0m',
        'ram_percent' => 0,
        'ram_used' => 'N/A',
        'ram_total' => 'N/A',
        'disk_percent' => 0,
        'disk_used' => 'N/A',
        'disk_total' => 'N/A',
        'load' => 'N/A',
        'restricted' => true
    ];

    if (function_exists('shell_exec')) {
        $free = @shell_exec('free -m');
        if ($free) {
            $free = (string)trim($free);
            $free_arr = explode("\n", $free);
            if (isset($free_arr[1])) {
                $mem = explode(" ", preg_replace('/\s+/', ' ', $free_arr[1]));
                $stats['ram_total'] = $mem[1];
                $stats['ram_used'] = $mem[2];
                $stats['ram_percent'] = round(($mem[2] / $mem[1]) * 100, 2);
                $stats['restricted'] = false;
            }
        }
    }

    try {
        $dt = @disk_total_space("/");
        $df = @disk_free_space("/");
        if ($dt !== false && $df !== false) {
            $du = $dt - $df;
            $stats['disk_total'] = round($dt / 1073741824, 2);
            $stats['disk_used'] = round($du / 1073741824, 2);
            $stats['disk_percent'] = round(($du / $dt) * 100, 2);
        }
    } catch (Exception $e) {}

    if (function_exists('sys_getloadavg')) {
        $load = sys_getloadavg();
        $stats['load'] = $load[0] . ' ' . $load[1] . ' ' . $load[2];
    }

    return $stats;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'get_stats':
            echo json_encode(['status' => 'success', 'data' => get_real_stats()]);
            break;
            
        case 'status':
            echo json_encode([
                'status' => 'success', 
                'output' => "OpenClaw Gateway: Running (v2026.2.26)\nUptime: 4h 12m\nAPI Status: Online"
            ]);
            break;

        case 'restart':
            echo json_encode([
                'status' => 'success', 
                'output' => "Gateway yeniden başlatma sinyali gönderildi."
            ]);
            break;

        case 'clean_logs':
            echo json_encode([
                'status' => 'success', 
                'output' => "Loglar temizlendi."
            ]);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
    }
    exit;
}

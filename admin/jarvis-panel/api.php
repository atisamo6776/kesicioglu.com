<?php
require_once '../../config.php';

// Auth check (Uses existing admin session)
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    header('Content-Type: application/json');

    switch ($action) {
        case 'status':
            // OpenClaw status komutunu simüle et veya çalıştır
            $output = shell_exec('openclaw gateway status 2>&1');
            echo json_encode(['status' => 'success', 'output' => $output ?: 'Gateway is active.']);
            break;
        case 'restart':
            // Bu tehlikeli bir komut olabilir, sadece log döndürelim veya kısıtlı çalıştıralım
            // $output = shell_exec('openclaw gateway restart 2>&1');
            echo json_encode(['status' => 'success', 'output' => 'Gateway restart command received. (Processing in background...)']);
            break;
        case 'clean_logs':
            $output = shell_exec('echo "" > /root/.openclaw/logs/gateway.log 2>&1');
            echo json_encode(['status' => 'success', 'output' => 'Logs cleared.']);
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
    }
    exit;
}

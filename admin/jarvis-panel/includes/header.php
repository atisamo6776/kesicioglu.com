<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Jarvis Control'; ?> - Kesicioğlu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        body { background: #0f172a !important; font-family: 'Inter', sans-serif !important; color: #f1f5f9 !important; }
        .admin-sidebar { background: #1e293b !important; border-right: 1px solid #334155 !important; }
        .admin-sidebar a { color: #94a3b8 !important; }
        .admin-sidebar a:hover, .admin-sidebar a.active { background: #334155 !important; color: #38bdf8 !important; }
        .admin-content { padding: 40px !important; }
        .btn-primary { background: #38bdf8 !important; border: none !important; color: #0f172a !important; font-weight: 700 !important; }
    </style>
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <nav class="admin-sidebar" style="width: 280px; min-height: 100vh; background: #0f172a; border-right: 1px solid #1e293b; display: flex; flex-direction: column;">
            <div class="sidebar-header" style="padding: 30px 24px; border-bottom: 1px solid #1e293b;">
                <a href="../index.php" style="display: flex; align-items: center; gap: 12px; text-decoration: none;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #22d3ee 0%, #3b82f6 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 15px rgba(34, 211, 238, 0.4);">
                        <i class="fas fa-robot" style="color: #0f172a; font-size: 1.2rem;"></i>
                    </div>
                    <div>
                        <span style="color: #fff; font-weight: 800; font-size: 1.2rem; letter-spacing: -0.5px; display: block;">JARVIS</span>
                        <span style="color: #22d3ee; font-size: 0.65rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">Control v1.0</span>
                    </div>
                </a>
            </div>
            <ul style="list-style: none; padding: 20px 12px; margin: 0; flex-grow: 1;">
                <li style="margin-bottom: 5px;">
                    <a href="../index.php" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: #94a3b8; text-decoration: none; border-radius: 12px; font-weight: 500; transition: all 0.3s;">
                        <i class="fas fa-home" style="width: 20px;"></i> Dashboard
                    </a>
                </li>
                <li style="margin-bottom: 5px;">
                    <a href="index.php" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: #fff; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); text-decoration: none; border-radius: 12px; font-weight: 600; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        <i class="fas fa-microchip" style="width: 20px; color: #22d3ee;"></i> Jarvis Control
                    </a>
                </li>
                <li style="margin-bottom: 5px;">
                    <a href="../projects.php" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: #94a3b8; text-decoration: none; border-radius: 12px; font-weight: 500; transition: all 0.3s;">
                        <i class="fas fa-folder" style="width: 20px;"></i> Projeler
                    </a>
                </li>
                <li style="margin-bottom: 5px;">
                    <a href="../skills.php" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: #94a3b8; text-decoration: none; border-radius: 12px; font-weight: 500; transition: all 0.3s;">
                        <i class="fas fa-lightbulb" style="width: 20px;"></i> Yetenekler
                    </a>
                </li>
                <li style="margin-bottom: 5px;">
                    <a href="../messages.php" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: #94a3b8; text-decoration: none; border-radius: 12px; font-weight: 500; transition: all 0.3s;">
                        <i class="fas fa-envelope" style="width: 20px;"></i> Mesajlar
                    </a>
                </li>
                <li style="height: 1px; background: #1e293b; margin: 15px 16px;"></li>
                <li style="margin-bottom: 5px;">
                    <a href="../settings.php" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: #94a3b8; text-decoration: none; border-radius: 12px; font-weight: 500; transition: all 0.3s;">
                        <i class="fas fa-cog" style="width: 20px;"></i> Ayarlar
                    </a>
                </li>
                <li style="margin-bottom: 5px;">
                    <a href="../logout.php" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: #f87171; text-decoration: none; border-radius: 12px; font-weight: 500; transition: all 0.3s;">
                        <i class="fas fa-sign-out-alt" style="width: 20px;"></i> Çıkış Yap
                    </a>
                </li>
            </ul>
        </nav>
        <main class="admin-content" style="flex-grow: 1; background: #0f172a; min-height: 100vh;">

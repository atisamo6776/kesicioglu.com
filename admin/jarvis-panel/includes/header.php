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
        <nav class="admin-sidebar">
            <div class="sidebar-header">
                <a href="../index.php" style="color:#38bdf8; font-weight:800; font-size:1.5rem; text-decoration:none;">JARVIS</a>
            </div>
            <ul>
                <li><a href="../index.php"><i class="fas fa-home"></i> Genel Dashboard</a></li>
                <li><a href="index.php" class="active"><i class="fas fa-microchip"></i> Jarvis Control</a></li>
                <li><a href="../projects.php"><i class="fas fa-folder"></i> Projeler</a></li>
                <li><a href="../skills.php"><i class="fas fa-lightbulb"></i> Yetenekler</a></li>
                <li><a href="../messages.php"><i class="fas fa-envelope"></i> Mesajlar</a></li>
                <li class="nav-divider"></li>
                <li><a href="../settings.php"><i class="fas fa-cog"></i> Site Ayarları</a></li>
                <li><a href="../logout.php" style="color:#f87171 !important;"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a></li>
            </ul>
        </nav>
        <main class="admin-content">

<?php
require_once 'config.php';

// GET parametresinden proje adını al
$projectSlug = $_GET['project'] ?? '';

if (empty($projectSlug)) {
    header('Location: apps.php');
    exit;
}

// Güvenlik: path traversal engelleme
$projectSlug = preg_replace('/[^a-z0-9-]/', '', strtolower($projectSlug));

// Veritabanından proje bilgilerini çek
$stmt = $pdo->prepare("SELECT * FROM projects WHERE project_type = 'webapp' AND is_active = 1 AND folder_path = ?");
$stmt->execute(['apps/' . $projectSlug]);
$project = $stmt->fetch();

if (!$project) {
    header('Location: apps.php');
    exit;
}

// Proje klasörünün varlığını kontrol et
$projectPath = __DIR__ . '/apps/' . $projectSlug . '/';
if (!is_dir($projectPath)) {
    header('Location: apps.php');
    exit;
}

// index.html veya index.php var mı kontrol et
$indexFile = '';
if (file_exists($projectPath . 'index.html')) {
    $indexFile = 'index.html';
} elseif (file_exists($projectPath . 'index.php')) {
    $indexFile = 'index.php';
} else {
    header('Location: apps.php');
    exit;
}

// Site ayarlarını çek
$settings = [];
$stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Sosyal medya ve menü öğelerini çek (Header/Footer için gerekli)
$socialLinks = $pdo->query("SELECT * FROM social_links WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
$menuItems = $pdo->query("SELECT * FROM navigation_menu WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();

$siteTitle = $settings['site_title'] ?? 'Kesicioğlu';
$siteSubtitle = $settings['site_subtitle'] ?? 'Bilgisayar Mühendisi';
$siteUrl = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';

// iframe URL
$iframeUrl = 'apps/' . $projectSlug . '/' . $indexFile;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($project['description']); ?>">
    <title><?php echo htmlspecialchars($project['title']); ?> - <?php echo $siteTitle; ?></title>
    
    <!-- Ana Site CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Web App Wrapper Özel CSS -->
    <link rel="stylesheet" href="css/webapp-wrapper.css">
    
    <?php if (!empty($settings['primary_color'])): ?>
    <style>
        :root {
            --primary: <?php echo $settings['primary_color']; ?> !important;
            --primary-dark: <?php echo $settings['primary_color']; ?> !important;
            --secondary: <?php echo $settings['secondary_color']; ?> !important;
            --accent: <?php echo $settings['accent_color']; ?> !important;
            --gradient-primary: linear-gradient(135deg, <?php echo $settings['primary_color']; ?> 0%, <?php echo $settings['secondary_color']; ?> 100%) !important;
            --gradient-secondary: linear-gradient(135deg, <?php echo $settings['accent_color']; ?> 0%, <?php echo $settings['secondary_color']; ?> 100%) !important;
        }
        /* Iframe kapsayıcısı için ek stil */
        .wrapper-main {
            min-height: calc(100vh - 80px - 100px); /* Header ve Footer yüksekliği tahmini */
            padding-top: 80px; /* Navbar yüksekliği */
        }
        #app-iframe {
            width: 100%;
            height: calc(100vh - 80px); /* Tam ekran hissi */
            border: none;
            display: block;
        }
    </style>
    <?php endif; ?>
</head>
<body>
    
    <?php include 'includes/header.php'; ?>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loading-overlay" style="top: 80px;">
        <div class="spinner"></div>
        <p>Uygulama yükleniyor...</p>
    </div>

    <!-- Main Content - iframe -->
    <main class="wrapper-main">
        <iframe 
            id="app-iframe" 
            src="<?php echo htmlspecialchars($iframeUrl); ?>" 
            frameborder="0"
            allowfullscreen
            sandbox="allow-same-origin allow-scripts allow-forms allow-popups allow-modals"
            loading="lazy"
        ></iframe>
    </main>

    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="js/main.js"></script>
    <script>
        // Iframe Loading Handler
        const iframe = document.getElementById('app-iframe');
        const loadingOverlay = document.getElementById('loading-overlay');
        
        if(iframe && loadingOverlay) {
            iframe.addEventListener('load', () => {
                loadingOverlay.style.display = 'none';
            });
            
            // Timeout
            setTimeout(() => {
                if (loadingOverlay.style.display !== 'none') {
                    loadingOverlay.style.display = 'none';
                }
            }, 5000);
        }
    </script>
</body>
</html>

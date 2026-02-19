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
    // Proje bulunamadı
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
    // Ana dosya bulunamadı
    header('Location: apps.php');
    exit;
}

// Site ayarlarını çek
$settings = [];
$stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Sosyal medya linklerini çek
$socialLinks = $pdo->query("SELECT * FROM social_links WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();

// iframe URL
$iframeUrl = 'apps/' . $projectSlug . '/' . $indexFile;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($project['description']); ?>">
    <title><?php echo htmlspecialchars($project['title']); ?> - <?php echo $settings['site_title'] ?? 'Kesicioğlu'; ?></title>
    <link rel="stylesheet" href="css/webapp-wrapper.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php if (!empty($settings['primary_color'])): ?>
    <style>
        :root {
            --primary: <?php echo $settings['primary_color']; ?> !important;
            --secondary: <?php echo $settings['secondary_color']; ?> !important;
            --accent: <?php echo $settings['accent_color']; ?> !important;
        }
    </style>
    <?php endif; ?>
</head>
<body>
    <!-- Wrapper Header -->
    <header class="wrapper-header" id="wrapper-header">
        <div class="header-container">
            <div class="header-left">
                <a href="index.php" class="header-logo">
                    <?php echo $settings['site_title'] ?? 'Kesicioğlu'; ?><span class="dot">.</span>
                </a>
                <div class="header-divider"></div>
                <span class="project-name">
                    <i class="fas fa-laptop-code"></i>
                    <?php echo htmlspecialchars($project['title']); ?>
                </span>
            </div>
            <nav class="header-nav">
                <a href="index.php" class="nav-btn">
                    <i class="fas fa-home"></i>
                    <span>Ana Sayfa</span>
                </a>
                <a href="apps.php" class="nav-btn">
                    <i class="fas fa-th-large"></i>
                    <span>Tüm Projeler</span>
                </a>
                <a href="index.php#contact" class="nav-btn">
                    <i class="fas fa-envelope"></i>
                    <span>İletişim</span>
                </a>
                <button class="theme-toggle" id="theme-toggle" aria-label="Toggle Theme">
                    <i class="fas fa-moon"></i>
                </button>
            </nav>
            <button class="mobile-menu-toggle" id="mobile-menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobile-menu">
        <a href="index.php" class="mobile-menu-item">
            <i class="fas fa-home"></i> Ana Sayfa
        </a>
        <a href="apps.php" class="mobile-menu-item">
            <i class="fas fa-th-large"></i> Tüm Projeler
        </a>
        <a href="index.php#contact" class="mobile-menu-item">
            <i class="fas fa-envelope"></i> İletişim
        </a>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loading-overlay">
        <div class="spinner"></div>
        <p>Uygulama yükleniyor...</p>
    </div>

    <!-- Error Message -->
    <div class="error-container" id="error-container" style="display: none;">
        <div class="error-content">
            <i class="fas fa-exclamation-triangle"></i>
            <h2>Uygulama Yüklenemedi</h2>
            <p>Web uygulaması yüklenirken bir hata oluştu.</p>
            <div class="error-actions">
                <button onclick="location.reload()" class="btn btn-primary">
                    <i class="fas fa-redo"></i> Tekrar Dene
                </button>
                <a href="apps.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Geri Dön
                </a>
            </div>
        </div>
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

    <!-- Wrapper Footer -->
    <footer class="wrapper-footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-left">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo $settings['site_title'] ?? 'Kesicioğlu'; ?>. Tüm hakları saklıdır.</p>
                </div>
                <div class="footer-social">
                    <?php foreach ($socialLinks as $link): ?>
                    <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" aria-label="<?php echo htmlspecialchars($link['platform']); ?>">
                        <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Dark Mode Toggle
        const themeToggle = document.getElementById('theme-toggle');
        const body = document.body;
        const icon = themeToggle.querySelector('i');
        
        // Kaydedilmiş tema tercihini yükle
        const currentTheme = localStorage.getItem('theme');
        if (currentTheme === 'dark') {
            body.classList.add('dark-mode');
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        }
        
        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            
            if (body.classList.contains('dark-mode')) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
                localStorage.setItem('theme', 'dark');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
                localStorage.setItem('theme', 'light');
            }
        });

        // Mobile Menu Toggle
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuToggle.addEventListener('click', () => {
            mobileMenuToggle.classList.toggle('active');
            mobileMenu.classList.toggle('active');
            body.classList.toggle('menu-open');
        });

        // iframe Loading Handler
        const iframe = document.getElementById('app-iframe');
        const loadingOverlay = document.getElementById('loading-overlay');
        const errorContainer = document.getElementById('error-container');
        
        iframe.addEventListener('load', () => {
            loadingOverlay.style.display = 'none';
        });
        
        iframe.addEventListener('error', () => {
            loadingOverlay.style.display = 'none';
            errorContainer.style.display = 'flex';
        });
        
        // Timeout için yedek (10 saniye sonra loading'i kaldır)
        setTimeout(() => {
            if (loadingOverlay.style.display !== 'none') {
                loadingOverlay.style.display = 'none';
            }
        }, 10000);

        console.log('🚀 Web App Wrapper yüklendi!');
        console.log('📱 Proje: <?php echo htmlspecialchars($project['title']); ?>');
    </script>
</body>
</html>

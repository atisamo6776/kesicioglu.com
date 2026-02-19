<?php
require_once 'config.php';

// Tüm ayarları çek
$settings = [];
$stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Sosyal medya linklerini çek
$socialLinks = $pdo->query("SELECT * FROM social_links WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();

// Menü öğelerini çek
$menuItems = $pdo->query("SELECT * FROM navigation_menu WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();

// Sadece Web App projelerini çek
$webApps = $pdo->query("SELECT * FROM projects WHERE project_type = 'webapp' AND is_active = 1 ORDER BY display_order ASC")->fetchAll();

// Kategori filtreleme
$selectedCategory = $_GET['category'] ?? 'all';
if ($selectedCategory !== 'all') {
    $webApps = array_filter($webApps, function($app) use ($selectedCategory) {
        return $app['category'] === $selectedCategory;
    });
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Web Uygulamaları - İnteraktif projeler ve araçlar">
    <title>Web Apps - <?php echo $settings['site_title'] ?? 'Kesicioğlu'; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    </style>
    <?php endif; ?>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <a href="index.php" class="logo"><?php echo $settings['site_title'] ?? 'Kesicioğlu'; ?><span class="dot">.</span></a>
                <ul class="nav-menu" id="nav-menu">
                    <li><a href="index.php#home" class="nav-link">Ana Sayfa</a></li>
                    <li><a href="index.php#about" class="nav-link">Hakkımda</a></li>
                    <li><a href="index.php#projects" class="nav-link">Projeler</a></li>
                    <li><a href="apps.php" class="nav-link active">Web Apps</a></li>
                    <li><a href="index.php#skills" class="nav-link">Yetenekler</a></li>
                    <li><a href="index.php#contact" class="nav-link">İletişim</a></li>
                </ul>
                <div class="nav-icons">
                    <button class="theme-toggle" id="theme-toggle" aria-label="Toggle Theme">
                        <i class="fas fa-moon"></i>
                    </button>
                    <button class="mobile-toggle" id="mobile-toggle" aria-label="Toggle Menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Web Apps Section -->
    <section class="projects" style="padding-top: 120px; min-height: 100vh;">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">İnteraktif Uygulamalar</span>
                <h2 class="section-title">Web Apps</h2>
                <p style="text-align: center; color: var(--text-secondary); max-width: 600px; margin: 16px auto 0;">
                    Tarayıcınızda çalışan kullanışlı araçlar ve uygulamalar. Hepsini ücretsiz kullanabilirsiniz.
                </p>
            </div>
            
            <?php if (empty($webApps)): ?>
            <!-- Boş Durum -->
            <div style="text-align: center; padding: 80px 20px;">
                <div style="font-size: 80px; color: var(--text-tertiary); margin-bottom: 24px;">
                    <i class="fas fa-code"></i>
                </div>
                <h3 style="color: var(--text-primary); margin-bottom: 12px;">Henüz Web App Eklenmedi</h3>
                <p style="color: var(--text-secondary); margin-bottom: 32px;">
                    Yakında burada kullanışlı web uygulamaları bulabileceksiniz.
                </p>
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Ana Sayfaya Dön
                </a>
            </div>
            <?php else: ?>
            <!-- Projeler Grid -->
            <div class="projects-grid" id="webapps-grid">
                <?php foreach ($webApps as $app): 
                    // Proje URL'ini belirle
                    $appUrl = 'webapp-wrapper.php?project=';
                    if (!empty($app['demo_url'])) {
                        // Harici URL varsa direkt ona git
                        $appUrl = $app['demo_url'];
                        $isExternal = true;
                    } elseif (!empty($app['folder_path'])) {
                        // Lokal web app - wrapper'a yönlendir
                        $folderName = str_replace('apps/', '', $app['folder_path']);
                        $appUrl .= urlencode($folderName);
                        $isExternal = false;
                    }
                ?>
                <div class="project-card" data-category="<?php echo htmlspecialchars($app['category'] ?? 'web'); ?>">
                    <div class="project-image">
                        <?php if (!empty($app['image'])): ?>
                        <img src="<?php echo htmlspecialchars($app['image']); ?>" alt="<?php echo htmlspecialchars($app['title']); ?>">
                        <?php else: ?>
                        <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 48px;">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <?php endif; ?>
                        <div class="project-overlay">
                            <a href="<?php echo htmlspecialchars($appUrl); ?>" <?php echo isset($isExternal) && $isExternal ? 'target="_blank"' : ''; ?> class="project-link" title="<?php echo isset($isExternal) && $isExternal ? 'Harici Siteyi Aç' : 'Uygulamayı Aç'; ?>">
                                <i class="fas fa-<?php echo isset($isExternal) && $isExternal ? 'external-link-alt' : 'rocket'; ?>"></i>
                            </a>
                            <?php if (!empty($app['github_url'])): ?>
                            <a href="<?php echo htmlspecialchars($app['github_url']); ?>" target="_blank" class="project-link" title="GitHub">
                                <i class="fab fa-github"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="project-tags">
                            <span class="tag" style="background: #dbeafe; color: #1e40af;">
                                <i class="fas fa-code"></i> WEB APP
                            </span>
                            <?php if (!empty($app['category'])): ?>
                            <span class="tag" style="background: #e0e7ff; color: #3730a3;">
                                <?php echo strtoupper($app['category']); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <h3 class="project-title"><?php echo htmlspecialchars($app['title']); ?></h3>
                        <p class="project-description">
                            <?php echo nl2br(htmlspecialchars($app['description'])); ?>
                        </p>
                        <?php if (!empty($app['tags'])): ?>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 16px;">
                            <?php 
                            $tags = explode(',', $app['tags']);
                            foreach ($tags as $tag): 
                                if (trim($tag)):
                            ?>
                            <span class="tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                        <?php endif; ?>
                        <div style="margin-top: 20px;">
                            <a href="<?php echo htmlspecialchars($appUrl); ?>" <?php echo isset($isExternal) && $isExternal ? 'target="_blank"' : ''; ?> class="btn btn-primary" style="width: 100%; justify-content: center;">
                                <i class="fas fa-<?php echo isset($isExternal) && $isExternal ? 'external-link-alt' : 'rocket'; ?>"></i> 
                                <?php echo isset($isExternal) && $isExternal ? 'Siteye Git' : 'Uygulamayı Aç'; ?>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-left">
                    <a href="index.php" class="footer-logo"><?php echo $settings['site_title'] ?? 'Kesicioğlu'; ?><span class="dot">.</span></a>
                    <p><?php echo htmlspecialchars($settings['footer_text'] ?? 'Bilgisayar Mühendisi • Web Developer'); ?></p>
                </div>
                <div class="footer-links">
                    <a href="index.php#home">Ana Sayfa</a>
                    <a href="index.php#about">Hakkımda</a>
                    <a href="index.php#projects">Projeler</a>
                    <a href="apps.php">Web Apps</a>
                    <a href="index.php#contact">İletişim</a>
                </div>
                <div class="footer-social">
                    <?php foreach ($socialLinks as $link): ?>
                    <a href="<?php echo htmlspecialchars($link['url']); ?>" aria-label="<?php echo htmlspecialchars($link['platform']); ?>" target="_blank">
                        <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo $settings['site_title'] ?? 'Kesicioğlu'; ?>. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="back-to-top" aria-label="Back to Top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="js/main.js"></script>
</body>
</html>

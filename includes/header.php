<!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <a href="<?php echo $siteUrl; ?>" class="logo"><?php echo $settings['site_title'] ?? 'Kesicioğlu'; ?><span class="dot">.</span></a>
                <ul class="nav-menu" id="nav-menu">
                    <?php foreach ($menuItems as $item): ?>
                    <li><a href="<?php echo $siteUrl . '/' . htmlspecialchars($item['menu_link']); ?>" class="nav-link"><?php echo htmlspecialchars($item['menu_text']); ?></a></li>
                    <?php endforeach; ?>
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

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-left">
                    <a href="<?php echo $siteUrl; ?>" class="footer-logo"><?php echo $settings['site_title'] ?? 'Kesicioğlu'; ?><span class="dot">.</span></a>
                    <p><?php echo htmlspecialchars($settings['footer_text'] ?? 'Bilgisayar Mühendisi • Web Developer'); ?></p>
                </div>
                <div class="footer-links">
                    <?php foreach ($menuItems as $item): ?>
                    <a href="<?php echo htmlspecialchars($item['menu_link']); ?>"><?php echo htmlspecialchars($item['menu_text']); ?></a>
                    <?php endforeach; ?>
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

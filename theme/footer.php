<footer class="site-footer">
  <div class="footer-widgets">
    <?php if ( is_active_sidebar( 'footer' ) ) : ?>
      <?php dynamic_sidebar( 'footer' ); ?>
    <?php endif; ?>
  </div>
  <div class="footer-bottom">
    <div class="footer-menu">
      <?php
      wp_nav_menu( [
        'theme_location' => 'footer',
        'menu_class'     => 'menu menu-footer',
        'container'      => false,
        'fallback_cb'    => false,
      ] );
      ?>
    </div>
    <p class="copyright">&copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?></p>
  </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>

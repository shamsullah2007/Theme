<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'aurora' ); ?></a>
<header class="site-header">
  <div class="topbar">
    <div class="brand">
      <?php if ( has_custom_logo() ) : ?>
        <?php the_custom_logo(); ?>
      <?php else : ?>
        <a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
      <?php endif; ?>
    </div>
    <div class="mega-search">
      <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
        <label class="screen-reader-text" for="aurora-search">Search for:</label>
        <select name="product_cat" class="search-category">
          <option value=""><?php esc_html_e( 'All Categories', 'aurora' ); ?></option>
          <?php
          $cats = get_terms( [ 'taxonomy' => 'product_cat', 'hide_empty' => true ] );
          foreach ( $cats as $cat ) {
            printf( '<option value="%1$s">%2$s</option>', esc_attr( $cat->slug ), esc_html( $cat->name ) );
          }
          ?>
        </select>
        <input type="search" id="aurora-search" class="search-field" placeholder="<?php esc_attr_e( 'Search products…', 'aurora' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
        <input type="hidden" name="post_type" value="product" />
        <button type="submit" class="search-submit"><?php esc_html_e( 'Search', 'aurora' ); ?></button>
      </form>
    </div>
    <div class="header-actions">
      <?php if ( is_user_logged_in() ) : ?>
        <?php if ( current_user_can( 'manage_woocommerce_products' ) || current_user_can( 'manage_options' ) ) : ?>
          <a class="add-product-link" href="<?php echo esc_url( aurora_get_product_manager_url( 'add' ) ); ?>" title="<?php esc_attr_e( 'Add Product', 'aurora' ); ?>">
            <span class="add-icon">+</span>
            <?php esc_html_e( 'Add Product', 'aurora' ); ?>
          </a>
        <?php endif; ?>
        <a class="account-link" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" title="<?php esc_attr_e( 'My Account', 'aurora' ); ?>"><?php esc_html_e( 'Account', 'aurora' ); ?></a>
        <a class="logout-link" href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>" title="<?php esc_attr_e( 'Logout', 'aurora' ); ?>"><?php esc_html_e( 'Logout', 'aurora' ); ?></a>
      <?php else : ?>
        <a class="login-link" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" title="<?php esc_attr_e( 'Login', 'aurora' ); ?>"><?php esc_html_e( 'Login', 'aurora' ); ?></a>
        <a class="register-link" href="<?php echo esc_url( add_query_arg( 'action', 'register', wc_get_page_permalink( 'myaccount' ) ) ); ?>" title="<?php esc_attr_e( 'Register', 'aurora' ); ?>"><?php esc_html_e( 'Register', 'aurora' ); ?></a>
      <?php endif; ?>
      <a class="cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'Shopping Cart', 'aurora' ); ?>">
        <span class="cart-icon">🛒</span>
        <span class="aurora-cart-count"><?php echo WC()->cart ? intval( WC()->cart->get_cart_contents_count() ) : 0; ?></span>
      </a>
    </div>
  </div>
  <nav class="primary-nav" aria-label="Primary">
    <?php
    wp_nav_menu( [
      'theme_location' => 'primary',
      'menu_class'     => 'menu menu-primary',
      'container'      => false,
      'fallback_cb'    => 'aurora_primary_menu_fallback',
    ] );
    ?>
  </nav>
</header>

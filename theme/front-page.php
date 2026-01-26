<?php
get_header();
$deals_title = get_theme_mod( 'aurora_deals_title', __( "Today's Deals", 'aurora' ) );
$featured_cat_ids = array_filter( array_map( 'absint', explode( ',', get_theme_mod( 'aurora_featured_categories', '' ) ) ) );
?>
<main id="primary" class="site-main home-layout">
  <section class="hero">
    <div class="hero-content">
      <h1><?php bloginfo( 'name' ); ?></h1>
      <p><?php bloginfo( 'description' ); ?></p>
      <a class="button" href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"><?php esc_html_e( 'Shop now', 'aurora' ); ?></a>
    </div>
    <div class="hero-media">
      <?php if ( has_header_image() ) : ?>
        <img src="<?php header_image(); ?>" alt="" />
      <?php else : ?>
        <div class="hero-placeholder"></div>
      <?php endif; ?>
    </div>
  </section>

  <section class="featured-rows">
    <header><h2><?php echo esc_html( $deals_title ); ?></h2></header>
    <ul class="product-grid">
      <?php
      $deals = wc_get_products( [ 'limit' => 8, 'orderby' => 'date', 'order' => 'DESC', 'status' => 'publish' ] );
      foreach ( $deals as $product ) {
          set_query_var( 'product', $product );
          get_template_part( 'template-parts/product', 'card' );
      }
      ?>
    </ul>
  </section>

  <?php if ( $featured_cat_ids ) : ?>
    <section class="featured-categories">
      <header><h2><?php esc_html_e( 'Featured Categories', 'aurora' ); ?></h2></header>
      <div class="category-grid">
        <?php
        foreach ( $featured_cat_ids as $cat_id ) {
            $term = get_term( $cat_id, 'product_cat' );
            if ( $term && ! is_wp_error( $term ) ) {
                echo '<a class="category-card" href="' . esc_url( get_term_link( $term ) ) . '">';
                echo '<span class="category-name">' . esc_html( $term->name ) . '</span>';
                echo '<span class="category-count">' . esc_html( $term->count ) . ' ' . esc_html__( 'items', 'aurora' ) . '</span>';
                echo '</a>';
            }
        }
        ?>
      </div>
    </section>
  <?php endif; ?>

  <section class="trending">
    <header><h2><?php esc_html_e( 'Trending Products', 'aurora' ); ?></h2></header>
    <ul class="product-grid slider">
      <?php
      $trending = wc_get_products( [ 'limit' => 8, 'status' => 'publish', 'orderby' => 'popularity' ] );
      foreach ( $trending as $product ) {
          set_query_var( 'product', $product );
          get_template_part( 'template-parts/product', 'card' );
      }
      ?>
    </ul>
  </section>
</main>
<?php get_footer(); ?>

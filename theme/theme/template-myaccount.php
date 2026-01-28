<?php
/**
 * Template Name: My Account
 * Custom My Account page with sidebar
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="myaccount-container">
        <?php
        // Include the custom sidebar
        $sidebar_file = get_template_directory() . '/woocommerce/myaccount/sidebar-navigation.php';
        if ( file_exists( $sidebar_file ) ) {
            include $sidebar_file;
        }
        ?>

        <div class="myaccount-content">
            <?php while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class( 'aurora-page' ); ?>>
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
    </div>
</main>

<style>
.myaccount-container {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 40px;
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
    align-items: start;
}

.myaccount-content {
    background: white;
    border-radius: 12px;
    padding: 0;
}

@media (max-width: 768px) {
    .myaccount-container {
        grid-template-columns: 1fr;
        gap: 25px;
        padding: 20px 15px;
    }

    .myaccount-content {
        order: 2;
    }

    .aurora-myaccount-sidebar {
        order: 1;
    }
}
</style>

<?php get_footer(); ?>

<?php
/**
 * Customizer settings for Aurora Storefront
 */

add_action( 'customize_register', 'aurora_customize_register' );
function aurora_customize_register( $wp_customize ) {
    $wp_customize->add_section( 'aurora_home', [
        'title'    => __( 'Aurora Homepage', 'aurora' ),
        'priority' => 30,
    ] );

    $wp_customize->add_setting( 'aurora_deals_title', [
        'default'           => __( 'Today\'s Deals', 'aurora' ),
        'sanitize_callback' => 'sanitize_text_field',
    ] );

    $wp_customize->add_control( 'aurora_deals_title', [
        'label'   => __( 'Deals Section Title', 'aurora' ),
        'section' => 'aurora_home',
        'type'    => 'text',
    ] );

    $wp_customize->add_setting( 'aurora_featured_categories', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ] );

    $wp_customize->add_control( 'aurora_featured_categories', [
        'label'       => __( 'Featured Category IDs (comma separated)', 'aurora' ),
        'description' => __( 'Enter WooCommerce product category IDs to feature on the homepage.', 'aurora' ),
        'section'     => 'aurora_home',
        'type'        => 'text',
    ] );
}

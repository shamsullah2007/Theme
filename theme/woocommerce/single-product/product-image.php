<?php
/**
 * Single product image gallery override
 */
defined( 'ABSPATH' ) || exit;

global $product;

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$post_thumbnail_id = $product->get_image_id();
$wrapper_classes   = apply_filters(
  'woocommerce_single_product_image_gallery_classes',
  [
    'woocommerce-product-gallery',
    'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
    'images',
  ]
);
?>
<figure class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>">
  <div class="woocommerce-product-gallery__wrapper">
    <?php
    if ( $post_thumbnail_id ) {
      echo wp_kses_post(
        apply_filters(
          'woocommerce_single_product_image_thumbnail_html',
          wc_get_product_image_caption( $post_thumbnail_id ),
          $post_thumbnail_id
        )
      );
    }
    ?>
    <?php
    foreach ( $product->get_gallery_image_ids() as $image_id ) {
      echo wp_kses_post(
        apply_filters(
          'woocommerce_single_product_image_thumbnail_html',
          wc_get_product_image_caption( $image_id ),
          $image_id
        )
      );
    }
    ?>
  </div>

  <?php
  if ( $post_thumbnail_id || $product->get_gallery_image_ids() ) {
    $attachment_ids = array_merge( [ $post_thumbnail_id ], $product->get_gallery_image_ids() );
    ?>
    <div class="flex-control-thumbs" role="region" aria-label="<?php esc_attr_e( 'Product thumbnails', 'woocommerce' ); ?>">
      <?php
      foreach ( $attachment_ids as $attachment_id ) {
        $image = wp_get_attachment_image( $attachment_id, [ 80, 80 ] );
        echo $image ? '<a class="flex-active" data-thumb="' . esc_url( wp_get_attachment_url( $attachment_id ) ) . '">' . wp_kses_post( $image ) . '</a>' : '';
      }
      ?>
    </div>
  <?php } ?>
</figure>

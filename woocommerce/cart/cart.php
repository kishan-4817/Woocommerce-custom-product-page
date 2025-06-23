<?php
/**
 * Custom Cart Page: Only Gift and Recommended Products
 *
 * @package WooCommerce\Templates
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<div class="cart-gift-products">
    <h2><?php esc_html_e( 'Gift Products', 'woocommerce' ); ?></h2>
    <div class="gift-products-list">
        <?php
        $gift_query = new WP_Query([
            'post_type' => 'product',
            'posts_per_page' => 4,
            'tax_query' => [
                [
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => 'gift',
                ],
            ],
        ]);
        if ( $gift_query->have_posts() ) :
            while ( $gift_query->have_posts() ) : $gift_query->the_post();
                global $product;
        ?>
            <div class="gift-product-item">
                <h3 class="product-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <div class="product-short-description">
                    <?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ); ?>
                </div>
            </div>
        <?php
            endwhile;
            wp_reset_postdata();
        else :
            echo '<p>' . esc_html__( 'No gift products found.', 'woocommerce' ) . '</p>';
        endif;
        ?>
    </div>
</div>

<div class="cart-recommended-products">
    <h2><?php esc_html_e( 'Recommended Products', 'woocommerce' ); ?></h2>
    <div class="recommended-products-list">
        <?php
        $cross_sells = WC()->cart->get_cross_sells();
        if ( ! empty( $cross_sells ) ) {
            $args = array(
                'post_type'      => 'product',
                'posts_per_page' => 4,
                'post__in'       => $cross_sells,
                'orderby'        => 'post__in',
            );
            $products = new WP_Query( $args );
            if ( $products->have_posts() ) :
                while ( $products->have_posts() ) : $products->the_post();
                    global $product;
        ?>
            <div class="recommended-product-item">
                <h3 class="product-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <div class="product-short-description">
                    <?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ); ?>
                </div>
            </div>
        <?php
                endwhile;
                wp_reset_postdata();
            endif;
        } else {
            echo '<p>' . esc_html__( 'No recommended products found.', 'woocommerce' ) . '</p>';
        }
        ?>
    </div>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>

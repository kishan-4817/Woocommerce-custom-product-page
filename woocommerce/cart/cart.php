<?php
/**
 * Custom Cart Page Layout - Clean Structure for Custom CSS
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<div class="cart-layout">
	<div class="cart-main">
		<h1 class="cart-title">Cart</h1>
		<form class="cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
			<div class="cart-list">
				<?php do_action( 'woocommerce_before_cart_contents' ); ?>
				<?php
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
					$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					$product_name = $_product->get_name();
					$product_desc = $_product->get_short_description();
					$product_img  = $_product->get_image('woocommerce_thumbnail');
					$product_price = WC()->cart->get_product_price( $_product );
					$is_on_sale = $_product->is_on_sale();
					$regular_price = wc_price($_product->get_regular_price());
					$gift_product_ids = get_post_meta($product_id, '_gift_product_id', true);
					$gift_product_ids = !empty($gift_product_ids) ? explode(',', $gift_product_ids) : [];
					?>
					<div class="cart-item">
						<div class="cart-item-image">
							<?php if ($product_permalink) { echo '<a href="' . esc_url($product_permalink) . '">' . $product_img . '</a>'; } else { echo $product_img; } ?>
						</div>
						<div class="cart-item-info">
							<div class="cart-item-title">
								<?php if ($product_permalink) { echo '<a href="' . esc_url($product_permalink) . '">' . esc_html($product_name) . '</a>'; } else { echo esc_html($product_name); } ?>
							</div>
							<div class="cart-item-desc"><?php echo wp_kses_post($product_desc); ?></div>
							<div class="cart-item-qty">
								<?php
								$min_quantity = $_product->is_sold_individually() ? 1 : 0;
								$max_quantity = $_product->is_sold_individually() ? 1 : $_product->get_max_purchase_quantity();
								$product_quantity = woocommerce_quantity_input([
									'input_name'   => "cart[{$cart_item_key}][qty]",
									'input_value'  => $cart_item['quantity'],
									'max_value'    => $max_quantity,
									'min_value'    => $min_quantity,
									'product_name' => $product_name,
								], $_product, false);
								echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
								?>
							</div>
						</div>
						<div class="cart-item-pricebox">
							<div class="cart-item-price"><?php echo $product_price; ?></div>
							<?php if ($is_on_sale) : ?>
								<div class="cart-item-oldprice"><?php echo $regular_price; ?></div>
							<?php endif; ?>
						</div>
						<div class="cart-item-remove">
							<?php
							echo apply_filters(
								'woocommerce_cart_item_remove_link',
								sprintf(
									'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
									esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
									esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
									esc_attr( $product_id ),
									esc_attr( $_product->get_sku() )
								),
								$cart_item_key
							);
							?>
						</div>
						<?php if (!empty($gift_product_ids)) : ?>
							<div class="cart-gifts">
								<div class="cart-gifts-title">Gifts with this product</div>
								<div class="cart-gifts-list">
									<?php foreach ($gift_product_ids as $gift_id) :
										$gift = wc_get_product($gift_id);
										if ($gift) : ?>
											<div class="cart-gift">
												<div class="cart-gift-image"><?php echo $gift->get_image('woocommerce_thumbnail'); ?></div>
												<div class="cart-gift-title"><?php echo esc_html($gift->get_name()); ?></div>
											</div>
										<?php endif; endforeach; ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				<?php }
				do_action( 'woocommerce_cart_contents' ); ?>
			</div>
			<div class="cart-actions">
				<?php if ( wc_coupons_enabled() ) { ?>
					<div class="cart-coupon">
						<label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label>
						<input type="text" name="coupon_code" class="cart-coupon-input" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" />
						<button type="submit" class="cart-coupon-btn" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_html_e( 'Apply coupon', 'woocommerce' ); ?></button>
						<?php do_action( 'woocommerce_cart_coupon' ); ?>
					</div>
				<?php } ?>
				<button type="submit" class="cart-update-btn" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>
				<?php do_action( 'woocommerce_cart_actions' ); ?>
				<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
			</div>
		</form>
		<hr class="cart-hr" />
		<div class="recommended-section">
			<div class="recommended-title">Recommended Products</div>
			<div class="recommended-list">
				<?php
				$cart_product_ids = [];
				foreach (WC()->cart->get_cart() as $cart_item) {
					$cart_product_ids[] = $cart_item['product_id'];
					$cart_product_ids[] = $cart_item['variation_id'];
				}
				$cart_product_ids = array_filter($cart_product_ids);
				$recommended_ids = [];
				foreach (WC()->cart->get_cart() as $cart_item) {
					$product_id = $cart_item['product_id'];
					$meta = get_post_meta($product_id, '_recommended_products', true);
					if (!empty($meta)) {
						$recommended_ids = array_merge($recommended_ids, explode(',', $meta));
					}
				}
				$recommended_ids = array_map('trim', $recommended_ids);
				$recommended_ids = array_unique($recommended_ids);
				$recommended_ids = array_diff($recommended_ids, $cart_product_ids);
				if (!empty($recommended_ids)) :
					foreach ($recommended_ids as $rec_id) :
						$rec_product = wc_get_product($rec_id);
						if ($rec_product) : ?>
							<div class="recommended-item">
								<div class="recommended-image"><?php echo $rec_product->get_image('woocommerce_thumbnail'); ?></div>
								<div class="recommended-name"><?php echo esc_html($rec_product->get_name()); ?></div>
							</div>
						<?php endif; endforeach;
				endif;
				?>
			</div>
		</div>
	</div>
	<div class="cart-summary">
		<div class="cart-summary-box">
			<?php woocommerce_cart_totals(); ?>
		</div>
		<div class="pincode-estimator">
			<div class="pincode-estimator-title">Estimated Delivery:</div>
			<div class="pincode-estimator-row">
				<input type="text" id="pincode_input" class="pincode-input" placeholder="Enter Pincode">
				<button type="button" id="check_pincode_btn" class="pincode-btn">Check</button>
			</div>
			<div id="delivery_estimate_result" class="pincode-estimator-result"></div>
		</div>
	</div>
</div>
<?php do_action( 'woocommerce_after_cart' ); ?>

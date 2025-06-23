<?php

add_action('after_setup_theme', function() {
    add_theme_support('woocommerce');
});

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('child-style', get_stylesheet_uri(), [], time());

    if (is_product()) {
        wp_enqueue_script('product-page-js', get_stylesheet_directory_uri() . '/js/product-page.js', ['jquery'], time(), true);
    }

    if (is_cart()) {
        wp_enqueue_script('cart-page-js', get_stylesheet_directory_uri() . '/js/cart-page.js', ['jquery'], time(), true);
        wp_localize_script('cart-page-js', 'wc_cart_params', ['ajax_url' => admin_url('admin-ajax.php')]);
    }
});

/**
 * Enqueue scripts and styles for the admin product edit page.
 */
function enqueue_admin_product_scripts($hook) {
    global $post;
    // Only enqueue on product add/edit screens
    if (($hook === 'post-new.php' || $hook === 'post.php') && isset($post->post_type) && $post->post_type === 'product') {
        // Custom admin styles and scripts
        wp_enqueue_style('admin-product-style', get_stylesheet_directory_uri() . '/admin-style.css', [], time());
        wp_enqueue_script('admin-product-js', get_stylesheet_directory_uri() . '/js/admin-product.js', ['jquery'], time(), true);
        // Select2 from CDN
        wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', [], '4.1.0');
        wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['jquery'], '4.1.0', true);
    }
}
add_action('admin_enqueue_scripts', 'enqueue_admin_product_scripts');

// add_action('woocommerce_before_add_to_cart_form', 'display_purchase_options');

function display_purchase_options() {
    global $product;

    if ($product->is_type('variable')) {
        ?>
        <div class="purchase-options-container">
            <h3>Purchase Options</h3>
            <div class="purchase-options">
                <label>
                    <input type="radio" name="purchase_option" value="single_subscription" checked="checked">
                    Single Drink Subscription
                </label>
                <label>
                    <input type="radio" name="purchase_option" value="double_subscription">
                    Double Drink Subscription
                </label>
                <label>
                    <input type="radio" name="purchase_option" value="try_once">
                    Try Once
                </label>
            </div>
        </div>
        <?php
    }
}

// add_filter('woocommerce_add_cart_item_data', 'save_purchase_option_to_cart_item', 10, 3);

function save_purchase_option_to_cart_item($cart_item_data, $product_id, $variation_id) {
    if (isset($_POST['purchase_option'])) {
        $cart_item_data['purchase_option'] = sanitize_text_field($_POST['purchase_option']);
    }

    if (isset($_POST['attribute_pa_flavor_2'])) {
        $cart_item_data['flavor_2'] = sanitize_text_field($_POST['attribute_pa_flavor_2']);
    }

    if (isset($cart_item_data['purchase_option']) &&
        ($cart_item_data['purchase_option'] == 'double_subscription' ||
         (isset($_POST['try_once_type']) && $_POST['try_once_type'] == 'double'))) {
        $cart_item_data['quantity'] = 2;
    }

    return $cart_item_data;
}

// add_action('woocommerce_before_calculate_totals', 'apply_subscription_discount');

function apply_subscription_discount($cart) {
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        if (isset($cart_item['purchase_option'])) {
            if ($cart_item['purchase_option'] == 'single_subscription' || $cart_item['purchase_option'] == 'double_subscription') {
                $product = $cart_item['data'];
                $price = $product->get_price();
                $discounted_price = $price * 0.75;
                $product->set_price($discounted_price);
            }
        }
    }
}

// add_action('woocommerce_after_add_to_cart_form', 'display_whats_included_box');

function display_whats_included_box() {
    global $product;
    $whats_included = get_post_meta($product->get_id(), '_whats_included', true);

    if (!empty($whats_included)) {
        echo '<div class="whats-included-box">';
        echo '<h3>What\'s Included</h3>';
        echo '<div>' . wpautop($whats_included) . '</div>';
        echo '<div class="delivery-frequency">Delivery: <span id="delivery-frequency-text">Every 30 Days</span></div>';
        echo '</div>';
    }
}

// add_action('woocommerce_after_single_product_summary', 'display_faqs_section');

function display_faqs_section() {
    global $product;
    $faqs_json = get_post_meta($product->get_id(), '_product_faqs', true);

    if (!empty($faqs_json)) {
        $faqs = json_decode($faqs_json, true);

        if (is_array($faqs) && !empty($faqs)) {
            echo '<div class="faqs-section">';
            echo '<h3>Frequently Asked Questions</h3>';
            echo '<div class="faq-accordion">';
            foreach ($faqs as $faq) {
                if (isset($faq['q']) && isset($faq['a'])) {
                    echo '<div class="faq-item">';
                    echo '<div class="faq-question">' . esc_html($faq['q']) . '</div>';
                    echo '<div class="faq-answer" style="display: none;">' . wpautop(esc_html($faq['a'])) . '</div>';
                    echo '</div>';
                }
            }
            echo '</div>';
            echo '</div>';
        }
    }
}

// add_action('woocommerce_after_cart_item_name', 'display_gift_product_in_cart', 10, 2);

function display_gift_product_in_cart($cart_item, $cart_item_key) {
    $product_id = $cart_item['product_id'];
    $gift_product_ids = get_post_meta($product_id, '_gift_product_id', true);
    if (!empty($gift_product_ids)) {
        $gift_product_ids = explode(',', $gift_product_ids);
        echo '<div class="gift-product-info">';
        echo '<strong>Your Gift:</strong> ';
        foreach ($gift_product_ids as $gift_id) {
            $gift_product = wc_get_product($gift_id);
            if ($gift_product) {
                echo '<a href="' . esc_url($gift_product->get_permalink()) . '">';
                echo $gift_product->get_image('thumbnail');
                echo esc_html($gift_product->get_name());
                echo '</a> ';
            }
        }
        echo '</div>';
    }
}

// add_action('woocommerce_after_cart', 'display_recommended_products');

function display_recommended_products() {
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

    if (!empty($recommended_ids)) {
        echo '<div class="recommended-products-section">';
        echo '<h2>You might also like...</h2>';
        echo '<ul class="products columns-3">';

        foreach ($recommended_ids as $product_id) {
            $product = wc_get_product($product_id);
            if ($product) {
                wc_get_template_part('content', 'product');
            }
        }

        echo '</ul>';
        echo '</div>';
    }
}

/**
 * Register a custom metabox for product settings.
 */
function add_product_custom_fields_metabox() {
    add_meta_box(
        'product_custom_fields_metabox',
        __('Product Custom Fields', 'text-domain'),
        'render_product_custom_fields_metabox',
        'product',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_product_custom_fields_metabox');

/**
 * Render the content of the custom metabox.
 */
function render_product_custom_fields_metabox($post) {
    // Add a nonce for security
    wp_nonce_field('product_custom_fields_save', 'product_custom_fields_nonce');

    // Retrieve existing values from the database
    $whats_included = get_post_meta($post->ID, '_whats_included', true);
    $gift_product_id = get_post_meta($post->ID, '_gift_product_id', true);
    $recommended_products = get_post_meta($post->ID, '_recommended_products', true);
    if (!is_array($recommended_products)) {
        $recommended_products = !empty($recommended_products) ? explode(',', $recommended_products) : [];
    }
    
    // Get all published products for the recommendation select box
    $all_products = get_posts([
        'post_type' => 'product',
        'numberposts' => -1,
        'post_status' => 'publish',
        'exclude' => [$post->ID],
    ]);
    ?>
    <table class="form-table">
        <tbody>
            <!-- What's Included -->
            <tr>
                <th><label for="_whats_included"><?php _e("What's Included", 'text-domain'); ?></label></th>
                <td>
                    <textarea name="_whats_included" id="_whats_included" rows="5" class="large-text"><?php echo esc_textarea($whats_included); ?></textarea>
                    <p class="description"><?php _e('This content will be displayed in the "What\'s Included" box on the product page.', 'text-domain'); ?></p>
                </td>
            </tr>
            <!-- Gift Product ID -->
            <tr>
                <th><label for="_gift_product_id"><?php _e('Gift Product ID', 'text-domain'); ?></label></th>
                <td>
                    <select name="_gift_product_id[]" id="_gift_product_id" multiple="multiple" class="custom-multiselect" style="width: 100%; max-width: 500px;">
                        <?php 
                        $gift_product_ids = $gift_product_id;
                        if (!is_array($gift_product_ids)) {
                            $gift_product_ids = !empty($gift_product_ids) ? explode(',', $gift_product_ids) : [];
                        }
                        foreach ($all_products as $product) : ?>
                            <option value="<?php echo esc_attr($product->ID); ?>" <?php selected(in_array($product->ID, $gift_product_ids)); ?>>
                                <?php echo esc_html($product->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">Select one or more gift products.</p>
                </td>
            </tr>
            <!-- Recommended Products -->
            <tr>
                <th><label for="_recommended_products"><?php _e('Recommended Products', 'text-domain'); ?></label></th>
                <td>
                    <select name="_recommended_products[]" id="_recommended_products" multiple="multiple" class="custom-multiselect" style="width: 100%; max-width: 500px;">
                        <?php foreach ($all_products as $product) : ?>
                            <option value="<?php echo esc_attr($product->ID); ?>" <?php selected(in_array($product->ID, $recommended_products)); ?>>
                                <?php echo esc_html($product->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">Select products to recommend.</p>
                </td>
            </tr>
             <!-- FAQs Repeater -->
            <tr>
                <th><?php _e('Product FAQs', 'text-domain'); ?></th>
                <td>
                    <div id="faq_repeater">
                        <div class="faq-items">
                            <?php
                            $faqs = get_post_meta($post->ID, '_product_faqs', true);
                            if (is_array($faqs)) {
                                foreach ($faqs as $index => $faq) {
                                    ?>
                                    <div class="faq-item">
                                        <div class="faq-item-content">
                                            <label><?php _e('Question', 'text-domain'); ?></label>
                                            <input type="text" name="_product_faqs[<?php echo $index; ?>][q]" value="<?php echo esc_attr($faq['q']); ?>" class="large-text">
                                            <label><?php _e('Answer', 'text-domain'); ?></label>
                                            <textarea name="_product_faqs[<?php echo $index; ?>][a]" rows="3" class="large-text"><?php echo esc_textarea($faq['a']); ?></textarea>
                                        </div>
                                        <button type="button" class="button remove-faq"><?php _e('Remove', 'text-domain'); ?></button>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <button type="button" class="button" id="add_faq"><?php _e('Add FAQ', 'text-domain'); ?></button>
                    </div>
                    <p class="description"><?php _e('Add frequently asked questions and answers.', 'text-domain'); ?></p>
                </td>
            </tr>
        </tbody>
    </table>
    <?php
}

/**
 * Save the custom fields data from the metabox.
 */
function save_product_custom_fields_metabox($post_id) {
    // Security checks
    if (!isset($_POST['product_custom_fields_nonce']) || !wp_verify_nonce($_POST['product_custom_fields_nonce'], 'product_custom_fields_save')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (isset($_POST['post_type']) && 'product' == $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    } else {
        return;
    }

    // Save What's Included
    if (isset($_POST['_whats_included'])) {
        update_post_meta($post_id, '_whats_included', wp_kses_post($_POST['_whats_included']));
    }

    // Save Gift Product ID
    if (isset($_POST['_gift_product_id']) && is_array($_POST['_gift_product_id'])) {
        $gift_ids = array_map('absint', $_POST['_gift_product_id']);
        update_post_meta($post_id, '_gift_product_id', implode(',', $gift_ids));
    } else {
        delete_post_meta($post_id, '_gift_product_id');
    }

    // Save Recommended Products
    if (isset($_POST['_recommended_products']) && is_array($_POST['_recommended_products'])) {
        $recommended_ids = array_map('absint', $_POST['_recommended_products']);
        update_post_meta($post_id, '_recommended_products', implode(',', $recommended_ids));
    } else {
        delete_post_meta($post_id, '_recommended_products');
    }

    // Save FAQs
    if (isset($_POST['_product_faqs']) && is_array($_POST['_product_faqs'])) {
        $faqs_data = [];
        foreach ($_POST['_product_faqs'] as $faq) {
            if (!empty($faq['q']) && !empty($faq['a'])) {
                $faqs_data[] = [
                    'q' => sanitize_text_field($faq['q']),
                    'a' => sanitize_textarea_field($faq['a']),
                ];
            }
        }
        update_post_meta($post_id, '_product_faqs', $faqs_data);
    } else {
        delete_post_meta($post_id, '_product_faqs');
    }
}
add_action('save_post', 'save_product_custom_fields_metabox');

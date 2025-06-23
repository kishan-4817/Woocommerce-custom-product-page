jQuery(document).ready(function($) {
    const purchaseOptions = $('input[name="purchase_option"]');
    const variationsForm = $('.variations_form');
    const variationsTable = variationsForm.find('table.variations tbody');

    let currentVariation = null;

    variationsForm.on('found_variation', function(event, variation) {
        currentVariation = variation;
        updatePriceDisplay();
    });

    variationsForm.on('hide_variation', function() {
        currentVariation = null;
        updatePriceDisplay();
    });

    // Container for Try Once options
    const tryOnceOptionsContainer = $('<div class="try-once-options" style="display: none;"></div>').insertAfter('.purchase-options');

    function initializeProductPage() {
        // Pre-select chocolate
        const flavorDropdown = variationsTable.find('select[name*="attribute_pa_flavor"]');
        if (flavorDropdown.length) {
            flavorDropdown.val('chocolate').trigger('change');
        }
        updateUI();
    }

    function updateUI() {
        const selectedOption = $('input[name="purchase_option"]:checked').val();

        // Remove second flavor dropdown if it exists
        $('.flavor-2-row').remove();
        tryOnceOptionsContainer.hide();

        if (selectedOption === 'double_subscription') {
            addSecondFlavorDropdown();
        } else if (selectedOption === 'try_once') {
            showTryOnceOptions();
        }
        updatePriceDisplay();
        updateDeliveryFrequency();
    }

    function addSecondFlavorDropdown() {
        const firstFlavorRow = variationsTable.find('tr:has(select[name*="attribute_pa_flavor"])').first();
        if (firstFlavorRow.length) {
            const secondFlavorRow = firstFlavorRow.clone();
            secondFlavorRow.addClass('flavor-2-row');
            secondFlavorRow.find('label').text('Flavor 2');
            const newSelect = secondFlavorRow.find('select');
            const newId = newSelect.attr('id') + '_2';
            const newName = newSelect.attr('name') + '_2';
            newSelect.attr('id', newId).attr('name', newName);
            secondFlavorRow.find('label').attr('for', newId);
            variationsTable.append(secondFlavorRow);
        }
    }

    function showTryOnceOptions() {
        if (tryOnceOptionsContainer.is(':empty')) {
            tryOnceOptionsContainer.html(`
                <label><input type="radio" name="try_once_type" value="single" checked> Single</label>
                <label><input type="radio" name="try_once_type" value="double"> Double</label>
            `);
        }
        tryOnceOptionsContainer.show();
        updateTryOnceUI();
    }

    function updateTryOnceUI() {
        const tryOnceType = $('input[name="try_once_type"]:checked').val();
         $('.flavor-2-row').remove();
        if (tryOnceType === 'double') {
            addSecondFlavorDropdown();
        }
    }

    function updateDeliveryFrequency() {
        const selectedOption = $('input[name="purchase_option"]:checked').val();
        const frequencyText = $('#delivery-frequency-text');

        if (selectedOption === 'single_subscription' || selectedOption === 'double_subscription') {
            frequencyText.text('Every 30 Days');
        } else if (selectedOption === 'try_once') {
            frequencyText.text('One Time');
        }
    }

    function updatePriceDisplay() {
        const priceContainer = $('.woocommerce-variation-price');
        if (!priceContainer.length || !currentVariation) {
            return;
        }

        const selectedOption = $('input[name="purchase_option"]:checked').val();
        let priceHtml = currentVariation.price_html;
        let price = currentVariation.display_price;
        let regular_price = currentVariation.display_regular_price;

        if (selectedOption === 'single_subscription' || selectedOption === 'double_subscription') {
            const sale_price_active = price < regular_price;
            const discounted_price_val = price * 0.75;
            const discounted_regular_price_val = regular_price * 0.75;
            
            let new_price_html = '<del>' + $(currentVariation.price_html).find('del').html() + '</del> ';
            new_price_html += '<ins><span class="woocommerce-Price-amount amount"><bdi>';
            new_price_html += '<span class="woocommerce-Price-currencySymbol">' + woocommerce_params.currency_symbol + '</span>' + discounted_price_val.toFixed(2);
            new_price_html += '</bdi></span></ins>';
            
            if(sale_price_active){
                 priceHtml = '<del>' + priceToHtml(regular_price) + '</del> <ins>' + priceToHtml(discounted_price_val) + ' (Subscription)</ins>';
            } else {
                 priceHtml = '<del>' + priceToHtml(regular_price) + '</del> <ins>' + priceToHtml(discounted_regular_price_val) + ' (Subscription)</ins>';
            }
        }
        
        priceContainer.html(priceHtml);
    }

    function priceToHtml(price) {
        const priceString = price.toFixed(2);
        return '<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">' + woocommerce_params.currency_symbol + '</span>' + priceString + '</bdi></span>';
    }

    purchaseOptions.on('change', updateUI);
    $('body').on('change', 'input[name="try_once_type"]', updateTryOnceUI);

    initializeProductPage();
    updateDeliveryFrequency();

    // FAQ Accordion
    $('.faq-question').on('click', function() {
        $(this).next('.faq-answer').slideToggle();
        $(this).parent('.faq-item').toggleClass('active');
    });
}); 
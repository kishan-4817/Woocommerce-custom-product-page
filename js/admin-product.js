jQuery(document).ready(function($) {
    const faqContainer = $('.faq-items');

    // Function to re-index the repeater fields
    function reindexFAQs() {
        faqContainer.find('.faq-item').each(function(index) {
            $(this).find('input, textarea').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    const newName = name.replace(/\[\d+\]/, '[' + index + ']');
                    $(this).attr('name', newName);
                }
            });
        });
    }

    // Add new FAQ item
    $('#add_faq').on('click', function() {
        const itemCount = faqContainer.find('.faq-item').length;
        const newItem = `
            <div class="faq-item">
                <div class="faq-item-content">
                    <label>Question</label>
                    <input type="text" name="_product_faqs[${itemCount}][q]" class="large-text">
                    <label>Answer</label>
                    <textarea name="_product_faqs[${itemCount}][a]" rows="3" class="large-text"></textarea>
                </div>
                <button type="button" class="button remove-faq">Remove</button>
            </div>`;
        faqContainer.append(newItem);
    });

    // Remove FAQ item
    faqContainer.on('click', '.remove-faq', function() {
        $(this).closest('.faq-item').remove();
        reindexFAQs();
    });

    // Make items sortable
    faqContainer.sortable({
        handle: '.faq-item-content',
        placeholder: 'faq-item-placeholder',
        forcePlaceholderSize: true,
        update: function() {
            reindexFAQs();
        }
    });
}); 
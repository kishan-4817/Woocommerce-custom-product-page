🛠️ WordPress Developer Assignment (WooCommerce)
📌 Objective
Develop a fully functional dynamic Product Page and Cart Page in WordPress using WooCommerce, matching the provided Figma design.
Do not use external plugins. Work within the Twenty Twenty-Five theme, using template overrides, custom CSS, and vanilla JS.

✅ Scope of Work
1. Product Media Gallery
Use default WooCommerce gallery for product image carousel.

Thumbnail click updates the main image.

Images update dynamically based on selected variant (flavor/color).

2. Purchase Options (Radio Group with Dynamic UI)
Purchase modes (as radio buttons):

Single Drink Subscription

Double Drink Subscription

Try Once

Dynamic behavior:

Change flavor selectors and pricing based on selection.

Show benefits, discounts, and included items per mode.

Pricing Logic:

Subscription = 25% off base price.

Global sales discount = 20% off.

Final example:

Base: $100

Subscription: $75

After 20% off:

Try Once: $80

Subscription: $60

3. Flavor Selection
Use WooCommerce product attributes:

Flavors: Chocolate, Vanilla, Orange

Selection options:

Single Drink: 1 dropdown

Double Drink: 2 dropdowns (Flavor 1 & 2)

Try Once: Select between Single or Double setup

Selections must be validated before proceeding.

4. Dynamic Pricing & Discounts
Fetch dynamic prices using WooCommerce:

Regular and sale prices

Show original and final price

Update in real-time based on user selection.

5. What's Included Box
Display items and delivery frequency:

"Every 30 Days" (subscription)

"One Time" (try once)

Populate from:

Product meta fields or short description

6. Add to Cart Logic
Pre-select:

“Single Drink Subscription” + “Chocolate” flavor on page load

Add variation to cart:

Use standard WooCommerce form or AJAX

Ensure correct variation is passed

7. FAQs Section
Implement accessible accordion UI

Pull FAQ content from:

WooCommerce product meta/custom field

Responsive and styled per design

🛒 Custom Cart Page Features
1. Cart Item Gifts
Gifts linked via product meta

Display one or more gift products below corresponding cart item

2. Recommended Products Section
Linked via product meta

Exclude products already in the cart

Display in grid layout (as per Figma)

3. Dynamic Totals & Pincode Delivery Estimator
Totals:

Subtotal, Discounts, and Total using WooCommerce hooks

Delivery Estimator:

Pincode input with "Check" button

Logic:

Starts with 456 → 2 Days

Ends with 123 → 4 Days

Others → 7 Days

🔒 Additional Notes / Restrictions
No external plugins allowed.

All data must be editable via WooCommerce product admin.

All logic must use:

Core WooCommerce

Template overrides

Twenty Twenty-Five theme

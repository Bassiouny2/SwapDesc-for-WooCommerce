# Trae Rules: Product Variation Descriptions (Trae)

These rules apply only to the plugin located at:
`c:\Users\DELL\Local Sites\consucorner\app\public\wp-content\plugins\product-var-trea`

## Scope

- Make changes only within this plugin.
- Do not modify other plugins or theme files when working under these rules.

## Structure

- Entry: [product-var-trea.php](file:///c:/Users/DELL/Local%20Sites/consucorner/app/public/wp-content/plugins/product-var-trea/product-var-trea.php)
- Admin: [class-admin.php](file:///c:/Users/DELL/Local%20Sites/consucorner/app/public/wp-content/plugins/product-var-trea/includes/class-admin.php)
- Frontend: [class-frontend.php](file:///c:/Users/DELL/Local%20Sites/consucorner/app/public/wp-content/plugins/product-var-trea/includes/class-frontend.php)
- AJAX: [class-ajax.php](file:///c:/Users/DELL/Local%20Sites/consucorner/app/public/wp-content/plugins/product-var-trea/includes/class-ajax.php)
- Compatibility: [class-compatibility.php](file:///c:/Users/DELL/Local%20Sites/consucorner/app/public/wp-content/plugins/product-var-trea/includes/class-compatibility.php)
- Assets:
  - [frontend.js](file:///c:/Users/DELL/Local%20Sites/consucorner/app/public/wp-content/plugins/product-var-trea/assets/frontend.js)
  - [frontend.css](file:///c:/Users/DELL/Local%20Sites/consucorner/app/public/wp-content/plugins/product-var-trea/assets/frontend.css)
  - [admin.css](file:///c:/Users/DELL/Local%20Sites/consucorner/app/public/wp-content/plugins/product-var-trea/assets/admin.css)

## Conventions

- Namespace: `PVT`; classes expose a static `init()` for hook registration.
- Meta key for variation content: `_pvt_variation_description` (do not rename without migration).
- Dynamic container:
  - ID: `pvt-description-root`
  - Class: `pvt-description-root`
- Asset handles:
  - Frontend style: `pvt-frontend`
  - Frontend script: `pvt-frontend`
  - Admin style: `pvt-admin`
- Only enqueue assets on single product pages (frontend) and product edit screens (admin).
- Follow WordPress security:
  - Sanitize incoming data with `wp_kses_post` or stricter as needed.
  - Escape outputs; for rich HTML descriptions use `apply_filters('the_content', ...)`.
  - Validate and cast IDs via `absint`.

## Frontend Behavior

- Inserts `<div id="pvt-description-root" ...>` into product summary and Woo description tab.
- JavaScript listens to WooCommerce variation events:
  - `found_variation` to swap content.
  - `reset_data` to restore original content.
- Uses localized `PVT_DATA.ajax_url` for fallback AJAX fetch with action `pvt_get_variation_description`.
- Keeps compatibility with Elementor and theme templates by creating the container inside the best available description area.
- Maintains a small cache per variation and persists last variation in `sessionStorage` (`pvt_last_variation_id`).

## Admin Behavior

- Adds a rich text editor per variation on variable products.
- Saves content to `_pvt_variation_description` on `woocommerce_save_product_variation`.
- Adds `pvt_description` to the variation payload for faster swapping on the frontend.

## Compatibility

- Elementor: Do not override content rendering; keep the container and let JS swap.
- Use defensive `try/catch` in Elementor hooks to avoid breaking rendering.

## Versioning

- Update the header `Version` in `product-var-trea.php` when releasing changes.
- Keep text domain: `product-var-trea`.

## Verification

- Admin:
  - Edit a variable product, enter content in “Variation Long Description”.
  - Save; confirm meta `_pvt_variation_description` exists per variation.
- Frontend:
  - Visit the product page; select variations and verify description swaps smoothly.
  - Reset variations; ensure original description restores.
- Elementor:
  - With Product Content or Tabs widgets, confirm swapping still works.

## Commands

- No standard build/test/lint commands defined for this plugin.
- If you have preferred commands (e.g., `php -l`, `phpcs`, or JS tooling), add them here for future runs.

# SwapDesc for WooCommerce

SwapDesc for WooCommerce lets you add a rich, per-variation long description and automatically swaps the product description when a customer selects a variation.

## Features

- Add a rich text description for each variation
- Swap the displayed description on variation selection
- Restore the original description when variations are reset
- Render dynamic description output only for variable products
- Compatible with standard WooCommerce templates and Elementor product widgets

## Requirements

- WordPress 6.0+
- WooCommerce 8.0+
- PHP 7.4+

## Installation

1. Download or clone this repository into `wp-content/plugins/product-var-trea`.
2. In WordPress admin, go to **Plugins** and activate **SwapDesc for WooCommerce**.

## Usage

1. Edit a variable product in WooCommerce.
2. Open the **Variations** tab.
3. For each variation, fill in **Variation Long Description**.
4. Save and update the product.

On the frontend, selecting a variation replaces the product description with the variation description and restores the original description when the selection is reset.

### Shortcode

Use this shortcode on single product templates:

`[pvt_description]`

Notes:

- The shortcode renders only on WooCommerce single variable product pages.
- It outputs the main description container that gets swapped when a variation is selected.
- For simple/external/grouped products, shortcode output is empty on the frontend.
- Global styling for this shortcode can be configured from **WooCommerce → Variation Descriptions**.

### Elementor

If you use Elementor for product pages:

1. Edit your Single Product template in Elementor.
2. Search for the widget **SwapDesc Description**.
3. Drag it into the area where you want the changing description to appear.
4. Publish/update the template.

Notes:

- Widget name: `SwapDesc Description`
- Widget internal name: `swapdesc_description`
- The widget outputs only for variable products on frontend product pages.
- You can style typography, colors, spacing, and border directly from the widget Style controls.

## Development Notes

- Plugin entry file: `product-var-trea.php`
- Namespace: `PVT`
- Variation meta key: `_pvt_variation_description`
- Frontend container: `#pvt-description-root`

## License

GPL-2.0-or-later. See [LICENSE](LICENSE).

# Genaker Product Color Swatches

OroCommerce extension for **Color Swatches and Variants**, analogous to [Genaker/ProductVariants](https://github.com/Genaker/ProductVariants) for Magento.

## Features

- **Color Variants** – `colorVariants` stores product IDs (comma/pipe/semicolon-separated) linking variant products
- **Size Variants** – `sizeVariants` stores product IDs of size variants
- **Linking by attribute** – Each variant product can have a `color` attribute; parent links them via IDs
- **Swatch display** – Shows variant product images as color swatches, or text when no image

## Installation

### Via Composer

```bash
composer require genaker/orocommerce-product-color-swatches
```

### Manual

1. Clone this repository into your project or add as a path repository in `composer.json`.

2. Add the bundle to `config/oro/bundles.yml`:

```yaml
bundles:
    - { name: Genaker\Bundle\ProductVariantsBundle\GenakerProductVariantsBundle, priority: 1000 }
```

3. Run platform update:

```bash
php bin/console oro:platform:update --force
```

## Usage

1. Add attributes **Color Variants** and **Size Variants** to your product attribute family.
2. Add a **color** attribute to variant products (optional, for hex/named colors).
3. On a product, enter product IDs in the fields, e.g. `123, 456, 789`.
4. The product page shows swatches: variant images or text, linking to each variant.

## Attribute Format

- **color_variants / size_variants**: product IDs, comma/pipe/semicolon-separated
- **Example**: `123, 456, 789` or `100|101|102`

## Swatch Display

- **Image**: If the variant product has an image, it is shown as the swatch.
- **Color**: If the variant has a `color` attribute (hex, e.g. `#ff0000`), used as background.
- **Text**: Otherwise the product name is shown.

## Testing

```bash
php bin/phpunit src/Genaker/Bundle/ProductVariantsBundle/Tests/Unit -c phpunit.xml.dist
```

## License

MIT

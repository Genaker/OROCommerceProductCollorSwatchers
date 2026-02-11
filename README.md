# OroCommerce Product Color Swatches & Variants Extension

![OroCommerce Product Color Swatches](assets/genaker-product-color-swatches.png)

> **Add native Magento-style color swatches and product variants to your OroCommerce storefront.** Link products by ID, display variant images or text swatches, and improve product discovery with visual variant selection.

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![OroCommerce](https://img.shields.io/badge/OroCommerce-6.1+-green.svg)](https://oroinc.com/)
[![PHP 8.1+](https://img.shields.io/badge/PHP-8.1+-purple.svg)](https://php.net/)

---

## What Is This?

**OroCommerce Product Color Swatches** is a free, open-source extension that brings **Magento-style color swatches and product variants** to OroCommerce. Instead of complex configurable products, you simply specify product IDs in custom attributes—and the extension displays them as clickable swatches (images or text) on the product page.

Perfect for apparel, shoes, accessories, and any catalog where products come in multiple colors or sizes.

---

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Attribute Format](#attribute-format)
- [Swatch Display](#swatch-display)
- [Testing](#testing)
- [License](#license)

---

## Features

| Feature | Description |
|--------|-------------|
| **Color Variants** | Store product IDs in `colorVariants` attribute. Display as swatches on the product page. |
| **Size Variants** | Store product IDs in `sizeVariants` attribute. Display as text or image swatches. |
| **Image Swatches** | Variant product images are shown as thumbnails when available. |
| **Color Swatches** | Use a `color` attribute (hex, e.g. `#ff0000`) for pure color swatches. |
| **Text Fallback** | No image? Display product name as a text swatch. |
| **SEO-Friendly** | Links to variant products improve internal linking and crawlability. |

---

## Requirements

- **PHP** 8.1 or higher
- **OroCommerce** 6.1 or higher
- **Oro Platform** 6.1 or higher

---

## Installation

### Via Composer (Recommended)

```bash
composer require genaker/orocommerce-product-color-swatches
```

### Manual Installation

1. Clone or download this repository:

```bash
git clone https://github.com/Genaker/OROCommerceProductCollorSwatchers.git
```

2. Add as a path repository in your project's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./OROCommerceProductCollorSwatchers"
        }
    ]
}
```

3. Require the package:

```bash
composer require genaker/orocommerce-product-color-swatches
```

4. Register the bundle in `config/oro/bundles.yml`:

```yaml
bundles:
    - { name: Genaker\Bundle\ProductVariantsBundle\GenakerProductVariantsBundle, priority: 1000 }
```

5. Run platform update:

```bash
php bin/console oro:platform:update --force
```

---

## Configuration

1. Go to **System** → **Entity Management** → **Product** → **Attribute Families**.
2. Edit your product attribute family.
3. Add the new attributes to the appropriate group:
   - **Color Variants (Product IDs)**
   - **Size Variants (Product IDs)**
4. Optionally add a **color** attribute (string or enum) to variant products for hex/named colors.

---

## Usage

1. Create your variant products (e.g. Red Small, Red Medium, Blue Small, Blue Medium).
2. On the parent product, fill in:
   - **Color Variants**: `101, 102, 103` (product IDs of color variants)
   - **Size Variants**: `201, 202, 203` (product IDs of size variants)
3. Save. The product page will automatically show swatches linking to each variant.

---

## Attribute Format

| Attribute | Format | Example |
|-----------|--------|---------|
| `colorVariants` | Product IDs, comma/pipe/semicolon-separated | `123, 456, 789` or `100\|101\|102` |
| `sizeVariants` | Product IDs, comma/pipe/semicolon-separated | `200, 201, 202` |

---

## Swatch Display

| Condition | Display |
|-----------|---------|
| Variant has image | Product image as swatch thumbnail |
| Variant has `color` attribute | Hex/named color as background |
| Neither | Product name as text swatch |

---

## Testing

Run unit tests:

```bash
php bin/phpunit src/Genaker/Bundle/ProductVariantsBundle/Tests/Unit -c phpunit.xml.dist
```

---

## Related Projects

- [Genaker/ProductVariants](https://github.com/Genaker/ProductVariants) – Magento 2 equivalent
- [Genaker/OROCommerceEmailSMTP](https://github.com/Genaker/OROCommerceEmailSMTP) – OroCommerce SMTP logging

---

## License

MIT © [Genaker](https://github.com/Genaker)

---

## Keywords

OroCommerce, color swatches, product variants, e-commerce, B2B, product attributes, Magento, swatch extension, product images, variant display

<?php

namespace Genaker\Bundle\ProductVariantsBundle\Layout\DataProvider;

use Doctrine\Persistence\ManagerRegistry;
use Oro\Bundle\AttachmentBundle\Manager\AttachmentManager;
use Oro\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\ProductBundle\Entity\ProductImage;
use Oro\Bundle\ProductBundle\Entity\ProductImageType;
use Oro\Bundle\ProductBundle\Entity\Repository\ProductRepository;
use Oro\Bundle\ProductBundle\Helper\ProductImageHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Provides color/size swatch data for product variant attributes.
 *
 * Links products by attribute: color_variants/size_variants store product IDs.
 * Variant products are shown as swatches (product images) or text.
 */
class ColorSwatchLayoutDataProvider
{
    private const ID_DELIMITERS = [',', '|', ';', ' '];

    public function __construct(
        private ManagerRegistry $doctrine,
        private AttachmentManager $attachmentManager,
        private ProductImageHelper $productImageHelper,
        private UrlGeneratorInterface $router,
    ) {
    }

    /**
     * Get color swatches for product (from colorVariants attribute = product IDs).
     * Each variant has 'color' attribute; display: product image or text.
     *
     * @return array<int, array{id: int, sku: string, name: string, url: string, imageUrl: string|null, color: string|null}>
     */
    public function getColorSwatches(Product $product): array
    {
        return $this->getSwatches($product, 'colorVariants');
    }

    /**
     * Get size swatches for product (from sizeVariants attribute = product IDs).
     * Display: product image or text.
     *
     * @return array<int, array{id: int, sku: string, name: string, url: string, imageUrl: string|null, color: string|null}>
     */
    public function getSizeSwatches(Product $product): array
    {
        return $this->getSwatches($product, 'sizeVariants');
    }

    /**
     * Check if product has any color or size variants.
     */
    public function hasAnyVariants(Product $product): bool
    {
        return $this->getColorSwatches($product) !== [] || $this->getSizeSwatches($product) !== [];
    }

    /**
     * @return array<int, array{id: int, sku: string, name: string, url: string, imageUrl: string|null, color: string|null}>
     */
    private function getSwatches(Product $product, string $attributeName): array
    {
        $value = $this->getAttributeValue($product, $attributeName);
        if ($value === null || $value === '') {
            return [];
        }

        $ids = $this->parseIds($value);
        if ($ids === []) {
            return [];
        }

        $variantProducts = $this->loadProductsByIds($ids, $product->getOrganization()?->getId());
        if ($variantProducts === []) {
            return [];
        }

        $byId = [];
        foreach ($variantProducts as $variant) {
            $byId[$variant->getId()] = $variant;
        }

        $result = [];
        foreach ($ids as $id) {
            if (isset($byId[$id])) {
                $variant = $byId[$id];
                $result[] = [
                    'id' => $variant->getId(),
                    'sku' => $variant->getSku(),
                    'name' => (string) $variant->getDefaultName(),
                    'url' => $this->router->generate('oro_product_frontend_product_view', ['id' => $variant->getId()], UrlGeneratorInterface::ABSOLUTE_PATH),
                    'imageUrl' => $this->getProductImageUrl($variant),
                    'color' => $this->getProductColorValue($variant),
                ];
            }
        }

        return $result;
    }

    private function getAttributeValue(Product $product, string $name): mixed
    {
        try {
            return $product->get($name);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return int[]
     */
    private function parseIds(string $value): array
    {
        $str = (string) $value;
        $parts = [$str];
        foreach (self::ID_DELIMITERS as $delim) {
            if (str_contains($str, $delim)) {
                $parts = array_map('trim', explode($delim, $str));
                break;
            }
        }

        $ids = [];
        foreach ($parts as $part) {
            if ($part !== '' && ctype_digit($part)) {
                $ids[] = (int) $part;
            }
        }

        return array_unique($ids);
    }

    /**
     * @param int[] $ids
     * @return Product[]
     */
    private function loadProductsByIds(array $ids, ?int $organizationId): array
    {
        if ($ids === []) {
            return [];
        }

        $qb = $this->getProductRepository()->getProductsQueryBuilder($ids)
            ->andWhere('p.status = :status')
            ->setParameter('status', Product::STATUS_ENABLED);

        if ($organizationId !== null) {
            $qb->andWhere('p.organization = :org')
                ->setParameter('org', $organizationId);
        }

        return $qb->getQuery()->getResult();
    }

    private function getProductImageUrl(Product $product): ?string
    {
        $images = $product->getImages();
        if ($images->isEmpty()) {
            return null;
        }

        $sorted = $this->productImageHelper->sortImages($images->toArray());
        $first = $sorted[0] ?? null;
        if (!$first instanceof ProductImage || !$first->getImage()) {
            return null;
        }

        if ($first->hasType(ProductImageType::TYPE_LISTING)) {
            return $this->attachmentManager->getFilteredImageUrl($first->getImage(), 'product_small');
        }

        return $this->attachmentManager->getFilteredImageUrl($first->getImage(), 'product_small');
    }

    private function getProductColorValue(Product $product): ?string
    {
        foreach (['color', 'spec_design'] as $attr) {
            try {
                $color = $product->get($attr);
                if ($color !== null) {
                    $str = method_exists($color, '__toString') ? (string) $color : (string) $color;
                    return $str !== '' ? $str : null;
                }
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }

    private function getProductRepository(): ProductRepository
    {
        $repo = $this->doctrine->getRepository(Product::class);
        assert($repo instanceof ProductRepository);

        return $repo;
    }
}

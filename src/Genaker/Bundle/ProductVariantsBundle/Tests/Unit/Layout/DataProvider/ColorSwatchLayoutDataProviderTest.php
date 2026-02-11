<?php

namespace Genaker\Bundle\ProductVariantsBundle\Tests\Unit\Layout\DataProvider;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Genaker\Bundle\ProductVariantsBundle\Layout\DataProvider\ColorSwatchLayoutDataProvider;
use Oro\Bundle\AttachmentBundle\Manager\AttachmentManager;
use Oro\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\ProductBundle\Entity\Repository\ProductRepository;
use Oro\Bundle\ProductBundle\Helper\ProductImageHelper;
use PHPUnit\Framework\TestCase;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ColorSwatchLayoutDataProviderTest extends TestCase
{
    private ManagerRegistry $doctrine;
    private AttachmentManager $attachmentManager;
    private ProductImageHelper $productImageHelper;
    private UrlGeneratorInterface $router;

    protected function setUp(): void
    {
        $this->doctrine = $this->createMock(ManagerRegistry::class);
        $this->attachmentManager = $this->createMock(AttachmentManager::class);
        $this->productImageHelper = $this->createMock(ProductImageHelper::class);
        $this->router = $this->createMock(UrlGeneratorInterface::class);
    }

    private function createProvider(): ColorSwatchLayoutDataProvider
    {
        return new ColorSwatchLayoutDataProvider(
            $this->doctrine,
            $this->attachmentManager,
            $this->productImageHelper,
            $this->router
        );
    }

    private function mockProductWithVariants(string $attributeName, string $value, ?int $orgId = 1): Product
    {
        $org = null;
        if ($orgId !== null) {
            $org = new class($orgId) {
                public function __construct(private int $id) {}
                public function getId(): int { return $this->id; }
            };
        }
        $product = $this->createMock(Product::class);
        $product->method('get')->with($attributeName)->willReturn($value);
        $product->method('getOrganization')->willReturn($org);
        return $product;
    }

    private function mockRepositoryWithProducts(array $products): void
    {
        $query = $this->createMock(AbstractQuery::class);
        $query->method('getResult')->willReturn($products);

        $qb = $this->createMock(QueryBuilder::class);
        $qb->method('andWhere')->willReturnSelf();
        $qb->method('setParameter')->willReturnSelf();
        $qb->method('getQuery')->willReturn($query);

        $repo = $this->createMock(ProductRepository::class);
        $repo->method('getProductsQueryBuilder')->willReturn($qb);

        $this->doctrine->method('getRepository')->with(Product::class)->willReturn($repo);
    }

    private function createVariantProduct(int $id, string $sku, string $name): Product
    {
        $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getId', 'getSku', 'getImages'])
            ->addMethods(['getDefaultName'])
            ->getMock();
        $product->method('getId')->willReturn($id);
        $product->method('getSku')->willReturn($sku);
        $product->method('getDefaultName')->willReturn($this->createLocalizedValue($name));
        $product->method('getImages')->willReturn(new \Doctrine\Common\Collections\ArrayCollection());
        return $product;
    }

    private function createLocalizedValue(string $value): object
    {
        return new class($value) {
            public function __construct(private string $value) {}
            public function __toString(): string { return $this->value; }
        };
    }

    public function testGetColorSwatchesReturnsEmptyWhenNoAttribute(): void
    {
        $product = $this->createMock(Product::class);
        $product->method('get')->with('colorVariants')->willThrowException(new \LogicException('No property'));

        $provider = $this->createProvider();

        self::assertSame([], $provider->getColorSwatches($product));
    }

    public function testGetColorSwatchesReturnsEmptyWhenAttributeEmpty(): void
    {
        $product = $this->mockProductWithVariants('colorVariants', '');
        $provider = $this->createProvider();

        self::assertSame([], $provider->getColorSwatches($product));
    }

    public function testGetColorSwatchesReturnsEmptyWhenInvalidIds(): void
    {
        $product = $this->mockProductWithVariants('colorVariants', 'abc,xyz');
        $this->mockRepositoryWithProducts([]);
        $provider = $this->createProvider();

        self::assertSame([], $provider->getColorSwatches($product));
    }

    public function testGetColorSwatchesReturnsSwatches(): void
    {
        $variant1 = $this->createVariantProduct(101, 'RED-S', 'Red Small');
        $variant2 = $this->createVariantProduct(102, 'RED-M', 'Red Medium');

        $product = $this->mockProductWithVariants('colorVariants', '101, 102');
        $this->mockRepositoryWithProducts([$variant1, $variant2]);

        $this->router->method('generate')
            ->with('oro_product_frontend_product_view', self::anything(), UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturnCallback(fn ($_, $params) => '/product/' . $params['id']);

        $provider = $this->createProvider();
        $swatches = $provider->getColorSwatches($product);

        self::assertCount(2, $swatches);
        self::assertSame(101, $swatches[0]['id']);
        self::assertSame('RED-S', $swatches[0]['sku']);
        self::assertSame('Red Small', $swatches[0]['name']);
        self::assertSame('/product/101', $swatches[0]['url']);
        self::assertNull($swatches[0]['imageUrl']);
        self::assertSame(102, $swatches[1]['id']);
    }

    public function testGetSizeSwatchesReturnsSwatches(): void
    {
        $variant = $this->createVariantProduct(201, 'SIZE-M', 'Medium');

        $product = $this->mockProductWithVariants('sizeVariants', '201');
        $this->mockRepositoryWithProducts([$variant]);

        $this->router->method('generate')
            ->with('oro_product_frontend_product_view', self::anything(), UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturnCallback(fn ($_, $params) => '/product/' . $params['id']);

        $provider = $this->createProvider();
        $swatches = $provider->getSizeSwatches($product);

        self::assertCount(1, $swatches);
        self::assertSame(201, $swatches[0]['id']);
        self::assertSame('SIZE-M', $swatches[0]['sku']);
    }

    public function testHasAnyVariantsReturnsFalseWhenNone(): void
    {
        $product = $this->createMock(Product::class);
        $product->method('get')->willThrowException(new \LogicException('No property'));

        $provider = $this->createProvider();

        self::assertFalse($provider->hasAnyVariants($product));
    }

    public function testHasAnyVariantsReturnsTrueWhenColorVariants(): void
    {
        $variant = $this->createVariantProduct(101, 'RED', 'Red');
        $product = $this->mockProductWithVariants('colorVariants', '101');
        $this->mockRepositoryWithProducts([$variant]);

        $this->router->method('generate')->willReturn('/product/101');

        $provider = $this->createProvider();

        self::assertTrue($provider->hasAnyVariants($product));
    }

    public function testParseIdsWithPipeDelimiter(): void
    {
        $variant = $this->createVariantProduct(1, 'A', 'A');
        $product = $this->mockProductWithVariants('colorVariants', '1|2|3');
        $this->mockRepositoryWithProducts([$variant]);

        $this->router->method('generate')->willReturnCallback(fn ($_, $p) => '/p/' . $p['id']);

        $provider = $this->createProvider();
        $swatches = $provider->getColorSwatches($product);

        self::assertCount(1, $swatches);
        self::assertSame(1, $swatches[0]['id']);
    }
}

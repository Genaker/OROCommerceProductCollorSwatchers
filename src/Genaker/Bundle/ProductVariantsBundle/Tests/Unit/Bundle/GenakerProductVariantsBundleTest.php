<?php

namespace Genaker\Bundle\ProductVariantsBundle\Tests\Unit\Bundle;

use Genaker\Bundle\ProductVariantsBundle\GenakerProductVariantsBundle;
use PHPUnit\Framework\TestCase;

class GenakerProductVariantsBundleTest extends TestCase
{
    public function testBundleCanBeInstantiated(): void
    {
        $bundle = new GenakerProductVariantsBundle();

        self::assertInstanceOf(GenakerProductVariantsBundle::class, $bundle);
    }

    public function testBundleHasCorrectName(): void
    {
        $bundle = new GenakerProductVariantsBundle();

        self::assertSame('GenakerProductVariantsBundle', $bundle->getName());
    }
}

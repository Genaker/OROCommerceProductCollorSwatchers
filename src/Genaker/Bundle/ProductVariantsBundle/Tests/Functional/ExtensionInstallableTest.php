<?php

namespace Genaker\Bundle\ProductVariantsBundle\Tests\Functional;

use Genaker\Bundle\ProductVariantsBundle\GenakerProductVariantsBundle;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

/**
 * Verifies the Genaker ProductVariantsBundle can be installed and loaded.
 *
 * @dbIsolationPerTest
 * @group install
 */
class ExtensionInstallableTest extends WebTestCase
{
    public function testExtensionCanBeInstalledWithoutIssues(): void
    {
        $this->initClient([], self::generateBasicAuthHeader());
        $container = self::getContainer();

        $bundles = $container->getParameter('kernel.bundles');
        self::assertArrayHasKey('GenakerProductVariantsBundle', $bundles);
        self::assertSame(GenakerProductVariantsBundle::class, $bundles['GenakerProductVariantsBundle']);

        self::assertTrue($container->has('genaker_product_variants.layout_data_provider.color_swatch'));
    }
}

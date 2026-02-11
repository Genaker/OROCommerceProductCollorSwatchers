<?php

namespace Genaker\Bundle\ProductVariantsBundle\Tests\Unit\DependencyInjection;

use Genaker\Bundle\ProductVariantsBundle\DependencyInjection\GenakerProductVariantsExtension;
use Oro\Bundle\TestFrameworkBundle\Test\DependencyInjection\ExtensionTestCase;

class GenakerProductVariantsExtensionTest extends ExtensionTestCase
{
    public function testLoad(): void
    {
        $this->loadExtension(new GenakerProductVariantsExtension());

        $this->assertDefinitionsLoaded([
            'genaker_product_variants.layout_data_provider.color_swatch',
        ]);
    }
}

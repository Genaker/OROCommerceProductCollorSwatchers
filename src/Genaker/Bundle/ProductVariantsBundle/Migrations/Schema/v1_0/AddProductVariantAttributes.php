<?php

namespace Genaker\Bundle\ProductVariantsBundle\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\EntityExtendBundle\Migration\OroOptions;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class AddProductVariantAttributes implements Migration
{
    private function getAttributeOptions(array $options): array
    {
        return array_merge_recursive(
            [
                'extend' => [
                    'is_extend' => true,
                    'owner' => ExtendScope::OWNER_CUSTOM,
                ],
                'attribute' => [
                    'is_attribute' => true,
                    'filterable' => false,
                    'enabled' => true,
                ],
                'importexport' => [
                    'excluded' => false,
                ],
            ],
            $options
        );
    }

    public function up(Schema $schema, QueryBag $queries): void
    {
        $table = $schema->getTable('oro_product');

        if (!$table->hasColumn('colorVariants')) {
            $table->addColumn(
                'colorVariants',
                'string',
                [
                    'length' => 4000,
                    'notnull' => false,
                    OroOptions::KEY => $this->getAttributeOptions([
                        'entity' => ['label' => 'genaker.product_variants.color_variants.label'],
                    ]),
                ]
            );
        }

        if (!$table->hasColumn('sizeVariants')) {
            $table->addColumn(
                'sizeVariants',
                'string',
                [
                    'length' => 4000,
                    'notnull' => false,
                    OroOptions::KEY => $this->getAttributeOptions([
                        'entity' => ['label' => 'genaker.product_variants.size_variants.label'],
                    ]),
                ]
            );
        }
    }
}

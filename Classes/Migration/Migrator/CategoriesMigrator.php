<?php

declare(strict_types=1);

namespace Jpmschuler\Ttnews2News\Migration\Migrator;

use In2code\Migration\Migration\Migrator\MigratorInterface;
use Jpmschuler\Ttnews2News\Migration\PropertyHelpers\GetParentCategoryPropertyHelper;

/**
 * Class CategoriesMigrator
 */
class CategoriesMigrator extends AbstractMigrator implements MigratorInterface
{
    protected string $tableName = 'sys_category';

    protected bool $enforce = true;

    /**
     * Filter selection of old records like "and pid > 0" (to prevent elements in a workflow e.g.)
     */
    protected string $additionalWhere = 'and _migrated=1 and _migrated_table="tt_news_cat" and _migrated_twice=0';

    protected array $values = [
        '_migrated_twice' => 1
// Don't migrate a second time (for other branches that should also be migrated)
    ];

    protected array $propertyHelpers = [
        'parent' => [
            [
                'className' => GetParentCategoryPropertyHelper::class
            ]
        ]
    ];
}

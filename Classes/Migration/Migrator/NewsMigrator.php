<?php

declare(strict_types=1);

namespace Jpmschuler\Ttnews2News\Migration\Migrator;

use In2code\Migration\Migration\Migrator\MigratorInterface;
use Jpmschuler\Ttnews2News\Migration\PropertyHelpers\CopySysFileReferencePropertyHelper;
use Jpmschuler\Ttnews2News\Migration\PropertyHelpers\CreateNewsCategoryRelationPropertyHelper;
use Jpmschuler\Ttnews2News\Migration\PropertyHelpers\CreateNewsRelatedRelationsPropertyHelper;

/**
 * Class NewsMigrator
 * To update previous imported news records with relations
 */
class NewsMigrator extends AbstractMigrator implements MigratorInterface
{
    protected string $tableName = 'tx_news_domain_model_news';

    protected bool $enforce = true;

    /**
     * Filter selection of old records like "and pid > 0" (to prevent elements in a workflow e.g.)
     */
    protected string $additionalWhere = 'and _migrated=1 and _migrated_table="tt_news" and _migrated_twice=0';

    protected array $values = [
        '_migrated_twice' => 1
// Don't migrate a second time (for other branches that should also be migrated)
    ];

    protected array $sql = [
        'end' => [
            'update sys_file_reference r left join tx_news_domain_model_news n on r.uid_foreign = n.uid
            set r.pid = n.pid where r.tablenames LIKE "tx_news_domain_model_news" and n.deleted=0'
        ]
    ];

    protected array $propertyHelpers = [
        'categories' => [
            [
                'className' => CreateNewsCategoryRelationPropertyHelper::class
            ]
        ],
        'fal_media' => [
            [
                'className' => CopySysFileReferencePropertyHelper::class,
                'configuration' => [
                    'oldFieldName' => 'image',
                    'oldTableName' => 'tt_news'
                ]
            ]
        ],
        'fal_related_files' => [
            [
                'className' => CopySysFileReferencePropertyHelper::class,
                'configuration' => [
                    'oldFieldName' => 'news_files',
                    'oldTableName' => 'tt_news'
                ]
            ]
        ],
        'related' => [
            [
                'className' => CreateNewsRelatedRelationsPropertyHelper::class
            ]
        ]
    ];
}

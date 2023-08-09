<?php
declare(strict_types=1);
namespace Jpmschuler\Ttnews2News\Migration\Migrator;

use In2code\Migration\Migration\Migrator\AbstractMigrator;
use In2code\Migration\Migration\Migrator\MigratorInterface;
use In2code\Migration\Migration\PropertyHelpers\SlugPropertyHelper;
use Jpmschuler\Ttnews2News\Migration\PropertyHelpers\CreateNewsCategoryRelationPropertyHelper;
use Jpmschuler\Ttnews2News\Migration\PropertyHelpers\CreateNewsFileRelationsPropertyHelper;
use Jpmschuler\Ttnews2News\Migration\PropertyHelpers\CreateNewsImageRelationAndMoveImagePropertyHelper;
use Jpmschuler\Ttnews2News\Migration\PropertyHelpers\CreateNewsRelatedRelationsPropertyHelper;

/**
 * Class NewsMigrator
 * To update previous imported news records with relations
 */
class NewsMigrator extends AbstractMigrator implements MigratorInterface
{
    /**
     * @var string
     */
    protected $tableName = 'tx_news_domain_model_news';

    /**
     * @var bool
     */
    protected $enforce = true;

    /**
     * Filter selection of old records like "and pid > 0" (to prevent elements in a workflow e.g.)
     *
     * @var string
     */
    protected $additionalWhere = 'and _migrated=1 and _migrated_table="tt_news" and _migrated_twice=0';

    /**
     * @var array
     */
    protected $values = [
        '_migrated_twice' => 1 // Don't migrate a second time (for other branches that should also be migrated)
    ];

    /**
     * @var array
     */
    protected $sql = [
        'end' => [
            'update sys_file_reference r left join tx_news_domain_model_news n on r.uid_foreign = n.uid
            set r.pid = n.pid where r.tablenames LIKE "tx_news_domain_model_news" and n.deleted=0'
        ]
    ];

    /**
     * @var array
     */
    protected $propertyHelpers = [
        'categories' => [
            [
                'className' => CreateNewsCategoryRelationPropertyHelper::class
            ]
        ],
        'fal_media' => [
            [
                'className' => CreateNewsImageRelationAndMoveImagePropertyHelper::class
            ]
        ],
        'fal_related_files' => [
            [
                'className' => CreateNewsFileRelationsPropertyHelper::class
            ]
        ],
        'related' => [
            [
                'className' => CreateNewsRelatedRelationsPropertyHelper::class
            ]
        ],
        'path_segment' => [
            [
                'className' => SlugPropertyHelper::class,
                'configuration' => [
                    'conditions' => [
                        'deleted' => [
                            '0'
                        ]
                    ]
                ]
            ]
        ]
    ];
}
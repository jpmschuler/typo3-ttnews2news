<?php

declare(strict_types=1);

namespace Jpmschuler\Ttnews2News\Migration\Importer;

use In2code\Migration\Migration\Importer\AbstractImporter;
use In2code\Migration\Migration\Importer\ImporterInterface;
use Jpmschuler\Ttnews2News\Migration\PropertyHelpers\CreateSortingNumberFromPropertyPropertyHelper;

/**
 * Class NewsCategoriesImporter
 */
class NewsCategoriesImporter extends AbstractImporter implements ImporterInterface
{
    /**
     * Table name where to migrate to
     *
     * @var string
     */
    protected $tableName = 'sys_category';

    /**
     * Table name from migrate to
     *
     * @var string
     */
    protected $tableNameOld = 'tt_news_cat';

    /**
     * @var bool
     */
    protected $truncate = false;

    /**
     * @var bool
     */
    protected $keepIdentifiers = false;

    /**
     * @var array
     */
    protected $mapping = [
        'pid' => 'pid',
        'title' => 'title',
        'parent_category' => 'parent',
        'fe_group' => 'fe_group',
        'sorting' => 'sorting'
    ];

    /**
     * PropertyHelpers are called after initial build via mapping
     *
     *      "newProperty" => [
     *          [
     *              "className" => class1::class,
     *              "configuration => ["red"]
     *          ],
     *          [
     *              "className" => class2::class
     *          ]
     *      ]
     *
     * @var array
     */
    protected $propertyHelpers = [
        'sorting' => [
            [
                'className' => CreateSortingNumberFromPropertyPropertyHelper::class,
                'configuration' => [
                    'property' => 'title'
                ]
            ]
        ]
    ];
}

<?php

declare(strict_types=1);

namespace Jpmschuler\Ttnews2News\Migration\Migrator;

use In2code\Migration\Migration\Migrator\MigratorInterface;
use In2code\Migration\Migration\PropertyHelpers\FlexFormGeneratorPropertyHelper;

/**
 * Class ContentMigrator
 */
class ContentMigrator extends AbstractMigrator implements MigratorInterface
{
    protected string $tableName = 'tt_content';

    protected array $propertyHelpers = [
        'pi_flexform' => [
            [
                // Build FlexForm for News plugin
                'className' => FlexFormGeneratorPropertyHelper::class,
                'configuration' => [
                    'condition' => [
                        'CType' => 'list',
                        'list_type' => '9'
// Tt_news plugin
                    ],
                    'flexFormTemplate' => 'EXT:migration_extend/Resources/Private/FlexForms/News.xml',
                    'flexFormField' => 'pi_flexform',
                    'overwriteValues' => [
                        'list_type' => 'news_pi1'
                    ],
                    'additionalMapping' => [
                        [
                            // create new variable {additionalMapping.switchableControllerActions}
                            'variableName' => 'switchableControllerActions',
                            'keyField' => 'flexForm:what_to_display',
// "flexForm:path/path" or: "row:uid"
                            'mapping' => [
                                'LIST' => 'News->list;News->detail',
                                'LIST2' => 'News->list;News->detail',
                                'LIST3' => 'News->list;News->detail',
                                'HEADER_LIST' => 'News->list;News->detail',
                                'LATEST' => 'News->list;News->detail',
                                'SINGLE' => 'News->detail',
                                'SINGLE2' => 'News->detail',
                                'AMENU' => 'News->list;News->detail',
                                'SEARCH' => 'News->list;News->detail',
                                'CATMENU' => 'News->list;News->detail',
                                'VERSION_PREVIEW' => 'News->list;News->detail',
                                'EVENT_FUTURE' => 'News->list;News->detail',
                                'EVENT_PAST' => 'News->list;News->detail',
                                'LATEST_EVENT_PAST' => 'News->list;News->detail',
                                'LATEST_EVENT_FUTURE' => 'News->list;News->detail',
                                'EVENT_CURRENT' => 'News->list;News->detail',
                                'LATEST_EVENT_CURRENT' => 'News->list;News->detail',
                                'EVENT_REGISTERABLE' => 'News->list;News->detail',
                                'LATEST_EVENT_REGISTERABLE' => 'News->list;News->detail'
                            ]
                        ],
                        [
                            // create new variable {additionalMapping.categorySetting}
                            'variableName' => 'categorySetting',
                            'keyField' => 'flexForm:categoryMode',
// "flexForm:path/path" or: "row:uid"
                            'mapping' => [
                                '0' => '',
// show all
                                '1' => 'or',
// show from categories (OR)
                                '2' => 'and',
// show from categories (AND)
                                '-1' => 'notand',
// don't show from categories (AND)
                                '-2' => 'notor',
// don't show from categories (OR)
                            ]
                        ],
                        [
                            // create new variable {additionalMapping.archiveSetting}
                            'variableName' => 'archiveSetting',
                            'keyField' => 'flexForm:archive',
// "flexForm:path/path" or: "row:uid"
                            'mapping' => [
                                '0' => '',
// don't care
                                '1' => 'archived',
// archived only
                                '-1' => 'active',
// not archived only
                            ]
                        ]
                    ]
                ]
            ]
        ],
    ];
}

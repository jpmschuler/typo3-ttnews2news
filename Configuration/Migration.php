<?php

use Jpmschuler\Ttnews2News\Migration\Importer\NewsCategoriesImporter;
use Jpmschuler\Ttnews2News\Migration\Importer\NewsImporter;
use Jpmschuler\Ttnews2News\Migration\Migrator\CategoriesMigrator;
use Jpmschuler\Ttnews2News\Migration\Migrator\ContentMigrator;
use Jpmschuler\Ttnews2News\Migration\Migrator\NewsMigrator;

return [
    // Default values if not given from CLI
    'configuration' => [
        'key' => '',
        'dryrun' => false,
        'limitToRecord' => null,
        'limitToPage' => 21033,
        'recursive' => true
    ],

    // Define your migrations
    'migrations' => [
        [
            'className' => NewsCategoriesImporter::class,
            'keys' => [
                'news',
                'categories',
                'categoriesimport'
            ]
        ],
        [
            'className' => CategoriesMigrator::class,
            'keys' => [
                'news',
                'categories',
                'categoriesmigration'
            ]
        ],
        [
            'className' => NewsImporter::class,
            'keys' => [
                'news'
            ]
        ],
        [
            'className' => NewsMigrator::class,
            'keys' => [
                'news'
            ]
        ]
    ]
];

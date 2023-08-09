<?php
return [
    // Default values if not given from CLI
    'configuration' => [
        'key' => '',
        'dryrun' => true,
        'limitToRecord' => null,
        'limitToPage' => 583,
        'recursive' => true
    ],

    // Define your migrations
    'migrations' => [
        [
            'className' => \Jpmschuler\Ttnews2News\Migration\Importer\NewsCategoriesImporter::class,
            'keys' => [
                'news',
                'categories',
                'categoriesimport'
            ]
        ],
        [
            'className' => \Jpmschuler\Ttnews2News\Migration\Migrator\CategoriesMigrator::class,
            'keys' => [
                'news',
                'categories',
                'categoriesmigration'
            ]
        ],
        [
            'className' => \Jpmschuler\Ttnews2News\Migration\Importer\NewsImporter::class,
            'keys' => [
                'news'
            ]
        ],
        [
            'className' => \Jpmschuler\Ttnews2News\Migration\Migrator\NewsMigrator::class,
            'keys' => [
                'news'
            ]
        ],
        [
            'className' => \Jpmschuler\Ttnews2News\Migration\Migrator\ContentMigrator::class,
            'keys' => [
                'content'
            ]
        ]
    ]
];
<?php

declare(strict_types=1);

namespace Jpmschuler\Ttnews2News\Migration\Importer;

use In2code\Migration\Migration\Importer\ImporterInterface;

/**
 * Class NewsImporter
 */
class NewsImporter extends AbstractImporter implements ImporterInterface
{
    protected string $tableName = 'tx_news_domain_model_news';

    protected string $tableNameOld = 'tt_news';

    protected bool $truncate = false;

    protected bool $keepIdentifiers = true;

    protected array $mapping = [
        'uid' => 'uid',
        'type' => 'type',
        'title' => 'title',
        'short' => 'teaser',
        'bodytext' => 'bodytext',
        'datetime' => 'datetime',
        'author' => 'author',
        'author_email' => 'author_email',
        'archivedate' => 'archive',
        'editlock' => 'editlock',
        'keywords' => 'keywords',
        'page' => 'internalurl',
        'ext_url' => 'externalurl',
        'slug' => 'path_segment',
        'hidden' => 'hidden',
        'sys_language_uid' => 'sys_language_uid',
        'l18n_parent' => 'l10n_parent',
        'l18n_diffsource' => 'l10n_diffsource',

        'category' => 'categories',
    ];
}

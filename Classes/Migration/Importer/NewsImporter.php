<?php

declare(strict_types=1);

namespace Jpmschuler\Ttnews2News\Migration\Importer;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use In2code\Migration\Exception\ConfigurationException;
use In2code\Migration\Migration\Importer\ImporterInterface;
use Jpmschuler\Ttnews2News\Migration\PropertyHelpers\GetNewUidIfAlreadyMigratedPropertyHelper;
use Jpmschuler\Ttnews2News\Migration\Repository\GeneralRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class NewsImporter
 */
class NewsImporter extends AbstractImporter implements ImporterInterface
{
    /**
     * @var string
     */
    protected $tableName = 'tx_news_domain_model_news';

    /**
     * @var string
     */
    protected $tableNameOld = 'tt_news';

    /**
     * @var bool
     */
    protected $truncate = false;

    /**
     * @var bool
     */
    protected $keepIdentifiers = true;

    /**
     * @var array
     */
    protected $mapping = [
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
        'fe_group' => 'fe_group',
        'sys_language_uid' => 'sys_language_uid',
        'l18n_parent' => 'l10n_parent',
        'l18n_diffsource' => 'l10n_diffsource',

        'category' => 'categories',
    ];
}

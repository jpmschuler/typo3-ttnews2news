<?php

declare(strict_types=1);

namespace Jpmschuler\Ttnews2News\Migration\Importer;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use In2code\Migration\Exception\ConfigurationException;
use In2code\Migration\Migration\Importer\ImporterInterface;
use In2code\Migration\Migration\PropertyHelpers\PropertyHelperInterface;
use Jpmschuler\Ttnews2News\Migration\Repository\GeneralRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class NewsImporter
 */
class AbstractImporter extends \In2code\Migration\Migration\Importer\AbstractImporter implements ImporterInterface
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
    protected $keepIdentifiers = false;

    /**
     * @var array
     */
    protected $mapping = [
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

    /**
     * @return void
     * @throws ConfigurationException
     * @throws DBALException
     * @throws Exception
     */
    public function start(): void
    {
        $this->executeSqlStart();
        /* @var GeneralRepository $generalRepository */
        $generalRepository = GeneralUtility::makeInstance(
            GeneralRepository::class,
            $this->configuration,
            $this->enforce
        );
        $records = $generalRepository->getRecords(
            $this->tableNameOld,
            $this->additionalWhere,
            $this->groupBy,
            $this->orderBy
        );
        $countImported = 0;
        $countUpdated = 0;
        foreach ($records as $propertiesOld) {
            $this->log->addNote(
                'Start importing ' . $this->tableName
                . ' (' . $this->tableNameOld . ':' . $propertiesOld['uid'] . ' from pid' . $propertiesOld['pid'] . ') ...'
            );
            $properties = $this->createPropertiesFromMapping($propertiesOld);
            $properties = $this->createPropertiesFromValues($properties, $propertiesOld);
            $properties = $this->createPropertiesFromPropertyHelpers($properties, $propertiesOld);
            $properties = $this->genericChanges($properties);
            $recordIsMigrated = $generalRepository->isRecordMigrated($this->tableName, $properties['_migrated_uid']);

            if ($recordIsMigrated > 0) {
                $this->log->addNote('Record already existing: ' . $recordIsMigrated);
            } else {
                $this->log->addNote('New Record found');
                $generalRepository->insertRecord($properties, $this->tableName);
                $countImported++;
            }
        }
        $this->executeSqlEnd();
        $this->finalMessageDetailed(count($records), $countImported, $countUpdated);
    }

    /**
     * @param array $records
     * @return void
     */
    protected function finalMessageDetailed($all, $imported, $updated)
    {
        if ($this->configuration['configuration']['dryrun'] === false) {
            $message = sprintf(
                '%s records found, %s imported, %s updated to %s',
                $all,
                $imported,
                $updated,
                $this->tableName
            );
        } else {
            $message = $all . ' records could be imported without dryrun to ' . $this->tableName;
        }
        $this->log->addMessage($message);
    }

    protected function createPropertiesFromPropertyHelpers(array $properties, array $propertiesOld): array
    {
        foreach ($this->propertyHelpers as $propertyName => $helperConfigurations) {
            foreach ($helperConfigurations as $key => $helperConfiguration) {
                if (is_int($key) === false) {
                    throw new ConfigurationException('Misconfiguration of your importer class', 1569574630);
                }
                if (class_exists($helperConfiguration['className']) === false) {
                    throw new ConfigurationException(
                        'Class ' . $helperConfiguration['className'] . ' does not exist',
                        1569574672
                    );
                }
                if (is_subclass_of($helperConfiguration['className'], PropertyHelperInterface::class) === false) {
                    throw new ConfigurationException(
                        'Class does not implement ' . PropertyHelperInterface::class,
                        1569574677
                    );
                }
                $helperClass = GeneralUtility::makeInstance(
                    $helperConfiguration['className'],
                    $properties,
                    $propertiesOld,
                    $propertyName,
                    $this->tableName,
                    (array)($helperConfiguration['configuration'] ?? []),
                    $this->configuration['configuration'] ?? []
                );
                $helperClass->initialize();
                $properties = $helperClass->returnRecord();
            }
        }
        return $properties;
    }
}

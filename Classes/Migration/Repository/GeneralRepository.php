<?php
declare(strict_types=1);

namespace Jpmschuler\Ttnews2News\Migration\Repository;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use In2code\Migration\Migration\Log\Log;
use In2code\Migration\Utility\DatabaseUtility;
use LogicException;
use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GeneralRepository extends \In2code\Migration\Migration\Repository\GeneralRepository
{
    public function updateRecord(array $properties, string $tableName): void
    {
        if (array_key_exists('_migrated_uid', $properties) === false) {
            throw new LogicException(
                'Record of table ' . $tableName . ' needs a _migrated_uid field for persisting',
                1568277411
            );
        }
        if ($this->getConfiguration('dryrun') === false) {
            $properties = $this->queue->updatePropertiesWithPropertiesFromQueue(
                $tableName,
                (int)$properties['_migrated_uid'],
                $properties
            );

            $connection = DatabaseUtility::getConnectionForTable($tableName);
            $connection->update($tableName, $properties, ['uid' => (int)$properties['_migrated_uid']]);
            $this->log->addMessage('Record updated', $properties, $tableName);
        } else {
            $this->log->addMessage('Record could be inserted', $properties, $tableName);
        }
    }

    public function insertRecord(array $properties, string $tableName)
    {
        if ($this->getConfiguration('dryrun') === false) {
            $properties = $this->queue->updatePropertiesWithPropertiesFromQueue(
                $tableName,
                (int)($properties['uid'] ?? 0),
                $properties
            );

            $connection = DatabaseUtility::getConnectionForTable($tableName);
            $connection->insert($tableName, $properties);
            $this->log->addMessage('Record inserted', $properties, $tableName);
        } else {
            $this->log->addMessage('Record could be inserted', $properties, $tableName);
        }
    }

    /**
     * @param string $tableName
     * @throws DBALException|Exception
     */
    public function isRecordMigrated(
        string $tableName,
        int    $oldUid
    ): int {
        $connection = DatabaseUtility::getConnectionForTable($tableName);
        /** @noinspection SqlNoDataSourceInspection */
        $query = 'select uid from ' . $tableName . ' where _migrated_uid = ' . $oldUid;
        $result = $connection->executeQuery($query)->fetchOne() ?: -1;

        $this->log->addNote($query .' did '.$result);
        return $result ?: -1;
    }
}

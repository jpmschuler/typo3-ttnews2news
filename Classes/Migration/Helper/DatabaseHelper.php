<?php

declare(strict_types=1);

namespace Jpmschuler\Ttnews2News\Migration\Helper;

use In2code\Migration\Utility\DatabaseUtility;
use In2code\Migration\Utility\StringUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DatabaseHelper extends \In2code\Migration\Migration\Helper\DatabaseHelper
{
    public function createRecord(string $tableName, array $row): int
    {
        $uid = 0;
        $excludeWhereFieldsFromSelect =['tstamp', 'crdate', 'uid'];
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);
        $queryBuilder = $queryBuilder
            ->select('*')
            ->from($tableName);
        foreach ($row as $columnName => $value) {
            if (!in_array($columnName, $excludeWhereFieldsFromSelect)) {
                $queryBuilder->andWhere(
                    $queryBuilder->expr()->eq($columnName, $queryBuilder->createNamedParameter($value, is_int($value) ?\PDO::PARAM_INT : \PDO::PARAM_STR))
                );
            }
        }

        $existingRow = $queryBuilder
            ->executeQuery()->fetchAssociative();
        if ($existingRow === false) {
            $connection = DatabaseUtility::getConnectionForTable($tableName);
            $row = $this->addTimeFieldsToRow($row, $tableName);
            $connection->insert($tableName, $row);
            $uid = (int)$connection->lastInsertId($tableName);
        } else {
            $this->log->addNote(
                'record already exists, skipped entry (' . $this->buildWhereClauseFromPropertiesArray($row) . ')'
            );
            if (!empty($existingRow['uid'])) {
                $uid = (int)$existingRow['uid'];
            }
        }
        return $uid;
    }
    protected function buildWhereClauseFromPropertiesArray(
        array $properties,
        array $excludeFields = [
            'tstamp',
            'crdate'
        ]
    ): string {
        $whereString = '';
        foreach ($properties as $propertyName => $propertyValue) {
            if (!in_array($propertyName, $excludeFields)) {
                if (!empty($whereString)) {
                    $whereString .= ' and ';
                }
                if (empty($propertyValue)) {
                    $whereString .= '(`' . $propertyName . '`=\'\' or `' . $propertyName . '` is null)';
                } else {
                    $whereString .= '`' . $propertyName . '`=';
                    if (is_numeric($propertyValue) === false) {
                        $propertyValue = StringUtility::quoteString($propertyValue);
                    }
                    $whereString .= $propertyValue;
                }
            }
        }
        return $whereString;
    }
}

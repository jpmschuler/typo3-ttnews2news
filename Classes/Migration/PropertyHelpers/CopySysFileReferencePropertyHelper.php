<?php

declare(strict_types=1);

namespace Jpmschuler\Ttnews2News\Migration\PropertyHelpers;

use In2code\Migration\Migration\PropertyHelpers\AbstractPropertyHelper;
use In2code\Migration\Migration\PropertyHelpers\PropertyHelperInterface;
use In2code\Migration\Utility\DatabaseUtility;
use Jpmschuler\Ttnews2News\Migration\Helper\DatabaseHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CopySysFileReferencePropertyHelper extends AbstractPropertyHelper implements PropertyHelperInterface
{
    protected $referenceTable = 'sys_file_reference';

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function manipulate(): void
    {
        $databaseHelper = GeneralUtility::makeInstance(DatabaseHelper::class);
        $newsUid = (int)$this->getPropertyFromRecord('uid');
        $newsUidOld = (int)$this->getPropertyFromRecord('_migrated_uid');
        $rows = $this->getOldPropertiesForRelation($newsUidOld);
        foreach ($rows as $row) {
            if ((int)($row['uid_foreign'] ?? 0) > 0) {
                if ($newsUidOld > 0) {
                    $newRow = $row;
                    $newRow['uid_foreign'] = $newsUid;
                    $newRow['tablenames'] = 'tx_news_domain_model_news';
                    $newRow['fieldname'] = $this->propertyName;
                    unset($newRow['uid']);
                    $databaseHelper->createRecord($this->referenceTable, $newRow);
                    $this->log->addMessage(
                        'New relation to existing ' . $this->propertyName .
                        ' with sys_file_reference ' . $row['uid_foreign'] . ' created'
                    );
                }
            } else {
                $this->log->addNote('Failed creating new relation ');
                $this->log->addNote(
                    print_r([$row, $this->getRecord()['_migrated_uid'] . '=>' . $this->getRecord()['uid']], true)
                );
            }
        }
    }

    /**
     * @param int $newsUidOld
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    protected function getOldPropertiesForRelation(int $newsUidOld): array
    {
        $connection = DatabaseUtility::getConnectionForTable($this->referenceTable);
        $query = 'select * from ' . $this->referenceTable .
            ' where tablenames="' . $this->configuration['oldTableName'] . '" ' .
            ' and fieldname="' . $this->configuration['oldFieldName'] . '" ' .
            ' and uid_foreign=' . (int)$newsUidOld;
        $rows = $connection->executeQuery(
            $query
        )->fetchAllAssociative();
        return $rows;
    }
}

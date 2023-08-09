<?php

declare(strict_types=1);

namespace Jpmschuler\Ttnews2News\Migration\PropertyHelpers;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use In2code\Migration\Migration\Helper\DatabaseHelper;
use In2code\Migration\Migration\PropertyHelpers\AbstractPropertyHelper;
use In2code\Migration\Migration\PropertyHelpers\PropertyHelperInterface;
use In2code\Migration\Utility\DatabaseUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class CreateNewsCategoryRelationPropertyHelper
 */
class CreateNewsCategoryRelationPropertyHelper extends AbstractPropertyHelper implements PropertyHelperInterface
{
    /**
     * @var string
     */
    protected $newTableName = 'sys_category_record_mm';

    /**
     * @var string
     */
    protected $oldTableName = 'tt_news_cat_mm';

    /**
     * @throws DBALException
     */
    public function manipulate(): void
    {
        $databaseHelper = GeneralUtility::makeInstance(DatabaseHelper::class);
        $newsUid = (int)$this->getPropertyFromRecord('uid');
        $newsUidOld = (int)$this->getPropertyFromRecord('_migrated_uid');
        $rows = $this->getOldProperties($newsUidOld);
        foreach ($rows as $row) {
            if ((int)$row['uid_foreign'] > 0) {
                $newCategoryUid = $this->getNewCategoryIdentifier((int)$row['uid_foreign']);
                if ($newCategoryUid > 0) {
                    $newRow = [
                        'uid_foreign' => $newsUid,
                        'uid_local' => $newCategoryUid,
                        'sorting' => $row['sorting'],
                        'tablenames' => 'tx_news_domain_model_news',
                        'fieldname' => $this->propertyName
                    ];
                    $databaseHelper->createRecord($this->newTableName, $newRow);
                    $this->log->addMessage('New relation to category with uid ' . $row['uid_foreign'] . ' created');
                }
            }
        }
    }

    /**
     * @param int $newsUidOld
     * @return array
     * @throws DBALException|Exception
     */
    protected function getOldProperties(int $newsUidOld): array
    {
        $connection = DatabaseUtility::getConnectionForTable($this->oldTableName);
        $rows = (array)$connection->executeQuery(
            'select * from ' . $this->oldTableName . ' where uid_local=' . (int)$newsUidOld
        )->fetchAssociative();
        return $rows;
    }

    /**
     * @param int $oldIdentifier
     * @return int
     */
    protected function getNewCategoryIdentifier(int $oldIdentifier): int
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable('sys_category');
        return (int)$queryBuilder
            ->select('uid')
            ->from('sys_category')
            ->where('_migrated_uid=' . $oldIdentifier . ' and _migrated_table="tt_news_cat"')
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchOne();
    }
}

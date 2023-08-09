<?php

declare(strict_types=1);

namespace Jpmschuler\Ttnews2News\Migration\PropertyHelpers;

use Doctrine\DBAL\DBALException;
use In2code\Migration\Migration\PropertyHelpers\AbstractPropertyHelper;
use In2code\Migration\Migration\PropertyHelpers\PropertyHelperInterface;
use In2code\Migration\Utility\DatabaseUtility;

/**
 * Class CreateSortingNumberFromPropertyPropertyHelper
 */
class CreateSortingNumberFromPropertyPropertyHelper extends AbstractPropertyHelper implements PropertyHelperInterface
{
    /**
     * @throws DBALException
     */
    public function manipulate(): void
    {
        $sortingArray = $this->getAllOldCategoriesSortedByProperty('sorting');
        $sorting = 10000;
        if (array_key_exists($this->getPropertyFromRecord('uid'), $sortingArray)) {
            $sorting = $sortingArray[$this->getPropertyFromRecordOld('uid')];
        } else {
            $this->log->addError('Category not sortable: ' . $this->getPropertyFromRecordOld('title'));
        }
        $this->setProperty($sorting);
    }

    /**
     * @param string $property
     * @return array
     * @throws DBALException
     */
    protected function getAllOldCategoriesSortedByProperty(string $property): array
    {
        $connection = DatabaseUtility::getConnectionForTable('tt_news_cat');
        $rows = (array)$connection->executeQuery(
            'select uid from tt_news_cat where deleted=0 order by "' . $property . '"'
        )->fetchAll();
        $categories = [];
        $sorting = 100;
        foreach ($rows as $row) {
            $categories[$sorting] = $row['uid'];
            $sorting += 100;
        }
        return array_flip($categories);
    }
}

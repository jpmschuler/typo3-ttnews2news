<?php

declare(strict_types=1);

namespace Jpmschuler\Ttnews2News\Migration\PropertyHelpers;

use In2code\Migration\Migration\PropertyHelpers\AbstractPropertyHelper;
use In2code\Migration\Migration\PropertyHelpers\PropertyHelperInterface;
use In2code\Migration\Utility\DatabaseUtility;

/**
 * Class GetParentCategoryPropertyHelper
 */
class GetParentCategoryPropertyHelper extends AbstractPropertyHelper implements PropertyHelperInterface
{
    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function manipulate(): void
    {
        $queryBuilder = DatabaseUtility::getConnectionForTable($this->table);
        $sql = 'select uid from sys_category where _migrated_uid=' . (int)$this->getProperty();
        $value = (string)$queryBuilder->executeQuery($sql)->fetchOne();
        if ($value > 0) {
            $this->log->addMessage('Replace ' . $this->getProperty() . ' with ' . $value . ' in ' . __CLASS__);
            $this->setProperty($value);
        }
    }

    /**
     * @return bool
     */
    public function shouldMigrate(): bool
    {
        return $this->getProperty() > 0;
    }
}

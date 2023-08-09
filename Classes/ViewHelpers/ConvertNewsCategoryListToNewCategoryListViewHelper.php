<?php
declare(strict_types=1);
namespace Jpmschuler\Ttnews2News\ViewHelpers;

use In2code\Migration\Utility\DatabaseUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class ConvertNewsCategoryListToNewCategoryListViewHelper
 */
class ConvertNewsCategoryListToNewCategoryListViewHelper extends AbstractViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('list', 'string', 'list with tt_news_cat uids', true);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $newList = [];
        $categoriesOld = GeneralUtility::intExplode(',', $this->arguments['list']);
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable('sys_category');
        foreach ($categoriesOld as $categoryOld) {
            $newList[] = (int)$queryBuilder
                ->select('uid')
                ->from('sys_category')
                ->where('_migrated_uid=' . $categoryOld . ' and _migrated_table="tt_news_cat"')
                ->setMaxResults(1)
                ->orderBy('uid', 'desc')
                ->execute()
                ->fetchColumn(0);
        }
        return implode(',', $newList);
    }
}
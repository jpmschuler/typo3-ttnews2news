<?php

declare(strict_types=1);

namespace Jpmschuler\Ttnews2News\Migration\PropertyHelpers;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use In2code\Migration\Migration\Helper\FileHelper;
use In2code\Migration\Migration\PropertyHelpers\AbstractPropertyHelper;
use In2code\Migration\Migration\PropertyHelpers\PropertyHelperInterface;
use In2code\Migration\Utility\DatabaseUtility;
use LogicException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CreateNewsImageRelationAndMoveFilePropertyHelper extends AbstractPropertyHelper implements PropertyHelperInterface
{
    /**
     * @var string
     */
    protected $targetFolder = 'files/_migrated/news_images/';

    /**
     * @var string
     */
    protected $oldFolder = 'uploads/pics/';

    /**
     * @throws DBALException|Exception
     */
    public function manipulate(): void
    {
        $fileHelper = GeneralUtility::makeInstance(FileHelper::class);
        $imageNames = $this->getImageNames();
        foreach ($imageNames as $key => $imageName) {
            $fileHelper->copyFileAndCreateReference(
                $this->oldFolder . $imageName,
                $this->targetFolder,
                $this->table,
                $this->propertyName,
                $this->getPropertyFromRecord('uid'),
                $this->getAdditionalProperties($key)
            );
            $this->log->addMessage('Image copied and created relation to it (' . $imageName . ')');
        }
    }

    /**
     * @param int $key
     * @return array
     * @throws DBALException|Exception
     */
    protected function getAdditionalProperties(int $key): array
    {
        $titleTexts = $this->getTitleTexts();
        $altTexts = $this->getAltTexts();
        $imageCaptions = $this->getImageCaptions();
        $links = $this->getImageLinks();

        $additionalProperties = ['showinpreview' => 1];
        if (array_key_exists($key, $titleTexts)) {
            $additionalProperties['title'] = $titleTexts[$key];
        }
        if (array_key_exists($key, $altTexts)) {
            $additionalProperties['alternative'] = $altTexts[$key];
        }
        if (array_key_exists($key, $imageCaptions)) {
            $additionalProperties['description'] = $imageCaptions[$key];
        }
        if (array_key_exists($key, $links)) {
            $additionalProperties['link'] = $links[$key];
        }
        return $additionalProperties;
    }

    /**
     * @return array
     * @throws DBALException|Exception
     */
    protected function getImageNames(): array
    {
        return GeneralUtility::trimExplode(',', $this->getPropertyFromRecordOld('image'), true);
    }

    /**
     * @return array
     * @throws DBALException|Exception
     */
    protected function getTitleTexts(): array
    {
        return GeneralUtility::trimExplode(PHP_EOL, $this->getPropertyFromRecordOld('imagetitletext'), true);
    }

    /**
     * @return array
     * @throws DBALException|Exception
     */
    protected function getAltTexts(): array
    {
        return GeneralUtility::trimExplode(PHP_EOL, $this->getPropertyFromRecordOld('imagealttext'), true);
    }

    /**
     * @return array
     * @throws DBALException|Exception
     */
    protected function getImageCaptions(): array
    {
        return GeneralUtility::trimExplode(PHP_EOL, $this->getPropertyFromRecordOld('imagecaption'), true);
    }

    /**
     * @return array
     * @throws DBALException|Exception
     */
    protected function getImageLinks(): array
    {
        return GeneralUtility::trimExplode(PHP_EOL, $this->getPropertyFromRecordOld('links'), true);
    }

    /**
     * Overrule original function and get values from original tt_news record
     *
     * @param string $propertyName
     * @return int|string
     * @throws DBALException|Exception|LogicException
     */
    protected function getPropertyFromRecordOld(string $propertyName): int|string
    {
        $propertiesOld = $this->getPropertiesFromOldRecord();
        if (array_key_exists($propertyName, $propertiesOld)) {
            return $propertiesOld[$propertyName];
        }
        throw new LogicException('Property does not exist in ' . __CLASS__, 1569587312);
    }

    /**
     * @return array
     * @throws DBALException|Exception
     */
    protected function getPropertiesFromOldRecord(): array
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable('tt_news');
        return (array)$queryBuilder->select('*')
            ->from('tt_news')
            ->where('uid=' . (int)$this->getPropertyFromRecord('_migrated_uid'))
            ->executeQuery()
            ->fetchAssociative();
    }
}

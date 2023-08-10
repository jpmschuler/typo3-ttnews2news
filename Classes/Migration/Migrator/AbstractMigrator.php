<?php
declare(strict_types=1);
namespace Jpmschuler\Ttnews2News\Migration\Migrator;

use In2code\Migration\Exception\ConfigurationException;
use In2code\Migration\Migration\PropertyHelpers\PropertyHelperInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AbstractMigrator extends \In2code\Migration\Migration\Migrator\AbstractMigrator {
    protected function manipulatePropertiesWithPropertyHelpers(array $properties, array $propertiesOld): array
    {
        foreach ($this->propertyHelpers as $propertyName => $helperConfigurations) {
            foreach ($helperConfigurations as $key => $helperConfiguration) {
                if (is_int($key) === false) {
                    throw new ConfigurationException('Misconfiguration of your migrator class', 1569574630);
                }
                if (class_exists($helperConfiguration['className']) === false) {
                    throw new ConfigurationException(
                        'Class ' . $helperConfiguration['className'] . ' does not exist',
                        1568285755
                    );
                }
                if (is_subclass_of($helperConfiguration['className'], PropertyHelperInterface::class) === false) {
                    throw new ConfigurationException(
                        'Class does not implement ' . PropertyHelperInterface::class,
                        1568285773
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

<?php

defined('TYPO3') || die();

(static function () {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['migrationextend'][]
        = 'Jpmschuler\Ttnews2News\ViewHelpers';
})();

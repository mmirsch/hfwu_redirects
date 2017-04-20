<?php
defined('TYPO3_MODE') or die();

if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) >= 7000000 ) {
    // Override  icon
    $GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
      0 => 'LLL:EXT:hfwu_redirects/Resources/Private/Language/locallang.xlf:redirects-folder',
      1 => 'hfwu_redirects',
      2 => 'apps-pagetree-folder-contains-hfwu_redirects'
    ];
}

$GLOBALS['TCA']['pages']['ctrl']['typeicon_classes']['contains-hfwu_redirects'] = 'apps-pagetree-folder-contains-hfwu_redirects';


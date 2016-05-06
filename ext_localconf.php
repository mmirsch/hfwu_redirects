<?php

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/index_ts.php']['preprocessRequest']['hfwu_redirects'] = 'HFWU\\HfwuRedirects\\Hooks\\RedirectHandling->handleRedirects';

/** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */

/*
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$iconRegistry->registerIcon(
	'apps-pagetree-folder-contains-redirects',
	\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
	['source' => 'EXT:hfwu_redirects/ext_icon.svg']
);
*/

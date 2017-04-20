<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

if (TYPO3_MODE === 'BE') {
	/**
	 * Registers a Backend Module
	 */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'HFWU.hfwu_redirects',
		'web',	 // Make module a submodule of 'web'
		'redirects',	// Submodule key
		'',						// Position
		[
			'Redirects' => 'aliasList,aliasListAjax,showQrCode,deleteEntry',
		],
		[
			'access' => 'user,group',
			'icon'   => 'EXT:hfwu_redirects/ext_icon.svg',
			'labels' => 'LLL:EXT:hfwu_redirects/Resources/Private/Language/locallang_redirects.xlf',
		]
	);

	// Register icon for folder type redirects
	$iconPath = 'EXT:hfwu_redirects/Resources/Public/Icons/qr.png';
	$iconName = 'apps-pagetree-folder-contains-hfwu_redirects';
	if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) >= 7000000 ) {
		/** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
		$iconRegistry->registerIcon(
			$iconName,
			\TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
			['source' => $iconPath]
		);
	} else {

		\TYPO3\CMS\Backend\Sprite\SpriteManager::addTcaTypeIcon(
			'pages',
			'contains-redirects',
			'../typo3conf/ext/hfwu_redirects/Resources/Public/Icons/tx_hfwuredirects_domain_model_redirects.png'
		);
		$GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
			0 => 'LLL:EXT:hfwu_redirects/Resources/Private/Language/locallang.xlf:redirects-folder',
			1 => 'redirects',
			2 => '../typo3conf/ext/hfwu_redirects/Resources/Public/Icons/tx_hfwuredirects_domain_model_redirects.png'
		];

	}

}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_hfwuredirects_domain_model_redirects', 'EXT:hfwu_redirects/Resources/Private/Language/locallang_csh_tx_hfwuredirects_domain_model_redirects.xlf');

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['hfwu_redirects'] = 'HFWU\\HfwuRedirects\\Hooks\\DataHandler';

$TYPO3_CONF_VARS['SYS']['lang']['cache']['clear_menu'] = TRUE;
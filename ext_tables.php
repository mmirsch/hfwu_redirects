<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

if (TYPO3_MODE === 'BE') {

	\TYPO3\CMS\Backend\Sprite\SpriteManager::addTcaTypeIcon('pages', 'contains-redirects',
		'../typo3conf/ext/hfwu_redirects/Resources/Public/Icons/tx_hfwuredirects_domain_model_redirects.png');
	$GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
		0 => 'LLL:EXT:hfwu_redirects/Resources/Private/Language/locallang_be.xlf:redirects-folder',
		1 => 'redirects',
		2 => '../typo3conf/ext/hfwu_redirects/Resources/Public/Icons/tx_hfwuredirects_domain_model_redirects.png'
	];

	/**
	 * Registers a Backend Module
	 */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'HFWU.' . $_EXTKEY,
		'web',	 // Make module a submodule of 'web'
		'redirects',	// Submodule key
		'',						// Position
		[
			'Redirects' => 'aliasList',
		],
		[
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.svg',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_redirects.xlf',
		]
	);

}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_hfwuredirects_domain_model_redirects', 'EXT:hfwu_redirects/Resources/Private/Language/locallang_csh_tx_hfwuredirects_domain_model_redirects.xlf');
//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_hfwuredirects_domain_model_redirects');

$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX']['HfwuRedirects::aliasList'] =[
	'callbackMethod' => 'HFWU\HfwuRedirects\Utility\AjaxDispatcher->dispatchAliasList',
	'csrfTokenCheck' => true
];

$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX']['HfwuRedirects::deleteRedirectEntry'] = [
	'callbackMethod' => 'HFWU\HfwuRedirects\Utility\AjaxDispatcher->dispatchDeleteRedirectEntry',
	'csrfTokenCheck' => true
];

$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX']['HfwuRedirects::showQrCodeAjax'] = [
	'callbackMethod' => 'HFWU\HfwuRedirects\Utility\AjaxDispatcher->dispatchShowQrCode',
	'csrfTokenCheck' => true
];

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['hfwu_redirects'] = 'HFWU\\HfwuRedirects\\Hooks\\DataHandler';


$TYPO3_CONF_VARS['SYS']['lang']['cache']['clear_menu'] = TRUE;
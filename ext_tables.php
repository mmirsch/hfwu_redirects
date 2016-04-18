<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

if (TYPO3_MODE === 'BE') {

	/**
	 * Registers a Backend Module
	 */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'HFWU.' . $_EXTKEY,
		'web',	 // Make module a submodule of 'web'
		'redirects',	// Submodule key
		'',						// Position
		array(
			'Redirects' => 'qrList, qrListAjax, aliasList, aliasListAjax, deleteRedirectEntryAjax, showQrCode, list',
		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_redirects.xlf',
		)
	);


}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'HFWU Redirects');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_hfwuredirects_domain_model_redirects', 'EXT:hfwu_redirects/Resources/Private/Language/locallang_csh_tx_hfwuredirects_domain_model_redirects.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_hfwuredirects_domain_model_redirects');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_hfwuredirects_domain_model_redirectcalls', 'EXT:hfwu_redirects/Resources/Private/Language/locallang_csh_tx_hfwuredirects_domain_model_redirectcalls.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_hfwuredirects_domain_model_redirectcalls');

$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX']['HfwuRedirects::aliasList'] = array(
	'callbackMethod' => 'HFWU\HfwuRedirects\Utility\AjaxDispatcher->dispatchAliasList',
	'csrfTokenCheck' => true
);

$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX']['HfwuRedirects::deleteRedirectEntry'] = array(
	'callbackMethod' => 'HFWU\HfwuRedirects\Utility\AjaxDispatcher->dispatchDeleteRedirectEntry',
	'csrfTokenCheck' => true
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['hfwu_redirects'] = 'HFWU\HfwuRedirects\Hooks\DataHandler';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/class.db_list.inc']['makeQueryArray']['hfwu_redirects'] = 'HFWU\HfwuRedirects\Hooks\DataHandler';

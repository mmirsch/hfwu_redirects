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
			'Redirects' => 'aliasList',
		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_redirects.xlf',
		)
	);

}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_hfwuredirects_domain_model_redirects', 'EXT:hfwu_redirects/Resources/Private/Language/locallang_csh_tx_hfwuredirects_domain_model_redirects.xlf');
//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_hfwuredirects_domain_model_redirects');

$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX']['HfwuRedirects::aliasList'] = array(
	'callbackMethod' => 'HFWU\HfwuRedirects\Utility\AjaxDispatcher->dispatchAliasList',
	'csrfTokenCheck' => true
);

$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX']['HfwuRedirects::deleteRedirectEntry'] = array(
	'callbackMethod' => 'HFWU\HfwuRedirects\Utility\AjaxDispatcher->dispatchDeleteRedirectEntry',
	'csrfTokenCheck' => true
);

$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX']['HfwuRedirects::showQrCodeAjax'] = array(
	'callbackMethod' => 'HFWU\HfwuRedirects\Utility\AjaxDispatcher->dispatchShowQrCode',
	'csrfTokenCheck' => true
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['hfwu_redirects'] = 'HFWU\\HfwuRedirects\\Hooks\\DataHandler';

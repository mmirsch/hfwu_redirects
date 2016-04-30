<?php
namespace HFWU\HfwuRedirects\Hooks;

use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Dbal\Database\DatabaseConnection;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\PageGenerator;

class RedirectHandling {

	protected static $errorpage='404';
	protected static $testQueryString='redirect_test=1';
	protected static $rootPid=1;
	protected static $redirectTable = 'tx_hfwuredirects_domain_model_redirects';
	protected static $startTime;

	/** @var DatabaseConnection $databaseConnection */
	protected $databaseConnection;
	/** @var TypoScriptFrontendController $tsFeController */
	protected $tsFeController;


	public function __construct() {
		$this->databaseConnection = $GLOBALS['TYPO3_DB'];
	}

	/**
	 * Checks if Redirect is necessary
	 *
	 * @param array $params
	 * @param TypoScriptFrontendController $tsFeController
	 * @return void
	 */
	function handleRedirects(&$params, &$tsFeController) {
		$this->tsFeController = $tsFeController;
		self::$startTime = microtime();
		$siteScript = $tsFeController->siteScript;
		$scriptParts = explode('?',$siteScript);
		$currentUrl = $scriptParts[0];
		$currentUrl = preg_replace('{^/|/$}','',$currentUrl);
		if (strpos($currentUrl,'/') !== false) {
			GeneralUtility::devLog('Alias: ' . $currentUrl . ';' .
				'ERROR: Wrong ALIAS (contains "/") ;' .
				'Duration: ' . $duration = microtime() - self::$startTime,
				'PageNotFoundHandling');
			return;
		}

		/*
		 * Check if page is called by backend for testing
		 */
		$queryString = $scriptParts[1];
		$testMode =  (self::$testQueryString === $queryString);

		$redirectTypo3PageId = -1;
		$redirectCount = 0;
		$redirectUid = -1;
		$redirectUrl = $this->getRedirectUrl($currentUrl, $redirectUid, $redirectTypo3PageId, $redirectCount);

		if (empty($redirectUrl)) {
			GeneralUtility::devLog('Alias: ' . $currentUrl . ';' .
				'ResultUrl: EMPTY! ;' .
				'Duration: ' . $duration = microtime() - self::$startTime,
				'PageNotFoundHandling');
			return;
		}

		if (!$testMode) {
			$this->databaseConnection->exec_UPDATEquery(self::$redirectTable, 'uid=' . (int)$redirectUid, array(
				'redirect_count' => (int)$redirectCount + 1
			));
		}

		GeneralUtility::devLog('Alias: ' . $currentUrl . ';' .
			'ResultUrl: ' . $redirectUrl . ';' .
			'Duration: ' . $duration = microtime() - self::$startTime,
			'PageNotFoundHandling');

		HttpUtility::redirect($redirectUrl,HttpUtility::HTTP_STATUS_301);
	}

	/*
 	 * @param string $shortUrl
 	 * @param string $redirectUid
 	 * @param string $redirectTypo3PageId
 	 * @param string $redirectCount
	 * return void
	 */
	public function getRedirectUrl($shortUrl, &$redirectUid, &$redirectTypo3PageId, &$redirectCount) {
		$redirectUrl = '';
		$redirectTypo3PageId = '';
		$queryString = '';
		$redirectCount = 0;
		$lang = 0;
		// Check redirect-db for existing entry
		$resultRedirects = $this->databaseConnection->sql_query(
			'SELECT uid,url_complete,url_hash,search_word,page,redirect_count,sys_language_uid FROM ' . self::$redirectTable .
			' WHERE deleted=0 AND hidden=0 AND short_url=' . $this->databaseConnection->fullQuoteStr($shortUrl, self::$redirectTable)
		);
		if (FALSE !== ($dataRedirects = $this->databaseConnection->sql_fetch_assoc($resultRedirects))) {
			if (!empty($dataRedirects['url_complete'])) {
				$redirectUrl = $dataRedirects['url_complete'];
			} else {
				$redirectTypo3PageId = (int)$dataRedirects['page'];
				$queryString = '';
				if (!empty($dataRedirects['search_word'])) {
					$searchword = str_replace(' ', '+', strtolower($dataRedirects['search_word']));
					$queryString .= 'q=' . $searchword;
				}
				if (!empty($dataRedirects['url_hash'])) {
					$queryString .= '#' . $dataRedirects['url_hash'];
				}
				$redirectCount = $dataRedirects['redirect_count'];
				$lang = $dataRedirects['sys_language_uid'];
				$redirectUid = $dataRedirects['uid'];
			}
		}
		$this->databaseConnection->sql_free_result($resultRedirects);
		if (empty($redirectUrl)) {
			if (empty($redirectTypo3PageId)) {
				return;
			}
			$redirectUrl = $this->getRedirectUrlViaTypolink($redirectTypo3PageId, $lang);
			if (!empty($queryString) && !empty($redirectUrl)) {
				if (strpos('?',$redirectUrl) === false) {
					$redirectUrl .= '?' . $queryString;
				} else {
					$redirectUrl .= '&' . $queryString;
				}
			}
		}
		return $redirectUrl;
	}

	/**
	 *
	 * @param int $redirectTypo3PageId
	 * @param int $lang
	 * @return  void
	 */
	protected function getRedirectUrlViaTypolink($redirectTypo3PageId, $lang) {
		try {
			$this->initTSFE($redirectTypo3PageId);
		} catch (Exception $e) {
			return '';
		}
		$conf = array(
			'parameter' => $redirectTypo3PageId,
			'forceAbsoluteUrl' => 1,
		);
		if ($lang!=0) {
			$conf['additionalParams'] = '&L=' . $lang;
		}
		$redirectUrl = $this->tsFeController->cObj->typoLink_URL($conf);
		return $redirectUrl;
	}

	/**
	 * Initializes TSFE
	 *
	 * @param int $pageId
	 * @return  Exception
	 */
	protected function initTSFE($pageId) {
		try {
			$this->tsFeController->determineId();
			$this->tsFeController->initTemplate();
			$this->tsFeController->getFromCache();
			$this->tsFeController->getConfigArray();
			$this->tsFeController->settingLanguage();
			$this->tsFeController->settingLocale();
			$this->tsFeController->newCObj();
		} catch (Exception $e) {
			return $e;
		}
	}

	/**
	 *
	 * @param int $redirectTypo3PageId
	 * @param int $lang
	 * @return  void
	 */
	protected function getRedirectUrlViaId($redirectTypo3PageId, $lang) {
		$redirectUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/index.php?id=' . $redirectTypo3PageId;

		if (ExtensionManagementUtility::isLoaded('realurl')) {
			$resultRealUrl = $this->databaseConnection->sql_query(
				'SELECT pagepath FROM tx_realurl_pathcache' .
				' WHERE page_id=' . (int)$redirectTypo3PageId . ' AND language_id=' . (int)$lang
			);
			if (FALSE !== ($dataRealUrl = $this->databaseConnection->sql_fetch_assoc($resultRealUrl))) {
				$redirectUrl = $dataRealUrl['pagepath'];
			}
		}
		return $redirectUrl;
	}


}
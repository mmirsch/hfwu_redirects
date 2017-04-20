<?php
namespace HFWU\HfwuRedirects\Hooks;

use HFWU\HfwuRedirects\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Dbal\Database\DatabaseConnection;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\PageGenerator;
use TYPO3\CMS\Frontend\Page\PageRepository;
use TYPO3\CMS\Frontend\Utility\EidUtility;

class RedirectHandling {

	protected static $protocol;
	protected static $testQueryString='redirect_test=1';
	protected static $rootPid=1;
	protected static $redirectTable = 'tx_hfwuredirects_domain_model_redirects';
	protected $host;
	protected $requestUri;
	protected $absoluteRequestUri;
	protected $queryString;
	protected $extensionConfiguration;

	/** @var DatabaseConnection $databaseConnection */
	protected static $databaseConnection;

	public function __construct() {
		$GLOBALS['TYPO3_DB']->connectDB();
		self::$databaseConnection = $GLOBALS['TYPO3_DB'];
		$extensionConfiguration = ExtensionUtility::getExtensionConfig();
		if (isset($extensionConfiguration['http_protocol'])) {
			self::$protocol = $extensionConfiguration['http_protocol'];
		} else {
			self::$protocol = 'http://';
		}
	}

	/**
	 * Checks if Redirect is necessary
	 *
	 * @param array $params1
	 * @param array $params2
	 * @return void
	 */
	function handleRedirects($params1, $params2) {
		$this->initUrlParts();
		if (strpos($this->requestUri,'/') !== false) {
			return;
		}

		$redirectTypo3PageId = -1;
		$redirectCount = 0;
		$redirectUid = -1;
		$redirectUrl = $this->getRedirectUrl($this->requestUri, $redirectUid, $redirectTypo3PageId, $redirectCount);

		if (empty($redirectUrl)) {
			return;
		}

		if (self::identicalUris($redirectUrl,$this->absoluteRequestUri)) {
			return;
		}
		/*
		 * Check if page is called by backend for testing
		 */
		$testMode =  (self::$testQueryString === $this->queryString);
		if (!$testMode) {
			self::$databaseConnection->exec_UPDATEquery(self::$redirectTable, 'uid=' . (int)$redirectUid, array(
				'redirect_count' => (int)$redirectCount + 1
			));
		}
		HttpUtility::redirect($redirectUrl,HttpUtility::HTTP_STATUS_301);
	}

	protected function initUrlParts() {
		$siteScript = $_SERVER['REQUEST_URI'];
		$scriptParts = explode('?',$siteScript);
		$currentUrl = $scriptParts[0];
		$this->requestUri = preg_replace('{^/|/$}','',$currentUrl);
		$this->queryString = $scriptParts[1];
		$this->host = $_SERVER['HTTP_HOST'];
		$this->absoluteRequestUri = self::$protocol . $this->host . '/' . $this->requestUri;
	}

	/*
	 * checks if two urls are identiccal ignoring host, querystring and trailing slashes
	 *
 	 * @param string $url1
 	 * @param string $url2
	 * return bool
	 */
	public static function identicalUris($url1 , $url2) {
		/*
		 * eventually remove host
		 */

		$extensionConfiguration = ExtensionUtility::getExtensionConfig();
		if (isset($extensionConfiguration['http_protocol'])) {
			self::$protocol = $extensionConfiguration['http_protocol'];
		} else {
			self::$protocol = 'http://';
		}


		$host = self::$protocol .  $_SERVER['HTTP_HOST'] . '/';
		$url1 = str_replace($host, '', $url1);
		$url2 = str_replace($host, '', $url2);
		/*
		 * remove querystring and trailing slashes
		 */
		$url1 = strtolower(trim(explode('?',$url1)[0],' /'));
		$url2 = strtolower(trim(explode('?',$url2)[0],' /'));
		return strcmp($url1, $url2) === 0;
	}

	/*
	 * checks if redirect for pageid results in given url
	 *
 	 * @param int $pageId
 	 * @param string $originalUrl
	 * return bool
	 */

	public static function redirectLoop($pageId, $originalUrl, $lang=0) {
		if (empty($pageId) || empty($originalUrl)) {
			return false;
		}
		$redirectUrl = self::getRedirectUrlViaTypolink($pageId, $lang);
		return self::identicalUris($originalUrl, $redirectUrl);
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
		$resultRedirects = self::$databaseConnection->sql_query(
			'SELECT uid,url_complete,search_word,page,redirect_count,sys_language_uid FROM ' . self::$redirectTable .
			' WHERE deleted=0 AND hidden=0 AND short_url=' . self::$databaseConnection->fullQuoteStr($shortUrl, self::$redirectTable)
		);
		if (FALSE !== ($dataRedirects = self::$databaseConnection->sql_fetch_assoc($resultRedirects))) {
			if (!empty($dataRedirects['url_complete'])) {
				$redirectUrl = $dataRedirects['url_complete'];
			} else {
				$redirectTypo3PageId = (int)$dataRedirects['page'];
				if (empty($redirectTypo3PageId)) {
					return;
				}
				$queryString = '';
				if (!empty($dataRedirects['search_word'])) {
					$searchword = str_replace(' ', '+', strtolower($dataRedirects['search_word']));
					$queryString .= 'q=' . $searchword;
				}
				$lang = $dataRedirects['sys_language_uid'];
				$redirectUrl = self::getRedirectUrlViaTypolink($redirectTypo3PageId, $lang);
				if (!empty($queryString) && !empty($redirectUrl)) {
					if (strpos('?',$redirectUrl) === false) {
						$redirectUrl .= '?' . $queryString;
					} else {
						$redirectUrl .= '&' . $queryString;
					}
				}
			}
			$redirectCount = $dataRedirects['redirect_count'];
			$redirectUid = $dataRedirects['uid'];
		}
		self::$databaseConnection->sql_free_result($resultRedirects);
		return $redirectUrl;
	}

	/**
	 *
	 * @param int $redirectTypo3PageId
	 * @param int $lang
	 * @return  void
	 */
	public static function getRedirectUrlViaTypolink($redirectTypo3PageId, $lang=0) {
		try {

// start timetracking, used by several methods in TSFE
			$GLOBALS['TT'] =  GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\TimeTracker\\NullTimeTracker');
			$GLOBALS['TT']->start();

// instanciate TSFE object
			/** @var TypoScriptFrontendController $TSFE */
			$TSFE = GeneralUtility::makeInstance(
				\TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::class,
				$GLOBALS['TYPO3_CONF_VARS'], $redirectTypo3PageId, 0
			);

// global object has to be instantiated because it is referenced by several methods in TSFE
			$GLOBALS['TSFE'] = &$TSFE;

// TCA should already be cached and has to be loaded
			EidUtility::initTca();

// fe-user is used for access checks
			$TSFE->initFEuser();

// checks if the page is in the domain and if its accessible and set several instance variables
			$TSFE->fetch_the_id();

// init template
			$TSFE->initTemplate();


// set different kind of configuration values
			$TSFE->getConfigArray();

//	creates an instance of ContentObjectRenderer to be used for calling typolink
			$TSFE->newCObj();

			$conf = array(
				'parameter' => $redirectTypo3PageId,
				'forceAbsoluteUrl' => 1,
			);
			if ($lang!=0) {
				$conf['additionalParams'] = '&L=' . $lang;
			}

// create typolink
			$redirectUrl = $TSFE->cObj->typoLink_URL($conf);
			return $redirectUrl;
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
		$redirectUrl = self::$protocol . $_SERVER['HTTP_HOST'] . '/index.php?id=' . $redirectTypo3PageId;
		return $redirectUrl;
	}


}
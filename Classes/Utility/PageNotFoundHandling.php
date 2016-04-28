<?php


class PageNotFoundHandling {

	protected static $errorpage='404';
	protected static $testStringArg='redirect_test';
	protected $objectManager;

	function initTSFE($id = 1) {
		$GLOBALS['TSFE']->determineId();
		$GLOBALS['TSFE']->initTemplate();
		$GLOBALS['TSFE']->getConfigArray();
		$GLOBALS['TSFE']->sys_page = $this->objectManager->get('TYPO3\CMS\Frontend\Page\PageRepository');

		if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('realurl')) {
			$rootline = \TYPO3\CMS\Backend\Utility\BackendUtility::BEgetRootLine($id);
			$host = \TYPO3\CMS\Backend\Utility\BackendUtility::firstDomainRecord($rootline);
			$_SERVER['HTTP_HOST'] = $host;
		}
	}

	function pageNotFound($params,$tsfeObj) {
		$url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . self::$errorpage;

		/**@var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
		$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
		/**@var $redirectsRepository \HFWU\HfwuRedirects\Domain\Repository\RedirectsRepository */
		$redirectsRepository = $this->objectManager->get('HFWU\HfwuRedirects\Domain\Repository\RedirectsRepository');
		$currentUrl = $params['currentUrl'];
		/*
		 * Check if page is called by backend for testing
		 */
		$testMode = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GET(self::$testStringArg));
		/*
		 * Remove test-arg from url if in testmode
		 */
		if ($testMode) {
			$currentUrl = preg_replace('{\?' . self::$testStringArg . '=1}','',$currentUrl);
		}
		$currentUrl = preg_replace('{^/|/$}','',$currentUrl);
		$this->initTSFE();
		/**@var $redirectResult \TYPO3\CMS\Extbase\Persistence\QueryResultInterface */
		$redirectResult = $redirectsRepository->findByShortUrl($currentUrl);
		if ($redirectResult->count()>0) {
			/**@var $redirect \HFWU\HfwuRedirects\Domain\Model\Redirects */
			$redirect = $redirectResult->getFirst();
			if (!empty($redirect)) {
				$url = $redirect->getUrlComplete();
				if (empty($url)) {

					$pageId = $redirect->getPage()->getUid();
					$conf = array(
						'parameter' => $pageId,
						'forceAbsoluteUrl' => 1,
					);
					/** @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $cObj */
					$cObj = $this->objectManager->get('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
					$url = $cObj->typolink_URL($conf);
					/*
					 * handle optional search string for opening accordion elements
					 */
					$searchWord = $redirect->getSearchWord();
					if (!empty($searchWord)) {
						$searchword = str_replace(' ','+',strtolower($searchWord));
						if (strpos('?',$url) === false) {
							$url .= '?q=' . $searchword;
						} else {
							$url .= '&q=' . $searchword;
						}
					}
					/*
					 * handle optional hash for opening accordion elements or do other things
					 */
					$urlHash = $redirect->getUrlHash();
					if (!empty($urlHash)) {
						$url .= '#' .  $urlHash;
					}
				}
				/*
				 * Store redirect call for statistics if not in test mode
				 */
				if (!$testMode) {
					$redirect->storeRedirectCall();
					$redirectsRepository->update($redirect);
					$this->objectManager->get('TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface')->persistAll();
				}
			}
		}
/*
 * do the redirect
 */
		\TYPO3\CMS\Core\Utility\HttpUtility::redirect($url,\TYPO3\CMS\Core\Utility\HttpUtility::HTTP_STATUS_301);
	}
}
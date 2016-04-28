<?php
namespace HFWU\HfwuRedirects\Hooks;

class RedirectHandling {

	protected $objectManager;

	function initTSFE() {
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

	/**
	 * Checks if Redirect is necessary
	 *
	 * @param array $params
	 * @param \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $tsFeController
	 * @return void
	 */
	function handleRedirects(&$params, &$tsFeController) {
		/**@var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
		$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
		/**@var $redirectsRepository \HFWU\HfwuRedirects\Domain\Repository\RedirectsRepository */
		$redirectsRepository = $this->objectManager->get('HFWU\HfwuRedirects\Domain\Repository\RedirectsRepository');
		$this->initTSFE();
		$currentUrl = $_SERVER['REQUEST_URI'];
		$currentUrl = preg_replace('{^/|/$}','',$currentUrl);

		/**@var $redirectResult \TYPO3\CMS\Extbase\Persistence\QueryResultInterface */
		$redirectResult = $redirectsRepository->findByShortUrl($currentUrl);
		if ($redirectResult->count()>0) {
			/**@var $redirect \HFWU\HfwuRedirects\Domain\Model\Redirects */
			$redirect = $redirectResult->getFirst();
			if (!empty($redirect)) {
				$url = $redirect->getUrlComplete();
				if (empty($url)) {

					$pageId = $redirect->getPageId();
					$conf = array(
						'parameter' => $pageId,
					);
					$searchWord = $redirect->getSearchWord();
					$urlHash = $redirect->getUrlHash();
					$additionalParams = '';
					/** @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $cObj */
					$cObj = $this->objectManager->get('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
					$url = $cObj->typolink_URL($conf);

					if (!empty($searchWord)) {
						$searchword = str_replace(' ','+',strtolower($searchWord));
						if (strpos('?',$url) === false) {
							$url .= '?q=' . $searchword;
						} else {
							$url .= '&q=' . $searchword;
						}
					}
					if (!empty($urlHash)) {
						$url .= '#' .  $urlHash;
					}
				}
				$cleanUrl = preg_replace('{^/|/$}','',$url);
				if ($currentUrl != $cleanUrl) {
					/**@var $redirectCallsRepository \HFWU\HfwuRedirects\Domain\Repository\RedirectCallsRepository */
					$redirectCallsRepository = $this->objectManager->get('HFWU\HfwuRedirects\Domain\Repository\RedirectCallsRepository');
					$redirectCallsRepository->storeRedirectCall($redirect);
					\TYPO3\CMS\Core\Utility\HttpUtility::redirect($url,\TYPO3\CMS\Core\Utility\HttpUtility::HTTP_STATUS_301);
				}
			}
		}
	}
}
<?php
namespace HFWU\HfwuRedirects\Utility;

use HFWU\HfwuRedirects\Domain\Repository\RedirectsRepository;
use TYPO3\CMS\Core\Http\AjaxRequestHandler;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use\HFWU\HfwuRedirects\Utility\Qr\QrCode;

class AjaxDispatcher {

	/** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
	protected $view;

	/**
	 * Request all redirects that match the filter given in argument "filter"
	 */
	public function dispatchDeleteRedirectEntry() {
		$getVars = GeneralUtility::_GET();
		$uid = $getVars['uid'];

		/** @var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
		$objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

		/** @var \HFWU\HfwuRedirects\Domain\Repository\RedirectsRepository $redirectsRepository */
		$redirectsRepository = $objectManager->get('HFWU\\HfwuRedirects\\Domain\\Repository\\RedirectsRepository');

		$redirectsRepository->removeEntry($uid);
		$objectManager->get('TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface')->persistAll();

		exit();
	}


	/**
	 * Request all redirects that match the filter given in argument "filter"
	 *
	 * @param array $params
	 * @param \TYPO3\CMS\Core\Http\AjaxRequestHandler $ajaxObj
	 * @return void
	 */
	public function dispatchShowQrCode($params = array(), AjaxRequestHandler &$ajaxObj = NULL)
	{
		$getVars = GeneralUtility::_GET();
		$uid = $getVars['uid'];
		/** @var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
		$objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

		/** @var \HFWU\HfwuRedirects\Domain\Repository\RedirectsRepository $redirectsRepository */
		$redirectsRepository = $objectManager->get('HFWU\\HfwuRedirects\\Domain\\Repository\\RedirectsRepository');

		/** @var \HFWU\HfwuRedirects\Domain\Model\Redirects $redirect */
		$redirect = $redirectsRepository->findByUid($uid);
		if (!empty($redirect)) {

			$shortUrl = $redirect->getShortUrl();
			$title = $redirect->getTitle();
			$siteUrl = GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
			$completeUrl = $siteUrl . $shortUrl;
			$extensionConfiguration = ExtensionUtility::getExtensionConfig();
			if (isset($extensionConfiguration['qr_imgsize'])) {
				$size = $extensionConfiguration['qr_imgsize'];
			} else {
				$size = 400;
			}
			if (isset($extensionConfiguration['qr_padding'])) {
				$padding = $extensionConfiguration['qr_padding'];
			} else {
				$padding = 40;
			}
			if (isset($extensionConfiguration['qr_fgcolor'])) {
				$foregroundColor = $extensionConfiguration['qr_fgcolor'];
			} else {
				$foregroundColor = '00308e';
			}
			$fgColor = QrCode::convertHexToRgbColor($foregroundColor);
			$bgColor = QrCode::convertHexToRgbColor('ffffff');

			if ($size > 300) {
				$errorCorrection = 'high';
			} elseif ($size > 100) {
				$errorCorrection = 'medium';
			} else {
				$errorCorrection = 'low';
			}

			/** @var $qrCode \HFWU\HfwuRedirects\Utility\Qr\QrCode */
			$qrCode = $objectManager->get('HFWU\HfwuRedirects\Utility\Qr\QrCode');

			$qrCode->setText($completeUrl)
				->setSize($size)
				->setPadding($padding)
				->setErrorCorrection($errorCorrection)
				->setForegroundColor($fgColor)
				->setBackgroundColor($bgColor);
			$image = $qrCode->get($qrCode::IMAGE_TYPE_PNG);

			$filename = 'qrcode_' . $title . '.png';
			/** @var $filenameCleaner \TYPO3\CMS\Core\Resource\Driver\LocalDriver */
			$filenameCleaner = $objectManager->get('TYPO3\CMS\Core\Resource\Driver\LocalDriver');
			$filename = $filenameCleaner->sanitizeFileName($filename);

			/**@var \TYPO3\CMS\Extbase\Mvc\Web\Response $response */
			$response = $objectManager->get('TYPO3\CMS\Extbase\Mvc\Web\Response');
			$response->setHeader('Cache-control', 'public', TRUE);
			$response->setHeader('Content-Description', 'File transfer', TRUE);
			$response->setHeader('Content-Disposition', 'attachment; filename=' . $filename, TRUE);
			$response->setHeader('Content-Length', strlen($image), TRUE);

			$response->setHeader('Content-Type', 'image/png', TRUE);
			$response->setHeader('Content-Transfer-Encoding', 'binary', TRUE);
			$response->sendHeaders();
			print($image);
			exit();
		}
	}

		/**
	 * Request all redirects that match the filter given in argument "filter"
	 *
	 * @param array $params
	 * @param \TYPO3\CMS\Core\Http\AjaxRequestHandler $ajaxObj
	 * @return void
	 */
	public function dispatchAliasList($params = array(), AjaxRequestHandler &$ajaxObj = NULL)
	{
		$getVars = GeneralUtility::_GET();
		$pid = $getVars['pid'];
		$filter = $getVars['filter'];
		$limit = $getVars['limit'];
		$filterTypes = $getVars['filter_types'];
		$siteUrl = $getVars['site_url'];
		$admin = BackendUtility::isBackendAdmin();
		/** @var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

		/** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
		$view = $objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
		$view->setTemplatePathAndFilename(ExtensionManagementUtility::extPath('hfwu_redirects') . 'Resources/Private/Templates/Redirects/AliasListAjax.html');
//		$view->setTemplateRootPaths(array(ExtensionManagementUtility::extPath('hfwu_redirects') . 'Resources/Private/Templates'));
		$view->setPartialRootPath(ExtensionManagementUtility::extPath('hfwu_redirects') . 'Resources/Private');
//		$view->setTemplate('Redirects/AliasListAjax.html');

		/** @var \HFWU\HfwuRedirects\Domain\Repository\RedirectsRepository $redirectsRepository */
		$redirectsRepository = $objectManager->get('HFWU\\HfwuRedirects\\Domain\\Repository\\RedirectsRepository');
		$redirects = $redirectsRepository->findRedirectsWithSearchWord($filter, $pid, $limit, $admin, $filterTypes);
		if ($redirects->count() > 0) {
			GeneralViewUtility::assignViewArguments($view, $siteUrl, $filter, $pid, $limit, $admin, $filterTypes, $redirects);
		}
		$output = $view->render();
		echo $output;
		exit();
	}


}


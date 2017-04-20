<?php
namespace HFWU\HfwuRedirects\Controller;

use HFWU\HfwuRedirects\Domain\Repository\RedirectsRepository;
use HFWU\HfwuRedirects\Domain\Model\Redirects;
use HFWU\HfwuRedirects\Utility\BackendUtility;
use HFWU\HfwuRedirects\Utility\ExtensionUtility;
use HFWU\HfwuRedirects\Utility\GeneralViewUtility;

use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;


/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/


/**
 * RedirectsController
 */
class RedirectsController extends ActionController
{

	/**
	 * redirectsRepository
	 *
	 * @var \HFWU\HfwuRedirects\Domain\Repository\RedirectsRepository
	 * @inject
	 */
	protected $redirectsRepository = NULL;

	/**
	 * redirectsRepository
	 *
	 * @var $currentExtensionConfig array
	 */
	protected $currentExtensionConfig = NULL;

	public function __construct()
	{
		$this->currentExtensionConfig = ExtensionUtility::getExtensionConfig();
	}

	/**
	 * action AliasList
	 *
	 * @return void
	 */
	public function aliasListAction()
	{
		$this->assignListView();
	}

	/**
	 * action AliasListAjax
	 *
	 * @return void
	 */
	public function aliasListAjaxAction()
	{
		$this->assignListView();
	}

	/**
	 * action showQrCode
	 *
	 * @return void
	 */
	public function showQrCodeAction()
	{
		$uid = $this->getArgument('uid');

		/** @var \HFWU\HfwuRedirects\Domain\Model\Redirects $redirect */
		$redirect =  $this->redirectsRepository->findByUid($uid);
		if (!empty($redirect)) {
			$shortUrl = $redirect->getShortUrl();
			$title = $redirect->getTitle();
			$siteUrl = GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
			$completeUrl = $siteUrl . $shortUrl;
			if (isset($this->currentExtensionConfig['qr_imgsize'])) {
				$size = $this->currentExtensionConfig['qr_imgsize'];
			} else {
				$size = 400;
			}
			if (isset($this->currentExtensionConfig['qr_padding'])) {
				$padding = $this->currentExtensionConfig['qr_padding'];
			} else {
				$padding = 40;
			}
			if (isset($this->currentExtensionConfig['qr_fgcolor'])) {
				$foregroundColor = $this->currentExtensionConfig['qr_fgcolor'];
			} else {
				$foregroundColor = '00308e';
			}

			$fgColor = \HFWU\HfwuRedirects\Utility\Qr\QrCode::convertHexToRgbColor($foregroundColor);
			$bgColor = \HFWU\HfwuRedirects\Utility\Qr\QrCode::convertHexToRgbColor('ffffff');

			if ($size > 300) {
				$errorCorrection = 'high';
			} elseif ($size > 100) {
				$errorCorrection = 'medium';
			} else {
				$errorCorrection = 'low';
			}

			/** @var $qrCode \HFWU\HfwuRedirects\Utility\Qr\QrCode */
			$qrCode = $this->objectManager->get(\HFWU\HfwuRedirects\Utility\Qr\QrCode::class);

			$qrCode->setText($completeUrl)
				->setSize($size)
				->setPadding($padding)
				->setErrorCorrection($errorCorrection)
				->setForegroundColor($fgColor)
				->setBackgroundColor($bgColor);
			$image = $qrCode->get($qrCode::IMAGE_TYPE_PNG);

			$filename = 'qrcode_' . $title . '.png';
			/** @var $filenameCleaner \TYPO3\CMS\Core\Resource\Driver\LocalDriver */
			$filenameCleaner = $this->objectManager->get(\TYPO3\CMS\Core\Resource\Driver\LocalDriver::class);
			$filename = $filenameCleaner->sanitizeFileName($filename);

			$this->response->setHeader('Cache-control', 'public', TRUE);
			$this->response->setHeader('Content-Description', 'File transfer', TRUE);
			$this->response->setHeader('Content-Disposition', 'attachment; filename=' . $filename, TRUE);
			$this->response->setHeader('Content-Length', strlen($image), TRUE);

			$this->response->setHeader('Content-Type', 'image/png', TRUE);
			$this->response->setHeader('Content-Transfer-Encoding', 'binary', TRUE);
			$this->response->sendHeaders();
			print($image);
			exit();
		}
	}

	/**
	 * action deleteEntry
	 *
	 * @return void
	 */
	public function deleteEntryAction()
	{
		$uid = $this->getArgument('uid');
		$this->redirectsRepository->removeEntry($uid);
		$this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface::class)->persistAll();
		exit();
	}


	/**
	 * assign view depending qrlist flag
	 * @param bool $qrRedirectsOnly
	 * @return void
	 */
	public function assignListView()
	{
		$pid = 0;

		$admin = BackendUtility::isBackendAdmin();
		if ($admin) {
			/*
			 * For backend admins a sysfolder has to be set in extension configuration.
			 * If this is not done, show flashmessage and quit.
			 */
			if (empty($this->currentExtensionConfig['sysfolder_redirects'])) {
				$this->addFlashMessage(
					LocalizationUtility::translate('error.no_sysfolder_redirects_set', 'hfwu_redirects'), '', AbstractMessage::ERROR
				);
				return;
			}
			$pid = $this->currentExtensionConfig['sysfolder_redirects'];
			$filterTypes = $this->getArgument('filter_types');
		} else {
			/*
			 * For editors the pid sent as get/post parameter will be used.
			 * If empty the current system folder will be used.
			 */
			$currentSysfolder = $this->getArgument('pid');
			if (empty($currentSysfolder)) {
				$currentSysfolder = GeneralUtility::_GP('id');
			}

			if (!empty($currentSysfolder)) {
				/**@var \TYPO3\CMS\Frontend\Page\PageRepository $pageRepository */
				$pageRepository = $this->objectManager->get(\TYPO3\CMS\Frontend\Page\PageRepository::class);
				/**@var array $pageData */
				$pageData = $pageRepository->getPage($currentSysfolder);
				if (intval($pageData['doktype']) == 254) {
					$pid = $currentSysfolder;
				}
			}
			if ($pid === 0) {
				$this->addFlashMessage(
					LocalizationUtility::translate('error.no_sysfolder_redirects_chosen', 'hfwu_redirects'), '', AbstractMessage::ERROR
				);
				return;
			}
		}
		$limit = $this->getArgument('limit');
		if (empty($limit)) {
			$limit = $this->currentExtensionConfig['limit'];
		}

		if (!$pid) {
			$this->addFlashMessage(
				LocalizationUtility::translate('error.no_redirects_pid', 'hfwu_redirects'), '', AbstractMessage::ERROR
			);
		} else {
			$filter = $this->getArgument('filter');
			$returnUrl = BackendUtility::getReturnUrl();
			/** @var QueryResultInterface $redirects */
			$redirects = $this->redirectsRepository->findRedirectsWithSearchWord($filter, $pid, $limit, $admin, $filterTypes);
			$siteUrl = 'http://' . GeneralUtility::getIndpEnv('HTTP_HOST');
			$pluginName = $this->request->getPluginName();
			$extensionName = $this->request->getControllerExtensionName();
			$argumentKey = strtolower('tx_' . $extensionName . '_' . $pluginName);
			GeneralViewUtility::assignViewArguments($this->view, $argumentKey, $siteUrl, $returnUrl, $filter, $pid, $limit, $admin, $filterTypes, $redirects);
		}
	}

	/**
	 * get search filter argument
	 *
	 * @return string
	 */
	protected function getArgument($key)
	{
		$getArguments = $this->request->getArguments();
		$filter = '';
		if (is_array($getArguments)) {
				if (isset($getArguments[$key])) {
				$filter = $getArguments[$key];
			}

		}
		return $filter;
	}


}
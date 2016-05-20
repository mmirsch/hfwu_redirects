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
		$this->assignView();
	}

	/**
	 * assign view depending qrlist flag
	 * @param bool $qrRedirectsOnly
	 * @return void
	 */
	public function assignView()
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
				$pageRepository = $this->objectManager->get('TYPO3\CMS\Frontend\Page\PageRepository');
				/**@var array $pageData */
				$pageData = $pageRepository->getPage($currentSysfolder);
				if ($pageData['doktype'] === "254") {
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
			GeneralViewUtility::assignViewArguments($this->view, $siteUrl, $returnUrl, $filter, $pid, $limit, $admin, $filterTypes, $redirects);
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
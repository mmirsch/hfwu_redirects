<?php
namespace HFWU\HfwuRedirects\Controller;

use HFWU\HfwuRedirects\Domain\Repository\RedirectsRepository;
use HFWU\HfwuRedirects\Domain\Model\Redirects;

use HFWU\HfwuRedirects\Utility\BackendUtility;
use HFWU\HfwuRedirects\Utility\GeneralViewUtility;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;


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
class RedirectsController extends ActionController {

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

    public function __construct() {
        $this->currentExtensionConfig = BackendUtility::getExtensionConfig();
    }

     /**
     * action AliasList
     *
     * @return void
     */
    public function aliasListAction() {
        $this->assignView();
    }

    /**
     * assign view depending qrlist flag
     * @param bool $qrRedirectsOnly
     * @return void
     */
    public function assignView() {
        $admin = BackendUtility::isBackendAdmin();
        if ($admin) {
            /*
             * For backend admins a sysfolder has to be set in extension configuration.
             * If this is not done, show flashmessage and quit.
             */
            if (empty($this->currentExtensionConfig['sysfolder_redirects'])) {
                $this->addFlashMessage(
                  LocalizationUtility::translate('error_no_sysfolder_redirects_set', 'hfwu_redirects'), '', AbstractMessage::ERROR
                );
                return;
            }
            $pid = $this->currentExtensionConfig['sysfolder_redirects'];
        } else {
            /*
             * For editors the pid sent as get/post parameter will be used.
             * If empty the current system folder will be used.
             */
            $pid = GeneralUtility::_GP('id');
            if (empty($pid)) {
                $pid = $this->getArgument('pid');
            }
        }
        $limit = $this->getArgument('limit');
        if (empty($limit)) {
            $limit = $this->currentExtensionConfig['limit'];
        }
        $filterTypes = $this->getArgument('filter_types');
        if (!$pid) {
            $this->addFlashMessage(
              LocalizationUtility::translate('error_no_redirects_pid', 'hfwu_redirects'), '', AbstractMessage::ERROR
            );
        } else {
            $filter = $this->getArgument('filter');
            /** @var QueryResultInterface $redirects */
            $redirects = $this->redirectsRepository->findRedirectsWithSearchWord($filter, $pid, $limit, $admin, $filterTypes);
            if ($redirects->count() > 0) {
                $siteUrl = 'http://' . GeneralUtility::getIndpEnv('HTTP_HOST');
                GeneralViewUtility::assignViewArguments($this->view, $siteUrl, $filter, $pid, $limit, $admin, $filterTypes, $redirects);
            } else {
                $this->addFlashMessage(
                  LocalizationUtility::translate('error_no_redirect_entries','hfwu_redirects'), '', AbstractMessage::INFO
                );
            }
        }
    }

    /**
     * get search filter argument
     *
     * @return string
     */
    protected function getArgument($key) {
        $getArguments =  $this->request->getArguments();
        $filter = '';
        if (is_array($getArguments)) {
            if (isset($getArguments[$key])) {
                $filter = $getArguments[$key];
            }
        }
        return $filter;
    }


}
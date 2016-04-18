<?php
namespace HFWU\HfwuRedirects\Controller;

use HFWU\HfwuRedirects\Domain\Repository\RedirectsRepository;
use HFWU\HfwuRedirects\Domain\Model\Redirects;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;


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
     * action AliasList
     *
     * @return void
     */
    public function aliasListAction() {
        $this->assignView(false);
    }

    /**
     * action AliasListAjax
     *
     * @return void
     */
    public function aliasListAjaxAction() {
        $this->aliasListAction();
    }

    /**
     * action QrList
     *
     * @return void
     */
    public function qrListAction() {
        $this->assignView(true);
    }

    /**
     * action QrListAjax
     *
     * @return void
     */
    public function qrListAjaxAction() {
        $this->qrListAction();
    }

    /**
     * assign view depending qrlist flag
     * @param bool $qrRedirectsOnly
     * @return void
     */
    public function assignView($qrRedirectsOnly=false ) {
        $filter = $this->getArgument('filter');
        /** @var QueryResultInterface $redirects */
        $redirects = $this->getResultList($filter, $qrRedirectsOnly);
        $siteUrl = GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
        $pid = $this->redirectsRepository->getPid();
        $this->view->assign('siteUrl', $siteUrl);
        $this->view->assign('filter', $filter);
        $this->view->assign('pid', $pid);
        $this->view->assign('redirects', $redirects);
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

    /**
     * get redirects by filter
     *
     * @param string $eingabe
     * @param bool $qrRedirectsOnly
     * @return QueryResultInterface|array
     */
    protected function getResultList($filter, $qrRedirectsOnly) {
        if ($qrRedirectsOnly) {
            $redirects = $this->redirectsRepository->findQrCodes($filter);
        } else {
            $redirects = $this->redirectsRepository->findRedirects($filter);
        }
        return $redirects;
    }

    /**
     * action deleteRedirectEntryAjax
     *
     * @return void
     */
    public function deleteRedirectEntryAjaxAction() {
        $id =  $this->getArgument('id');
        if (!empty($id)) {
            $this->redirectsRepository->removeEntry($id);
        }

    }


    /**
     * action showQrCode
     *
     * @param Redirects $redirect
     * @return void
     */
    public function showQrCodeAction(Redirects $redirect) {
        $shortUrl = $redirect->getShortUrl();
        $title = $redirect->getTitle();
        $siteUrl = GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
        $completeUrl = $siteUrl . $shortUrl;
        $this->createQrCode($completeUrl, $title);
    }


    /**
     * action list
     *
     * @return void
     */
    public function listAction() {
        $redirects = $this->redirectsRepository->findAll();
        $this->view->assign('redirects', $redirects);
    }
    
    /**
     * action show
     *
     * @param Redirects $redirects
     * @return void
     */
    public function showAction(Redirects $redirects) {
        $this->view->assign('redirects', $redirects);
    }
    
    /**
     * action new
     *
     * @return void
     */
    public function newAction() {
        
    }
    
    /**
     * action create
     *
     * @param Redirects $newRedirects
     * @return void
     */
    public function createAction(Redirects $newRedirects)     {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        $this->redirectsRepository->add($newRedirects);
        $this->redirect('list');
    }
    
    /**
     * action edit
     *
     * @param Redirects $redirects
     * @ignorevalidation $redirects
     * @return void
     */
    public function editAction(Redirects $redirects) {
        $this->view->assign('redirects', $redirects);
    }
    
    /**
     * action update
     *
     * @param Redirects $redirects
     * @return void
     */
    public function updateAction(Redirects $redirects) {
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        $this->redirectsRepository->update($redirects);
        $this->redirect('list');
    }
    
    /**
     * action delete
     *
     * @param Redirects $redirects
     * @return void
     */
    public function deleteAction(Redirects $redirects)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        $this->redirectsRepository->remove($redirects);
        $this->redirect('list');
    }

    public function createQrCode($url, $title, $size=400) {
        if ($size > 300) {
            $errorCorrection = 'high';
        } elseif ($size > 100) {
            $errorCorrection = 'medium';
        } else {
            $errorCorrection = 'low';
        }

        /** @var $qrCode \HFWU\HfwuRedirects\Utility\Qr\QrCode */
        $qrCode = $this->objectManager->get('HFWU\HfwuRedirects\Utility\Qr\QrCode');

        $qrCode->setText($url)
          ->setSize($size)
          ->setPadding(50)
          ->setErrorCorrection($errorCorrection)
          ->setForegroundColor(array('r' => 0, 'g' => 48, 'b' => 94, 'a' => 0))
          ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0));
        $image = $qrCode->get($qrCode::IMAGE_TYPE_PNG);

        $filename = 'qrcode_' . $title . '.png';
        /** @var $filenameCleaner \TYPO3\CMS\Core\Resource\Driver\LocalDriver */
        $filenameCleaner = $this->objectManager->get('TYPO3\CMS\Core\Resource\Driver\LocalDriver');
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
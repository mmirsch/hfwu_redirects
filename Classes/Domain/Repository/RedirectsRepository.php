<?php
namespace HFWU\HfwuRedirects\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

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
 * The repository for Redirects
 */
class RedirectsRepository extends Repository {
	/**
	 * Life cycle method.
	 *
	 * @param int $pid
	 * @return void
	 */
	public function initializeObject($pid=0) {
		/**@var $querySettings \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings */
		$querySettings = $this->objectManager->get('TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings');
		if ($pid) {
			$querySettings->setStoragePageIds(array($pid));
		} else {
			// don't add the pid constraint
			$querySettings->setRespectStoragePage(FALSE);
		}
		$this->setDefaultQuerySettings($querySettings);
	}

	/**
	 * @return QueryResultInterface|array
	 */
	public function getPid() {
		$this->initializeObject(false);
		/**@var $resultElem \HFWU\HfwuRedirects\Domain\Model\Redirects */
		$resultElem = $this->findAll()->getFirst();
		return $resultElem->getPid();

	}

	/**
	 * @param $filter string
	 * @param int $pid
	 * @return QueryResultInterface|array
	 */
	public function findRedirects($filter, $pid)
	{
		$this->initializeObject($pid);
		$query = $this->createQuery();
		$queryDefinition = $query->logicalOr(
			$query->like('shortUrl', '%' . $filter . '%'),
			$query->logicalOr(
				$query->like('title', '%' . $filter . '%'),
				$query->logicalOr(
					$query->like('urlComplete', '%' . $filter . '%'),
					$query->like('pageId.title', '%' . $filter . '%')
				)
			)
		);
		$queryResult = $query->matching(
			$queryDefinition
		)->execute();
		if ($queryResult->count() > 0) {
			return $this->additionalInfos($queryResult);
		} else {
			return NULL;
		}
	}
		/**
		 * Find all redirects containing searchword
		 * @param $filter string
		 * @param int $pid
		 * @return QueryResultInterface|array
		 */
		public function findRedirectsWithSearchWord($filter, $pid) {
			$query = $this->createQuery();
			$sql = 'select tx_hfwuredirects_domain_model_redirects.*';
			$sql .= ' from tx_hfwuredirects_domain_model_redirects LEFT JOIN pages ON tx_hfwuredirects_domain_model_redirects.page_id=pages.uid';
			$sql .= ' where ( tx_hfwuredirects_domain_model_redirects.title LIKE "%' . $filter . '%" OR ' .
												'tx_hfwuredirects_domain_model_redirects.short_url LIKE "%' . $filter . '%" OR ' .
												'tx_hfwuredirects_domain_model_redirects.url_complete LIKE "%' . $filter . '%" OR ' .
												'pages.title LIKE "%' . $filter . '%")';
			;
			$sql .= ' AND tx_hfwuredirects_domain_model_redirects.hidden = 0';
			$sql .= ' AND tx_hfwuredirects_domain_model_redirects.deleted = 0';
			$sql .= ' AND tx_hfwuredirects_domain_model_redirects.pid = ' . $pid;
			$sql .= ' AND pages.hidden = 0';
			$sql .= ' AND pages.deleted = 0';
			$queryResult = $query->statement($sql)->execute();
			if ($queryResult->count() > 0) {
				return $this->additionalInfos($queryResult);
			} else {
				return NULL;
			}
	}

	/**
	 * @param $queryResult QueryResultInterface
	 * @return array
	 */
	public function additionalInfos($queryResult) {
		/**@var $redirectCallsRepository \HFWU\HfwuRedirects\Domain\Repository\RedirectCallsRepository */
		$redirectCallsRepository = $this->objectManager->get('HFWU\HfwuRedirects\Domain\Repository\RedirectCallsRepository');

		$resultList = array();
		/**@var $resultElem \HFWU\HfwuRedirects\Domain\Model\Redirects */
		foreach($queryResult as $resultElem) {
			$urlComplete = $resultElem->getUrlComplete();
			if (empty($urlComplete)) {
				$pageId = $resultElem->getPageId();
				$pageTitle = $this->getPageTitle($pageId);
			} else {
				$pageTitle = $urlComplete;
			}
			$resultElem->setPageTitle($pageTitle);
			/**@var $redirectCalls \HFWU\HfwuRedirects\Domain\Model\RedirectCalls */
			$redirectCalls = $redirectCallsRepository->findOneByRedirect($resultElem->getUid());
			if (empty($redirectCalls)) {
				$redirectCount = 0;
			} else {
				$redirectCount = $redirectCalls->getCount();
			}
			$resultElem->setRedirectCount($redirectCount);
			$resultList[] = $resultElem;
		}
		return $resultList;
	}


	/**
	 * @param $pageId int
	 * @return String
	 */
	public function getPageTitle($pageId) {
		/**@var \TYPO3\CMS\Frontend\Page\PageRepository $pageSelect */
		$pageSelect = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\Page\PageRepository');
		$page = $pageSelect->getPage($pageId);
		return $page['title'];
	}

	/**
	 * @param $uid int
	 * @return String
	 */
	public function removeEntry($uid) {
		$entry = $this->findByUid($uid);
		if (!empty($entry)) {
			$this->remove($entry);
		}
	}

}
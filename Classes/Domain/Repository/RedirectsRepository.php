<?php
namespace HFWU\HfwuRedirects\Domain\Repository;

use HFWU\HfwuRedirects\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
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
		$querySettings->setRespectSysLanguage(FALSE);
		$this->setDefaultQuerySettings($querySettings);
		$this->setDefaultOrderings($this->getDefaultOrderings());
	}

	/**
	 * @return array
	 */
	public function getDefaultOrderings() {
		$defaultOrderings = array(
			'title' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
		);
			return $defaultOrderings;
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
	public function findRedirectsWithSearchWord($filter, $pid, $limit, $admin, $filterTypes)
	{
		$this->initializeObject();
		/** @var \TYPO3\CMS\Extbase\Persistence\QueryInterface $query */
		$query = $this->createQuery();
		$queryDefinition = $query->logicalOr (
			$query->like('shortUrl', '%' . $filter . '%'),
			$query->logicalOr(
				$query->like('title', '%' . $filter . '%'),
				$query->logicalOr(
					$query->like('urlComplete', '%' . $filter . '%'),
					$query->like('page.title', '%' . $filter . '%')
				)
			)
		);
		if (!$admin) {
			$queryDefinition = $query->logicalAnd (
				$query->equals('pid', $pid),
				$queryDefinition
			);
		}
		if ($filterTypes==='redirects_only') {
			$queryDefinition = $query->logicalAnd (
				$query->equals('is_qr_url', 0),
				$queryDefinition
			);
		} else if ($filterTypes==='qr_codes_only') {
			$queryDefinition = $query->logicalAnd (
				$query->equals('is_qr_url', 1),
				$queryDefinition
			);
		}
		$limit = intval($limit);
		if (MathUtility::canBeInterpretedAsInteger($limit) && $limit>0) {
			$query->setLimit($limit);
		}
		$queryResult = $query->matching(
			$queryDefinition
		)->execute();
		if ($queryResult->count() > 0 && !$admin) {
			/**@var array $extensionConfiguration */
			$extensionConfiguration = BackendUtility::getExtensionConfig();
			if (isset($extensionConfiguration['access_restriction_mode'])) {
				$accessRestrictionMode = $extensionConfiguration['access_restriction_mode'];
			} else {
				$accessRestrictionMode = 'all';
			}
			switch ($accessRestrictionMode) {
				case 'user':
					$this->checkUserAccess($queryResult);
					break;
				case 'group':
					$this->checkGroupAccess($queryResult);
					break;
				default:
			// no access restrictions
			}
		}
		return $queryResult;
	}

	/**
	 * Check if current backend user is member in one of the groups set for each entry
	 *
	 * @param $queryResult QueryResultInterface
	 * @return void
	 */
	public function checkGroupAccess(&$queryResult) {
			$currentUserGroups = BackendUtility::getBackendUserGroups();
			/**@var $resultElem \HFWU\HfwuRedirects\Domain\Model\Redirects */
			foreach($queryResult as $resultElem) {
				$entryGroups = explode(',',$resultElem->getUsergroups());
				$result = array_intersect($currentUserGroups,$entryGroups);
				$access = !empty($result);
				$resultElem->setAccess($access);
			}
	}

	/**
	 * Check if current backend user is creator of entries
	 *
	 * @param $queryResult QueryResultInterface
	 * @return void
	 */
	public function checkUserAccess(&$queryResult) {
		$currentUserId = BackendUtility::getPropertyFromBackendUser('uid');
		/**@var $resultElem \HFWU\HfwuRedirects\Domain\Model\Redirects */
		foreach($queryResult as $resultElem) {
			$access = ($currentUserId === $resultElem->_getProperty('cruser_id'));
			$resultElem->setAccess($access);
		}
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
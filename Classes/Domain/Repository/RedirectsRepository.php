<?php
namespace HFWU\HfwuRedirects\Domain\Repository;

use HFWU\HfwuRedirects\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
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
	 * @param string $filter
	 * @return void
	 */
	public function initializeObject($filter='') {
		/**@var $querySettings \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings */
		$querySettings = $this->objectManager->get('TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings');
		/*
		 * non-admins will only see entries in current sysfolder
		 */
		// don't add storage folder constraint
		$querySettings->setRespectStoragePage(FALSE);
		// don't add language constraint
		$querySettings->setRespectSysLanguage(FALSE);

		if (!empty($filter)) {
			$querySettings->setIgnoreEnableFields(true);
			$querySettings->setIncludeDeleted(true);
		}

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
	 * @param string $filter
	 * @param int $pid
	 * @param int $limit
	 * @param bool $admin
	 * @param string $filterTypes
	 * @return QueryResultInterface|array
	 */
	public function findRedirectsWithSearchWord($filter, $pid, $limit, $admin, $filterTypes)	{

		$this->initializeObject($filter);
		/** @var \TYPO3\CMS\Extbase\Persistence\QueryInterface $query */
		$query = $this->createQuery();

		/** @var \TYPO3\CMS\Extbase\Persistence\QueryInterface $queryDefinitionBase */
		$queryDefinitionBase = NULL;

		/** @var \TYPO3\CMS\Extbase\Persistence\QueryInterface $queryDefinition */
		$queryDefinition = NULL;

		/*
		 * editors will only view entries in current pid
		 */
		if ($admin) {
			if ($filterTypes==='redirects_only') {
				$queryDefinitionBase = $query->equals('is_qr_url', 0);
			} else if ($filterTypes==='qr_codes_only') {
				$queryDefinitionBase = $query->equals('is_qr_url', 1);
			}
		} else {
			$queryDefinitionBase = $query->logicalAnd(
				$query->equals('pid', $pid),
				$query->equals('is_qr_url', 1)
			);
		}

		if (!empty($filter)) {
			/** @var \TYPO3\CMS\Extbase\Persistence\QueryInterface $queryDefinitionFilter */
			$queryDefinitionFilter = $query->logicalOr (
				$query->like('shortUrl', '%' . $filter . '%'),
				$query->logicalOr(
					$query->like('title', '%' . $filter . '%'),
					$query->logicalOr(
						$query->like('urlComplete', '%' . $filter . '%'),
						$query->like('page.title', '%' . $filter . '%')
					)
				)
			);
			if (empty($queryDefinitionBase)) {
				$queryDefinition = $queryDefinitionFilter;
			} else {
				$queryDefinition = $query->logicalAnd(
					$queryDefinitionBase,
					$queryDefinitionFilter
				);
			}
			$queryDefinition = $query->logicalAnd(
				$query->logicalAnd(
					$query->equals('deleted', '0'),
					$query->equals('hidden', '0')
				),
				$queryDefinition
			);
		} else {
			$queryDefinition = $queryDefinitionBase;
		}
		$limit = intval($limit);
		if (MathUtility::canBeInterpretedAsInteger($limit) && $limit>0) {
			$query->setLimit($limit);
		}
		if (!empty($queryDefinition)) {
			$query = $query->matching(
				$queryDefinition
			);
		}
		$queryResult = $query->execute();
		return $queryResult;
	}

	/**
	 * Delete entry from database
	 *
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
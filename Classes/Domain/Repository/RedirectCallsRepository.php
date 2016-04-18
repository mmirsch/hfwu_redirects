<?php
namespace HFWU\HfwuRedirects\Domain\Repository;

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
 * The repository for RedirectCalls
 */
class RedirectCallsRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	/**
	 * @param $redirect \HFWU\HfwuRedirects\Domain\Model\Redirects
	 * @return $redirectCall
	 */
	public function findOneByRedirect($redirectId)
	{
		$querySettings = $this->objectManager->get('TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings');
		// don't add the pid constraint
		$querySettings->setRespectStoragePage(FALSE);
		$this->setDefaultQuerySettings($querySettings);

		$query = $this->createQuery();

		/**@var $queryResult \TYPO3\CMS\Extbase\Persistence\QueryResultInterface */
		$queryResult = $query->matching(
				$query->equals('redirect', $redirectId)
		)->execute();

		/**@var $redirectCall \HFWU\HfwuRedirects\Domain\Model\RedirectCalls */
		if ($queryResult->count() > 0) {
			return $queryResult->getFirst();
		}
		return null;
	}

	/**
	 * @param $redirect \HFWU\HfwuRedirects\Domain\Model\Redirects
	 * @return array
	 */
	public function storeRedirectCall(\HFWU\HfwuRedirects\Domain\Model\Redirects $redirect) {

		// Testing if entry for this redirect exists
		$redirectId = $redirect->getUid();
		/**@var $redirectCall \HFWU\HfwuRedirects\Domain\Model\RedirectCalls */
		$redirectCall = $this->findOneByRedirect($redirectId);
		if (!empty($redirectCall)) {
			// redirect exists, count has to be incremented
			$redirectCall->setCount($redirectCall->getCount()+1);
			$this->update($redirectCall);
		} else {
			// first call of this redirect
			$redirectCall = $this->objectManager->get('HFWU\HfwuRedirects\Domain\Model\RedirectCalls');
			$redirectCall->setRedirect($redirectId);
			$redirectCall->setCount(1);
			// pid will be taken from redirect
			$redirectCall->setPid($redirect->getPid());
			$this->add($redirectCall);
		}
		$this->objectManager->get('TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface')->persistAll();
	}
}
<?php

namespace  HFWU\HfwuRedirects\Hooks;

use HFWU\HfwuRedirects\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DataHandler {


	/**
	 * Manipulate short_url and url_complete
	 *
	 * @param string $status status
	 * @param string $table table name
	 * @param int $recordUid id of the record
	 * @param array $fields fieldArray
	 * @param \TYPO3\CMS\Core\DataHandling\DataHandler $parentObject parent Object
	 * @return void
	 */

	public function processDatamap_afterDatabaseOperations(
		$status,
		$table,
		$recordUid,
		array $fields,
		\TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler
	) {
		// Clear category cache
		if ($table === 'tx_hfwuredirects_domain_model_redirects' &&
				(($status === 'new' && $fields['is_qr_url'] ) ||
					($fields['url_complete']))) {
			/*
			 * If non admins create a new qr-code, the shorturl has to be created new
			 *
			 * !BackendUtility::isBackendAdmin() &&
			 *
			 */
			if (!\TYPO3\CMS\Core\Utility\MathUtility::canBeInterpretedAsInteger($recordUid)) {
				$recordUid = intval($dataHandler->substNEWwithIDs[$recordUid]);
			}
			/**@var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
			$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

			/**@var $redirectsRepository \HFWU\HfwuRedirects\Domain\Repository\RedirectsRepository */
			$redirectsRepository = $objectManager->get('HFWU\HfwuRedirects\Domain\Repository\RedirectsRepository');
			/**@var $redirect \HFWU\HfwuRedirects\Domain\Model\Redirects */
			$redirect = $redirectsRepository->findByUid($recordUid);
			if ($status === 'new' && $fields['is_qr_url']) {
				$short_url = 'qr-' . $fields['crdate'];
				$redirect->setShortUrl($short_url);
			}
			if ($fields['url_complete']) {
				$urlComplete = $fields['url_complete'];
				if (strpos('http',$urlComplete) === false) {
					$urlComplete = 'http://' .$urlComplete;
					$redirect->setUrlComplete($urlComplete);
				}
			}
			$redirectsRepository->update($redirect);
			$objectManager->get('TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface')->persistAll();

		}
	}

	//processDatamap_postProcessFieldArray($status, $table, $id, $fieldArray, $this);
	/**
	 * Prevent saving record where neither page_id nor url_complete is entered
	 *
	 * @param string $status
	 * @param string $table
	 * @param int $id
	 * @param array $fieldArray
	 * @param $dataHandler \TYPO3\CMS\Core\DataHandling\DataHandler
	 * @return void
	 */
	public function processDatamap_postProcessFieldArray (
		$status,
		$table,
		$id,
		&$fieldArray,
		\TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler) {
		if ($table === 'tx_hfwuredirects_domain_model_redirects') {
			$error = false;
			// check pageId or urlComplete is filled
			if ($status === 'new' && empty($fieldArray['page_id']) && empty($fieldArray['url_complete'])) {
				$dataHandler->log($table, $id, 1, 0, 1,
					'Daten unvollständig: es muss entweder das Feld "pageId" oder das Feld "urlComplete" ausgefüllt weden.',
					0, [$table]);
				$error = true;

			}
			if (isset($fieldArray['short_url'])) {
				if ($fieldArray['is_qr_url'] === '0' && $fieldArray['short_url'] === '') {
					$dataHandler->log($table, $id, 1, 0, 1,
						'Daten unvollständig: das Feld "shortUrl" muss ausgefüllt werden.',
						0, [$table]);
					$error = true;
				} else if ($fieldArray['short_url'] !== '') {
					/**@var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
					$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
					/**@var $redirectsRepository \HFWU\HfwuRedirects\Domain\Repository\RedirectsRepository */
					$redirectsRepository = $objectManager->get('HFWU\HfwuRedirects\Domain\Repository\RedirectsRepository');
					/**@var $redirect \HFWU\HfwuRedirects\Domain\Model\Redirects */
					$redirect = $redirectsRepository->findByShortUrl($fieldArray['short_url']);
					if ($status === 'new') {
						$maxcount = 0;
					} else {
						$maxcount = 1;
					}
					if (($redirect->count()>$maxcount)) {
						$dataHandler->log($table, $id, 1, 0, 1,
							'Der Eintrag "' . $fieldArray['short_url'] . '" als shortUrl existiert bereits.',
							0, [$table]);
						$error = true;
					}
				}
			}
			if ($error) {
				$fieldArray = [];
			}
		}
	}

}

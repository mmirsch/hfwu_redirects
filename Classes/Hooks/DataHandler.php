<?php

namespace  HFWU\HfwuRedirects\Hooks;

use HFWU\HfwuRedirects\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

class DataHandler {

	/**
	 * Prevent saving record where neither page_id nor url_complete is entered
	 * Evantually manipulate short_url and url_complete
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
			$fieldValues = $dataHandler->checkValue_currentRecord;
			if (count($fieldArray)>0) {
				foreach ($fieldArray as $key=>$value) {
					$fieldValues[$key] = $value;
				}
			}

			$error = false;
			// check pageId or urlComplete is filled
			if (empty($fieldValues['page']) && empty($fieldValues['url_complete'])) {
				$dataHandler->log($table, $id, 1, 0, 1,
					'Daten unvollständig: es muss entweder das Feld "TYPO3 Seiten-ID" oder das Feld "komplette URL" ausgefüllt weden.',
					0, [$table]);
				$error = true;
			}
			/*
			 * generate shorturl if flag "is_qr_code" is set
			 */
			if ($status === 'new' && (int)$fieldValues['is_qr_url'] === 1) {
				$fieldValues['short_url'] = 'qr-' . time();
			}
			/*
			 * eventually add "http://" if "url_complete" ist set
			 */
			if (!empty($fieldValues['url_complete'])) {
				if (strpos($fieldValues['url_complete'], 'http')===false) {
					$fieldValues['url_complete'] = 'http://' . $fieldValues['url_complete'];
				}
				if (RedirectHandling::identicalUris($fieldValues['url_complete'],$fieldValues['short_url'])) {
					$dataHandler->log($table, $id, 1, 0, 1,
						'Endlosschleife: die komplette URL ist identisch mit der shortURL.',
						0, [$table]);
					$error = true;
				}
			} else if (!empty($fieldValues['page'])) {
				if (RedirectHandling::redirectLoop($fieldValues['page'],$fieldValues['short_url'],$fieldValues['sys_language_uid'])) {
					$dataHandler->log($table, $id, 1, 0, 1,
						'Endlosschleife: shortURL wird auf die als TYPO3-Seite eingetragene Seite umgeleitet.',
						0, [$table]);
					$error = true;
				}
			}
			if ($fieldValues['short_url'] === '') {
				$dataHandler->log($table, $id, 1, 0, 1,
					'Daten unvollständig: das Feld "shortUrl" muss ausgefüllt werden.',
					0, [$table]);
				$error = true;
			} else {
					/**@var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
				$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
				/**@var $redirectsRepository \HFWU\HfwuRedirects\Domain\Repository\RedirectsRepository */
				$redirectsRepository = $objectManager->get('HFWU\HfwuRedirects\Domain\Repository\RedirectsRepository');
				/**@var $redirect \HFWU\HfwuRedirects\Domain\Model\Redirects */
				$redirect = $redirectsRepository->findByShortUrl($fieldValues['short_url']);
				if ($status === 'new') {
					$maxcount = 0;
				} else {
					$maxcount = 1;
				}
				if (($redirect->count()>$maxcount)) {
					$dataHandler->log($table, $id, 1, 0, 1,
						'Der Eintrag "' . $fieldValues['short_url'] . '" als shortUrl existiert bereits.',
						0, [$table]);
					$error = true;
				}
			}
			if ($error) {
				$fieldArray = [];
			} else {
				$fieldArray = $fieldValues;
			}
		}
	}
}

<?php

namespace  HFWU\HfwuRedirects\Hooks;

use HFWU\HfwuRedirects\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
			$error = false;
			// check pageId or urlComplete is filled
			if ($status === 'new' && empty($fieldArray['page']) && empty($fieldArray['url_complete'])) {
				$dataHandler->log($table, $id, 1, 0, 1,
					'Daten unvollständig: es muss entweder das Feld "TYPO3 Seiten-ID" oder das Feld "komplette URL" ausgefüllt weden.',
					0, [$table]);
				$error = true;
			}
			/*
			 * generate shorturl if flag "is_qr_code" is set
			 */
			if ($status === 'new' && $fieldArray['is_qr_url'] === '1') {
				$fieldArray['short_url'] = 'qr-' . time();
			}
			/*
			 * eventually add "http://" if "url_complete" ist set
			 */
			if ($fieldArray['url_complete']!='') {
				if (!StringUtility::beginsWith($fieldArray['url_complete'], 'http')) {
					$fieldArray['url_complete'] = 'http://' .$fieldArray['url_complete'];
				}
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

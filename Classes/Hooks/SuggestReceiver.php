<?php
namespace  HFWU\HfwuRedirects\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Utility\IconUtility;


/**
 * Custom suggest receiver for tags
 *
 * @package	TYPO3
 * @subpackage	hfwu_redirects
 */
class SuggestReceiver extends \TYPO3\CMS\Backend\Form\Element\SuggestDefaultReceiver{

	/**
	 * The constructor of this class
	 *
	 * @param string $table The table to query
	 * @param array $config The configuration (TCA overlayed with TSconfig) to use for this selector
	 * @return void
	 */
	public function __construct($table, $config) {
		$this->table = $table;
		$this->config = $config;
		// get a list of all the pages that should be looked on
		if (isset($config['pidList'])) {
			$allowedPages = ($pageIds = GeneralUtility::trimExplode(',', $config['pidList']));
			$depth = (int)$config['pidDepth'];
			foreach ($pageIds as $pageId) {
				if ($pageId > 0) {
					\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($allowedPages, $this->getAllSubpagesOfPage($pageId, $depth));
				}
			}
			$this->allowedPages = array_unique($allowedPages);
		}
		if (isset($config['maxItemsInResultList'])) {
			$this->maxItems = $config['maxItemsInResultList'];
		}
	}


	/**
	 * Queries a table for records and completely processes them
	 *
	 * Returns a two-dimensional array of almost finished records; the only need to be put into a <li>-structure
	 *
	 * If you subclass this class, you will most likely only want to overwrite the functions called from here, but not
	 * this function itself
	 *
	 * @param array $params
	 * @param integer $ref The parent object
	 * @return array Array of rows or FALSE if nothing found
	 */
	public function queryTable(&$params, $recursionCounter = 0) {
		$rows = array();
		$this->params = &$params;
		$start = $recursionCounter * 50;
		$this->prepareSelectStatement();
		$this->prepareOrderByStatement();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->table, $this->selectClause, '', $this->orderByStatement, $start . ', 50');
		$allRowsCount = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
		if ($allRowsCount) {
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				// check if we already have collected the maximum number of records
				if (count($rows) > $this->maxItems) {
					break;
				}
				$spriteIcon = IconUtility::getSpriteIconForRecord(
					$this->table, $row, array('style' => 'margin: 0 4px 0 -20px; padding: 0;')
				);
				$uid = $row['t3ver_oid'] > 0 ? $row['t3ver_oid'] : $row['uid'];
				$path = $this->getRecordPath($row, $uid);
				if (strlen($path) > 30) {
					$croppedPath = '<abbr title="' . htmlspecialchars($path) . '">' . htmlspecialchars(($GLOBALS['LANG']->csConvObj->crop($GLOBALS['LANG']->charSet, $path, 10) . '...' . $GLOBALS['LANG']->csConvObj->crop($GLOBALS['LANG']->charSet, $path, -20))) . '</abbr>';
				} else {
					$croppedPath = htmlspecialchars($path);
				}
				$label = $this->getLabel($row);
				$entry = array(
					'text' => '<span class="suggest-label">' . $label . '</span><span class="suggest-uid">[' . $uid . ']</span><br />
								<span class="suggest-path">' . $croppedPath . '</span>',
					'table' => $this->mmForeignTable ? $this->mmForeignTable : $this->table,
					'label' => $label,
					'path' => $path,
					'uid' => $uid,
					'style' => '',
					'class' => isset($this->config['cssClass']) ? $this->config['cssClass'] : '',
					'sprite' => $spriteIcon
				);
				$rows[$uid] = $this->renderRecord($row, $entry);
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			// if there are less records than we need, call this function again to get more records
			if (count($rows) < $this->maxItems && $allRowsCount >= 50 && $recursionCounter < $this->maxItems) {
				$tmp = self::queryTable($params, ++$recursionCounter);
				$rows = array_merge($tmp, $rows);
			}
		}
		return $rows;
	}

}
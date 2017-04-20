<?php
namespace  HFWU\HfwuRedirects\Hooks;

use HFWU\HfwuRedirects\Utility\BackendUtility;
use HFWU\HfwuRedirects\Utility\ExtensionUtility;
use TYPO3\CMS\Backend\Form\Wizard\SuggestWizardDefaultReceiver;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Utility\IconUtility;


/**
 * Custom suggest receiver for tags
 *
 * @package	TYPO3
 * @subpackage	hfwu_redirects
 */
class SuggestReceiver extends SuggestWizardDefaultReceiver {

	/**
	 * The list of pages that are forbidden to perform the search for records on
	 *
	 * @var array Array of PIDs
	 */
	protected $forbiddenPages = array();

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
		$extensionConfiguration = ExtensionUtility::getExtensionConfig();
		$admin = BackendUtility::isBackendAdmin();
		/*
		 * respect settings for editors concerning allowed and forbidden pagetrees
		 */
		if (!$admin ||true) {
			$depth = (int)$config['pidDepth'];
			if (isset($extensionConfiguration['suggest_allowed_pages'])) {
				$allowedPages = ($pageIds = GeneralUtility::trimExplode(',', $extensionConfiguration['suggest_allowed_pages']));
				foreach ($pageIds as $pageId) {
					if ($pageId > 0) {
						ArrayUtility::mergeRecursiveWithOverrule($allowedPages, $this->getAllSubpagesOfPage($pageId, $depth));
					}
				}
				$this->allowedPages = array_unique($allowedPages);
			}
			if (isset($extensionConfiguration['suggest_forbidden_pages'])) {
				$forbiddenPages = ($pageIds = GeneralUtility::trimExplode(',', $extensionConfiguration['suggest_forbidden_pages']));
				foreach ($pageIds as $pageId) {
					if ($pageId > 0) {
						ArrayUtility::mergeRecursiveWithOverrule($forbiddenPages, $this->getAllSubpagesOfPage($pageId, $depth));
					}
				}
				$this->forbiddenPages = array_unique($forbiddenPages);
			}
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


		$searchString = $this->params['value'];
		$searchWholePhrase = $this->config['searchWholePhrase'];
		$searchUid = (int)$searchString;
		if (strlen($searchString)>0) {
			$searchString = $GLOBALS['TYPO3_DB']->quoteStr($searchString, 'pages');
			if ($searchString[0]!='^' && $searchWholePhrase) {
				$this->selectClause = 'title LIKE \'%' . $GLOBALS['TYPO3_DB']->escapeStrForLike($searchString, $this->table) . '%\'';
			} else {
				$this->selectClause =  'title LIKE \'' . $GLOBALS['TYPO3_DB']->escapeStrForLike($searchString, $this->table) . '%\'';
			}

			// treat numbers as page id
			if ($searchUid > 0 && $searchUid == $searchString) {
				$this->selectClause = '(' . $this->selectClause . ' OR uid = ' . $searchUid . ')';
			}


		} else {
			$this->selectClause = 'TRUE';
		}
		if (isset($this->config['searchCondition']) && strlen($this->config['searchCondition']) > 0) {
			$this->selectClause .= ' AND ' . $this->config['searchCondition'];
		}

		if (count($this->allowedPages)) {
			$pidListAllowed = $GLOBALS['TYPO3_DB']->cleanIntArray($this->allowedPages);
			if (count($pidListAllowed)) {
				$this->selectClause .= ' AND pid NOT IN (' . implode(', ', $pidListAllowed) . ') ';
			}
		}

		if (count($this->forbiddenPages)) {
			$pidListForbidden = $GLOBALS['TYPO3_DB']->cleanIntArray($this->forbiddenPages);
			if (count($pidListForbidden)) {
				$this->selectClause .= ' AND pid NOT IN (' . implode(', ', $pidListForbidden) . ') ';
			}
		}

		if ($GLOBALS['TCA'][$this->table]['ctrl']['label']) {
			$this->orderByStatement = $GLOBALS['TCA'][$this->table]['ctrl']['label'];
		}

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->table, $this->selectClause, '', $this->orderByStatement, $start . ', 50');
		$allRowsCount = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
		if ($allRowsCount) {
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				// check if we already have collected the maximum number of records
				if (count($rows) > $this->maxItems) {
					break;
				}
				$this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
				$spriteIcon = $this->iconFactory->getIconForRecord($this->table, $row, Icon::SIZE_SMALL)->render();
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
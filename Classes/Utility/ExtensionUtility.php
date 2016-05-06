<?php
namespace HFWU\HfwuRedirects\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;



/**
 * Extension utility functions
 *
 * @package hfwu_redirects
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class ExtensionUtility {

     /**
     * @return array
     */
    public static function getExtensionConfig() {
        return unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['hfwu_redirects']);
    }


}

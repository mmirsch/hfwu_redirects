<?php
namespace HFWU\HfwuRedirects\ViewHelpers\Be;

use HFWU\HfwuRedirects\Utility\BackendUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;


/**
 * Is Backend Admin?
 *
 * @package TYPO3
 * @subpackage Fluid
 */

class IsAdminViewHelper extends AbstractViewHelper {

    /**
     * Is Backend Admin?
     *
     * @return bool
     */
    public function render() {
        return BackendUtility::isBackendAdmin();
    }


}

<?php
namespace HFWU\HfwuRedirects\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper RemoveXss
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class RawAndRemoveXssViewHelper extends AbstractViewHelper {
    /**
     * ViewHelper combines Raw and RemoveXss Methods
     *
     * @return string
     */
    public function render() {
        $string = $this->renderChildren();
        $string = GeneralUtility::removeXSS($string);

        return $string;
    }
}

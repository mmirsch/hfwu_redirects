<?php
namespace HFWU\HfwuRedirects\ViewHelpers\Be;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * BackendCreateLinkViewHelper
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class BackendCreateLinkViewHelper extends AbstractViewHelper {

    /**
     * Create a link for backend edit
     *
     * @param string $tableName
     * @param int $identifier
     * @param string $returnUrl
     * @param bool $addReturnUrl
     * @return string
     */
    public function render($tableName, $identifier, $returnUrl = '', $addReturnUrl = true) {
        return \HFWU\HfwuRedirects\Utility\BackendUtility::createNewUri($tableName, $identifier, $addReturnUrl, $returnUrl);
    }
}

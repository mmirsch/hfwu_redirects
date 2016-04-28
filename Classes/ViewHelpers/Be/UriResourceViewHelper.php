<?php
namespace HFWU\HfwuRedirects\ViewHelpers\Be;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * BackendEditLinkViewHelper
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class UriResourceViewHelper extends AbstractViewHelper {

    /**
     * Create a link for backend edit
     *
     * @param string $path
     * @return string
     */
    public function render($path) {
        $uri = 'EXT:hfwu_redirects/Resources/Public/' . $path;
        $uri = GeneralUtility::getFileAbsFileName($uri);
        $uri =  '../' . PathUtility::stripPathSitePrefix($uri);
        return $uri;
    }
}

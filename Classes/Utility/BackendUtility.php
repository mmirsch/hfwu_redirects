<?php
namespace HFWU\HfwuRedirects\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;



/**
 * Backend utility functions
 *
 * @package hfwu_redirects
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class BackendUtility {

    /**
     * Check if backend user is admin
     *
     * @return bool
     */
    public static function isBackendAdmin() {
        if (isset(self::getBackendUserAuthentication()->user)) {
            return self::getBackendUserAuthentication()->user['admin'] === 1;
        }
        return false;
    }

    /**
     * Get usergroups of current user
     *
     * @return array
     */
    public static function getBackendUserGroups() {
        if (isset(self::getBackendUserAuthentication()->user)) {
            $groups = self::getBackendUserAuthentication()->user['usergroup'];
            if (!empty($groups)) {
                return explode(',',$groups);
            }
        }
        return array();
    }

    /**
     * Get property from backend user
     *
     * @param string $property
     * @return string
     */
    public static function getPropertyFromBackendUser($property = 'uid') {
        if (!empty(self::getBackendUserAuthentication()->user[$property])) {
            return self::getBackendUserAuthentication()->user[$property];
        }
        return '';
    }

    /**
     * @return BackendUserAuthentication
     */
    protected static function getBackendUserAuthentication() {
        return $GLOBALS['BE_USER'];
    }

    /**
     * Create an URI edit or create new record, depending on parameter "mode"
     *
     * @param string $tableName
     * @param int $identifier
     * @param string $mode
     * @param bool $addReturnUrl
     * @return string
     * @todo remove condition for TYPO3 7.2 in upcoming major version
     */
    public static function createBackendModuleUri($tableName, $identifier, $mode, $addReturnUrl, $returnUrl)
    {
        // use new link generation in backend for TYPO3 7.2 or newer
        $t3Version72 = GeneralUtility::compat_version('7.2');
        if ($t3Version72) {
            $uriParameters = [
              'edit' => [
                $tableName => [
                  $identifier => $mode
                ]
              ]
            ];
            if ($addReturnUrl) {
                if (empty($returnUrl)) {
                    $uriParameters['returnUrl'] = self::getReturnUrl($t3Version72);
                } else {
                    $uriParameters['returnUrl'] = $returnUrl;
                }

            }
            $editLink = BackendUtilityCore::getModuleUrl('record_edit', $uriParameters);
        } else {
            $editLink = self::getSubFolderOfCurrentUrl();
            $editLink .= 'typo3/alt_doc.php?edit[' . $tableName . '][' . $identifier . ']=' . $mode;
            if ($addReturnUrl) {
                if (empty($returnUrl)) {
                    $editLink .= '&returnUrl=' . self::getReturnUrl($t3Version72);
                } else {
                    $editLink .= '&returnUrl=' . $returnUrl;
                }
            }
        }
        return $editLink;
    }

    /**
     * Create an URI to create a new record
     *
     * @param string $tableName
     * @param int $identifier
     * @param bool $addReturnUrl
     * @return string
     */
    public static function createNewUri($tableName, $identifier, $addReturnUrl = true, $returnUrl='') {
        return self::createBackendModuleUri($tableName, $identifier, 'new', $addReturnUrl, $returnUrl);
     }

    /**
     * Create an URI to edit any record
     *
     * @param string $tableName
     * @param int $identifier
     * @param bool $addReturnUrl
     * @return string
     */
    public static function createEditUri($tableName, $identifier, $addReturnUrl = true, $returnUrl='') {
        return self::createBackendModuleUri($tableName, $identifier, 'edit', $addReturnUrl, $returnUrl);
    }

    /**
     * Get return URL from current request
     *
     * @param bool $t3Version72
     * @return string
     * @todo remove condition for TYPO3 7.2 in upcoming major version
     */
    public static function getReturnUrl($t3Version72='')    {
        if (empty($t3Version72)) {
            // use new link generation in backend for TYPO3 7.2 or newer
            $t3Version72 = GeneralUtility::compat_version('7.2');
        }
        if ($t3Version72) {
            $uri = self::getModuleUrl(self::getModuleName(), self::getCurrentParameters());
        } else {
            $uri = rawurlencode(
                self::getSubFolderOfCurrentUrl() . GeneralUtility::getIndpEnv('TYPO3_SITE_SCRIPT')
            );
        }
        return $uri;
    }


    /**
     * Get module name
     *
     * @return string
     */
    protected static function getModuleName() {
        return (string) GeneralUtility::_GET('M');
    }

    /**
     * Get all GET/POST params without module name and token
     *
     * @param array $getParameters
     * @return array
     */
    public static function getCurrentParameters($getParameters = []) {
        if (empty($getParameters)) {
            $getParameters = GeneralUtility::_GET();
        }
        $parameters = [];
        $ignoreKeys = [
            'M',
            'moduleToken'
        ];
        foreach ($getParameters as $key => $value) {
            if (in_array($key, $ignoreKeys)) {
                continue;
            }
            $parameters[$key] = $value;
        }
        return $parameters;
    }

    /**
     * Read pid from returnUrl
     *        URL example:
     *        http://www.hfwu.de/typo3/alt_doc.php?&
     *        returnUrl=%2Ftypo3%2Fsysext%2Fcms%2Flayout%2Fdb_layout.php%3Fid%3D17%23
     *        element-tt_content-14&edit[tt_content][14]=edit
     *
     * @param string $returnUrl normally used for testing
     * @return int
     */
    public static function getPidFromBackendPage($returnUrl = '') {
        if (empty($returnUrl)) {
            $returnUrl = GeneralUtility::_GP('returnUrl');
        }
        $urlParts = parse_url($returnUrl);
        parse_str($urlParts['query'], $queryParts);
        return (int) $queryParts['id'];
    }

    /**
     * Returns the URL to a given module
     *      mainly used for visibility settings or deleting
     *      a record via AJAX
     *
     * @param string $moduleName Name of the module
     * @param array $urlParameters URL parameters that should be added as key value pairs
     * @return string Calculated URL
     * @todo remove condition for TYPO3 6.2 in upcoming major version
     */
    public static function getModuleUrl($moduleName, $urlParameters = []) {
        if (GeneralUtility::compat_version('7.2')) {
            $uri = BackendUtilityCore::getModuleUrl($moduleName, $urlParameters);
        } else {
            $uri = 'tce_db.php?' . BackendUtilityCore::getUrlToken('tceAction');
        }
        return $uri;
    }

    /**
     * Returns the Page TSconfig for page with id, $id
     *
     * @param int $pid
     * @param array $rootLine
     * @param bool $returnPartArray
     * @return array Page TSconfig
     * @see \TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser
     */
    public static function getPagesTSconfig($pid, $rootLine = null, $returnPartArray = false) {
        return BackendUtilityCore::getPagesTSconfig($pid, $rootLine, $returnPartArray);
    }

    /**
     * Get Subfolder of current TYPO3 Installation
     *        and never return "//"
     *
     * @param bool $leadingSlash will be prepended
     * @param bool $trailingSlash will be appended
     * @param string $testHost can be used for a test
     * @param string $testUrl can be used for a test
     * @return string
     */
    public static function getSubFolderOfCurrentUrl(
      $leadingSlash = true,
      $trailingSlash = true,
      $testHost = null,
      $testUrl = null
    ) {
        $subfolder = '';
        $typo3RequestHost = GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST');
        if ($testHost) {
            $typo3RequestHost = $testHost;
        }
        $typo3SiteUrl = GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
        if ($testUrl) {
            $typo3SiteUrl = $testUrl;
        }

        // if subfolder
        if ($typo3RequestHost . '/' !== $typo3SiteUrl) {
            $subfolder = substr(str_replace($typo3RequestHost . '/', '', $typo3SiteUrl), 0, -1);
        }
        if ($trailingSlash && substr($subfolder, 0, -1) !== '/') {
            $subfolder .= '/';
        }
        if ($leadingSlash && $subfolder[0] !== '/') {
            $subfolder = '/' . $subfolder;
        }
        return $subfolder;
    }



}

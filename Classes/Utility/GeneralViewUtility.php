<?php
namespace HFWU\HfwuRedirects\Utility;

use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Fluid\View\AbstractTemplateView;


/**
 * View utility functions
 *
 * @package hfwu_redirects
 *
 */
class GeneralViewUtility {

    /*
     * Does general assignments to view
     * Used by redirect controller and ajax dispatcher
     *
     * @param \TYPO3\CMS\Fluid\View\AbstractTemplateView $view
     * @param string $argumentKey
     * @param string $siteUrl
     * @param string $returnUrl
     * @param string $filter
     * @param int $pid
     * @param int $limit
     * @param bool $admin
     * @param bool $qrcodesOnly
     * @param \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $redirects
     */
    public static function assignViewArguments($view, $argumentKey, $siteUrl, $returnUrl, $filter, $pid, $limit, $admin, $filterTypes, QueryResultInterface $redirects)
    {
        $view->assign('argumentKey', $argumentKey);
        $view->assign('siteUrl', $siteUrl);
        $view->assign('returnUrl', $returnUrl);
        $view->assign('filter', $filter);
        $view->assign('pid', $pid);
        $view->assign('limit', $limit);
        $view->assign('admin', $admin);
        $view->assign('filter_types', $filterTypes);
        $view->assign('redirects', $redirects);
        $view->assign('error',0);
    }

}

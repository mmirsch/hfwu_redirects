<?php
namespace HFWU\HfwuRedirects\Utility\Tca;


use HFWU\HfwuRedirects\Utility\BackendUtility;
use TYPO3\CMS\Beuser\Domain\Repository\BackendUserGroupRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\BackendUserGroup;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;


class TcaUserFunc {

    /**
     * Listing pages for editors, that don't have access to the whole pagetree
     *
     * @param array $params
     * @return void
     */
    public function listPages(&$params)
    {
        $params['items'] = [];
        foreach ($this->getAllForms($this->getStartPid(), $params['row']['sys_language_uid']) as $form) {
            $params['items'][] = [
                htmlspecialchars($form['title']),
                (int) $form['uid']
            ];
        }
    }

    /**
     * List usergroups of logged in User
     *
     * @param array $param
     * @return void
     */
    public function listUserGroups(&$params)
    {
        /** @var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
        $objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

        /**@var \TYPO3\CMS\Beuser\Domain\Repository\BackendUserGroupRepository $backendUserGroupRepository */
        $backendUserGroupRepository = $objectManager->get('TYPO3\CMS\Beuser\Domain\Repository\BackendUserGroupRepository');
        /**@var $querySettings \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings */
        $querySettings = $objectManager->get('TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings');
        $querySettings->setRespectStoragePage(FALSE);
        $backendUserGroupRepository->setDefaultQuerySettings($querySettings);

        if (BackendUtility::isBackendAdmin()) {
            $currentUserGroups = $backendUserGroupRepository->findAll();
            /**@var \TYPO3\CMS\Beuser\Domain\Model\BackendUserGroup $beGroup */
            foreach ($currentUserGroups as $beGroup) {

                $params['items'][] = [
                  $beGroup->getTitle(),
                  $beGroup->getUid()
                ];
            }
        } else {
            $currentUserGroups = BackendUtility::getBackendUserGroups();
            if (is_array($currentUserGroups) && count($currentUserGroups)>0) {
                foreach ($currentUserGroups as $uid) {
                    /**@var \TYPO3\CMS\Beuser\Domain\Model\BackendUserGroup $beGroup */
                    $beGroup = $backendUserGroupRepository->findByUid($uid);
                    $params['items'][] = [
                      $beGroup->getTitle(),
                      $uid
                    ];
                }

            }
        }

    }

}

<?php
namespace HFWU\HfwuRedirects\Utility;


use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class AjaxDispatcher {

	/**
	 * Main function of the class, will run the function call process.
	 */
	public function bootstrap($bootstrapConfiguration, $arguments) {
		/** @var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

		// Bootstrap initialization.
		\TYPO3\CMS\Core\Core\Bootstrap::getInstance()
			->initializeTypo3DbGlobal()
			->initializeBackendUser();

		// Add the controller arguments to the global $_GET var.
		/** @var $extensionService \TYPO3\CMS\Extbase\Service\ExtensionService */
		$extensionService = $objectManager->get('TYPO3\CMS\Extbase\Service\ExtensionService');
		$pluginNamespace = $extensionService->getPluginNamespace($bootstrapConfiguration['extensionName'], $bootstrapConfiguration['pluginName']);
		\TYPO3\CMS\Core\Utility\GeneralUtility::_GETset($arguments, $pluginNamespace);

		// Calling the controller by running an Extbase Bootstrap with the
		// correct configuration.
		/** @var $bootstrap \TYPO3\CMS\Extbase\Core\Bootstrap */
		$bootstrap = $objectManager->get('TYPO3\CMS\Extbase\Core\Bootstrap');
		return $bootstrap->run('', $bootstrapConfiguration);
	}

	/**
	 * Request all redirects that match the filter given in argument "filter"
	 */
	public function dispatchAliasList() {
		$pid = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('pid');
		$filter = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('filter');
		$action = 'aliasListAjax';
		$bootstrapConfiguration = array(
			'extensionName'                 => 'HfwuRedirects',
			'pluginName'                    => 'web_HfwuRedirectsRedirects',
			'vendorName'                    => 'HFWU',
			'controller'                    => 'Redirects',
			'switchableControllerActions'   => array(
				'Redirects'     => array($action)
			)
		);
		$arguments = array('action'=>$action, 'filter'=>$filter, 'pid'=>$pid);

		$result = $this->bootstrap($bootstrapConfiguration, $arguments);
		// Display the final result on screen.
		echo $result;
	}

	/**
	 * Request all redirects that match the filter given in argument "filter"
	 */
	public function dispatchDeleteRedirectEntry() {
		$id = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('id');
		$arguments = array('action'=>'deleteRedirectEntryAjax', 'id'=>$id);

		$bootstrapConfiguration = array(
			'extensionName'                 => 'HfwuRedirects',
			'pluginName'                    => 'web_HfwuRedirectsRedirects',
			'vendorName'                    => 'HFWU',
			'controller'                    => 'Redirects',
			'switchableControllerActions'   => array(
				'Redirects'     => array('deleteRedirectEntryAjax')
			)
		);

		$this->bootstrap($bootstrapConfiguration, $arguments);
		exit();
	}


}


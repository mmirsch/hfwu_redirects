<?php
return array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:hfwu_redirects/Resources/Private/Language/locallang_db.xlf:tx_hfwuredirects_domain_model_redirects',
		'label' => 'title',
		'label_alt' => 'short_url',
		'label_alt_force' => 1,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,

		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'short_url,url_complete,url_hash,search_word,',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('hfwu_redirects') . 'Resources/Public/Icons/tx_hfwuredirects_domain_model_redirects.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'hidden, title, is_qr_url, short_url, page_id, url_complete, search_word, url_hash',
	),
	'types' => array(
		'1' => array('showitem' => 'hidden;;1, title, is_qr_url, short_url, page_id, url_complete, search_word, url_hash, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(

		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),

		'title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:hfwu_redirects/Resources/Private/Language/locallang_db.xlf:tx_hfwuredirects_domain_model_redirects.title',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'is_qr_url' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:hfwu_redirects/Resources/Private/Language/locallang_db.xlf:tx_hfwuredirects_domain_model_redirects.is_qr_url',
			'config' => array(
				'type' => 'check',
				'default' => 1,
			)
		),
		'short_url' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:hfwu_redirects/Resources/Private/Language/locallang_db.xlf:tx_hfwuredirects_domain_model_redirects.short_url',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,alphanum_x'
			),
		),
		'url_complete' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:hfwu_redirects/Resources/Private/Language/locallang_db.xlf:tx_hfwuredirects_domain_model_redirects.url_complete',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,domainname'
			),
		),
		'url_hash' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:hfwu_redirects/Resources/Private/Language/locallang_db.xlf:tx_hfwuredirects_domain_model_redirects.url_hash',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'search_word' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:hfwu_redirects/Resources/Private/Language/locallang_db.xlf:tx_hfwuredirects_domain_model_redirects.search_word',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'page_id' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:hfwu_redirects/Resources/Private/Language/locallang_db.xlf:tx_hfwuredirects_domain_model_redirects.page_id',
			/*
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'int'
			),
			*/
			'config' => array(
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'pages',
				'minitems' => 0,
				'maxitems' => 1,
				'size' => 1,
				'appearance' => array(
					'collapseAll' => 0,
					'levelLinksPosition' => 'top',
					'showSynchronizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'showAllLocalizationLink' => 1
				),
				'wizards' => array(
					'suggest' => array(
						'type' => 'suggest',
						'default' => array(
							'searchWholePhrase' => 1,
						),
					),
				),
			),

		),
	),
);
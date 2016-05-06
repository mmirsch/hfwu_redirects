<?php
return [
	'ctrl' => [
		'title'	=> 'LLL:EXT:hfwu_redirects/Resources/Private/Language/locallang_db.xlf:tx_hfwuredirects_domain_model_redirects',
		'label' => 'title',
		'label_alt' => 'short_url',
		'label_alt_force' => 1,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => true,
		'versioningWS' => false,
		'versioning_followPages' => false,

		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => [
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		],
		'searchFields' => 'short_url,url_complete,search_word,',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('hfwu_redirects') . 'Resources/Public/Icons/tx_hfwuredirects_domain_model_redirects.gif'
	],
	'interface' => [
		'showRecordFieldList' => 'hidden, sys_language_uid, title, is_qr_url, short_url, page, url_complete, search_word, redirect_count',
	],
	'types' => [
		'1' => ['showitem' =>
			'l10n_parent, l10n_diffsource, title, is_qr_url, short_url, page, url_complete, search_word,' .
			'--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, sys_language_uid, hidden, starttime, endtime'],
	],
	'palettes' => [
		'1' => ['showitem' => ''],
	],
	'columns' => [

		'hidden' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => [
				'type' => 'check',
			],
		],
		'starttime' => [
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => [
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => [
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				],
			],
		],
		'endtime' => [
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => [
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => [
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				],
			],
		],

		'title' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:hfwu_redirects/Resources/Private/Language/locallang_db.xlf:tx_hfwuredirects_domain_model_redirects.title',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			],
		],
		'is_qr_url' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:hfwu_redirects/Resources/Private/Language/locallang_db.xlf:tx_hfwuredirects_domain_model_redirects.is_qr_url',
			'config' => [
				'type' => 'check',
				'default' => 1,
			]
		],
		'short_url' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:hfwu_redirects/Resources/Private/Language/locallang_db.xlf:tx_hfwuredirects_domain_model_redirects.short_url',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,alphanum_x'
			],
		],
		'url_complete' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:hfwu_redirects/Resources/Private/Language/locallang_db.xlf:tx_hfwuredirects_domain_model_redirects.url_complete',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,domainname'
			],
		],
		'search_word' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:hfwu_redirects/Resources/Private/Language/locallang_db.xlf:tx_hfwuredirects_domain_model_redirects.search_word',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			],
		],
		'page' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:hfwu_redirects/Resources/Private/Language/locallang_db.xlf:tx_hfwuredirects_domain_model_redirects.page',
				'config' => [
					'type' => 'group',
					'internal_type' => 'db',
					'allowed' => 'pages',
					'foreign_table' => 'pages',
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
					'wizards' => [
						'suggest' => [
							'type' => 'suggest',
							'default' => [
								'title' => 'Bitte den Seitentitel oder die Seiten-ID eingeben',
								'receiverClass' => 'HFWU\\HfwuRedirects\\Hooks\\SuggestReceiver',
								'searchWholePhrase' => 1,
								'searchCondition' => 'doktype<>254',
								'minimumCharacters' => 1,
							],
						],
					],
					/*
					'treeConfig' => [
						'expandAll' => false,
						'parentField' => 'pid',
						'appearance' => [
							'showHeader' => true,
						],
					],

/*
					'type' => 'input',
					'foreign_table' => 'pages',
*/
				],
		],
		'sys_language_uid' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'special' => 'languages',
				'items' => [
					[
						'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',-1,'flags-multiple'
					],
				],
				'default' => 0,
			]
		],
	],
];


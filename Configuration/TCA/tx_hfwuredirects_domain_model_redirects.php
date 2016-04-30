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
		'dividers2tabs' => TRUE,
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,

		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => [
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		],
		'searchFields' => 'short_url,url_complete,url_hash,search_word,',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('hfwu_redirects') . 'Resources/Public/Icons/tx_hfwuredirects_domain_model_redirects.gif'
	],
	'interface' => [
		'showRecordFieldList' => 'hidden, sys_language_uid, title, is_qr_url, short_url, page, url_complete, search_word, url_hash, cruser_id, usergroups, redirect_count',
	],
	'types' => [
		'1' => ['showitem' => 'hidden;;1, sys_language_uid, title, is_qr_url, short_url, page, url_complete, search_word, url_hash, cruser_id, usergroups, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'],
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
		'url_hash' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:hfwu_redirects/Resources/Private/Language/locallang_db.xlf:tx_hfwuredirects_domain_model_redirects.url_hash',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
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
				'type' => 'input',
				'size' => 30,
				'eval' => 'int',
				'foreign_table' => 'pages',
			],
		],
		'redirect_count' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:hfwu_redirects/Resources/Private/Language/locallang_db.xlf:tx_hfwuredirects_domain_model_redirects.redirect_count',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			],
		],
		'cruser_id' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_tca.xlf:be_users.username',
			'config' => [
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'be_users',
				'size' => '1',
				'minitems' => '1',
				'maxitems' => '1',
			],
		],
		'usergroups' => [
			'label' => 'LLL:EXT:lang/locallang_tca.xlf:be_users.group',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectMultipleSideBySide',
				'itemsProcFunc' => 'HFWU\HfwuRedirects\Utility\Tca\TcaUserFunc->listUserGroups',
				'foreign_table' => 'be_groups',
				'size' => '20',
				'minitems' => '1',
				'maxitems' => '20',
				'wizards' => [
					'suggest' => [
						'type' => 'suggest',
						'default' => [
							'searchWholePhrase' => 1,
						],
					],
				],
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


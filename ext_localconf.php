<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'LeipzigUniversityLibrary.' . $_EXTKEY,
	'Publications',
	array(
		'Publication' => 'list, show',
		'Author' => 'list, show',
		'Organization' => 'list, show',
		
	),
	// non-cacheable actions
	array(
		'Publication' => '',
		'Author' => '',
		'Organization' => '',
		
	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'LeipzigUniversityLibrary.' . $_EXTKEY,
	'Authors',
	array(
		'Publication' => 'list, show',
		'Author' => 'list, show',
		'Organization' => 'list, show',
		
	),
	// non-cacheable actions
	array(
		'Publication' => '',
		'Author' => '',
		'Organization' => '',
		
	)
);

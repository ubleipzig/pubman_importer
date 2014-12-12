<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'LeipzigUniversityLibrary.' . $_EXTKEY,
	'Publications',
	array(
		'Publication' => 'list, show',
	),
	// non-cacheable actions
	array(
		'Publication' => 'list, show',
	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'LeipzigUniversityLibrary.' . $_EXTKEY,
	'Authors',
	array(
		'Author' => 'list, show',
		'Organization' => 'list, show',
	),
	// non-cacheable actions
	array(
		'Author' => 'list, show',
		'Organization' => 'list, show',
	)
);
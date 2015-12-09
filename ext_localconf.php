<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'LeipzigUniversityLibrary.' . $_EXTKEY,
	'Journals',
	array(
		'Journal' => 'list, show',
		'Issue' => 'show',
		'Article' => 'show',
		'Content' => 'show, stream',
	),
	// non-cacheable actions
	array(
		'Journal' => 'list, show',
		'Issue' => 'show',
		'Article' => 'show',
		'Content' => 'show, stream',
	)
);
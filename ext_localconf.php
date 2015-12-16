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
		'Content' => 'show, stream'
	),
	// non-cacheable actions
	array(
		'Journal' => 'list, show',
		'Issue' => 'show',
		'Article' => 'show',
		'Content' => 'show, stream',
	)
);

/***************
 * Add Bootstrap Package autoconfig to realurl
 */
if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('realurl')) {
	$realUrlVersion = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getExtensionVersion('realurl');
	if(\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger($realUrlVersion) < 2000000) {
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration'][$_EXTKEY] = 'LeipzigUniversityLibrary\\PubmanImporter\\Hooks\\RealUrl\\AutoConfig->addConfigVersion1x';
	} else {
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration'][$_EXTKEY] = 'LeipzigUniversityLibrary\\PubmanImporter\\Hooks\\RealUrl\\AutoConfig->addConfigVersion2x';
	}
	unset($realUrlVersion);
}

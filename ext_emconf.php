<?php

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Pubman Importer',
	'description' => 'queries an escidoc server for journals and displays from journal to content',
	'category' => 'plugin',
	'author' => 'Ulf Seltmann',
	'author_email' => 'seltmann@ub.uni-leipzig.de',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'version' => '1.0.2',
	'clearCacheOnLoad' => 0,
	'constraints' => array(
		'depends' => array(
			'typo3' => '6.2.0-6.2.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);

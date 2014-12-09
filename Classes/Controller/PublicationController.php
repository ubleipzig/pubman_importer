<?php
namespace LeipzigUniversityLibrary\PubmanImporter\Controller;


/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2014
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * PublicationController
 */
class PublicationController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * publicationRepository
	 *
	 * @var \LeipzigUniversityLibrary\PubmanImporter\Domain\Repository\PublicationRepository
	 * @inject
	 */
	protected $publicationRepository = NULL;

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('pubmanimporter');
		$publication = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Publication', $params['objectId'], $this->settings);

		if (!empty($params['objectId'])) {
			$publicationTitle = $publication->getTitle();
			$publications 	  = $publication->getPublications($params['objectId']);
		} else {
			$publications = $publication->getPublications();
		}

		$mimeType = $params['properties']['mime-type'];

		$this->view->assign('publications', $publications);
		$this->view->assign('publicationTitle', $publicationTitle);
	}


	// @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Publication $publication

     /**
	 * action show
	 *
	 * @return void
	 */
     public function showAction() {
     	$params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('tx_pubmanimporter_publications');

     	$publication = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Publication', $params['publication'], $this->settings);

     	$identifier = $publication->getIdentifier();
     	$authors 	= $publication->getAuthors();
     	$title 		= $publication->getTitle();

     	$this->view->assign('identifier', $identifier);
     	$this->view->assign('authors', $authors);
     	$this->view->assign('title', $title);
     	$this->view->assign('publication', $publication->getPublication());


     }

 }
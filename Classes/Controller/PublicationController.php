<?php
namespace LeipzigUniversityLibrary\PubmanImporter\Controller;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

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
class PublicationController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction()
	{
		$params = GeneralUtility::_GET('pubmanimporter');
		$publication = GeneralUtility::makeInstance('\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Publication', $params['objectId'], $this->settings);

		if (!empty($params['objectId'])) {
			$publicationTitle = $publication->getTitle();
			$publications = $publication->getPublications($params['objectId']);
		} else {
			$publications = $publication->getPublications();
			$publicationTitle = '';
		}

		$mimeType = $params['properties']['mime-type'];

		$this->view->assign('publications', $publications);
		$this->view->assign('publicationTitle', $publicationTitle);
		$this->view->assign('mimeType', $mimeType);
	}

	/**
	 * action show
	 *
	 * @return void
	 */
	public function showAction()
	{
		$params = GeneralUtility::_GET('tx_pubmanimporter_publications');

		$publication = GeneralUtility::makeInstance('\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Publication', $params['publication'], $this->settings);

		$this->view->assign('identifier', $publication->getIdentifier());
		$this->view->assign('authors', $publication->getAuthors());
		$this->view->assign('title', $publication->getTitle());
		$this->view->assign('publication', $publication->getPublication());
	}
}
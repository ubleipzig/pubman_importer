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
 * AuthorController
 */
class AuthorController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction()
	{
		$author = GeneralUtility::makeInstance('\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author', NULL, $this->settings);
		$authors = $author->getAuthors();

		// sort array by surname
		$family_name = array();
		foreach ($authors as $key => &$row) {
			if (!$row['family_name'] && $row['name']) {
				$matches = [];
				preg_match('/(?<family_name>\w+)\s*,\s*(?<given_name>\w)/', $row['name'], $matches);
				$row['family_name'] = $matches['family_name'];
				$row['given_name'] = $matches['given_name'];
			}

			$family_name[$key] = $row['family_name'];
		}
		array_multisort($family_name, SORT_ASC, $authors);

		$this->view->assign('authors', $authors);
	}

	/**
	 * action show
	 *
	 * @return void
	 */
	public function showAction()
	{
		$params = GeneralUtility::_GET('tx_pubmanimporter_authors');
		$author = GeneralUtility::makeInstance('\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author', $params['author']['family_name'], $this->settings);

		$publications = $author->getPublications();
		$author = $params['author'];

		$this->view->assign('author', $author);
		$this->view->assign('family_name', $author['family_name']);
		$this->view->assign('name', $author['name']);
		$this->view->assign('given_name', $author['given_name']);
		$this->view->assign('degree', $author['degree']);
		$this->view->assign('link', $author['link']);
		$this->view->assign('publications', $publications);
		$this->view->assign('source_url', $this->settings{'source_url'});
		$this->view->assign('item_view', $this->settings{'item_view'});
	}
}

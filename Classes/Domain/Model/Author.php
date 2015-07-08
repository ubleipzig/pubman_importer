<?php
namespace LeipzigUniversityLibrary\PubmanImporter\Domain\Model;
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
 * Author
 */
class Author extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * author
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author>
	 * @cascade remove
	 */
	//protected $author = NULL;

	/**
	 * organization
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Organization>
	 * @cascade remove
	 */
	protected $organization = NULL;

	/**
	 * __construct
	 */
	public function __construct($familyName, $settings = NULL) {
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();

		$this->settings = $settings;
		$this->familyName = $familyName;
	}

	public function getAuthors(){
		$authRepo = GeneralUtility::makeInstance('\LeipzigUniversityLibrary\PubmanImporter\Domain\Repository\AuthorRepository', $this->settings);
		$authorIdentifiers = $authRepo->loadList();
		$authors = array();
		foreach($authorIdentifiers as $authorIdentifier){
			$author = $authRepo->getAuthorDetails($authorIdentifier);
			$authors[] = $author;
		}
		return $authors;
	}

	public function getPublications() {
		$authRepo = GeneralUtility::makeInstance('\LeipzigUniversityLibrary\PubmanImporter\Domain\Repository\AuthorRepository', $this->settings);

		return $authRepo->getPublications($this->familyName);
	}

	/**
	 * Initializes all ObjectStorage properties
	 * Do not modify this method!
	 * It will be rewritten on each save in the extension builder
	 * You may modify the constructor of this class instead
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		//$this->author = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->organization = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * Adds a Author
	 *
	 * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author $author
	 * @return void
	 */
	//public function addAuthor(\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author $author) {
	//	$this->author->attach($author);
	//}

	/**
	 * Removes a Author
	 *
	 * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author $authorToRemove The Author to be removed
	 * @return void
	 */
	//public function removeAuthor(\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author $authorToRemove) {
	//	$this->author->detach($authorToRemove);
	//}

	/**
	 * Returns the author
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author> $author
	 */
	//public function getAuthor() {
	//	return $this->author;
	//}

	/**
	 * Sets the author
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author> $author
	 * @return void
	 */
	//public function setAuthor(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $author) {
	//	$this->author = $author;
	//}

}
<?php
namespace LeipzigUniversityLibrary\PubmanImporter\Domain\Model;


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
 * Organization
 */
class Organization extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * member
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author>
	 * @cascade remove
	 */
	protected $member = NULL;

	/**
	 * publication
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Publication>
	 * @cascade remove
	 */
	protected $publication = NULL;

	/**
	 * __construct
	 */
	public function __construct() {
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
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
		$this->member = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->publication = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * Adds a Author
	 *
	 * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author $member
	 * @return void
	 */
	public function addMember(\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author $member) {
		$this->member->attach($member);
	}

	/**
	 * Removes a Author
	 *
	 * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author $memberToRemove The Author to be removed
	 * @return void
	 */
	public function removeMember(\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author $memberToRemove) {
		$this->member->detach($memberToRemove);
	}

	/**
	 * Returns the member
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author> $member
	 */
	public function getMember() {
		return $this->member;
	}

	/**
	 * Sets the member
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author> $member
	 * @return void
	 */
	public function setMember(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $member) {
		$this->member = $member;
	}

	/**
	 * Adds a Publication
	 *
	 * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Publication $publication
	 * @return void
	 */
	public function addPublication(\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Publication $publication) {
		$this->publication->attach($publication);
	}

	/**
	 * Removes a Publication
	 *
	 * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Publication $publicationToRemove The Publication to be removed
	 * @return void
	 */
	public function removePublication(\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Publication $publicationToRemove) {
		$this->publication->detach($publicationToRemove);
	}

	/**
	 * Returns the publication
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Publication> $publication
	 */
	public function getPublication() {
		return $this->publication;
	}

	/**
	 * Sets the publication
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Publication> $publication
	 * @return void
	 */
	public function setPublication(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $publication) {
		$this->publication = $publication;
	}

}
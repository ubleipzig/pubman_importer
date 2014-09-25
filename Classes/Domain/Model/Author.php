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
 * Author
 */
class Author extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * publication
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Publication>
	 * @cascade remove
	 */
	protected $publication = NULL;

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
		$this->publication = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->organization = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
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

	/**
	 * Adds a Organization
	 *
	 * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Organization $organization
	 * @return void
	 */
	public function addOrganization(\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Organization $organization) {
		$this->organization->attach($organization);
	}

	/**
	 * Removes a Organization
	 *
	 * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Organization $organizationToRemove The Organization to be removed
	 * @return void
	 */
	public function removeOrganization(\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Organization $organizationToRemove) {
		$this->organization->detach($organizationToRemove);
	}

	/**
	 * Returns the organization
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Organization> $organization
	 */
	public function getOrganization() {
		return $this->organization;
	}

	/**
	 * Sets the organization
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Organization> $organization
	 * @return void
	 */
	public function setOrganization(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $organization) {
		$this->organization = $organization;
	}

}
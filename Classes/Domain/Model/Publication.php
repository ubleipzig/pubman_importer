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
 * Publication
 */
class Publication extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * component
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Component>
	 * @cascade remove
	 */
	protected $component = NULL;

	/**
	 * organization
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Organization>
	 * @cascade remove
	 */
	protected $organization = NULL;

	/**
	 * author
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author>
	 * @cascade remove
	 */
	protected $author = NULL;

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
		$this->component = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->organization = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->author = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * Adds a Component
	 *
	 * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Component $component
	 * @return void
	 */
	public function addComponent(\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Component $component) {
		$this->component->attach($component);
	}

	/**
	 * Removes a Component
	 *
	 * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Component $componentToRemove The Component to be removed
	 * @return void
	 */
	public function removeComponent(\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Component $componentToRemove) {
		$this->component->detach($componentToRemove);
	}

	/**
	 * Returns the component
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Component> $component
	 */
	public function getComponent() {
		return $this->component;
	}

	/**
	 * Sets the component
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Component> $component
	 * @return void
	 */
	public function setComponent(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $component) {
		$this->component = $component;
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

	/**
	 * Adds a Author
	 *
	 * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author $author
	 * @return void
	 */
	public function addAuthor(\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author $author) {
		$this->author->attach($author);
	}

	/**
	 * Removes a Author
	 *
	 * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author $authorToRemove The Author to be removed
	 * @return void
	 */
	public function removeAuthor(\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author $authorToRemove) {
		$this->author->detach($authorToRemove);
	}

	/**
	 * Returns the author
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author> $author
	 */
	public function getAuthor() {
		return $this->author;
	}

	/**
	 * Sets the author
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author> $author
	 * @return void
	 */
	public function setAuthor(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $author) {
		$this->author = $author;
	}

}
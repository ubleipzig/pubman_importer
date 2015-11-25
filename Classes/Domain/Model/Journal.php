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
 * Journal
 */
class Journal extends AbstractItem {

	/**
	 * issue
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Issue>
	 * @cascade remove
	 */
	protected $issue = NULL;

	/**
	 * Initializes all ObjectStorage properties
	 * Do not modify this method!
	 * It will be rewritten on each save in the extension builder
	 * You may modify the constructor of this class instead
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		$this->issue = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();

		return parent::initStorageObjects();
	}

	/**
	 * Adds a Issue
	 *
	 * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Issue $issue
	 * @return void
	 */
	public function addIssue(\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Issue $issue) {
		$this->issue->attach($issue);
	}

	/**
	 * Removes a Issue
	 *
	 * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Issue $issueToRemove The Issue to be removed
	 * @return void
	 */
	public function removeIssue(\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Issue $issueToRemove) {
		$this->issue->detach($issueToRemove);
	}

	/**
	 * Returns the issue
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Issue> $issue
	 */
	public function getIssue() {
		return $this->issue;
	}

	/**
	 * Sets the issue
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Issue> $issue
	 * @return void
	 */
	public function setIssue(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $issue) {
		$this->issue = $issue;
	}
}
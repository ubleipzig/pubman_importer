<?php
/**
 * Copyright (C) Leipzig University Library 2017 <info@ub.uni-leipzig.de>
 *
 * @author  Ulf Seltmann <seltmann@ub.uni-leipzig.de>
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License

 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

namespace LeipzigUniversityLibrary\PubmanImporter\Domain\Model;

/**
 * Class Journal
 */
class Journal extends Item {

	/**
	 * The issue storage container
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
	 * Adds an issue
	 *
	 * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Issue $issue
	 * @return void
	 */
	public function addIssue(\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Issue $issue) {
		$this->issue->attach($issue);
	}

	/**
	 * Removes an issue
	 *
	 * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Issue $issueToRemove The Issue to be removed
	 * @return void
	 */
	public function removeIssue(\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Issue $issueToRemove) {
		$this->issue->detach($issueToRemove);
	}
}
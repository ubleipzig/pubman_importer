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
 * Class Item
 */
class Item extends \LeipzigUniversityLibrary\PubmanImporter\Library\ItemAbstract {

	/**
	 * /escidocItemList:item-list/escidocItem:item/escidocMetadataRecords:md-records/escidocMetadataRecords:md-record
	 *	  /publication:publication/source:source/eterms:issue
	 * @var string
	 */
	protected $issueTerm;

	/**
	 * /escidocItemList:item-list/escidocItem:item/escidocMetadataRecords:md-records/escidocMetadataRecords:md-record
	 *	  /publication:publication/dcterms:issued[@xsi:type="dcterms:W3CDTF"]
	 * @var string
	 */
	protected $issuedYear;

	/**
	 * /escidocItemList:item-list/escidocItem:item/escidocMetadataRecords:md-records/escidocMetadataRecords:md-record
	 *	  /publication:publication/source:source/dc:identifier[xsi:type="eterms:URI"]
	 *
	 * @var string
	 */
	protected $identifier;

	/**
	 * Title of item
	 *
	 * @var string
	 */

	protected $title;

	/**
	 * Release date of item
	 *
	 * @var DateTime
	 */
	protected $releaseDate;

	/**
	 * publisher of item
	 *
	 * @var string
	 */
	protected $publisher;

	/**
	 * abstract of item
	 *
	 * @var string
	 */
	protected $abstract;

	/**
	 * subject of item
	 *
	 * @var string
	 */
	protected $subject;

	/**
	 * member
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Creator>
	 * @cascade remove
	 */
	protected $creator = NULL;

	/**
	 * component
	 *
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Component>
	 * @cascade remove
	 */
	protected $component = NULL;


	/**
	 * Initializes all ObjectStorage properties
	 * Do not modify this method!
	 * It will be rewritten on each save in the extension builder
	 * You may modify the constructor of this class instead
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		$this->creator = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->component = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * Adds a Creator
	 *
	 * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Creator $creator
	 * @return void
	 */
	public function addCreator(\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Creator $creator) {
		$this->creator->attach($creator);
	}

	/**
	 * Removes a Creator
	 *
	 * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Creator $creatorToRemove The Creator to be removed
	 * @return void
	 */
	public function removeCreator(\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Creator $creatorToRemove) {
		$this->creator->detach($creatorToRemove);
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
}
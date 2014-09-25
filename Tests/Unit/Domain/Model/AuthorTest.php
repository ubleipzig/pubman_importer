<?php

namespace LeipzigUniversityLibrary\PubmanImporter\Tests\Unit\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class AuthorTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {
	/**
	 * @var \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = new \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author();
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function getPublicationReturnsInitialValueForPublication() {
		$newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->assertEquals(
			$newObjectStorage,
			$this->subject->getPublication()
		);
	}

	/**
	 * @test
	 */
	public function setPublicationForObjectStorageContainingPublicationSetsPublication() {
		$publication = new \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Publication();
		$objectStorageHoldingExactlyOnePublication = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$objectStorageHoldingExactlyOnePublication->attach($publication);
		$this->subject->setPublication($objectStorageHoldingExactlyOnePublication);

		$this->assertAttributeEquals(
			$objectStorageHoldingExactlyOnePublication,
			'publication',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function addPublicationToObjectStorageHoldingPublication() {
		$publication = new \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Publication();
		$publicationObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('attach'), array(), '', FALSE);
		$publicationObjectStorageMock->expects($this->once())->method('attach')->with($this->equalTo($publication));
		$this->inject($this->subject, 'publication', $publicationObjectStorageMock);

		$this->subject->addPublication($publication);
	}

	/**
	 * @test
	 */
	public function removePublicationFromObjectStorageHoldingPublication() {
		$publication = new \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Publication();
		$publicationObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('detach'), array(), '', FALSE);
		$publicationObjectStorageMock->expects($this->once())->method('detach')->with($this->equalTo($publication));
		$this->inject($this->subject, 'publication', $publicationObjectStorageMock);

		$this->subject->removePublication($publication);

	}

	/**
	 * @test
	 */
	public function getOrganizationReturnsInitialValueForOrganization() {
		$newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->assertEquals(
			$newObjectStorage,
			$this->subject->getOrganization()
		);
	}

	/**
	 * @test
	 */
	public function setOrganizationForObjectStorageContainingOrganizationSetsOrganization() {
		$organization = new \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Organization();
		$objectStorageHoldingExactlyOneOrganization = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$objectStorageHoldingExactlyOneOrganization->attach($organization);
		$this->subject->setOrganization($objectStorageHoldingExactlyOneOrganization);

		$this->assertAttributeEquals(
			$objectStorageHoldingExactlyOneOrganization,
			'organization',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function addOrganizationToObjectStorageHoldingOrganization() {
		$organization = new \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Organization();
		$organizationObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('attach'), array(), '', FALSE);
		$organizationObjectStorageMock->expects($this->once())->method('attach')->with($this->equalTo($organization));
		$this->inject($this->subject, 'organization', $organizationObjectStorageMock);

		$this->subject->addOrganization($organization);
	}

	/**
	 * @test
	 */
	public function removeOrganizationFromObjectStorageHoldingOrganization() {
		$organization = new \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Organization();
		$organizationObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('detach'), array(), '', FALSE);
		$organizationObjectStorageMock->expects($this->once())->method('detach')->with($this->equalTo($organization));
		$this->inject($this->subject, 'organization', $organizationObjectStorageMock);

		$this->subject->removeOrganization($organization);

	}
}

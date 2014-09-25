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
 * Test case for class \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Publication.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class PublicationTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {
	/**
	 * @var \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Publication
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = new \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Publication();
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function getComponentReturnsInitialValueForComponent() {
		$newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->assertEquals(
			$newObjectStorage,
			$this->subject->getComponent()
		);
	}

	/**
	 * @test
	 */
	public function setComponentForObjectStorageContainingComponentSetsComponent() {
		$component = new \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Component();
		$objectStorageHoldingExactlyOneComponent = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$objectStorageHoldingExactlyOneComponent->attach($component);
		$this->subject->setComponent($objectStorageHoldingExactlyOneComponent);

		$this->assertAttributeEquals(
			$objectStorageHoldingExactlyOneComponent,
			'component',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function addComponentToObjectStorageHoldingComponent() {
		$component = new \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Component();
		$componentObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('attach'), array(), '', FALSE);
		$componentObjectStorageMock->expects($this->once())->method('attach')->with($this->equalTo($component));
		$this->inject($this->subject, 'component', $componentObjectStorageMock);

		$this->subject->addComponent($component);
	}

	/**
	 * @test
	 */
	public function removeComponentFromObjectStorageHoldingComponent() {
		$component = new \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Component();
		$componentObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('detach'), array(), '', FALSE);
		$componentObjectStorageMock->expects($this->once())->method('detach')->with($this->equalTo($component));
		$this->inject($this->subject, 'component', $componentObjectStorageMock);

		$this->subject->removeComponent($component);

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

	/**
	 * @test
	 */
	public function getAuthorReturnsInitialValueForAuthor() {
		$newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->assertEquals(
			$newObjectStorage,
			$this->subject->getAuthor()
		);
	}

	/**
	 * @test
	 */
	public function setAuthorForObjectStorageContainingAuthorSetsAuthor() {
		$author = new \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author();
		$objectStorageHoldingExactlyOneAuthor = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$objectStorageHoldingExactlyOneAuthor->attach($author);
		$this->subject->setAuthor($objectStorageHoldingExactlyOneAuthor);

		$this->assertAttributeEquals(
			$objectStorageHoldingExactlyOneAuthor,
			'author',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function addAuthorToObjectStorageHoldingAuthor() {
		$author = new \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author();
		$authorObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('attach'), array(), '', FALSE);
		$authorObjectStorageMock->expects($this->once())->method('attach')->with($this->equalTo($author));
		$this->inject($this->subject, 'author', $authorObjectStorageMock);

		$this->subject->addAuthor($author);
	}

	/**
	 * @test
	 */
	public function removeAuthorFromObjectStorageHoldingAuthor() {
		$author = new \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Author();
		$authorObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('detach'), array(), '', FALSE);
		$authorObjectStorageMock->expects($this->once())->method('detach')->with($this->equalTo($author));
		$this->inject($this->subject, 'author', $authorObjectStorageMock);

		$this->subject->removeAuthor($author);

	}
}

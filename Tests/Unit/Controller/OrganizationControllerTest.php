<?php
namespace LeipzigUniversityLibrary\PubmanImporter\Tests\Unit\Controller;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 
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
 * Test case for class LeipzigUniversityLibrary\PubmanImporter\Controller\OrganizationController.
 *
 */
class OrganizationControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \LeipzigUniversityLibrary\PubmanImporter\Controller\OrganizationController
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = $this->getMock('LeipzigUniversityLibrary\\PubmanImporter\\Controller\\OrganizationController', array('redirect', 'forward', 'addFlashMessage'), array(), '', FALSE);
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function listActionFetchesAllOrganizationsFromRepositoryAndAssignsThemToView() {

		$allOrganizations = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array(), array(), '', FALSE);

		$organizationRepository = $this->getMock('LeipzigUniversityLibrary\\PubmanImporter\\Domain\\Repository\\OrganizationRepository', array('findAll'), array(), '', FALSE);
		$organizationRepository->expects($this->once())->method('findAll')->will($this->returnValue($allOrganizations));
		$this->inject($this->subject, 'organizationRepository', $organizationRepository);

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('organizations', $allOrganizations);
		$this->inject($this->subject, 'view', $view);

		$this->subject->listAction();
	}

	/**
	 * @test
	 */
	public function showActionAssignsTheGivenOrganizationToView() {
		$organization = new \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Organization();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('organization', $organization);

		$this->subject->showAction($organization);
	}
}

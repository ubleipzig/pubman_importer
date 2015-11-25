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
 * Article
 */
abstract class AbstractItem extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

    /**
     * Title of article
     *
     * @var string
     */

    protected $title;

    /**
     * Release date of article
     *
     * @var DateTime
     */
    protected $releaseDate;

    /**
     * publisher of article
     *
     * @var string
     */
    protected $publisher;

    /**
     * abstract of article
     *
     * @var string
     */
    protected $abstract;
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
     * Returns the creator
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Creator> $creator
     */
    public function getCreator() {
        return $this->creator;
    }

    /**
     * Sets the creator
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Creator> $creator
     * @return void
     */
    public function setCreator(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $creator) {
        $this->creator = $creator;
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

    public function setUid($uid) {
        $this->uid = $uid;
    }

    public function getUid() {
        return $this->uid;
    }

    public function setPid($pid) {
        $this->pid = $pid;
    }

    public function getPid() {
        return $this->pid;
    }

    public function setTitle($value) {
        $this->title = $value;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setReleaseDate($value) {
        $this->releaseDate = $value;
    }

    public function getReleaseDate() {
        return $this->releaseDate;
    }

    public function setPublisher($value) {
        $this->publisher = $value;
    }

    public function getPublisher() {
        return $this->publisher;
    }

    public function setAbstract($value) {
        $this->abstract = $value;
    }

    public function getAbstract() {
        return $this->abstract;
    }
}
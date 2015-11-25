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
 * Component
 */
class Creator extends \TYPO3\CMS\Extbase\DomainObject\AbstractValueObject {

	protected $familyName;

    protected $givenName;

    /**
     * article
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Article>
     * @cascade remove
     */
    protected $article = NULL;

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
        $this->article = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Adds a Article
     *
     * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Article $article
     * @return void
     */
    public function addArticle(\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Article $article) {
        $this->article->attach($article);
    }

    /**
     * Removes a Article
     *
     * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Article $articleToRemove The Article to be removed
     * @return void
     */
    public function removeArticle(\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Article $articleToRemove) {
        $this->article->detach($articleToRemove);
    }

    /**
     * Returns the article
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Article> $article
     */
    public function getArticle() {
        return $this->article;
    }

    /**
     * Sets the article
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Article> $article
     * @return void
     */
    public function setArticle(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $article) {
        $this->article = $article;
    }

    public function setFamilyName($familyName) {
        $this->familyName = $familyName;
    }

    public function getFamilyName() {
        return $this->familyName;
    }

    public function setGivenName($givenName) {
        $this->givenName = $givenName;
    }

    public function getGivenName() {
        return $this->givenName;
    }

    public function setUid($uid) {
        $this->uid = $uid;
    }

    public function getUid() {
        return $this->uid;
    }
}
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
class Article extends \LeipzigUniversityLibrary\PubmanImporter\Library\AbstractItemModel {

    /**
     * /escidocItemList:item-list/escidocItem:item/escidocMetadataRecords:md-records/escidocMetadataRecords:md-record
     *      /publication:publication/source:source/eterms:start-page
     *
     * @var string
     */
    protected $startPage;

    /**
     * /escidocItemList:item-list/escidocItem:item/escidocMetadataRecords:md-records/escidocMetadataRecords:md-record
     *      /publication:publication/source:source/eterms:end-page
     *
     * @var string
     */
    protected $endPage;


    public function setStartPage($value) {
        $this->startPage = $value;
    }

    public function getStartPage() {
        return $this->startPage;
    }

    public function setEndPage($value) {
        $this->endPage =$value;
    }

    public function getEndPage() {
        return $this->endPage;
    }
}
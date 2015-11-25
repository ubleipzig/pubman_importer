<?php
namespace LeipzigUniversityLibrary\PubmanImporter\Domain\Repository;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

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
 * The repository for Organizations
 */
class CreatorRepository extends \LeipzigUniversityLibrary\PubmanImporter\Library\PMIRepository
{

    protected $_escidocPublicationType = 'http://purl.org/escidoc/metadata/ves/publication-types/article';

    public function __construct() {
        return call_user_func_array(array('parent', '__construct'), func_get_args());
    }

    public function parse() {
        if (0 === (int)$this->countAll()) {
            throw new \Exception('no data found');
        }

        $result = [];

        foreach ($this->_xpath->query('/escidocItemList:item-list/escidocItem:item') as $itemNode) {
            $result[] = $this->parseCreator($itemNode);
        }

        return $result;
    }

    public function parseCreator($itemNode) {
        $model = GeneralUtility::makeInstance('\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Creator');

        $publication = $this->_xpath->query('escidocMetadataRecords:md-records/escidocMetadataRecords:md-record/publication:publication', $itemNode)->item(0);

        $model->setUid($this->getNodeAttr('objid', $itemNode));
        $this->parseComponents($this->_xpath->query('escidocComponents:components/escidocComponents:component', $itemNode), $model);
        $this->parseCreators($this->_xpath->query('eterms:creator', $publication), $model);

        $model->setTitle($this->_xpath->query('dc:title', $publication)->item(0)->nodeValue);
        $model->setReleaseDate(new \DateTime($this->_xpath->query('escidocItem:properties/prop:latest-release/release:date', $itemNode)->item(0)->nodeValue));
        $model->setPublisher($this->_xpath->query('eterms:publishing-info/dc:publisher', $publication)->item(0)->nodeValue);
        $model->setAbstract($this->_xpath->query('dcterms:abstract', $publication)->item(0)->nodeValue);

        return $model;
    }


}
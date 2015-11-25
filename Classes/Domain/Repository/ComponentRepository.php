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
class ComponentRepository extends \LeipzigUniversityLibrary\PubmanImporter\Library\PMIRepository
{

    protected $_escidocPublicationType = 'http://purl.org/escidoc/metadata/ves/publication-types/article';

    protected $_url = 'https://publishing.ub.uni-leipzig.de';


    public function __construct() {
        $this->_httpRequest = GeneralUtility::makeInstance('\TYPO3\CMS\Core\Http\HttpRequest');
    }

    /**
     * Finds an object matching the given identifier.
     *
     * @param integer $uid The identifier of the object to find
     * @return object The matching object if found, otherwise NULL
     * @api
     */
    public function findByUid($uid) {
        $this->_url .= $uid;


        return $this->execute()->parse();
    }

    public function parse() {
        return $this->parseComponent();
    }

    public function parseComponent() {
        $model = GeneralUtility::makeInstance('\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Component');



        $model->setHtmlContent($this->_domDocument->saveXML($this->_xpath->query('/tei:TEI')->item(0)));

        return $model;
    }


}
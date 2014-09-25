<?php
namespace LeipzigUniversityLibrary\PubmanImporter\Domain\Repository;


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
 * The repository for Publications
 */
class PublicationRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

        private $cObject;
        private $rootObjectId = "ubl:14001";
        private $pubman_options = array (   'source_url'
                                                => 'http://141.39.229.2',
                                            'item_view'
                                                => '/pubman/item',
                                            'search_export_interface'
                                                => '/search/SearchAndExport?cqlQuery=',
                                            'search_string'
                                                //Artikel zu Heft
                                                //=> 'escidoc.objecttype=%22item%22%20and%20escidoc.content-model.objid=%22ubl%3A5001%22%20AND%20((escidoc.any-identifier%3D"ubl%3A14003"+NOT+escidoc.objid%3D"ubl%3A14003"))%20',
                                                //Hefte zu Zeitschrift
                                                => 'escidoc.objecttype=%22item%22%20and%20escidoc.content-model.objid=%22ubl%3A5001%22%20AND%20((escidoc.any-identifier%3D"ubl%3A14001"+NOT+escidoc.objid%3D"ubl%3A14001"))%20',
                                            'fulltext_option_without'
                                                => 1,
                                            'fulltext_option_nonpublic'
                                                => 1,
                                        );


        public function __construct() {
            $this->cObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
        }


        private function getChildren($objectId, $startRecord, $maxRecords, $nestedCall = false) {
            $tmp_pubman_options = $this->pubman_options;

            $tmp_pubman_options['search_string'] = 'escidoc.objecttype=%22item%22%20and%20escidoc.content-model.objid=%22ubl%3A5001%22%20AND%20((escidoc.any-identifier%3D"' .
                                                    urlencode($objectId) . '"+NOT+escidoc.objid%3D"' . urlencode($objectId) . '"))%20';

            $citation = 'ESCIDOC_XML_V13'; //AJP, JUS, APA

            $query = array( 'cqlQuery' => $tmp_pubman_options['search_string'],
                            'exportFormat' => $citation, //ESCIDOC_XML_V13
                            'outputFormat' => 'escidoc_snippet',
                            'sortKeys' => 'escidoc.publication.source.issue',
                            'sortOrder' => 'ascending',
                            'startRecord' => $startRecord,
                            'maximumRecords' => $maxRecords);

            $rest = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\LeipzigUniversityLibrary\PubmanImporter\Library\PMIRest', $tmp_pubman_options);
            $data = $rest->getData($tmp_pubman_options['search_export_interface'], $query);

            $items = $rest->parseXML($data);

            if (count($items) > 0 && !$nestedCall) { //use nestedCall to traverse only one level down
                for ($i=0; $i<count($items); $i++) {

                    if (isset($items[$i]['object_id'])) {
                        //check for each child-item with an escidoc-id if it has children of its own
                       $childItems = $this->getChildren(($items[$i]['object_id']), $startRecord, $maxRecords, true);

                       //set hasChildren flag for items that have children
                       if (count($childItems)>0) $items[$i]['hasChildren'] = true;
                       else $items[$i]['hasChildren'] = false;

                       if ($items[$i]['hasChildren'] && !$nestedCall) {
                           //create link to list
                           $items[$i]['link'] = $this->cObject->getTypolink_URL($GLOBALS['TSFE']->id,
                                                                                array( 'pubmanimporter[objectId]' => $items[$i]['object_id'], )
                                                                               );
                       } else {
                           //create link to detail
                       }
                    }

                }
            }

            return $items;
        }

        private function buildSearchString() {

        }

        public function loadList($objectId = null, $startRecord = 1, $maxRecords = 1000) {

            if (!empty($objectId)) {
                $items = $this->getChildren($objectId,$startRecord, $maxRecords);
            } else {
                $items = $this->getChildren($this->rootObjectId,$startRecord, $maxRecords);
            }

            //return $this->pubman_options;
            return $items;
        }

}
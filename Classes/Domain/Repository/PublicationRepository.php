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

    public function __construct($settings = NULL) {
        $this->settings = $settings;
        $this->cObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
        $this->rootObjectId = $this->settings['root_object_id'];

        $this->pubman_options = array (
            'source_url'                => $settings['source_url'],
            'item_view'                 => $this->settings['item_view'],
            'search_export_interface'   => $this->settings['publication_search_export_interface'],
            //Artikel zu Heft //=> 'escidoc.objecttype=%22item%22%20and%20escidoc.content-model.objid=%22ubl%3A5001%22%20AND%20((escidoc.any-identifier%3D"ubl%3A14003"+NOT+escidoc.objid%3D"ubl%3A14003"))%20',
            //Hefte zu Zeitschrift
            'search_string'             => $this->settings['search_string'],
            'fulltext_option_without'   => intval($this->settings['fulltext_option_without']),
            'fulltext_option_nonpublic' => intval($this->settings['fulltext_option_nonpubic']),
            );
    }

    private function getAllItems() {
        $tmp_pubman_options = $this->pubman_options;
        $tmp_pubman_options['search_string'] = $this->settings['search_string_all_items'];
        $citation = $this->settings['citation_format'];

        $query = array( 'cqlQuery'       => $tmp_pubman_options['search_string'],
                        'exportFormat'   => $citation, //ESCIDOC_XML_V13
                        'outputFormat'   => $this->settings['output_format'],
                        'sortKeys'       => $this->settings['sort_keys'],
                        'sortOrder'      => $this->settings['sort_order'],
                        'startRecord'    => $startRecord,
                        'maximumRecords' => $maxRecords);

        $rest = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\LeipzigUniversityLibrary\PubmanImporter\Library\PMIRest', $tmp_pubman_options);
        $data = $rest->getData($tmp_pubman_options['search_export_interface'], $query);
        $items = $rest->parseXML($data);

        return $items;
    }



    private function getChildren($objectId, $startRecord, $maxRecords, $nestedCall = false) {
        $tmp_pubman_options = $this->pubman_options;
        $search_string_tmp  = $this->settings['search_string_children'];

        //$tmp_pubman_options['search_string'] = 'escidoc.objecttype=%22item%22%20and%20escidoc.content-model.objid=%22ubl%3A5001%22%20AND%20((escidoc.any-identifier%3D"' .
        //                                        urlencode($objectId) . '"+NOT+escidoc.objid%3D"' . urlencode($objectId) . '"))%20';
        $tmp_pubman_options['search_string'] = $search_string_tmp[0] . urlencode($objectId) . $search_string_tmp[1] . urlencode($objectId) . $search_string_tmp[2];
       //$tmp_pubman_options['search_string'] = 'escidoc.objecttype=%22item%22%20and%20escidoc.content-model.objid=%22ubl%3A5001%22%20AND%20((escidoc.any-identifier%3D"' .
                                               // urlencode($objectId) . '"))%20';

        $citation =  $this->settings['citation_format'];; //AJP, JUS, APA

        $query = array( 'cqlQuery'       => $tmp_pubman_options['search_string'],
                        'exportFormat'   => $citation, //ESCIDOC_XML_V13
                        'outputFormat'   => 'escidoc_snippet',
                        'sortKeys'       => 'escidoc.publication.source.issue',
                        'sortOrder'      => 'ascending',
                        'startRecord'    => $startRecord,
                        'maximumRecords' => $maxRecords);

        $rest = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\LeipzigUniversityLibrary\PubmanImporter\Library\PMIRest', $tmp_pubman_options);
        $data = $rest->getData($tmp_pubman_options['search_export_interface'], $query);
        $items = $rest->parseXML($data, "pubRepo");

        if (count($items) > 0 && !$nestedCall) { //use nestedCall to traverse only one level down
            for ($i=0; $i<count($items); $i++) {

                // server URL
                $items[$i]['source_url'] = $tmp_pubman_options['source_url'];

                if (isset($items[$i]['object_id'])) {
                    //check for each child-item with an escidoc-id if it has children of its own
                 $childItems = $this->getChildren(($items[$i]['object_id']), $startRecord, $maxRecords, true);

                   //set hasChildren flag for items that have children
                 if (count($childItems)>0) $items[$i]['hasChildren'] = true;
                 else $items[$i]['hasChildren'] = false;

                 if ($items[$i]['hasChildren'] && !$nestedCall) {
                       //create link to list
                     $items[$i]['link'] = $this->cObject->getTypolink_URL($GLOBALS['TSFE']->id,
                        array( 'pubmanimporter[objectId]' => $items[$i]['object_id'],'tx_pubmanimporter_publications[action]' => 'list', )
                        );
                 } else {
                     $items[$i]['link'] = $this->cObject->getTypolink_URL($GLOBALS['TSFE']->id,
                        array( 'pubmanimporter[objectId]' => $items[$i]['object_id'],'tx_pubmanimporter_publications[action]' => 'show', )
                        );
                 }
             }

         }
     }

     return $items;
 }

 private function buildSearchString() {

 }

 public function loadList($objectId = null, $startRecord = 1, $maxRecords = 1000, $listAuthors = false) {
    if($listAuthors) {
        $items = $this->getAllItems();
    } else {
        if (!empty($objectId)) {
            $items = $this->getChildren($objectId,$startRecord, $maxRecords);
        } else {
            $items = $this->getChildren($this->rootObjectId,$startRecord, $maxRecords);
        }
    }
    return $items;
}

// get all authors
public function getAuthors() {
    $publications = $this->loadList(null, 1, 1000, true);
    $authors = array();
    foreach ($publications as $publication) {
        foreach ($publication['creators'] as $creator) {
            if ($creator['identifier']){
                $authors[] = $creator['identifier'];   
            }
        }
    }
    return array_unique($authors);
}

public function getAuthorsForRecord($id) {
    $publications = $this->loadList(null, 1, 1000, true);
    foreach ($publications as $publication) {
        if ($publication['object_id'] == $id){
            return $publication['creators'];
        }
    }
    return [];
}

public function getTitleForRecord($id) {
    $publications = $this->loadList(null, 1, 1000, true);
    foreach ($publications as $publication) {
        if ($publication['object_id'] == $id){
            return $publication['escidoc_title'];
        }
    }
    return "";
}

public function getPublication($id) {
    $publications = $this->loadList(null, 1, 1000, true);
    foreach ($publications as $publication) {
        if ($publication['object_id'] == $id){
            return $publication;
        }
    }
    return [];
}

public function getXML() {
    $tmp_pubman_options = $this->pubman_options;
    $objectId = $this->rootObjectId;
    $search_string_tmp  = $this->settings['search_string_children'];
    $tmp_pubman_options['search_string'] = $search_string_tmp[0] .
    urlencode($objectId) . $search_string_tmp[1] . urlencode($objectId) . $search_string_tmp[2];
    //$tmp_pubman_options['search_string'] = 'escidoc.objecttype=%22item%22%20and%20escidoc.content-model.objid=%22ubl%3A5001%22%20AND%20((escidoc.any-identifier%3D"' . urlencode($objectId) . '"))%20';
    $citation = 'ESCIDOC_XML_V13'; //AJP, JUS, APA

    $query = array( 'cqlQuery' => $tmp_pubman_options['search_string'],
                        'exportFormat' => $citation, //ESCIDOC_XML_V13
                        'outputFormat' => 'escidoc_snippet',
                        'sortKeys' => 'escidoc.publication.source.issue',
                        'sortOrder' => 'ascending',
                        'startRecord' => '1',
                        'maximumRecords' => '1000');

    $rest = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\LeipzigUniversityLibrary\PubmanImporter\Library\PMIRest', $tmp_pubman_options);
    $data = $rest->getData($tmp_pubman_options['search_export_interface'], $query);

    return $data;
    }

}
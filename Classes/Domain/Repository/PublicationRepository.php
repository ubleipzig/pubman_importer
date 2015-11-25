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
            'host_name'                 => $this->settings['host_name'],
            'port_number'               => $this->settings['port_number'],
            'content_model_id'          => $this->settings['content_model_id'],
            'context_id'                => $this->settings['context_id'],
            'source_url'                => $this->settings['source_url'],
            'item_view'                 => $this->settings['item_view'],
            'search_export_interface'   => $this->settings['publication_search_export_interface'],
            'search_string'             => $this->_buildSearchString(),
            'fulltext_option_without'   => intval($this->settings['fulltext_option_without']),
            'fulltext_option_nonpublic' => intval($this->settings['fulltext_option_nonpubic']),
            );
    }

    /**
     * shows all Articles of an issue
     *
     * @return mixed
     */
    private function getAllItems() {
        $tmp_pubman_options = $this->pubman_options;
        $tmp_pubman_options['search_string'] = $this->_buildAllItemsSearchString();

        $query = array( 'cqlQuery'       => $this->_buildAllItemsSearchString(),
                        'exportFormat'   => $this->settings['citation_format'], //ESCIDOC_XML_V13
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

    /**
     * shows all issues
     *
     * @param $objectId
     * @param $startRecord
     * @param $maxRecords
     * @param bool|false $nestedCall
     * @return mixed
     */
    private function getChildren($objectId, $startRecord, $maxRecords, $nestedCall = false) {

        $query = array( 'cqlQuery'       => $this->_buildChildrenSearchString($objectId),
                        'exportFormat'   => $this->settings['citation_format'], //ESCIDOC_XML_V13
                        'outputFormat'   => 'escidoc_snippet',
                        'sortKeys'       => 'escidoc.publication.source.issue',
                        'sortOrder'      => 'ascending',
                        'startRecord'    => $startRecord,
                        'maximumRecords' => $maxRecords);

        $rest = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\LeipzigUniversityLibrary\PubmanImporter\Library\PMIRest', $this->pubman_options);
        $data = $rest->getData($this->pubman_options['search_export_interface'], $query);
        $items = $rest->parseXML($data, "pubRepo");

        if (count($items) > 0 && !$nestedCall) { //use nestedCall to traverse only one level down
            for ($i=0; $i<count($items); $i++) {

                // server URL
                $items[$i]['source_url'] = $this->pubman_options['source_url'];

                if (isset($items[$i]['object_id'])) {
                    //check for each child-item with an escidoc-id if it has children of its own
                    $childItems = $this->getChildren(($items[$i]['object_id']), $startRecord, $maxRecords, true);

                   //set hasChildren flag for items that have children
                    if (count($childItems)>0) $items[$i]['hasChildren'] = true;
                    else $items[$i]['hasChildren'] = false;

                    if ($items[$i]['hasChildren'] && !$nestedCall) {
                        //create link to list
                        $items[$i]['link'] = $this->cObject->getTypolink_URL($GLOBALS['TSFE']->id,
                            array( 'tx_pubmanimporter_publications[objectId]' => $items[$i]['object_id'],'tx_pubmanimporter_publications[action]' => 'list', )
                        );
                    } else {
                        $items[$i]['link'] = $this->cObject->getTypolink_URL($GLOBALS['TSFE']->id,
                            array( 'tx_pubmanimporter_publications[objectId]' => $items[$i]['object_id'],'tx_pubmanimporter_publications[action]' => 'show', )
                        );
                    }
                }
            }
        }

        return $items;
    }

    public function getXML() {
        $tmp_pubman_options = $this->pubman_options;
        $tmp_pubman_options['search_string'] = $this->_buildChildrenSearchString($this->rootObjectId);
        $query = array( 'cqlQuery' => $this->_buildChildrenSearchString($this->rootObjectId),
                        'exportFormat' => $this->settings['citation_format'], //ESCIDOC_XML_V13
                        'outputFormat' => 'escidoc_snippet',
                        'sortKeys' => 'escidoc.publication.source.issue',
                        'sortOrder' => 'ascending',
                        'startRecord' => '1',
                        'maximumRecords' => '1000');

        $rest = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\LeipzigUniversityLibrary\PubmanImporter\Library\PMIRest', $tmp_pubman_options);
        $data = $rest->getData($this->pubman_options['search_export_interface'], $query);

        return $data;
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

    private function _buildSearchString() {
        return sprintf(
                $this->settings['search_string'],
                $this->settings['content_model_id'],
                $this->settings['root_object_id'],
                $this->settings['root_object_id']
        );
    }

    private function _buildAllItemsSearchString() {
        return sprintf(
                $this->settings['search_string_all_items'],
                $this->settings['content_model_id'],
                $this->settings['context_id']
        );
    }

    private function _buildChildrenSearchString($objectId) {
        return sprintf(
                $this->settings['search_string_children'],
                $this->settings['content_model_id'],
                $objectId,
                $objectId
        );
    }
}
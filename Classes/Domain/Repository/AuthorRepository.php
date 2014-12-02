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
 * The repository for Authors
 */
class AuthorRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

    private $cObject;

    public function __construct() {
        $this->cObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
    }

    private function parseXML($data) { 
        if (!$data) {
            return '';
        }
        $xml = \DOMDocument::loadXML($data);
        $xpath = new \DOMXPath($xml);
        $authors = array();
        $nodes = $xpath->query('//rdf:Description');
        for ($i = 0; $i < $nodes->length; $i++) {
            $node = $nodes->item($i);
            $author = array (
                'name' => '',
                'link' => ''
                );
            $titleNode = $node->getElementsByTagNameNS($node->lookupNamespaceURI('dc'),'title')->item(0);
            $author['name'] = $titleNode->textContent;
            $author['link'] = $node->getAttribute('rdf:about');
            $authors[] = $author;
        }
        return $authors;
    }

     /**
     * Returns the author-details for a given number
     *
     * @return array $author
     */
    public function getAuthorDetails($identifier) {
        $data = file_get_contents($identifier."?format=rdf");
        $xml = \DOMDocument::loadXML($data);
        $xpath = new \DOMXPath($xml);
        $node = $xpath->query('//rdf:Description')->item(0);
        $author = array(
            'family_name' => '',
            'name' => '',
            'given_name' => '',
            'degree' => '',
            'link' => ''
            );
        if($node) {
            
            $familyNameNode = $node->getElementsByTagNameNS($node->lookupNamespaceURI('foaf'),'family_name')->item(0);
            $author['family_name'] = $familyNameNode->textContent;

            $nameNode = $node->getElementsByTagNameNS($node->lookupNamespaceURI('dc'),'title')->item(0);
            $author['name'] = $nameNode->textContent;

            $givenNameNode = $node->getElementsByTagNameNS($node->lookupNamespaceURI('foaf'),'givenname')->item(0);
            $author['given_name'] = $givenNameNode->textContent;

            $degreeNode = $node->getElementsByTagNameNS($node->lookupNamespaceURI('escidoc'),'degree')->item(0);
            $author['degree'] = $degreeNode->textContent;
            $author['link'] = $node->getAttribute('rdf:about');
        }
        return $author;

    }

    /**
     * Returns an author's publications
     *
     * @return array $publications
     */
    public function getPublications($identifier) {
        $source_url = 'http://141.39.229.2';
        $search_export_interface = '/search/SearchAndExport?';
        $search_options          = 'exportFormat=ESCIDOC_XML_V13&outputFormat=pdf&sortKeys=_relevance_&sortOrder=descending&startRecord=1&maximumRecords=50&cqlQuery=escidoc.publication.creator.person.family-name%3D';

        $publications = [];
        $search_url = $source_url.$search_export_interface.$search_options.$identifier;
        $data = file_get_contents($search_url);
        $xml = simplexml_load_string($data);
        $titleXPath = "/escidocItemList:item-list/escidocItem:item/escidocMetadataRecords:md-records[1]/escidocMetadataRecords:md-record[1]/publication:publication[1]/*[namespace-uri()='http://purl.org/dc/elements/1.1/' and local-name()='title'][1]";
        foreach( $xml -> xpath($titleXPath) as $child){
            $publications[]=(string)$child;
        }

        return $publications;
    }

    public function loadList() {
        $pubRepo = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\LeipzigUniversityLibrary\PubmanImporter\Domain\Repository\PublicationRepository');
        $authors = $pubRepo->getAuthors();
        return $authors;
    }


}
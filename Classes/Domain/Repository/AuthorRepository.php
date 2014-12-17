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
    
    public function __construct($settings = NULL) {
        $this->cObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
        $this->settings = $settings;
    }

    private function parseXML($data) { 
        $authors = array();
        if (!$data) {
            return $authors;
        }
        $xml = \DOMDocument::loadXML($data);
        $xpath = new \DOMXPath($xml);
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
        $source_url                 = $this->settings['source_url'];
        $search_export_interface    = $this->settings['search_export_interface'];
        $search_options             = $this->settings['search_options'];
        $publicationIds             = array();
        $publicationTitles          = array();
        $search_url                 = $source_url.$search_export_interface.$search_options.$identifier;
        $data                       = file_get_contents($search_url);
        $xml                        = simplexml_load_string($data);
        $objidXPath                 = $this->settings['objidXPath'];
        $titleXPath                 = $this->settings['titleXPath'];
        foreach( $xml -> xpath($objidXPath) as $child){
            $publicationIds[]=(string)$child;
        }
        foreach( $xml -> xpath($titleXPath) as $child){
            $publicationTitles[]=(string)$child;
        }
        return array_combine($publicationIds, $publicationTitles);
    }

    public function loadList() {
        $pubRepo = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\LeipzigUniversityLibrary\PubmanImporter\Domain\Repository\PublicationRepository', $this->settings);
        $authors = $pubRepo->getAuthors($this->settings);
        return $authors;
    }
}
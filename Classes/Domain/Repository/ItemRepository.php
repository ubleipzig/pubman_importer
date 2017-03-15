<?php
/**
 * Copyright (C) Leipzig University Library 2017 <info@ub.uni-leipzig.de>
 *
 * @author  Ulf Seltmann <seltmann@ub.uni-leipzig.de>
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License

 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

namespace LeipzigUniversityLibrary\PubmanImporter\Domain\Repository;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ItemRepository
 */
class ItemRepository extends \LeipzigUniversityLibrary\PubmanImporter\Library\RepositoryAbstract
{

	/**
	 * Extracts component specific information from dom
	 *
	 * @param \DOMNodeList $nodeList
	 * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Item $model
	 * @return $this
	 */
	public function parseComponents($nodeList, $model) {
		$componentList = [];
		$mimeTypeList = [];
		foreach ($nodeList as $node) {
			$component = GeneralUtility::makeInstance('\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Component');
			$component->setUid($node->getAttribute('objid'));
			$component->setMimeType($this->_xpath->query('escidocComponents:properties/prop:mime-type', $node)->item(0)->nodeValue);
			$component->setFileName($this->_xpath->query('escidocComponents:properties/prop:file-name', $node)->item(0)->nodeValue);

			$componentFileMetadata = $this->_xpath->query('escidocMetadataRecords:md-records/escidocMetadataRecords:md-record/file:file', $node)->item(0);
			$component->setLicense($this->_xpath->query('dcterms:license', $componentFileMetadata)->item(0)->nodeValue);

			$contentNode = $this->_xpath->query('escidocComponents:content', $node)->item(0);
			$component->setStorage($contentNode->getAttribute('storage'));
			$component->setPath($contentNode->getAttribute('xlink:href'));
			$component->setUrl($this->getUrl());

			$componentList[] = $component;
			$mimeTypeList[] = $component->getMimeType();
		}

		array_multisort($mimeTypeList, SORT_DESC, $componentList);

		foreach ($componentList as $component) {
			$model->addComponent($component);
		}

		return $this;
	}

	/**
	 * Extracts creator specific information from dom
	 *
	 * @param \DOMNodeList $nodeList
	 * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Item $model
	 * @return $this
	 */
	public function parseCreators($nodeList, $model) {
		foreach ($nodeList as $node) {
			$creator = GeneralUtility::makeInstance('\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Creator');
			$creator->setFamilyName($this->_xpath->query('person:person/eterms:family-name', $node)->item(0)->nodeValue);
			$creator->setGivenName($this->_xpath->query('person:person/eterms:given-name', $node)->item(0)->nodeValue);
			$creator->setOrganization($this->_xpath->query('person:person/organization:organization/dc:title', $node)->item(0)->nodeValue);
			$creator->setAddress($this->_xpath->query('person:person/organization:organization/eterms:address', $node)->item(0)->nodeValue);
			$creator->setUid($this->_xpath->query('person:person/organization:organization/dc:identifier', $node)->item(0)->nodeValue);
			$creator->setHref($this->_xpath->query('person:person/dc:identifier[@xsi:type="eterms:CONE"]', $node)->item(0)->nodeValue);
			$model->addCreator($creator);
		}

		return $this;
	}

	/**
	 * Extracts generic information from dom
	 *
	 * @param \DOMNode $node
	 * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Item $model
	 * @return $this
	 */
	public function parseGenerics($node, $model) {
		$this->_publicationNode = $this->_xpath->query('escidocMetadataRecords:md-records/escidocMetadataRecords:md-record/publication:publication', $node)->item(0);

		$model->setUid($node->getAttribute('objid'));
		$this->parseComponents($this->_xpath->query('escidocComponents:components/escidocComponents:component', $node), $model);
		$this->parseCreators($this->_xpath->query('eterms:creator', $this->_publicationNode), $model);

		$model->setTitle($this->_xpath->query('dc:title', $this->_publicationNode)->item(0)->nodeValue);
		$model->setReleaseDate(new \DateTime($this->_xpath->query('escidocItem:properties/prop:latest-release/release:date', $node)->item(0)->nodeValue));
		$model->setPublisher($this->_xpath->query('eterms:publishing-info/dc:publisher', $this->_publicationNode)->item(0)->nodeValue);
		$model->setAbstract($this->_xpath->query('dcterms:abstract', $this->_publicationNode)->item(0)->nodeValue);
		$model->setSubject($this->_xpath->query('dcterms:subject', $this->_publicationNode)->item(0)->nodeValue);

		$model->setIssuedYear($this->_xpath->query('dcterms:issued[@xsi:type="dcterms:W3CDTF"]', $this->_publicationNode)->item(0)->nodeValue);
		$model->setIssueTerm($this->_xpath->query('source:source/eterms:issue', $this->_publicationNode)->item(0)->nodeValue);

		return $this;
	}

	/**
	 * Extracts information from the dom
	 *
	 * @param bool|false $pid
	 * @return array
	 * @throws \Exception
	 */
	public function parse($pid = false)
	{
		$this->parseXml();

		$result = [];

		foreach ($this->_xpath->query('/escidocItemList:item-list/escidocItem:item') as $itemNode) {
			$model = GeneralUtility::makeInstance('\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Item');

			$this->parseGenerics($itemNode, $model);

			if ($pid) $model->setPid($pid);

			$result[] = $model;
		}

		return $result;
	}
}
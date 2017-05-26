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

namespace LeipzigUniversityLibrary\PubmanImporter\Library;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * abstract class RepositoryAbstract
 *
 */
abstract class RepositoryAbstract
{
	/**
	 * The content model object id every item consists of
	 *
	 * @var string
	 */
	protected $_escidocContentModelObjId;

	/**
	 * The context object id
	 *
	 * @var string
	 */
	protected $_escidocContextObjId;

	/**
	 * The cql-query patterns for getting the expected item(s)
	 *
	 * @var array
	 */
	protected $_cqlQueryPattern = [
		'all' => 'escidoc.objecttype="item" AND escidoc.content-model.objid="%1$s" AND escidoc.context.objid="%2$s"',
		'byPid' => 'escidoc.objecttype="item" AND escidoc.content-model.objid="%1$s" AND escidoc.context.objid="%2$s" AND (escidoc.any-identifier="%3$s" NOT escidoc.objid="%3$s")',
		'byUid' => 'escidoc.objecttype="item" AND escidoc.content-model.objid="%1$s" AND escidoc.context.objid="%2$s" AND escidoc.objid="%3$s"',
		'byCreator' => 'escidoc.objecttype="item" AND escidoc.content-model.objid="%1$s" AND escidoc.context.objid="%2$s" AND escidoc.publication.creator.person.organization.identifier="%3$s"',
	];

	/**
	 * The export format we parse
	 *
	 * @var string
	 */
	protected $_exportFormat = 'ESCIDOC_XML_V13';

	/**
	 * The sort direction
	 *
	 * @var string
	 */
	protected $_sortOrder = 'descending';

	/**
	 * The default sorting
	 *
	 * @var string
	 */
	protected $_sortKeys = 'sort.escidoc.property.latest-release.date';

	/**
	 * The offset record
	 *
	 * @var string
	 */
	protected $_startRecord = '1';

	/**
	 * The maximum count of records
	 *
	 * @var string
	 */
	protected $_maximumRecords = '5000';

	/**
	 * The query settings
	 *
	 * @var array
	 */
	protected $_querySettings = [];

	/**
	 * The scheme host port part of the uri
	 *
	 * @var string
	 */
	protected $_url;

	/**
	 * The path part of the uri
	 *
	 * @var string
	 */
	protected $_path;

	/**
	 * The query part of the uri
	 *
	 * @var string
	 */
	protected $_query;

	/**
	 * The parsed xml dom document
	 *
	 * @var \DomDocument
	 */
	protected $_domDocument;

	/**
	 * The source xml string
	 *
	 * @var
	 */
	protected $_body;

	/**
	 * The xpath object of $_domDocument
	 *
	 * @var \DOMXPath
	 */
	protected $_xpath;

	/**
	 * The dom node for the publication element (since its used by multiple parsers)
	 *
	 * @var \DOMNode
	 */
	protected $_publicationNode;

	/**
	 * The http request object
	 * its not injected though
	 *
	 * @var \TYPO3\CMS\Core\Http\HttpRequest
	 * @inject
	 */
	protected $_httpRequest;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->_querySettings = [
			'exportFormat' => $this->_exportFormat,
			'sortKeys' => $this->_sortKeys,
			'sortOrder' => $this->_sortOrder,
			'startRecord' => $this->_startRecord,
			'maximumRecords' => $this->_maximumRecords
		];

		$this->_httpRequest = GeneralUtility::makeInstance('\TYPO3\CMS\Core\Http\HttpRequest');
	}

	/**
	 * Overrides the property values from settings (injected by the controller's 'settings' property)
	 *
	 * @param $settings
	 */
	public function setOptions($settings) {
		foreach ($settings as $key => $value) {
			if (false === property_exists($this, '_' . $key)) continue;

			$this->{'_' . $key} = $value;
		}
	}

	/**
	 * Streams directly to output without buffering or copying to memory
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
	 */
	public function stream() {
		$this->createQuery();

		$uri = $this->_url . $this->_path . (empty($this->_query) ? '' : '?' . $this->_query);

		if (ob_get_level()) ob_end_clean();

		$curlRequest = curl_init();
		curl_setopt($curlRequest, CURLOPT_URL, $uri);
		curl_setopt($curlRequest, CURLOPT_RETURNTRANSFER, false);
		curl_setopt($curlRequest, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curlRequest, CURLOPT_CONNECTTIMEOUT ,30);
		curl_setopt($curlRequest, CURLOPT_TIMEOUT, 400);
		$fileResponse = curl_exec($curlRequest);
		curl_close($curlRequest);
		echo $fileResponse;

		return $this;
	}

	/**
	 * Returns a query for objects of this repository
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
	 * @throws \Exception if http request failed
	 */
	public function execute() {
		$this->createQuery();

		$response = $this->_httpRequest->setUrl($this->_url . $this->_path . (empty($this->_query) ? '' : '?' . $this->_query))->send();

		if ($response->getStatus() !== 200) {
			throw new \Exception(sprintf('Request failed: %s', $response->getStatus()));
		}

		$this->_body = $response->getBody();

		return $this;
	}

	/**
	 * Parses the xml string into the dom document and its xpath handle
	 *
	 * @return $this
	 */
	public function parseXml() {
		libxml_use_internal_errors(true);
		$this->_domDocument = new \DOMDocument();
		$this->_domDocument->loadXML($this->_body);
		$this->_xpath = new \DOMXPath($this->_domDocument);

		return $this;
	}

	/**
	 * Creates a query string from the query settings
	 *
	 * @return $this
	 */
	public function createQuery()
	{
		$this->_query = http_build_query($this->_querySettings);

		return $this;
	}

	/**
	 * Returns all objects of this repository.
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array The query result
	 */
	public function findAll() {
		$this->_querySettings['cqlQuery'] = sprintf(
			$this->_cqlQueryPattern['all'],
			$this->_escidocContentModelObjId,
			$this->_escidocContextObjId
		);

		return $this->execute()->parse();
	}

	/**
	 * Finds an object matching the given identifier.
	 *
	 * @param int|\TYPO3\CMS\Extbase\DomainObject\AbstractEntity $id The identifier of the object to find
	 * @return object The matching object if found, otherwise NULL
	 * @throws \Exception
	 */
	public function findByUid($id) {
		if ($id instanceof \TYPO3\CMS\Extbase\DomainObject\AbstractEntity) $id = $id->getUid();

		$this->_querySettings['cqlQuery'] = sprintf(
			$this->_cqlQueryPattern['byUid'],
			$this->_escidocContentModelObjId,
			$this->_escidocContextObjId,
			$id
		);

		return $this->execute()->parse()[0];
	}

	/**
	 * Finds an object matching the given parent identifier.
	 *
	 * @param int|\TYPO3\CMS\Extbase\DomainObject\AbstractEntity $id The identifier of the parent object to find
	 * @return mixed
	 * @throws \Exception
	 */
	public function findByPid($id) {
		if ($id instanceof \TYPO3\CMS\Extbase\DomainObject\AbstractEntity) $id = $id->getUid();

		$this->_querySettings['cqlQuery'] = sprintf(
			$this->_cqlQueryPattern['byPid'],
			$this->_escidocContentModelObjId,
			$this->_escidocContextObjId,
			$id
		);

		return $this->execute()->parse($id);
	}

	/**
	 * Finds an object matching the given creator identifier.
	 *
	 * @param int|\TYPO3\CMS\Extbase\DomainObject\AbstractEntity $id The identifier of the parent object to find
	 * @return mixed
	 * @throws \Exception
	 */
	public function findByCreator($id) {
		if ($id instanceof \TYPO3\CMS\Extbase\DomainObject\AbstractEntity) $id = $id->getUid();

		$this->_querySettings['cqlQuery'] = sprintf(
			$this->_cqlQueryPattern['byCreator'],
			$this->_escidocContentModelObjId,
			$this->_escidocContextObjId,
			$id
		);

		return $this->execute()->parse($id);
	}

	/**
	 * Returns the total number objects of this repository.
	 *
	 * @return integer The object count
	 * @api
	 */
	public function countAll() {
		return $this->_xpath->query('/escidocItemList:item-list')->item(0)->getAttribute('number-of-records');
	}

	/**
	 * Returns the url
	 *
	 * @return string
	 */
	public function getUrl() {
		return $this->_url;
	}

	/**
	 * Returns the body string
	 *
	 * @return mixed
	 */
	public function getBody() {
		return $this->_body;
	}

	/**
	 * Defines the parse method to must be implemented
	 *
	 * @return mixed
	 */
	abstract public function parse();
}

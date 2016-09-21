<?php
namespace LeipzigUniversityLibrary\PubmanImporter\Library;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class RepositoryAbstract
{
    /**
     * the content model object id every item consists of
     *
     * @var string
     */
    protected $_escidocContentModelObjid;

    /**
     * the context object id
     *
     * @var string
     */
    protected $_escidocContextObjid;

    /**
     * the cql-query patterns for getting the expected item(s)
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
     * the export format we parse
     *
     * @var string
     */
    protected $_exportFormat = 'ESCIDOC_XML_V13';

    /**
     * the sort direction
     *
     * @var string
     */
    protected $_sortOrder = 'descending';

    /**
     * the default sorting
     *
     * @var string
     */
    protected $_sortKeys = 'sort.escidoc.property.latest-release.date';

    /**
     * the offset record
     *
     * @var string
     */
    protected $_startRecord = '1';

    /**
     * the maximum count of records
     *
     * @var string
     */
    protected $_maximumRecords = '5000';

    /**
     *
     * @var array
     */
    protected $_querySettings = [];

    /**
     * the scheme host port part of the uri
     *
     * @var string
     */
    protected $_url;

    /**
     * the path part of the uri
     *
     * @var string
     */
    protected $_path;

    /**
     * the query part of the uri
     *
     * @var string
     */
    protected $_query;

    /**
     * the parsed xml dom document
     *
     * @var \DomDocument
     */
    protected $_domDocument;

    /**
     * the source xml string
     *
     * @var
     */
    protected $_body;

    /**
     * the xpath object of $_domDocument
     *
     * @var \DOMXPath
     */
    protected $_xpath;

    /**
     * the dom node for the publication element (since its used by multiple parsers)
     *
     * @var \DOMNode
     */
    protected $_publicationNode;

    /**
     * the http request object
     * its not injected though
     *
     * @var \TYPO3\CMS\Core\Http\HttpRequest
     * @inject
     */
    protected $_httpRequest;

    /**
     * constructor
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
     * overrides the property values from settings (injected by the controller's 'settings' property)
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
     * Returns a query for objects of this repository
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
     * @api
     * @throws \Exception if http request failed
     */
    public function execute()
    {
        $this->createQuery();

        $response = $this->_httpRequest->setUrl($this->_url . $this->_path . (empty($this->_query) ? '' : '?' . $this->_query))->send();

        if ($response->getStatus() !== 200) {
            throw new \Exception(sprintf('Request failed: %s', $response->getStatus()));
        }

        $this->_body = $response->getBody();

        return $this;
    }

    /**
     * parses the xml string into the dom document and its xpath handle
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
     * creates a query string from the query settings
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
     * @api
     */
    public function findAll() {
        $this->_querySettings['cqlQuery'] = sprintf(
            $this->_cqlQueryPattern['all'],
            $this->_escidocContentModelObjid,
            $this->_escidocContextObjid
        );

        return $this->execute()->parse();
    }

    /**
     * Finds an object matching the given identifier.
     *
     * @param int|\TYPO3\CMS\Extbase\DomainObject\AbstractEntity $id The identifier of the object to find
     * @return object The matching object if found, otherwise NULL
     * @api
     * @throws \Exception
     */
    public function findByUid($id) {
        if ($id instanceof \TYPO3\CMS\Extbase\DomainObject\AbstractEntity) $id = $id->getUid();

        $this->_querySettings['cqlQuery'] = sprintf(
            $this->_cqlQueryPattern['byUid'],
            $this->_escidocContentModelObjid,
            $this->_escidocContextObjid,
            $id
        );

        return $this->execute()->parse()[0];
    }

    /**
     * Finds an object matching the given parent identifier.
     *
     * @param int|\TYPO3\CMS\Extbase\DomainObject\AbstractEntity $id The identifier of the parent object to find
     * @return mixed
     * @api
     * @throws \Exception
     */
    public function findByPid($id) {
        if ($id instanceof \TYPO3\CMS\Extbase\DomainObject\AbstractEntity) $id = $id->getUid();

        $this->_querySettings['cqlQuery'] = sprintf(
            $this->_cqlQueryPattern['byPid'],
            $this->_escidocContentModelObjid,
            $this->_escidocContextObjid,
            $id
        );

        return $this->execute()->parse($id);
    }

    /**
     * Finds an object matching the given creator identifier.
     *
     * @param int|\TYPO3\CMS\Extbase\DomainObject\AbstractEntity $id The identifier of the parent object to find
     * @return mixed
     * @api
     * @throws \Exception
     */
    public function findByCreator($id) {
        if ($id instanceof \TYPO3\CMS\Extbase\DomainObject\AbstractEntity) $id = $id->getUid();

        $this->_querySettings['cqlQuery'] = sprintf(
            $this->_cqlQueryPattern['byCreator'],
            $this->_escidocContentModelObjid,
            $this->_escidocContextObjid,
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
     * returns the url
     *
     * @return string
     */
    public function getUrl() {
        return $this->_url;
    }

    /**
     * returns the body string
     *
     * @return mixed
     */
    public function getBody() {
        return $this->_body;
    }

    /**
     * defines the parse method to must be implemented
     *
     * @return mixed
     */
    abstract public function parse();
}

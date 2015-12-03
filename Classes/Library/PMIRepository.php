<?php
namespace LeipzigUniversityLibrary\PubmanImporter\Library;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class PMIRepository implements \TYPO3\CMS\Extbase\Persistence\RepositoryInterface
{

    protected $_escidocContentModelObjid = 'escidoc:2001';

    protected $_escidocContextObjid = 'ubl:10444';

    protected $_escidocPublicationType;

    protected $_cqlQueryPattern = [
        'all' => 'escidoc.objecttype="item" AND escidoc.content-model.objid="%1$s" AND escidoc.context.objid="%2$s" AND escidoc.publication.type="%3$s"',
        'byPid' => 'escidoc.objecttype="item" AND escidoc.content-model.objid="%1$s" AND escidoc.context.objid="%2$s" AND (escidoc.any-identifier="%3$s" NOT escidoc.objid="%3$s")',
        'byUid' => 'escidoc.objecttype="item" AND escidoc.content-model.objid="%1$s" AND escidoc.context.objid="%2$s" AND escidoc.objid="%3$s" AND escidoc.publication.type="%4$s"',
        'byCreator' => 'escidoc.objecttype="item" AND escidoc.content-model.objid="%1$s" AND escidoc.context.objid="%2$s" AND escidoc.publication.creator.person.organization.identifier="%3$s"',
    ];

    protected $_exportFormat = 'ESCIDOC_XML_V13';

    protected $_sortOrder = 'descending';

    protected $_sortKeys = 'sort.escidoc.property.latest-release.date';

    protected $_startRecord = '1';

    protected $_maximumRecords = '5000';

    protected $_querySettings = [];

    protected $_url = 'https://publishing.ub.uni-leipzig.de';

    protected $_path = '/search/SearchAndExport';

    protected $_query = '';

    protected $_domDocument;

    protected $_body;

    protected $_xpath;

    protected $_publicationNode;


    /**
     * @var \TYPO3\CMS\Core\Http\HttpRequest
     * @inject
     */
    protected $_httpRequest;

    /**
     * PMIRepository Constructor
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

    public function setOptions($settings) {
        foreach ($settings as $key => $value) {
            if (false === property_exists($this, '_' . $key)) continue;

            $this->{'_' . $key} = $value;
        }
    }

    /**
     * Adds an object to this repository.
     *
     * @param object $object The object to add
     * @return void
     * @api
     */
    public function add($object)
    {
        throw new \Exception(__METHOD__ . ' not implemented on readonly repository');
    }

    /**
     * Removes an object from this repository.
     *
     * @param object $object The object to remove
     * @return void
     * @api
     */
    public function remove($object)
    {
        throw new \Exception(__METHOD__ . ' not implemented on readonly repository');
    }

    /**
     * Replaces an existing object with the same identifier by the given object
     *
     * @param object $modifiedObject The modified object
     * @api
     */
    public function update($modifiedObject)
    {
        throw new \Exception(__METHOD__ . ' not implemented on readonly repository');
    }

    /**
     * Removes all objects of this repository as if remove() was called for
     * all of them.
     *
     * @return void
     * @api
     */
    public function removeAll()
    {
        throw new \Exception(__METHOD__ . ' not implemented on readonly repository');
    }

    /**
     * Sets the default query settings to be used in this repository
     *
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface $defaultQuerySettings The query settings to be used by default
     * @return void
     * @api
     */
    public function setDefaultQuerySettings(\TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface $defaultQuerySettings)
    {
        return $this;
    }

    /**
     * Sets the property names to order the result by per default.
     * Expected like this:
     * array(
     * 'foo' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
     * 'bar' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING
     * )
     *
     * @param array $defaultOrderings The property names to order by
     * @return void
     * @api
     */
    public function setDefaultOrderings(array $defaultOrderings) {
        var_dump($defaultOrderings);
    }

    /**
     * Returns a query for objects of this repository
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
     * @api
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

    public function parseXml() {
        $this->_domDocument = \DOMDocument::loadXML($this->_body);
        $this->_xpath = new \DOMXPath($this->_domDocument);

        return $this;
    }

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
            $this->_escidocContextObjid,
            $this->_escidocPublicationType
        );

        return $this->execute()->parse();
    }

    /**
     * Finds an object matching the given identifier.
     *
     * @param integer $uid The identifier of the object to find
     * @return object The matching object if found, otherwise NULL
     * @api
     */
    public function findByUid($id) {
        if ($id instanceof \TYPO3\CMS\Extbase\DomainObject\AbstractEntity) $id = $id->getUid();

        $this->_querySettings['cqlQuery'] = sprintf(
            $this->_cqlQueryPattern['byUid'],
            $this->_escidocContentModelObjid,
            $this->_escidocContextObjid,
            $id,
            $this->_escidocPublicationType
        );

        return  $this->execute()->parse()[0];
    }

    public function findByPid($id) {
        if ($id instanceof \TYPO3\CMS\Extbase\DomainObject\AbstractEntity) $id = $id->getUid();

        $this->_querySettings['cqlQuery'] = sprintf(
            $this->_cqlQueryPattern['byPid'],
            $this->_escidocContentModelObjid,
            $this->_escidocContextObjid,
            $id,
            $this->_escidocPublicationType
        );

        return $this->execute()->parse($id);
    }

    public function findByCreator($id) {
        if ($id instanceof \TYPO3\CMS\Extbase\DomainObject\AbstractEntity) $id = $id->getUid();

        $this->_querySettings['cqlQuery'] = sprintf(
            $this->_cqlQueryPattern['byCreator'],
            $this->_escidocContentModelObjid,
            $this->_escidocContextObjid,
            $id,
            $this->_escidocPublicationType
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
     * Finds an object matching the given identifier.
     *
     * @param mixed $identifier The identifier of the object to find
     * @return object The matching object if found, otherwise NULL
     * @api
     */
    public function findByIdentifier($uid) {
        $this->findById($uid);
    }

    public function parseComponents($nodeList, $model) {
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
            $model->addComponent($component);
        }

        return $this;
    }

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

    public function parseGenerics($node, $model) {
        $this->_publicationNode = $this->_xpath->query('escidocMetadataRecords:md-records/escidocMetadataRecords:md-record/publication:publication', $node)->item(0);

        $model->setUid($node->getAttribute('objid'));
        $this->parseComponents($this->_xpath->query('escidocComponents:components/escidocComponents:component', $node), $model);
        $this->parseCreators($this->_xpath->query('eterms:creator', $this->_publicationNode), $model);

        $model->setTitle($this->_xpath->query('dc:title', $this->_publicationNode)->item(0)->nodeValue);
        $model->setReleaseDate(new \DateTime($this->_xpath->query('escidocItem:properties/prop:latest-release/release:date', $node)->item(0)->nodeValue));
        $model->setPublisher($this->_xpath->query('eterms:publishing-info/dc:publisher', $this->_publicationNode)->item(0)->nodeValue);
        $model->setAbstract($this->_xpath->query('dcterms:abstract', $this->_publicationNode)->item(0)->nodeValue);
        $model->setIdentifier($this->_xpath->query('source:source/dc:identifier[@xsi:type="eterms:URI"]', $this->_publicationNode)->item(0)->nodeValue);

        return $this;
    }

    public function getUrl() {
        return $this->_url;
    }

    public function getBody() {
        return $this->_body;
    }

    abstract public function parse();
}

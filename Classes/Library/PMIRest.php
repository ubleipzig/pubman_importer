<?php
namespace LeipzigUniversityLibrary\PubmanImporter\Library;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * REST Interface Class
 * All the communication with the PubMan REST interface is handled by this class
 *
 * @package pubman_import
 * @author
 */
class PMIRest implements \TYPO3\CMS\Core\SingletonInterface
{

	/*
	 * Possible options:
	 *      [source_url] - Base-URL, e.g. http://pubman.mpdl.mpg.de
	 *      [item_view]  - Set path to full item view in PubMan, e.g. /pubman/item/
	 *      [search_export_interface] - Set path to the Search & Export Interface, e.g. /search/SearchAndExport?cqlQuery=
	 *      [search_string] - Default search string e.g. "escidoc.metadata=test and escidoc.content-model.objid=escidoc:persistent4".<br/>You can use the CQL query from the PubMan advanced search.<br/>Depending on your search string it can take some time to fetch the corresponding items from PubMan.
	 *      [update_interval] - Set the update interval to check for new and updated PubMan items.<br/>If you choose "Manually" you can start the update from the "Maintenance" tab.
	 *                          values: hourly, daily, weekly, monthly, manually
	 *      [fulltext_option_nonpublic], [fulltext_option_without] - With this option set you can choose which PubMan items you want to be imported.
	 *                          values: 'Items with non-public fulltexts' => 0,
	 *                                  'Items without fulltexts' => 0
	 *      [public_available_only_cc] - With this option set you allow only PubMan items to be imported that have publicly available fulltext provided with a CC license.
	 *                          values: 'No' => 0,
	 *                                  'Yes' => 1
	 *      [citation_style] - Set the default citation style.
	 *                          values: 'APA' => 0,
	 *                                  'JUS' => 1,
	 *                                  'AJP' => 2,
	 *
	 */
	private $options;

	/**
	 * @var \TYPO3\CMS\Core\Http\HttpRequest
	 * @inject
	 */
	protected $httpRequest;

	/**
	 * PMIRest Contructor
	 */
	public function __construct($option)
	{
		$this->httpRequest = GeneralUtility::makeInstance('\TYPO3\CMS\Core\Http\HttpRequest');
		$this->options = $option;
	}

	/**
	 * Requests data from destination host, uri
	 *
	 * @param string $uri uri
	 * @param array $query query array
	 * @return string data response
	 */
	public function getData($uri, $query)
	{
		PMILog::debug('Entering getData');
		try {
			$http_url = $this->options['source_url'] . $uri . $this->query2string($query);
			$this->httpRequest->setUrl($http_url);
			PMILog::push(sprintf('PubMan Query:"%s"', $http_url));
			$response = $this->httpRequest->send();

			if ($response->getStatus() !== 200) {
				PMILog::push(sprintf(
					'Got HTTP Code %d (%s)',
					$response->getStatus(),
					$this->getStatusCodeMessage($response->getStatus())
				));
			}
			return $response->getBody();

		} catch(\Exception $e) {
			PMILog::push(sprintf(
				'Error (%s): %s while connecting to %s on port %d',
				$e->getCode(),
				$e->getMessage(),
				$this->options['host_name'],
				$this->options['port_number']
			));
		}
		PMILog::debug("Leaving getData");
	}

	public function parseXML($data)
	{
		PMILog::debug('Entering parseXML');
		if (!$data) {
			PMILog::push('No XML data to process');
			return '';
		}
		$xml = \DOMDocument::loadXML($data);

		$posts = array();
		$escidocItems = $xml->getElementsByTagNameNS($xml->lookupNamespaceURI('escidocItem'), 'item');

		if ($escidocItems->length)
			PMILog::push('Items to process: ' . $escidocItems->length);
		else
			PMILog::push('All entries loaded...');

		for ($i = 0; $i < $escidocItems->length; $i++) {

			$escidocItem = $escidocItems->item($i);

			$post = array('escidoc_content' => '', 'escidoc_title' => '', 'fulltextlink' => '', 'extfulltextlink' => '', 'escidoc_date' => '');

			$post['object_id'] = $this->getNodeAttr($escidocItem, 'objid');
			list(, $post['escidoc_id']) = explode(':', $this->getNodeAttr($escidocItem, 'objid'));
			PMILog::debug("Working on escidoc with id " . $post['escidoc_id']);

			$publication = $this->getNodeByPath($escidocItem, array('escidocMetadataRecords:md-records', 'escidocMetadataRecords:md-record', 'publication:publication'));

			$creatorNodesList = $publication->getElementsByTagNameNS($publication->lookupNamespaceURI('eterms'), 'creator');

			//create array for multiple creators
			$creators = array();

			$creatorArray = $this->dnl2array($creatorNodesList);
			foreach ($creatorArray as $creatorNode) {
				//build array with single creator information
				$creator = array();
				$creator['family-name'] = $this->getNodeValueByPath($creatorNode, array('person:person', 'eterms:family-name'));
				$creator['given-name'] = $this->getNodeValueByPath($creatorNode, array('person:person', 'eterms:given-name'));
				$creator['identifier'] = $this->getNodeValueByPath($creatorNode, array('person:person', 'dc:identifier'));

				//push creator-info-array into array for multiple creators
				array_push($creators, $creator);
			}

			$post['creators'] = $creators;

			$post['title'] = $this->getNodeValueByPath($publication, array('dc:title'));

			$post['publisher'] = $this->getNodeValueByPath($publication, array('eterms:publishing-info', 'dc:publisher'));
			$post['publisher-place'] = $this->getNodeValueByPath($publication, array('eterms:publishing-info', 'eterms:place'));

			/**
			 * foreach eterms:creator
			 * eterms:family-name
			 * eterms:given-name
			 * dc:identifier xsi:type="eterms:CONE"
			 *
			 * dc:title
			 *
			 * eterms:publishing-info
			 * dc:publisher
			 * eterms:place
			 *
			 * eterms:published-online xsi:type="dcterms:W3CDTF"
			 * dcterms:issued xsi:type="dcterms:W3CDTF"
			 */

			$componentNodes = $this->getNodeByPath($escidocItem, array('escidocComponents:components'));

			/**
			 * escidocComponents:components
			 * foreach escidocComponents:component objid="ubl:14002"
			 * escidocComponents:content xlink:type="simple" xlink:title="NI 95_96_Druck.pdf" xlink:href="/ir/item/ubl:14003/components/component/ubl:14002/content" storage="internal-managed"
			 */

			$content = [];
			$extFulltext = [];
			$license = "";
			$properties = array();
			$storage = "";
			if ($componentNodes) {
				$componentNodeList = $componentNodes->childNodes;
				$componentArray = $this->dnl2array($componentNodeList);
				foreach ($componentArray as $componentNode) {
					// extract properties
					$propertiesNode = $this->getNodeByPath($componentNode, array('escidocComponents:properties'));
					if ($propertiesNode) {
						$properties['file-name'] = $this->getNodeValueByPath($propertiesNode, array('prop:file-name'));
						$properties['mime-type'] = $this->getNodeValueByPath($propertiesNode, array('prop:mime-type'));
					}
					// extract full text links
					$contentNode = $this->getNodeByPath($componentNode, array('escidocComponents:content'));
					if ($contentNode) {
						$storage = $this->getNodeAttr($contentNode, 'storage');
						$href = $this->getNodeAttr($contentNode, 'href');
						if ($storage == "internal-managed") {
							if ($href) {
								$content[] = [
									'href' => $href,
									'mime-type' => $properties['mime-type'],
									'file-name' => $properties['file-name']
								];
								PMILog::debug("Found fulltext link: $href");
							}
						} else if ($storage == "external-url") {
							// seems to be an external reference
							if ($href) {
								$extFulltext[] = [
									'href' => $href,
									'mime-type' => $properties['mime-type'],
									'file-name' => $properties['file-name']
								];
								PMILog::debug("Found external fulltext link: $href");
							}
						} else {
							// seems to be different stuff
						}
					}
					// search for cc licenses. The first one we find, will be used
					if ($license == "") {
						$mdRecordsNode = $this->getNodeByPath($componentNode, array('escidocMetadataRecords:md-records'));
						if ($mdRecordsNode) {
							$mdRecordNodes = $mdRecordsNode->childNodes;
							if ($mdRecordNodes) {
								$mdRecordNodeArray = $this->dnl2array($mdRecordNodes);
								foreach ($mdRecordNodeArray as $mdRecordNode) {
									// if we haven't found a cc licence yet read the license tag
									if ($license == "") {
										$license = $this->getNodeValueByPath($mdRecordNode, array('file:file', 'dcterms:license'));
										if ($license != "") {
											PMILog::debug("Found possible license $license");
											if (strncmp($license, 'http://creativecommons', strlen('http://creativecommons'))) {
												PMILog::debug("It seems not to be a CC license and will be ignored");
												$license = "";
											}
										}
									}
								}
							}
							if ($license != "") {
								PMILog::debug("Found license $license");
							} else {
								PMILog::debug("No CC license found");
							}
						}
					}
				}
			}
			PMILog::debug("Combined links: " . $content);
			$post['license'] = $license;

			// process all nodes which can contain keywords (controlled vocabulary)
			/*$pubNodes = $publication->childNodes;
			if ($pubNodes) {
				$pubNodeArray = $this->dnl2array($pubNodes);
				foreach ($pubNodeArray As $pubNode) {
					if ($pubNode->nodeName == "dc:subject") {
						$dcSubjectsNode = $pubNode;
						// replace newline, enter and ; with , before explode so we can accept this delimiters
						$dcSubjects = explode(',', str_replace(array("\r\n", "\n", "\r", ";"), ",", $dcSubjectsNode->textContent));

						if ($this->options['autocreate_category'] && $dcSubjects != "") {
							PMILog::debug("Categories will be created");
							// Check if there's a given ISO type which can be used as parent category
							$ISOType = $this->getNodeAttr($dcSubjectsNode, 'type');
							$parentCat = 0;
							if ($ISOType != "") {
								$ISOType = explode(":", $ISOType);
								PMILog::debug("Main category will be $ISOType[1]");
								// we will create the category if there are keywords in it
							} else {
								PMILog::debug("No main category forund");
							}
							foreach ($dcSubjects as $subject) {
								if ($subject) {
									PMILog::debug("Found category $subject");
									$cat_id = get_cat_ID($subject);
									PMILog::debug("Checking WP for category $subject ... found ID $cat_id");
									if ($cat_id == 0) {
										// now we can create the parent category
										$parentCat = wp_create_category(trim($ISOType[1]));
										// and the category itself
										$cat_id = wp_create_category(trim($subject), (int) $parentCat);
										PMILog::debug("Created new WP category for $subject with ID $cat_id and parent $parentCat");
									}
									$post['post_category'][] = $cat_id;
									if ((int) $parentCat > 0) {
										$post['post_category'][] = (int) $parentCat;
									}
								}
							}
						}
					}
				}
			}
			if (!array_key_exists("post_category", $post)) {
				PMILog::debug("Setting post category to standard value (0)");
				$post['post_category'] = 0;
			}
			// replace enter, newline and ; with , before explode so we can accept this delimiters
			$dctermsSubjects = explode(',', str_replace(array("\r\n", "\n", "\r", ";"), ",", $this->getNodeValueByPath($publication, array('dcterms:subject'))));
			foreach ($dctermsSubjects as $tag) {
				if (!array_key_exists('post_tags', $post) || !is_array($post['post_tags'])) {
					$post['post_tags'] = array();
				}
				$post['post_tags'][] = trim($tag);
			}
			unset($dcSubjects, $dctermsSubjects);*/

			$post['escidoc_content'] = $this->getNodeByPath($escidocItem, array('escidocItem:properties', 'prop:content-model-specific', 'dcterms:bibliographicCitation'))->textContent;
			$post['escidoc_title'] = $this->getNodeValueByPath($publication, array('dc:title'));
			$post['properties'] = $properties;
			$post['fulltextlink'] = $content;
			$post['extfulltextlink'] = $extFulltext;
			$post['storage'] = $storage;
			// get a date that can be used for the wp post
			$dateEventEnd = $this->getNodeValueByPath($publication, array('event:event', 'eterms:end-date'));
			if ($dateEventEnd == "") {
				// try if there is a start date for an event
				$dateEventEnd = $this->getNodeValueByPath($publication, array('event:event', 'eterms:start-date'));
			}
			if ($dateEventEnd <> "") {
				PMILog::debug("Found event end date: $dateEventEnd");
				$post['escidoc_date'] = $dateEventEnd;
			} else {
				$datePublishedPrint = $this->getNodeValueByPath($publication, array('dcterms:issued'));
				if ($datePublishedPrint <> "") {
					PMILog::debug("Using published in print date $datePublishedPrint");
					$post['escidoc_date'] = $datePublishedPrint;
				} else {
					$datePublishedOnline = $this->getNodeValueByPath($publication, array('eterms:published-online'));
					if ($datePublishedOnline <> '') {
						PMILog::debug("Using published online date $datePublishedOnline");
						$post['escidoc_date'] = $datePublishedOnline;
					} else {
						$dateAccepted = $this->getNodeValueByPath($publication, array('dcterms:dateAccepted'));
						if ($dateAccepted <> '') {
							PMILog::debug("Using accepted date $dateAccepted");
							$post['escidoc_date'] = $dateAccepted;
						} else {
							$dateSubmitted = $this->getNodeValueByPath($publication, array('dcterms:dateSubmitted'));
							if ($dateSubmitted <> '') {
								PMILog::debug("Using submitted date $dateSubmitted");
								$post['escidoc_date'] = $dateSubmitted;
							} else {
								$dateModified = $this->getNodeValueByPath($publication, array('dcterms:modified'));
								if ($dateModified <> '') {
									PMILog::debug("Using modified date $dateModified");
									$post['escidoc_date'] = $dateModified;
								} else {
									$dateCreated = $this->getNodeValueByPath($publication, array('dcterms:created'));
									PMILog::debug("Using created date $dateCreated");
									$post['escidoc_date'] = $dateCreated;
								}
							}
						}
					}
				}
			}
			$posts[] = $post;
		}
		PMILog::debug("Leaving parseXML");
		return $posts;
	}


	/**
	 * Returns HTTP error codes
	 * @param integer $status HTTP error code
	 *
	 * @return string Human readable error message (localized)
	 */
	private function getStatusCodeMessage($status)
	{
		$codes = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => '(Unused)',
			307 => 'Temporary Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported'
		);
		return (isset($codes[$status])) ? __($codes[$status]) : '';
	}

	/**
	 * Conversion of array to string in uri format
	 *
	 * @param array $query array of options
	 * @return string encoded uri string
	 */
	private function query2string($query)
	{
		$result = '';
		foreach ($query as $key => $value) {
			$result .= $key . '=' . urlencode($value) . '&';
		}
		return '?' . rtrim($result, '&');
	}

	/**
	 * Converts a node to an array
	 * @param DOMNode $node
	 * @return array with values
	 */
	private function node2array($node)
	{
		$array = false;
		if ($node->hasAttributes()) {
			foreach ($node->attributes as $attr) {
				$array[$attr->nodeName] = $attr->nodeValue;
			}
		}
		if ($node->hasChildNodes()) {
			if ($node->childNodes->length == 1) {
				$array[$node->firstChild->nodeName] = $node->firstChild->nodeValue;
			} else {
				foreach ($node->childNodes as $childNode) {
					if ($childNode->nodeType != XML_TEXT_NODE) {
						$array[$childNode->nodeName][] = $this->node2array($childNode);
					}
				}
			}
		}
		return $array;
	}

	/**
	 * Returns the value of a node by its path
	 * @param DOMNode $dom Startpoint of the path
	 * @param array $path array with strings that define the path from the given node
	 * @return String value of the node
	 */
	private function getNodeValueByPath($dom, $path)
	{
		$node = $this->getNodeByPath($dom, $path);
		if ($node)
			return $node->nodeValue;
		else
			return FALSE;
	}

	/**
	 * Returns the node for a given node and a path
	 * @param DOMNode $dom Startpoint of the path
	 * @param array $path array with strings that define the path from the given node
	 * @return DOMNode node which is found at the path or FALSE if not found
	 */
	private function getNodeByPath($dom, $path)
	{
		$node = $dom;
		$pathIndex = 0;
		while ($pathIndex < count($path)) {
			$looking_for = $path[$pathIndex];
			if ($node && $node->childNodes) {
				foreach ($node->childNodes as $child) {
					if (count($path) >= $pathIndex + 1 && $child->nodeName == $path[$pathIndex]) {
						$node = $child;
						$pathIndex++;
						// break 2;
					}
				}
			}
			if (count($path) >= $pathIndex + 1 && $looking_for == $path[$pathIndex])
				return FALSE;
		}
		return $node;
	}

	/**
	 * Returns the value of the attribute of a DOM node
	 * @param DOMNode $dom Node of which the attribute should be read
	 * @param String $nodeAttr name of the attribute
	 * @return String value of the attribute or FALSE if not found
	 */
	private function getNodeAttr($dom, $nodeAttr)
	{
		if ($dom && $dom->attributes) {
			for ($i = 0; $i < $dom->attributes->length; $i++) {
				if ($dom->attributes->item($i)->name == $nodeAttr)
					return $dom->attributes->item($i)->value;
			}
		}
		return FALSE;
	}

	/**
	 * Extends the query with parameters for fulltextes and cc licenses
	 * @param String $query The query to extend
	 * @return String new query
	 */
	private function extendQuery($query)
	{
		PMILog::debug("Entering extendQuery");
		$internal_managed = ' and escidoc.component.content.storage="internal-managed"';
		$visibility_public = ' and escidoc.component.visibility="public"';
		if ($this->options['fulltext_option_without']) {
			PMILog::debug("Without fulltexts.");
			$internal_managed = "";
		}
		if ($this->options['fulltext_option_nonpublic']) {
			PMILog::debug("Non public fulltexts.");
			$visibility_public = "";
		}
		$query .= $internal_managed . $visibility_public;


		// CC License
		if ($this->options['public_available_only_cc'])
			$query .= ' and escidoc.component.file.license="http://creativecommons*"';

		return $query;
	}

	/**
	 * Converts a DOMNodeList to an Array
	 * @param DOMNodeList $domnodelist The node list which should be converted
	 * @return array Array with nodes
	 */
	private function dnl2array($domnodelist)
	{
		$return = array();
		for ($i = 0; $i < $domnodelist->length; ++$i) {
			$return[] = $domnodelist->item($i);
		}
		return $return;
	}

}

?>

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
 * Class ContentRepository
 */
class ContentRepository extends \LeipzigUniversityLibrary\PubmanImporter\Library\RepositoryAbstract
{
	/**
	 * The base uri where the content relies
	 *
	 * @var
	 */
	protected $baseUri;

	/**
	 * All components
	 *
	 * @var array
	 */
	protected $assets = [];

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->_httpRequest = GeneralUtility::makeInstance('\TYPO3\CMS\Core\Http\HttpRequest');
	}

	/**
	 * Sets the base uri
	 *
	 * @param $value
	 */
	public function setBaseUri($value) {
		$this->baseUri = $value;
	}

	/**
	 * Returns the base uri
	 *
	 * @return mixed
	 */
	public function getBaseUri() {
		return $this->baseUri;
	}

	/**
	 * Sets the assets
	 *
	 * @param $value
	 */
	public function setAssets($value) {
		$this->assets = $value;
	}

	/**
	 * Returns the assets
	 *
	 * @return array
	 */
	public function getAssets() {
		return $this->assets;
	}
	/**
	 * Finds an object matching the given identifier.
	 *
	 * @param integer $component The identifier of the object to find
	 * @return object The matching object if found, otherwise NULL
	 * @api
	 */
	public function findByComponent($component)
	{
		if ($component->getMimeType() === 'text/html') {
			$this->_path = $component->getPath();
			return $this->execute()->parse();

		}

		if ($component->getMimeType() === 'application/pdf') {
			$this->_path = $component->getPath();

			return $this->execute()->getBody();
		}
	}

	/**
	 * Extracts the content specific information
	 *
	 * @return string
	 */
	public function parse() {
		libxml_use_internal_errors(true);
		$this->_domDocument = new \DOMDocument();
		$this->_domDocument->loadHTML($this->_body);
		$this->_xpath = new \DOMXPath($this->_domDocument);

		$this->manipulateHref();
		$this->manipulateImageSrc();

		return $this->getBodyContent();
	}

	/**
	 * Manipulates the anchors according to the base uri so that they work with a base-href element in html
	 *
	 * @return $this
	 */
	protected function manipulateHref() {
		foreach ($this->_xpath->query('//*[@href]') as $node) {
			$href = $node->getAttribute('href');
			if (substr($href, 0, 1) !== '#') continue;

			$node->setAttribute('href', $this->getBaseUri() . $href);
		}

		return $this;
	}

	/**
	 * Manipulates the anchors according to the base uri so that they work with a base-href element in html
	 *
	 * @return $this
	 */
	protected function manipulateImageSrc() {
		foreach ($this->getAssets() as $asset) {
			$path = sprintf('//div[@class="figure"]/img[@src="%s"]', $asset->getFileName());
			foreach ($this->_xpath->query($path) as $node) {
				$node->setAttribute('src', $asset->getHref());
			}
		}

		return $this;
	}

	/**
	 * Returns the body as string
	 *
	 * @return string
	 */
	protected function getBodyContent() {
		$value = '';

		foreach ($this->_xpath->query('/html/body')->item(0)->childNodes as $node) {
			$value .= $this->_domDocument->saveXML($node);
		}

		return $value;
	}
}
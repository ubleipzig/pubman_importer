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
 * Class ArticleRepository
 */
class ArticleRepository extends ItemRepository {

	/**
	 * The cql query patterns
	 *
	 * @var array
	 */
	protected $_cqlQueryPattern = [
		'all' => 'escidoc.objecttype="item" AND escidoc.content-model.objid="%1$s" AND escidoc.context.objid="%2$s" AND escidoc.publication.type="http://purl.org/escidoc/metadata/ves/publication-types/article"',
		'byPid' => 'escidoc.objecttype="item" AND escidoc.content-model.objid="%1$s" AND escidoc.context.objid="%2$s" AND (escidoc.any-identifier="%3$s" NOT escidoc.objid="%3$s")',
		'byUid' => 'escidoc.objecttype="item" AND escidoc.content-model.objid="%1$s" AND escidoc.context.objid="%2$s" AND escidoc.objid="%3$s" AND escidoc.publication.type="http://purl.org/escidoc/metadata/ves/publication-types/article"',
		'byCreator' => 'escidoc.objecttype="item" AND escidoc.content-model.objid="%1$s" AND escidoc.context.objid="%2$s" AND escidoc.publication.creator.person.organization.identifier="%3$s"',
	];

	/**
	 * The sort is not working properly since its not integer- but string-sort
	 *
	 * @var string
	 */
	protected $_sortKeys = 'sort.escidoc.publication.source.start-page';

	/**
	 * The sort order
	 *
	 * @var string
	 */
	protected $_sortOrder = 'ascending';

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
			$model = GeneralUtility::makeInstance('\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Article');

			$this->parseGenerics($itemNode, $model);

			$this->parseArticle($this->_publicationNode, $model);

			if ($pid) $model->setPid($pid);

			$result[] = $model;
		}

		return $result;
	}

	/**
	 * extracts article specific information from dom
	 *
	 * @param $node
	 * @param $model
	 * @return $this
	 */
	public function parseArticle($node, $model) {
		$model->setStartPage($this->_xpath->query('source:source/eterms:start-page', $node)->item(0)->nodeValue);
		$model->setEndPage($this->_xpath->query('source:source/eterms:end-page', $node)->item(0)->nodeValue);
		$model->setIdentifier($this->_xpath->query('dc:identifier[@xsi:type="eterms:URN"]', $node)->item(0)->nodeValue);

		return $this;
	}
}
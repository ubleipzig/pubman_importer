<?php
namespace LeipzigUniversityLibrary\PubmanImporter\Domain\Repository;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

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
 * The repository for Articles
 */
class ArticleRepository extends ItemRepository
{

    /**
     * the cql query patterns
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
     * sort is not working properly since its not integer- but string-sort
     *
     * @var string
     */
    protected $_sortKeys = 'sort.escidoc.publication.source.start-page';

    /**
     * the sort oder
     *
     * @var string
     */
    protected $_sortOrder = 'ascending';

    /**
     * extracts information from the dom
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
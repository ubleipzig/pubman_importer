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

namespace LeipzigUniversityLibrary\PubmanImporter\Domain\Model;

/**
 * Class Creator
 */
class Creator extends \LeipzigUniversityLibrary\PubmanImporter\Library\ItemAbstract
{

	/**
	 * The family name of the creator
	 *
	 * @var string
	 */
	protected $familyName;

	/**
	 * The first name of the creator
	 *
	 * @var string
	 */
	protected $givenName;

	/**
	 * the organization the creator is associated with
	 *
	 * @var string
	 */
	protected $organization;

	/**
	 * The address of the creator
	 *
	 * @var string
	 */
	protected $address;

	/**
	 * The scheme, host, port part of uri to the creator's page
	 *
	 * @var string
	 */
	protected $url;

	/**
	 * The path part of the uri to the creator's page
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * The position within the associated organization
	 *
	 * @var string
	 */
	protected $position;

	/**
	 * the article
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Article>
	 * @cascade remove
	 */
	protected $article = NULL;

	/**
	 * Initializes all ObjectStorage properties
	 * Do not modify this method!
	 * It will be rewritten on each save in the extension builder
	 * You may modify the constructor of this class instead
	 *
	 * @return void
	 */
	protected function initStorageObjects()
	{
		$this->article = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * Adds an article
	 *
	 * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Article $article
	 * @return void
	 */
	public function addArticle(\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Article $article)
	{
		$this->article->attach($article);
	}

	/**
	 * Removes an article
	 *
	 * @param \LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Article $articleToRemove The Article to be removed
	 * @return void
	 */
	public function removeArticle(\LeipzigUniversityLibrary\PubmanImporter\Domain\Model\Article $articleToRemove)
	{
		$this->article->detach($articleToRemove);
	}

	/**
	 * Sets url and path by the href
	 *
	 * @param string $value
	 */
	public function setHref($value)
	{
		if (!$value)
			return;

		$path = parse_url($value, PHP_URL_PATH);

		$url = strstr($value, $path, true);

		$this->setUrl($url);
		$this->setPath($path);
	}

	/**
	 * Returns the href based on url and path
	 *
	 * @return string
	 */
	public function getHref()
	{
		return $this->getUrl() . $this->getPath();
	}
}
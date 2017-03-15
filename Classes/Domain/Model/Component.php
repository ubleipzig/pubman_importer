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
 * Class Component
 */
class Component extends \LeipzigUniversityLibrary\PubmanImporter\Library\ItemAbstract
{
	/**
	 * The content of the component
	 *
	 * @var string
	 */
	protected $content;

	/**
	 * The url to the component content
	 *
	 * @var string
	 */
	protected $url;

	/**
	 * The path to the component content
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * The filename of the component
	 *
	 * @var string
	 */
	protected $fileName;

	/**
	 * The mime type of the component
	 *
	 * @var string
	 */
	protected $mimeType;

	/**
	 * The license of the component
	 *
	 * @var string
	 */
	protected $license;

	/**
	 * The storage type of the component
	 *
	 * @var string
	 */
	protected $storage;

	/**
	 * No storage objects to initialize for this model
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		return;
	}

	/**
	 * Returns the href based on url and path
	 *
	 * @return string
	 */
	public function getHref() {
		return $this->getUrl() . $this->getPath();
	}

	/**
	 * Returns the file name of the component
	 *
	 * @return string
	 */
	public function getFileName() {
		return $this->fileName;
	}
}
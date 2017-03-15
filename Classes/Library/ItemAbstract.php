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

/**
 * abstract class ItemAbstract
 */
abstract class ItemAbstract extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * Constructor
	 */
	public function __construct() {
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
	}

	/**
	 * Initializes all ObjectStorage properties
	 * Do not modify this method!
	 * It will be rewritten on each save in the extension builder
	 * You may modify the constructor of this class instead
	 *
	 * @return void
	 */
	abstract protected function initStorageObjects();

	/**
	 * Returns the uid, overridden because of string type uid
	 *
	 * @return string
	 */
	public function getUid() {
		return $this->uid;
	}

	/**
	 * Sets the pid, overridden because of string type pid
	 *
	 * @param string $pid
	 */
	public function setPid($pid) {
		$this->pid = $pid;
	}

	/**
	 * Returns the pid, overridden because of string type pid
	 *
	 * @return string
	 */
	public function getPid() {
		return $this->pid;
	}

	/**
	 * Magic method covers all setters and getters
	 *
	 * @param string $method
	 * @param array $arguments
	 * @return mixed
	 * @throws \Exception in case of method or property are invalid
	 */
	public function __call($method, $arguments) {
		$pattern = '/^(?<method>[gs]et)(?<property>.*)$/';

		$matches = [];

		if (!preg_match($pattern, $method, $matches)) {
			throw new \Exception('no handling for method '. $method . ' defined');
		}

		$property = lcfirst($matches['property']);

		if (!property_exists($this, $property)) {
			throw new \Exception('property '. $property . ' does not exist in ' . get_class($this));
		}

		if ($matches['method'] === 'set') {
			return $this->${property} = $arguments[0];
		} else if ($matches['method'] === 'get') {
			return $this->${property};
		}
	}
}
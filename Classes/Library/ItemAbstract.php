<?php
namespace LeipzigUniversityLibrary\PubmanImporter\Library;


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
 * ItemAbstract
 */
abstract class ItemAbstract extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

    /**
     * __construct
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
     * returns the uid, overridden because of string type uid
     *
     * @return string
     */
    public function getUid() {
        return $this->uid;
    }

    /**
     * sets the pid, overridden because of string type pid
     *
     * @param string $pid
     */
    public function setPid($pid) {
        $this->pid = $pid;
    }

    /**
     * returns the pid, overridden because of string type pid
     *
     * @return string
     */
    public function getPid() {
        return $this->pid;
    }

    /**
     * magic method covers all setters and getters
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
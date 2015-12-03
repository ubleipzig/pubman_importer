<?php
namespace LeipzigUniversityLibrary\PubmanImporter\Domain\Model;

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
 * Component
 */
class Component extends \TYPO3\CMS\Extbase\DomainObject\AbstractValueObject
{

    protected $content;

    protected $url;

    protected $path;

    protected $fileName;

    protected $mimeType;

    protected $license;

    protected $storage;

    public function setContent($value) {
        $this->content = $value;
    }

    public function getContent() {
        return $this->content;
    }

    public function setMimeType($value)
    {
        $this->mimeType = $value;
    }

    public function getMimeType() {
        return $this->mimeType;
    }

    public function setFileName($value) {
        $this->fileName = $value;
    }

    public function getFileName() {
        return $this->fileName;
    }

    public function setLicense($value) {
        $this->license = $value;
    }

    public function getLicense() {
        return $this->license;
    }

    public function setStorage($value) {
        $this->storage = $value;
    }

    public function getStorage() {
        return $this->storage;
    }

    public function setUid($value) {
        $this->uid = $value;
    }

    public function getUid() {
        return $this->uid;
    }

    public function setUrl($value) {
        $this->url = $value;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setPath($value) {
        $this->path = $value;
    }

    public function getPath() {
        return $this->path;
    }

    public function getHref() {
        return $this->getUrl() . $this->getPath();
    }
}
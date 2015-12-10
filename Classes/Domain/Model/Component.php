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
class Component extends \LeipzigUniversityLibrary\PubmanImporter\Library\ItemAbstract
{
    /**
     * the content of the component
     *
     * @var string
     */
    protected $content;

    /**
     * the url to the component content
     *
     * @var string
     */
    protected $url;

    /**
     * the path to the component content
     *
     * @var string
     */
    protected $path;

    /**
     * the filename of the component
     *
     * @var string
     */
    protected $fileName;

    /**
     * the mime type of the component
     *
     * @var string
     */
    protected $mimeType;

    /**
     * the license of the component
     *
     * @var string
     */
    protected $license;

    /**
     * the storage type of the component
     *
     * @var string
     */
    protected $storage;

    /**
     * no storage objects to initialize for this model
     *
     * @return void
     */
    protected function initStorageObjects() {
        return;
    }

    /**
     * returns the href based on url and path
     *
     * @return string
     */
    public function getHref() {
        return $this->getUrl() . $this->getPath();
    }
}
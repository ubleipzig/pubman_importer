<?php
namespace LeipzigUniversityLibrary\PubmanImporter\Controller;

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
 * ContentController
 */
class ContentController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

    /**
     * ItemRepository
     *
     * @var \LeipzigUniversityLibrary\PubmanImporter\Domain\Repository\ItemRepository
     * @inject
     */
    protected $ItemRepository = NULL;

    /**
     * ContentRepository
     *
     * @var \LeipzigUniversityLibrary\PubmanImporter\Domain\Repository\ContentRepository
     * @inject
     */
    protected $ContentRepository = NULL;

    /**
     * action show
     *
     * @param string $Component
     * @param string $Item
     * @param string $Article
     * @param string $Issue
     * @param string $Journal
     * @param string $Context
     * @return void
     */
    public function showAction($Component, $Item, $Article = false, $Issue = false, $Journal = false, $Context = false) {
        $this->ItemRepository->setOptions($this->settings);
        $this->ContentRepository->setOptions($this->settings);
        $this->ContentRepository->setBaseUri($this->request->getRequestUri());

        try {
            $Item = $this->ItemRepository->findByUid($Item);
            $this->ContentRepository->setAssets($Item->getComponent());
            foreach ($this->ContentRepository->getAssets() as $component) {
                if ($component->getUid() !== $Component) continue;
                $component->setContent($this->ContentRepository->findByComponent($component));
                $Component = $component;
                break;
            }

            $this->view->assign('Component', $Component);
            $this->view->assign('Item', $Item);
        } catch (\Exception $e) {
            $this->view->assign('Error', $e);
        }

        $this->view->assign('Article', $Article);
        $this->view->assign('Issue', $Issue);
        $this->view->assign('Journal', $Journal);
        $this->view->assign('Context', $Context);
    }

    /**
     * action showConverted
     *
     * @param string $Component
     * @param string $Item
     * @param string $Article
     * @param string $Issue
     * @param string $Journal
     * @param string $Context
     * @return void
     */
    public function showConvertedAction($Component, $Item, $Article = false, $Issue = false, $Journal = false, $Context = false) {
        return $this->showAction($Component, $Item, $Article = false, $Issue = false, $Journal = false, $Context = false);
    }

    /**
     * action stream
     *
     * @param string $Component
     * @param string $Item
     */
    public function streamAction($Component, $Item) {
        $this->ItemRepository->setOptions($this->settings);
        $this->ContentRepository->setOptions($this->settings);

        try {
            $Item = $this->ItemRepository->findByUid($Item);

            foreach ($Item->getComponent() as $component) {
                if ($component->getUid() !== $Component) continue;
                $component->setContent($this->ContentRepository->findByComponent($component));
                $this->response->setHeader('Content-Type', 'application/pdf');
                $this->response->setContent($component->getContent());
                $this->response->send();
                exit;
            }
        }catch (\Exception $e) {
            $this->view->assign('Error', $e);
        }
    }
}
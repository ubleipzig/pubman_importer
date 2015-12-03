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
     * ArticleRepository
     *
     * @var \LeipzigUniversityLibrary\PubmanImporter\Domain\Repository\ArticleRepository
     * @inject
     */
    protected $ArticleRepository = NULL;

    /**
     * ContentRepository
     *
     * @var \LeipzigUniversityLibrary\PubmanImporter\Domain\Repository\ContentRepository
     * @inject
     */
    protected $ContentRepository = NULL;

    /**
     * action list
     *
     * @param string $Component
     * @return void
     */
    public function listAction($Component) {
        $Components = $this->ContentRepository->findByComponent($Component);
        $this->view->assign('Components', $Components);
    }

    /**
     * action show
     *
     * @param string $Component
     * @param string $Article
     * @param string $Issue
     * @param string $Journal
     * @param string $Context
     * @return void
     */
    public function showAction($Component, $Article, $Issue = false, $Journal = false, $Context = false) {
        $Article = $this->ArticleRepository->findByUid($Article);
        $this->ContentRepository->setBaseUri($this->request->getRequestUri());

        foreach ($Article->getComponent() as $component) {
            if ($component->getUid() !== $Component) continue;
            $component->setContent($this->ContentRepository->findByComponent($component));
            $Component = $component;
            break;
        }

        $this->view->assign('Component', $Component);
        $this->view->assign('Article', $Article);
        $this->view->assign('Issue', $Issue);
        $this->view->assign('Journal', $Journal);
        $this->view->assign('Context', $Context);
    }

    /**
     * action stream
     *
     * @param string $Component
     * @param string $Article
     */
    public function streamAction($Component, $Article) {
        $Article = $this->ArticleRepository->findByUid($Article);

        foreach ($Article->getComponent() as $component) {
            if ($component->getUid() !== $Component) continue;
            $component->setContent($this->ContentRepository->findByComponent($component));
            $this->response->setHeader('Content-Type', 'application/pdf');
            $this->response->setContent($component->getContent());
            $this->response->send();
            exit;
        }
    }
}
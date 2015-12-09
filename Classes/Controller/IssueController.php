<?php
namespace LeipzigUniversityLibrary\PubmanImporter\Controller;
use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
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
 * IssueController
 */
class IssueController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

    /**
     * IssueRepository
     *
     * @var \LeipzigUniversityLibrary\PubmanImporter\Domain\Repository\IssueRepository
     * @inject
     */
    protected $IssueRepository = NULL;

    /**
     * ArticleRepository
     *
     * @var \LeipzigUniversityLibrary\PubmanImporter\Domain\Repository\ArticleRepository
     * @inject
     */
    protected $ArticleRepository = NULL;

    /**
     * action list
     *
     * @param string $Journal
     * @return void
     */
    public function listAction($Journal) {
        $Issues = $this->IssueRepository->findByPid($Journal);
        $this->view->assign('Issues', $Issues);
    }

    /**
     * action show
     *
     * @param string $Issue
     * @param string $Journal
     * @param string $Context
     * @return void
     */
    public function showAction($Issue, $Journal = false, $Context = false) {
        $Issue = $this->IssueRepository->findByUid($Issue);

        $articleCollection = $this->ArticleRepository->findByPid($Issue);

        $articleStartPages = array_map(function($item) {
            return $item->getStartPage();
        }, $articleCollection);

        array_multisort($articleStartPages, SORT_ASC, SORT_NUMERIC, $articleCollection);

        foreach ($articleCollection as $Article) {
            $Issue->addArticle($Article);
        }

        $this->view->assign('Issue', $Issue);
        $this->view->assign('Journal', $Journal);
        $this->view->assign('Context', $Context);
    }
}
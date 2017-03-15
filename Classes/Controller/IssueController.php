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
namespace LeipzigUniversityLibrary\PubmanImporter\Controller;

use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Class IssueController
 */
class IssueController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

	/**
	 * The IssueRepository
	 *
	 * @var \LeipzigUniversityLibrary\PubmanImporter\Domain\Repository\IssueRepository
	 * @inject
	 */
	protected $IssueRepository = NULL;

	/**
	 * The ArticleRepository
	 *
	 * @var \LeipzigUniversityLibrary\PubmanImporter\Domain\Repository\ArticleRepository
	 * @inject
	 */
	protected $ArticleRepository = NULL;

	/**
	 * Shows the issue
	 *
	 * @param string $Issue
	 * @param string $Journal
	 * @param string $Context
	 * @return void
	 */
	public function showAction($Issue, $Journal = false, $Context = false)
	{
		$this->IssueRepository->setOptions($this->settings);
		$this->ArticleRepository->setOptions($this->settings);

		try {
			$Issue = $this->IssueRepository->findByUid($Issue);
			$articleCollection = $this->ArticleRepository->findByPid($Issue);

			$articleStartPages = array_map(function ($item) {
				return $item->getStartPage();
			}, $articleCollection);

			array_multisort($articleStartPages, SORT_ASC, SORT_NUMERIC, $articleCollection);

			foreach ($articleCollection as $Article) {
				$Issue->addArticle($Article);
			}

			$this->view->assign('Issue', $Issue);
		} catch (\Exception $e) {
			$this->view->assign('Error', $e);
		}
		$this->view->assign('Journal', $Journal);
		$this->view->assign('Context', $Context);
	}
}
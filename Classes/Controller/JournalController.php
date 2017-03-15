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

/**
 * Class JournalController
 */
class JournalController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * The JournalRepository
	 *
	 * @var \LeipzigUniversityLibrary\PubmanImporter\Domain\Repository\JournalRepository
	 * @inject
	 */
	protected $JournalRepository = NULL;

	/**
	 * The JournalRepository
	 *
	 * @var \LeipzigUniversityLibrary\PubmanImporter\Domain\Repository\IssueRepository
	 * @inject
	 */
	protected $IssueRepository = NULL;

	/**
	 * Lists all journals or redirects to show the journal if its the only one
	 *
	 * @return void
	 */
	public function listAction() {
		$this->JournalRepository->setOptions($this->settings);

		try {
			$Journals = $this->JournalRepository->findAll();

			if (count($Journals) === 1) {
				$this->uriBuilder->reset()->setArguments(array(
					'L' => $GLOBALS['TSFE']->sys_language_uid,
					'tx_pubmanimporter_journals[Journal]' => $Journals[0],
					'tx_pubmanimporter_journals[Context]' => false
				));
				$uri = $this->uriBuilder->uriFor('show', array(), 'Journal', 'pubmanimporter', 'Journals');
				$this->redirectToUri($uri);
			} else $this->view->assign('Journals', $Journals);
		} catch (\Exception $e) {
			$this->view->assign('Error', $e);
		}
	}

	/**
	 * Shows the specified journal
	 *
	 * @param string $Journal
	 * @param string $Context
	 * @return void
	 */
	public function showAction($Journal, $Context = false) {
		$this->JournalRepository->setOptions($this->settings);
		$this->IssueRepository->setOptions($this->settings);

		try {
			$Journal = $this->JournalRepository->findByUid($Journal);

			$issueCollection = $this->IssueRepository->findByPid($Journal);

			$issueTerms = array_map(function ($item) {
				return intval($item->getIssueTerm());
			}, $issueCollection);

			array_multisort($issueTerms, SORT_DESC, SORT_NUMERIC, $issueCollection);

			foreach ($issueCollection as $Issue) {
				$Journal->addIssue($Issue);
			}
			$this->view->assign('Journal', $Journal);

		} catch(\Exception $e) {
			$this->view->assign('Error', $e);
		}

		$this->view->assign('Context', $Context);
	}
}
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
 * Class ContentController
 */
class ContentController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * The ItemRepository
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
	 * Shows the specified item component as content
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
	 * Streams the item's component as pdf (we just expect it to be pdf)
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
				$this->response->setHeader('Content-Type', 'application/pdf');
				$this->response->setHeader('Content-Disposition', 'inline; filename="' . $component->getFilename() . '"');
				$this->response->send();
				$this->ContentRepository->findByComponent($component);
				exit;
			}
		} catch (\Exception $e) {
			$this->view->assign('Error', $e);
		}
	}
}
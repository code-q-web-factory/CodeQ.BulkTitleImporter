<?php

namespace CodeQ\BulkTitleImporter\Controller;

/*
 * This file is part of the CodeQ.BulkTitleImporter package.
 */

use CodeQ\BulkTitleImporter\Service\ImportService;
use InvalidArgumentException;
use Neos\ContentRepository\Domain\Model\Workspace;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\View\ViewInterface;
use Neos\Flow\ResourceManagement\Exception;
use Neos\Flow\ResourceManagement\PersistentResource;
use Neos\Fusion\View\FusionView;
use Neos\Neos\Controller\Module\AbstractModuleController;
use Neos\Neos\Service\UserService;
use Neos\Neos\Ui\ContentRepository\Service\WorkspaceService;

class ImportModuleController extends AbstractModuleController
{
    /**
     * @var string
     */
    protected $defaultViewObjectName = FusionView::class;

    /**
     * @Flow\Inject
     * @var ImportService
     */
    protected $importService;

    /**
     * @Flow\Inject
     * @var WorkspaceService
     */
    protected $workspaceService;

    /**
     * @Flow\Inject
     * @var UserService
     */
    protected $userService;

    public function indexAction(): void
    {
        $this->view->assign('workspaces', [
            [
                'name' => $this->userService->getPersonalWorkspaceName(),
                'title' => 'Persönlicher Arbeitsbereich'
            ],
            ...array_filter($this->workspaceService->getAllowedTargetWorkspaces(), static fn (array $workspace) => $workspace['name'] !== 'live')
        ]);
    }

    /**
     * @param PersistentResource $file
     * @param string             $targetWorkspaceName
     *
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\StopActionException
     */
    public function importAction(PersistentResource $file, string $targetWorkspaceName): void
    {
        try {
            $importResult = $this->importService->importFromPersistentResource($file, $targetWorkspaceName);
        } catch (Exception | InvalidArgumentException $exception) {
            $this->addFlashMessage('The file could not be imported: ' . $exception->getMessage(), 'Error', \Neos\Error\Messages\Message::SEVERITY_ERROR);
            $this->redirect('index');
            return;
        }
        $this->view->assign('importResult', $importResult);
        $this->view->assign('targetWorkspaceName', $targetWorkspaceName);
    }

    /**
     * @param FusionView $view
     *
     * @return void
     */
    protected function initializeView(ViewInterface $view): void
    {
        parent::initializeView($view);
        $view->setFusionPathPattern('resource://CodeQ.BulkTitleImporter/Private/Fusion/Module');
    }
}

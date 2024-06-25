<?php

namespace CodeQ\BulkTitleImporter\Service;

use CodeQ\BulkTitleImporter\Dto\ImportResult;
use InvalidArgumentException;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\ResourceManagement\Exception;
use Neos\Flow\ResourceManagement\PersistentResource;

class ImportService
{
    /**
     * @Flow\Inject
     * @var FileService
     */
    protected FileService $fileService;

    /**
     * @Flow\Inject
     * @var NodeFindingService
     */
    protected NodeFindingService $nodeFindingService;

    /**
     * @Flow\InjectConfiguration(path="nodeProperty")
     * @var string
     */
    protected string $nodeProperty;

    /**
     * @param PersistentResource $file
     * @param string             $targetWorkspaceName
     *
     * @return ImportResult
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function importFromPersistentResource(PersistentResource $file, string $targetWorkspaceName): ImportResult
    {
        $errorBuffer = [];
        $temporaryFilePath = $file->createTemporaryLocalCopy();
        $rows = $this->fileService->read($temporaryFilePath);
        $this->import($rows, $targetWorkspaceName, $errorBuffer);
        return new ImportResult(
            $errorBuffer,
            count($rows)
        );
    }

    /**
     * @param array  $rows
     * @param string $targetWorkspaceName
     * @param array  $errorBuffer
     *
     * @return void
     */
    protected function import(array $rows, string $targetWorkspaceName, array &$errorBuffer): void
    {
        foreach ($rows as [$url, $title]) {
            $node = $this->nodeFindingService->tryToResolvePublicUriToNode($url, $targetWorkspaceName);
            if ($node === null) {
                $errorBuffer[] = $url;
                continue;
            }
            $node->setProperty($this->nodeProperty, self::sanitizeTitle($title));
        }
    }

    protected static function sanitizeTitle(string $title): string
    {
        return trim(strip_tags($title));
    }
}

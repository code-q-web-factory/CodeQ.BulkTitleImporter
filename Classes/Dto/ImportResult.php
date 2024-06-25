<?php

namespace CodeQ\BulkTitleImporter\Dto;

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\ValueObject
 */
class ImportResult
{
    /**
     * @var array<string> Error messages that occurred during the import
     */
    protected array $errors = [];

    /**
     * @var int Number of nodes that have been imported
     */
    protected int $importedNodes = 0;

    public function __construct(array $errors = [], int $importedNodes = 0)
    {
        $this->errors = $errors;
        $this->importedNodes = $importedNodes - count($errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getImportedNodes(): int
    {
        return $this->importedNodes;
    }
}

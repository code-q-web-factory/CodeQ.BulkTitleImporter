<?php

namespace CodeQ\BulkTitleImporter\Tests\Unit;

use CodeQ\BulkTitleImporter\Service\FileService;
use InvalidArgumentException;
use Neos\Flow\Tests\UnitTestCase;

class FileServiceTest extends UnitTestCase
{
    public function testRead(): void
    {
        $fileService = new FileService();
        $excel = $fileService->read(__DIR__ . '/../Fixtures/test.xlsx');
        $this->assertEquals([
            ['https://neos-with-title-importer.ddev.site/en/features.html', 'Features NEU'],
            ['https://neos-with-title-importer.ddev.site/uk/try-me.html', 'Try me NEU'],
        ], $excel
        );
    }

    public function testReadWithInvalidFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $fileService = new FileService();
        $fileService->read(__DIR__ . '/../Fixtures/invalid.xlsx');
    }
}

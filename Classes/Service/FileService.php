<?php

namespace CodeQ\BulkTitleImporter\Service;

use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class FileService
{
    /**
     * @var Xlsx
     */
    protected IReader $reader;

    public function __construct()
    {
        $this->reader = IOFactory::createReader(IOFactory::READER_XLSX);
    }

    /**
     * @param string $filePath
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public function read(string $filePath): array
    {
        $spreadsheet = $this->reader->load($filePath, IReader::READ_DATA_ONLY);
        $worksheet = $spreadsheet->getAllSheets()[0];
        $rows = $worksheet->toArray();
        $this->validateFileContents($rows);
        // Remove the header row
        array_shift($rows);
        return $rows;
    }

    /**
     * @param array $rows
     *
     * @return void
     * @throws InvalidArgumentException
     */
    protected function validateFileContents(array $rows): void
    {
        if (count($rows) < 2) {
            throw new InvalidArgumentException('The file must contain at least two rows.');
        }
        if ($rows[0] !== ['URL', 'Title']) {
            throw new InvalidArgumentException('The first row must contain the headers "URL" and "Title".');
        }
    }
}

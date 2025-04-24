<?php

namespace App\Service\Import;

class ImportLogger
{

    public function LogImportResult(ImportResult $importResult): void
    {
        $output = sprintf(
            "Import completed. Total: %d, Successful: %d, Failed: %d\n",
            $importResult->getTotal(),
            $importResult->getSuccess(),
            count($importResult->getFailedRows()),
        );

        if (!empty($importResult->getFailedRows())) {
            $output .= "Failed rows:\n";
            foreach ($importResult->getFailedRows() as $failed) {
                $output .= sprintf(" - Line [%d]: %s\n", $failed['line'], $failed['reason']);
            }
        }

        echo $output;
    }
}
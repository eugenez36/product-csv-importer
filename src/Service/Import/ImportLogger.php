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
            foreach ($importResult->getFailedRows() as $line => $failed) {
                $reasonList = '';
                foreach ($failed as $reason) {
                    $reasonList .= "| " . $reason;
                }
                $output .= sprintf(" - Line [%d]: %s |\n", $line, $reasonList);
            }
        }

        echo $output;
    }
}
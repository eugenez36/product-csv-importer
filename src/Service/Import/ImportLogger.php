<?php

namespace App\Service\Import;

class ImportLogger
{
    public function generateTotalHeaderString(ImportResult $importResult): string
    {
        return sprintf(
            "Import completed. Total: %d, Successful: %d, Failed: %d\n",
            $importResult->getTotal(),
            $importResult->getSuccess(),
            count($importResult->getFailedRows()),
        );
    }

    public function generateFailedRow(ImportResult $importResult): string
    {
        $failedRowsString = "";
        foreach ($importResult->getFailedRows() as $rowLine => $failedRow) {
            $failedRowsString .= "-Line[$rowLine]: |";
            foreach ($failedRow as $errors) {
                $failedRowsString .= sprintf("%s|", $errors);
            }
            $failedRowsString .= "\n";
        }

        return $failedRowsString;
    }

    public function LogImportResult(ImportResult $importResult): void
    {
        $output = $this->generateTotalHeaderString($importResult);

        if (!empty($importResult->getFailedRows())) {
            $output .= "Failed rows:\n";
            $output .= $this->generateFailedRow($importResult);
        }

        echo $output;
    }
}
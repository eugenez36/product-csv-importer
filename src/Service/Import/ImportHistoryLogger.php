<?php

namespace App\Service\Import;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class ImportHistoryLogger implements LoggerInterface
{
    private const LOG_CATEGORY = 'product_import';
    private const MAX_FILE_SiZE = 1024 * 1024 * 4;
    private const MAX_LOG_FILES = 5;

    public function __construct(
        private string $logDirectory,
        private string $environment,
    )
    {
        $this->ValidateLogDirectory();
    }

    public function log($level, $message, array $context = []): void
    {
        $this->writeLog(
            $this->formatMessage($level, $message, $context),
            $this->getLogLevelFilename($level)
        );
    }

    private function getLogFilePath(string $fileName): string
    {
        return sprintf('%s/%s_%s.log',
            rtrim($this->logDirectory, '/'),
            self::LOG_CATEGORY,
            $fileName
        );
    }

    private function getLogLevelFilename(string $level): string
    {
        return match ($level) {
            LogLevel::ERROR => 'error',
            LogLevel::WARNING => 'alert',
            default => 'info',
        };
    }

    private function rotateIfNeeded(string $filePath): void
    {
        if (file_exists($filePath) && filesize($filePath) >= self::MAX_FILE_SiZE) {
            $this->rotateLogFile($filePath);
        }
    }

    private function formatMessage(string $level, string $message, array $context): string
    {
        return json_encode([
            'timestamp' => ((new \DateTimeImmutable())->format(\DateTimeInterface::ATOM)),
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'environment' => $this->environment,
        ], JSON_UNESCAPED_SLASHES, JSON_UNESCAPED_UNICODE);
    }

    private function writeLog(string $message, string $levelFileName): void
    {
        try {
            $logPath = $this->getLogFilePath($levelFileName);
            $this->rotateIfNeeded($logPath);

            file_put_contents($logPath, $message . PHP_EOL, FILE_APPEND | LOCK_EX);
        } catch (\Throwable $e) {
            error_log(sprintf('Logging failed [%s] %s', date('Y-m-d H:i:s'), $e->getMessage()));
        }
    }

    private function rotateLogFile(string $filePath): void
    {
        for ($i = self::MAX_LOG_FILES - 1; $i >= 0; $i--) {
            $currentFile = $i === 0 ? $filePath : "$filePath.$i";
            $newFile = "$filePath." . $i++;

            if (file_exists($currentFile)) {
                rename($currentFile, $newFile);
            }
        }
    }

    private function ValidateLogDirectory(): void
    {
        if (!is_dir($this->logDirectory) && !mkdir($this->logDirectory, 0755, true)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $this->logDirectory));
        }
        if (!is_writable($this->logDirectory)) {
            throw new \RuntimeException(sprintf('Log directory "%s" is not writable', $this->logDirectory));
        }
    }

    public function emergency(\Stringable|string $message, array $context = []): void
    {
        // TODO: Implement emergency() method.
    }

    public function alert(\Stringable|string $message, array $context = []): void
    {
        // TODO: Implement alert() method.
    }

    public function critical(\Stringable|string $message, array $context = []): void
    {
        // TODO: Implement critical() method.
    }

    public function error(\Stringable|string $message, array $context = []): void
    {
        // TODO: Implement error() method.
    }

    public function warning(\Stringable|string $message, array $context = []): void
    {
        // TODO: Implement warning() method.
    }

    public function notice(\Stringable|string $message, array $context = []): void
    {
        // TODO: Implement notice() method.
    }

    public function info(\Stringable|string $message, array $context = []): void
    {
        // TODO: Implement info() method.
    }

    public function debug(\Stringable|string $message, array $context = []): void
    {
        // TODO: Implement debug() method.
    }
}
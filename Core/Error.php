<?php

namespace Core;

use ErrorException;
use Exception;

/**
 * Error and exception handler
 */
class Error
{
    /**
     * Convert all errors to exceptions
     * @param int $level
     * @param string $message
     * @param string $file
     * @param int $line
     * @throws ErrorException
     */
    public static function errorHandler(int $level, string $message, string $file, int $line): void
    {
        if (error_reporting() !== 0) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    public static function exceptionHandler($exception): void
    {
        echo "<h1>Fatal error</h1>";
        echo "<p><strong>Uncaught exception: </strong>'" . get_class($exception) . "'</p>";
        echo "<p><strong>Message: </strong>'" . $exception->getMessage() . "'</p>";
        echo "<p>Stack trace:<pre>" . $exception->getTraceAsString() . "</pre></p>";
        echo "<p>Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";
    }
}
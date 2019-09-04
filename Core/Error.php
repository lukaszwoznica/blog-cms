<?php

namespace Core;

use App\Config;
use ErrorException;
use Throwable;

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

    /**
     * Exception handler
     * @param Throwable $exception
     * @param bool $caught
     */
    public static function exceptionHandler(Throwable $exception, bool $caught = false): void
    {
        $exception_status = "Uncaught";
        if ($caught){
            $exception_status = "Caught";
        }

        $error_code = $exception->getCode();
        if ($error_code != 404) {
            $error_code = 500;
        }

        http_response_code($error_code);

        if (Config::SHOW_ERRORS){
            if(!$caught)
                echo "<h1>Fatal error</h1>";
            echo "<p><strong>$exception_status exception: </strong>'" . get_class($exception) . "'</p>";
            echo "<p><strong>Message: </strong>'" . $exception->getMessage() . "'</p>";
            echo "<p>Stack trace:<pre>" . $exception->getTraceAsString() . "</pre></p>";
            echo "<p>Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";
        } else {
            $log_file = dirname(__DIR__) . "/logs/" .date('Y-m-d') . '.txt';
            ini_set('error_log', $log_file);

            $error_details = "$exception_status exception: '" . get_class($exception) . "'";
            $error_details .= " with message '" . $exception->getMessage() . "'";
            $error_details .= "\nStack trace: " . $exception->getTraceAsString();
            $error_details .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();

            error_log($error_details);

            View::renderTemplate("Errors/$error_code.html");
        }


    }
}
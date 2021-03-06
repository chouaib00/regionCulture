<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 21/06/2017
 * Time: 15:08
 */

namespace PocFramework\Handler;


class CliPhpError
{
    /**
     * Invoke error handler
     * @param \Throwable $error
     * @return \Psr\Http\Message\ResponseInterface|void
     */
    public function __invoke(\Throwable $error)
    {
        $this->writeToErrorLog($error);
    }

    /**
     * Wraps the error_log function so that this can be easily tested
     *
     * @param $message
     */
    protected function logError($message)
    {
        error_log($message);
    }

    /**
     * Write to the error log if displayErrorDetails is false
     *
     * @param \Exception|\Throwable $throwable
     *
     * @return void
     */
    protected function writeToErrorLog($throwable)
    {
        $message = 'Poc Application Error:' . PHP_EOL;
        $message .= $this->renderThrowableAsText($throwable);
        while ($throwable = $throwable->getPrevious()) {
            $message .= PHP_EOL . 'Previous error:' . PHP_EOL;
            $message .= $this->renderThrowableAsText($throwable);
        }


        $this->logError($message);
    }

    /**
     * Render error as Text.
     *
     * @param \Exception|\Throwable $throwable
     *
     * @return string
     */
    protected function renderThrowableAsText($throwable)
    {
        $text = sprintf('Type: %s' . PHP_EOL, get_class($throwable));

        if ($code = $throwable->getCode()) {
            $text .= sprintf('Code: %s' . PHP_EOL, $code);
        }

        if ($message = $throwable->getMessage()) {
            $text .= sprintf('Message: %s' . PHP_EOL, htmlentities($message));
        }

        if ($file = $throwable->getFile()) {
            $text .= sprintf('File: %s' . PHP_EOL, $file);
        }

        if ($line = $throwable->getLine()) {
            $text .= sprintf('Line: %s' . PHP_EOL, $line);
        }

        if ($trace = $throwable->getTraceAsString()) {
            $text .= sprintf('Trace: %s', $trace);
        }

        return $text;
    }
}
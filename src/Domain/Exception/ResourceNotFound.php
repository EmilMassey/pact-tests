<?php

namespace Emil\PactTests\Domain\Exception;

class ResourceNotFound extends \RuntimeException
{
    public function __construct(string $message = 'Resource not found', int $code = 404, \Throwable $previous = null)
    {
        return parent::__construct($message, $code, $previous);
    }
}
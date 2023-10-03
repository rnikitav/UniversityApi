<?php

namespace App\Exceptions\Inner;

use RuntimeException;

class InvalidDataSetException extends RuntimeException
{
    private array $data = [];

    public function setData(array $data = []): void
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public static function instance(string $message, array $data = []): self
    {
        $instance = new self($message);
        if ($data) {
            $instance->setData($data);
        }
        return $instance;
    }
}

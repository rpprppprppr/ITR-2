<?php

namespace src\Blog\Http;

class SuccessfulResponse extends Response
{
    public function __construct(
        private readonly array $data = []
    ) {}

    protected function isSuccess(): bool
    {
        return true;
    }

    protected function payload(): array
    {
        return $this->data;
    }

    public function payloadData(): array
    {
        return $this->payload();
    }
}
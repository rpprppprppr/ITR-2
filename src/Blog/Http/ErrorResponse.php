<?php

namespace src\Blog\Http;

class ErrorResponse extends Response
{
    public function __construct(
        private readonly string $reason = "Something goes wrong"
    ) {}

    public function reason(): string
    {
        return $this->reason;
    }

    protected function isSuccess(): bool
    {
        return false;
    }

    protected function payload(): array
    {
        return ['reason' => $this->reason];
    }
}
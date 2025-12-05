<?php

namespace src\Blog\Http;

abstract class Response
{
    // Убираем константу
    abstract protected function isSuccess(): bool;
    abstract protected function payload(): array;

    public function send(): void
    {
        $data = ["success" => $this->isSuccess()] + $this->payload();

        header("Content-type: application/json");
        echo json_encode($data, JSON_THROW_ON_ERROR);
    }
}
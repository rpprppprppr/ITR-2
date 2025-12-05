<?php

namespace src\Blog\Http\Actions;

use src\Blog\Http\Request;
use src\Blog\Http\Response;

interface ActionsInterface
{
    public function handle(Request $request): Response;
}
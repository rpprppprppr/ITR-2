<?php

namespace src\Blog\Http\Auth;

use src\Blog\User;
use src\Blog\Http\Request;

interface AuthenticationInterface
{
    public function user(Request $request): User;
}
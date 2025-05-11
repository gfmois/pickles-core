<?php

use Pickles\Auth\Auth;
use Pickles\Auth\Authenticatable;

function auth(): ?Authenticatable
{
    return Auth::user();
}

function isGuest(): bool
{
    return Auth::isGuest();
}

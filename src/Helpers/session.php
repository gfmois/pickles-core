<?php

use Pickles\Session\Session;

function session(): Session
{
    return app()->getSession();
}

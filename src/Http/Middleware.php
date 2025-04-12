<?php

namespace Pickles\Http;

use Closure;
use Pickles\Http\Request;
use Pickles\Http\Response;

interface Middleware
{
    public function handle(Request $request, Closure $next): Response;
}

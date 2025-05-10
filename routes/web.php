<?php

use Pickles\Http\Request;
use Pickles\Http\Response;
use Pickles\Routing\Route;

Route::get("/", fn (Request $request) => Response::text("Pickles Framework Working"));
Route::get("/form", fn (Request $request) => view("form"));
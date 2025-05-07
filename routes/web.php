<?php

use Pickles\Http\Request;
use Pickles\Http\Response;
use Pickles\Routing\Route;


var_dump("Inside");
die;
Route::get("/", fn (Request $request) => Response::text("Pickles Framework Working"));
Route::POST("/form", fn (Request $request) => view("form"));
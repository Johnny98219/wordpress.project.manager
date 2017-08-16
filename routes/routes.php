<?php

use CPM\Core\Router\Router;
use CPM\Core\Permissions\Administrator;
use CPM\Foo\Validators\Foo_Validator;
use CPM\Foo\Sanitizers\Foo_Sanitizer;

$router = Router::singleton();

$router->get( 'foo', 'CPM/Foo/Controllers/Foo_Controller@index' );

$router->post( 'foo', 'CPM/Foo/Controllers/Foo_Controller@store' )
    ->permission( [Administrator::class] )
    ->validator( Foo_Validator::class );

$router->get( 'foo/{id}', 'CPM/Foo/Controllers/Foo_Controller@show' )
    ->permission( [Administrator::class] )
    ->sanitizer( Foo_Sanitizer::class );

$router->put( 'foo/{id}', 'CPM/Foo/Controllers/Foo_Controller@update' )
    ->permission( [Administrator::class] )
    ->validator( Foo_Validator::class )
    ->sanitizer( Foo_Sanitizer::class );

$router->delete( 'foo/{id}', 'CPM/Foo/Controllers/Foo_Controller@destroy' )
    ->permission( [Administrator::class] )
    ->sanitizer( Foo_Sanitizer::class );
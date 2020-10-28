<?php
/** @psalm-suppress UndefinedFunction */
return [

    /*
    |--------------------------------------------------------------------------
    | Destination
    |--------------------------------------------------------------------------
    |
    | Set's where pinba will send the data. Possible values are "pinba", "file", "null"
    |
    */

    'pinba_destination' => env('PINBA_DESTINATION', null),

    /*
    |--------------------------------------------------------------------------
    | Hostname name
    |--------------------------------------------------------------------------
    |
    | Set custom hostname instead of the result of gethostname() used by default.
    |
    */

    'pinba_hostname'   => env('PINBA_HOSTNAME', gethostname()),

    /*
    |--------------------------------------------------------------------------
    | Server name
    |--------------------------------------------------------------------------
    |
    | Set custom server name instead of $_SERVER['SERVER_NAME'] used by default.
    |
    */

    'pinba_servername' => config('app.name'),


    /*
    |--------------------------------------------------------------------------
    | Schema
    |--------------------------------------------------------------------------
    |
    | Set request schema (HTTP/HTTPS/whatever).
    |
    */

    'pinba_schema' => env('PINBA_SCHEMA', null),
];

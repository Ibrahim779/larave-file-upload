<?php

return [

    /*
    |--------------------------------------------------------------------------
    | File Max width and height
    |--------------------------------------------------------------------------
    |
    | It is Used For auto resizing.
    |
    */

    "max-width" => 1024,

    "max-height" => 768,
    
    /*
    |--------------------------------------------------------------------------
    | File Quality
    |--------------------------------------------------------------------------
    |
    | It is normalized for all file types to a range 
    | from 0 (poor quality, small file) to 100 (best quality, big file).
    |
    | The default value is 60.
    */

    "quality" => 60,

    /*
    |--------------------------------------------------------------------------
    | File Extension
    |--------------------------------------------------------------------------
    |
    | This extension for images only. By default, we
    | will set this value to "webp" since this is 
    | provides superior lossless and lossy compression for images on the web.
    |
    | Supported: ['webp', 'png', 'jpg', ...] Or null if you want set file extention.
    */

    "extension" => 'webp',
];
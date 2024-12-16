<?php


namespace IIsmail\FileUpload\Traits;

use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait HashFileName
{

    /**
     * Return Hashed File Name.
     *
     * @access protected
     * @return string
     */
    protected function hashFileName()
    {
        return hash('md5', rand(100, 999)) . Str::random(10);
    }

}

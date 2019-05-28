# file-upload

<?php

namespace App\Http\Controllers;

use Uploader;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Change user's avatar.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changeAvatar(Request $request)
    {
        Uploader::upload('avatar');

        //
    }
}
f

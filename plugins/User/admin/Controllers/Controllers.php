<?php

namespace Plugins\User\Api\Controllers;

use Illuminate\Routing\Controller;

class PingController extends Controller
{
    public function index()
    {
        return response()->json([
            'ok' => true,
            'plugin' => 'User',
            'message' => 'pong',
        ]);
    }
}

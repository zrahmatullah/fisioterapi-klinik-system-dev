<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AI\OllamaService;

class AIController extends Controller
{
    protected $ai;

    public function __construct(OllamaService $ai)
    {
        $this->ai = $ai;
    }

    public function command(Request $request)
    {
        $text = $request->message;

        $result = $this->ai->parseCommand($text);

        return response()->json($result);
    }
}
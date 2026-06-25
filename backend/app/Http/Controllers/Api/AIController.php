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
        $request->validate(['message' => 'required|string|max:500']);

        $roleId = auth()->user()->role_id;

        $roleMap = [13 => 'admin', 14 => 'terapis', 15 => 'orang_tua'];
        $role = $roleMap[$roleId] ?? 'unknown';

        $result = $this->ai->parseCommand($request->message, $role);

        return response()->json($result);
    }
}
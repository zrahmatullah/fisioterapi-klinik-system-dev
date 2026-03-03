<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

class ApiCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:api-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all GET API endpoints';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🔍 Checking all API routes...\n");

        $routes = Route::getRoutes();
        $baseUrl = rtrim(config('app.url'), '/');

        foreach ($routes as $route) {

            $uri = $route->uri();

            // hanya cek route api/*
            if (!str_starts_with($uri, 'api/')) {
                continue;
            }

            // skip route yang butuh parameter
            if (str_contains($uri, '{')) {
                $this->warn("⏭ SKIP $uri (butuh parameter)");
                continue;
            }

            $methods = $route->methods();

            // skip selain GET
            if (!in_array('GET', $methods)) {
                $this->warn("⏭ SKIP $uri (".implode(',', $methods).")");
                continue;
            }

            try {
                $url = $baseUrl . '/' . $uri;

                $start = microtime(true);
                $res = Http::timeout(5)->get($url);
                $time = round((microtime(true) - $start) * 1000, 2);

                if ($res->successful()) {
                    $this->info("✔ $uri -> {$res->status()} ({$time}ms)");
                } else {
                    $this->error("✘ $uri -> {$res->status()} ({$time}ms)");
                }

            } catch (\Throwable $e) {
                $this->error("💥 $uri -> ERROR: ".$e->getMessage());
            }
        }

        $this->info("\n✅ API check selesai.");
    }
}

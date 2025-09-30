<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    public $piPath = NULL;
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // plugin.json 파일들을 읽어서 routes.php 자동 로드
        $pluginsRoot = config('plugins.root');



        //$metaJsonPath = "/home/bahamut/mewpi/api/plugins/User/plugin.json";
        //$meta = json_decode(file_get_contents($metaJsonPath), true);
        //print_r($meta);
        //$pluginsPath = base_path()."/../plugins";
        //echo $pluginsPath . '/*/plugin.json';
        //foreach (glob($pluginsPath . '/*/plugin.json') as $pluginFile) {
        //    $meta = json_decode(file_get_contents($pluginFile), true);
        //    print_r($meta);
        //    if (!empty($meta['routes']) && file_exists(base_path($meta['routes']))) {
        //        print_r($meta['routes']);
        //        echo base_path($meta['routes']);
        //        $this->loadRoutesFrom(base_path($meta['routes']));
        //    }
        //}

        // login: 5회/분 (이미 있다면 유지)
        RateLimiter::for('login', function (Request $r) {
            $email = (string) $r->input('email', 'noemail');
            return Limit::perMinute(5)->by($email.'|'.$r->ip());
        });

        // refresh: 30회/분 (IP + 토큰 일부)
        RateLimiter::for('refresh', function (Request $r) {
            $part = substr((string)$r->input('refresh_token', 'none'), 0, 10);
            return Limit::perMinute(30)->by($part.'|'.$r->ip());
        });
    }
}

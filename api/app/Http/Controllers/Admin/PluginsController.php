<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PluginsIndexRequest;
use App\Http\Requests\Admin\PluginsStoreRequest;
use App\Http\Requests\Admin\PluginsUpdateRequest;
use App\Http\Resources\Admin\PluginResource;
use App\Models\Plugin;
use App\Models\MetaLog;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PluginsController extends Controller
{
    // GET /api/admin/plugins
    public function index(PluginsIndexRequest $r)
    {
        $v = $r->validated();
        $perPage = (int)($v['per_page'] ?? 20);

        $paginator = Plugin::query()
            ->search($v['search_field'] ?? null, $v['search_term'] ?? null)
            ->sort($v['sort_by'] ?? 'id', $v['direction'] ?? 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return ApiResponse::ok([
            'data' => PluginResource::collection($paginator)->resolve(),
            'meta' => [
                'page'      => $paginator->currentPage(),
                'per_page'  => $paginator->perPage(),
                'total'     => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    // GET /api/admin/plugins/{slug}
    public function show(string $slug)
    {
        $plugin = Plugin::with(['versions','dependencies'])->where('slug', $slug)->first();
        if (!$plugin) {
            return ApiResponse::error('PLUGIN_NOT_FOUND', '플러그인을 찾을 수 없습니다.', 404);
        }
        return ApiResponse::ok(PluginResource::make($plugin)->resolve());
    }

    // POST /api/admin/plugins
    public function store(PluginsStoreRequest $r)
    {
        $v = $r->validated();

        $plugin = new Plugin();
        $plugin->slug        = $v['slug'];
        $plugin->name        = $v['name'];
        $plugin->version     = $v['version'];
        $plugin->path        = $v['path'];
        $plugin->manifest    = json_encode($v['manifest'], JSON_UNESCAPED_UNICODE);
        $plugin->repo_url    = $v['repo_url'] ?? null;
        $plugin->author_name = $v['author_name'] ?? null;
        $plugin->author_url  = $v['author_url'] ?? null;
        $plugin->license     = $v['license'] ?? null;
        $plugin->description = $v['description'] ?? null;
        $plugin->installed   = 1;
        $plugin->installed_at = now();
        $plugin->save();

        MetaLog::recordAdmin(
            action: 'plugin.installed',
            level: 'info',
            adminId: $r->user()->id ?? null,
            message: 'Plugin installed',
            context: ['slug' => $plugin->slug, 'name' => $plugin->name],
            request: $r
        );

        return ApiResponse::ok(PluginResource::make($plugin)->resolve(), 'created', 201);
    }

    // PATCH /api/admin/plugins/{slug}
    public function update(PluginsUpdateRequest $r, string $slug)
    {
        $plugin = Plugin::where('slug', $slug)->first();
        if (!$plugin) {
            return ApiResponse::error('PLUGIN_NOT_FOUND', '플러그인을 찾을 수 없습니다.', 404);
        }

        $plugin->fill($r->validated());
        $plugin->save();

        MetaLog::recordAdmin(
            action: 'plugin.updated',
            level: 'info',
            adminId: $r->user()->id ?? null,
            message: 'Plugin updated',
            context: ['slug' => $plugin->slug],
            request: $r
        );

        return ApiResponse::ok(PluginResource::make($plugin)->resolve(), 'updated');
    }

    // DELETE /api/admin/plugins/{slug}
    public function destroy(Request $r, string $slug)
    {
        $plugin = Plugin::where('slug', $slug)->first();
        if (!$plugin) {
            return ApiResponse::error('PLUGIN_NOT_FOUND', '플러그인을 찾을 수 없습니다.', 404);
        }

        DB::transaction(function () use ($plugin, $r) {
            $slug = $plugin->slug;
            $plugin->delete();

            MetaLog::recordAdmin(
                action: 'plugin.deleted',
                level: 'warning',
                adminId: $r->user()->id ?? null,
                message: 'Plugin deleted',
                context: ['slug' => $slug],
                request: $r
            );
        });

        return ApiResponse::ok(['slug' => $slug, 'message' => '플러그인이 삭제되었습니다.']);
    }

    // POST /api/admin/plugins/{slug}/enable
    public function enable(Request $r, string $slug)
    {
        $plugin = Plugin::where('slug', $slug)->first();
        if (!$plugin) {
            return ApiResponse::error('PLUGIN_NOT_FOUND', '플러그인을 찾을 수 없습니다.', 404);
        }

        $plugin->enabled = 1;
        $plugin->enabled_at = now();
        $plugin->save();

        MetaLog::recordAdmin(
            action: 'plugin.enabled',
            level: 'info',
            adminId: $r->user()->id ?? null,
            message: 'Plugin enabled',
            context: ['slug' => $plugin->slug],
            request: $r
        );

        return ApiResponse::ok(['slug' => $plugin->slug, 'enabled' => true]);
    }

    // POST /api/admin/plugins/{slug}/disable
    public function disable(Request $r, string $slug)
    {
        $plugin = Plugin::where('slug', $slug)->first();
        if (!$plugin) {
            return ApiResponse::error('PLUGIN_NOT_FOUND', '플러그인을 찾을 수 없습니다.', 404);
        }

        $plugin->enabled = 0;
        $plugin->disabled_at = now();
        $plugin->save();

        MetaLog::recordAdmin(
            action: 'plugin.disabled',
            level: 'info',
            adminId: $r->user()->id ?? null,
            message: 'Plugin disabled',
            context: ['slug' => $plugin->slug],
            request: $r
        );

        return ApiResponse::ok(['slug' => $plugin->slug, 'enabled' => false]);
    }
}

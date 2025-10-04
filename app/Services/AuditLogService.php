<?php

namespace App\Services;

use App\Enums\AuditLogSeverityEnum;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class AuditLogService
{
    private function getSeverityFromStatusCode(?int $statusCode): AuditLogSeverityEnum
    {
        $code = $statusCode ?? 200;

        return match (true) {
            $code >= 500 => AuditLogSeverityEnum::CRITICAL,
            $code >= 400 => AuditLogSeverityEnum::ERROR,
            $code >= 300 => AuditLogSeverityEnum::WARNING,
            default      => AuditLogSeverityEnum::INFO,
        };
    }

    public function log(Request $request, Response $response): void
    {
        $routeName = $request->route()?->getName() ?? 'unknown';
        $status    = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : null;

        // napr. log_auth_login_401 alebo log_unknown_200
        $method = 'log_'.Str::replace('.', '_', $routeName).'_'.($status ?? 200);

        if (method_exists($this, $method)) {
            $this->$method($request, $response);
            return;
        }

        $this->log_default($request, $response, [
            'route_name'       => $routeName,
            'http_status_code' => $status,
        ]);
    }

    public function log_default(Request $request, Response $response, array $data = []): void
    {
        $user       = $data['user'] ?? auth()->user();     // null-safe
        $routeName  = $data['route_name'] ?? $request->route()?->getName() ?? 'unknown';
        $statusCode = $data['http_status_code'] ?? (method_exists($response, 'getStatusCode') ? $response->getStatusCode() : null);

        // bezpečne prečítaj parametre aj keď žiadna route nie je
        $parameters = [];
        $route = $request->route();
        $routeParams = method_exists($route, 'parameters') ? $route->parameters() : [];
        foreach ($routeParams as $name => $model) {
            if (is_object($model)) {
                $parameters[$name]['id']   = $model->id ?? null;
                $parameters[$name]['code'] = $model->getAttribute('code') ?? null;
                $parameters[$name]['name'] = $model->getAttribute('name') ?? null;
            } else {
                $parameters[$name]['id'] = $model;
            }
        }

        AuditLog::create([
            'user_id'         => $user?->id, // môže byť null
            'severity'        => $data['severity'] ?? $this->getSeverityFromStatusCode($statusCode),
            'http_status_code'=> $statusCode,
            'ip_address'      => $data['ip_address'] ?? request()->ip(),
            'url'             => $data['url'] ?? $request->fullUrl(),
            'method'          => $data['method'] ?? $request->getMethod(),
            'route'           => $routeName,
            'action'          => __('routes.'.$routeName), // ak nemáš preklad, kľudne nechaj len $routeName
            'user_agent'      => $data['user_agent'] ?? $request->header('User-Agent'),
            'parameters'      => $parameters,
        ]);
    }
}

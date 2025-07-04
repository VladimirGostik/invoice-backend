<?php

namespace App\Services;

use App\Enums\AuditLogSeverityEnum;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuditLogService
{
    /**
     * Function to determine severity based on HTTP status code
     */
    private function getSeverityFromStatusCode(int $statusCode): AuditLogSeverityEnum
    {
        return match (true) {
            $statusCode >= 500 => AuditLogSeverityEnum::CRITICAL,
            $statusCode >= 400 => AuditLogSeverityEnum::ERROR,
            $statusCode >= 300 => AuditLogSeverityEnum::WARNING,
            default => AuditLogSeverityEnum::INFO,
        };
    }

    /**
     * Log user action
     */
    public function log(Request $request, Response $response): void
    {
        // Build method name. Example: log_auth_login_401
        $method = 'log_'
            .Str::replace('.', '_', $request->route()?->getName())
            .'_'.$response->getStatusCode();

        if (method_exists(static::class, $method)) {
            // Execute existing method
            $this->$method($request, $response);
        } else {
            // Use default logging method
            $this->log_default($request, $response);
        }
    }

    /**
     * Default log action
     */
    public function log_default(Request $request, Response $response, array $data = null): void
    {
        $user = $data['user'] ?? auth()->user();
        $entity = $data['entity'] ?? app('entity');
        $routeName = $data['route_name'] ?? $request->route()?->getName();

        // Get eloquent models from the route
        $parameters = [];
        foreach (request()->route()->parameters() as $name => $model) {
            if (is_object($model)) {
                $parameters[$name]['id'] = $model->id;

                if ($model->getAttribute('code')) {
                    $parameters[$name]['code'] = $model->getAttribute('code');
                }

                if ($model->getAttribute('name')) {
                    $parameters[$name]['name'] = $model->getAttribute('name');
                }
            } else {
                $parameters[$name]['id'] = $model;
            }
        }

        AuditLog::create([
            'user_id' => $user?->id,
            'entity_id' => $entity?->id,
            'severity' => $data['severity'] ?? $this->getSeverityFromStatusCode($response->getStatusCode()), // Determine severity based on HTTP status
            'http_status_code' => $data['http_status_code'] ?? $response->getStatusCode(),
            'ip_address' => $data['ip_address'] ?? get_client_ip(),
            'url' => $data['url'] ?? $request->fullUrl(),
            'method' => $data['method'] ?? $request->getMethod(),
            'route' => $routeName,
            'action' => __('routes.'.$routeName),
            'user_agent' => $data['user_agent'] ?? $request->header('User-Agent'),
            'parameters' => $parameters,
        ]);
    }
}
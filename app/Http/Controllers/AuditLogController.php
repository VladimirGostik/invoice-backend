<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuditLogRequest;
use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use App\Repositories\Interfaces\AuditLogRepositoryInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group('Audit_logs')]
class AuditLogController extends Controller
{
    use AuthorizesRequests;

    /**
     * Audit log repository
     */
    protected AuditLogRepositoryInterface $logs;

    public function __construct(AuditLogRepositoryInterface $logs)
    {
        $this->logs = $logs;
    }

    /**
     * List events
     *
     * Display a listing of the audit log events.
     */
    #[QueryParam('page', 'int', 'Set the page number for pagination. Default: 1')]
    #[QueryParam('per_page', 'int', 'Set the number of records per page. Default: 10')]
    #[QueryParam('sort', 'string', 'Set sorting column, use "-" prefix for descending sorting. Default: -created_at')]
    #[QueryParam('filter[user_id]', 'int', 'Filter records by user ID.')]
    #[QueryParam('filter[severity]', 'string', 'Filter records by severity.')]
    #[QueryParam('filter[http_status_code]', 'string', 'Filter records by the HTTP status code.')]
    #[QueryParam('filter[action]', 'string', 'Filter records by action.')]
    #[QueryParam('filter[ip_address]', 'string', 'Filter records by ip address.')]
    #[QueryParam('filter[from_created_at]', 'string', 'Filter records by created_at from datetime.', example: '2018-01-01 12:00:00')]
    #[QueryParam('filter[to_created_at]', 'string', 'Filter records by created_at to datetime.', example: '2018-12-31 15:00:00')]
    #[ResponseFromApiResource(AuditLogResource::class, AuditLog::class, with: ['user', 'entity'], paginate: 10)]
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', AuditLog::class);

        return AuditLogResource::collection(
            $this->logs->search($request->all())
        );
    }

    /**
     * Get event
     *
     * Display the specified audit log record.
     */
    #[ResponseFromApiResource(AuditLogResource::class, AuditLog::class, with: ['user'])]
    public function show(AuditLog $auditLog): AuditLogResource
    {
        $this->authorize('view', $auditLog);

        $auditLog->load('user');

        return new AuditLogResource($auditLog);
    }
}
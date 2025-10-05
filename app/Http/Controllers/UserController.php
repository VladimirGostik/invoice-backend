<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\EmailExistsRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;

#[Group('Zoznam používateľov')]
class UserController extends Controller
{

    use AuthorizesRequests;
    /**
     * User repository
     */
    protected UserRepositoryInterface $users;

    public function __construct(UserRepositoryInterface $users)
    {
        $this->users = $users;
    }

    /**
     * List users
     *
     * Display a filtered listing of users.
     */

    #[QueryParam('page', 'int', 'Set the page number for pagination. Default: 1', example: 1)]
    #[QueryParam('per_page', 'int', 'Set the number of records per page. Default: 10', example: 10)]
    #[QueryParam('sort', 'string', 'Set sorting column, use "-" prefix for descending sorting. Default: -created_at', example: '-created_at')]
    #[QueryParam('filter[email]', 'string', 'Filter records by email.', example: 'test@test.sk')]
    #[QueryParam('filter[first_name]', 'string', 'Filter records by first name.', example: 'John')]
    #[QueryParam('filter[last_name]', 'string', 'Filter records by last name.', example: 'Doe')]
    #[QueryParam('filter[phone]', 'string', 'Filter records by phone number.', example: '+420123456789')]
    #[QueryParam('filter[state]', 'string', 'Filter records by state.', example: 'ACTIVE')]
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', User::class);

        return UserResource::collection(
            $this->users->search($request->all())
        );
    }

    /**
     * Create a user
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $user = $this->users->create($request->validated());

        return response()->json([
            'id' => $user->id,
        ], 201);
    }

    /**
     * Get user
     *
     * Get the specified user detail.
     */
    public function show(User $user): UserResource
    {
        $user = auth()->user();
        $this->authorize('view', $user);
        return new UserResource($user);
    }

    /**
     * Update user
     *
     * Update the specified user in storage.
     */
    #[BodyParam('email', 'string', 'The email of the user.', required: true, example: 'admin@example.com')]
    #[BodyParam('first_name', 'string', 'The first name of the user.', required: true, example: 'John')]
    #[BodyParam('last_name', 'string', 'The last name of the user.', required: true, example: 'Doe')]
    #[BodyParam('phone', 'string', 'The phone number of the user.', required: true, example: '+420123456789')]
    #[BodyParam('password', 'string', 'The password of the user.', required: false, example: 'lbyjEi&DQ^*]aL')]
    #[BodyParam('password_confirmation', 'string', 'The password confirmation.', required: false, example: 'lbyjEi&DQ^*]aL')]
    #[BodyParam('state', 'string', 'The state of the user.', required: false, example: 'ACTIVE')]
    public function update(UpdateRequest $request, User $user): Response
    {
        $this->users->update($user, Arr::except($request->validated(), ['password', 'password_confirmation']));

        // Update password
        if ($request->has('password')) {
            $this->users->changePassword($user, $request->get('password'));
        }

        return response()->noContent();
    }

    /**
     * Delete user
     */
    public function destroy(User $user): Response
    {
        $this->authorize('delete', $user);

        $this->users->delete($user);

        return response()->noContent();
    }

    /**
     * Change password
     *
     * Change the password of the specified user.
     *
     */
    #[BodyParam('old_password', 'string', 'The old password of the user.', required: true)]
    #[BodyParam('password', 'string', 'The new password of the user.', required: true, example: 'lbyjEi&DQ^*]aL')]
    #[BodyParam('password_confirmation', 'string', 'The new password confirmation.', required: true, example: 'lbyjEi&DQ^*]aL')]
    public function changePassword(User $user, ChangePasswordRequest $request): Response|JsonResponse
    {
        $this->authorize('update', $user);

        // Validate the old password
        if (! Hash::check($request->get('old_password'), $user->password)) {
            return response()->json([
                'error' => __('messages.the_old_password_is_incorrect'),
            ], 422);
        }

        $this->users->changePassword($user, $request->get('password'));

        return response()->noContent();
    }

    /**
     * Email validation
     *
     * Check if a user with specified email exists
     */
    #[BodyParam('email', 'string', 'The email of the user.', required: true, example: 'admin@example.com')]
    public function emailExists(EmailExistsRequest $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $exists = (bool) User::select('id')->where('email', $request->email)->first();

        return response()->json(compact('exists'));
    }
}

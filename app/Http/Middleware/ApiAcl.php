<?php
declare (strict_types = 1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;

/**
 * Class Acl
 */
class ApiAcl
{
    /**
     * The authentication guard factory instance.
     *
     * @var JWTAuth
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param JWTAuth $auth
     */
    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @param string $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role = null)
    {
        try {
            $user = $this->auth->parseToken()->authenticate();
            if (!$user) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        if (!empty($role)) {
            $requiredRoles = explode('|', $role);
            $userModules = $user->modules();

            foreach ($userModules as $userModule) {
                if (in_array($userModule, $requiredRoles)) {
                    $request->merge(['user' => $user]);
                    return $next($request);
                }
            }
        } else {
            $request->merge(['user' => $user]);
            return $next($request);
        }
        return response()->json(['no_privilege'], 403);
    }
}

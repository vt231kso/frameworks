<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenBlacklistedException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

final class JwtAuthenticate
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (! JWTAuth::parseToken()->authenticate()) {
                return $this->fail('user_not_found', 'Користувача не знайдено');
            }
        } catch (TokenExpiredException) {
            return $this->fail('token_expired', 'Термін дії токена минув, оновіть його');
        } catch (TokenBlacklistedException) {
            // має стояти перед TokenInvalidException
            return $this->fail('token_revoked', 'Токен відкликано');
        } catch (TokenInvalidException) {
            return $this->fail('token_invalid', 'Невалідний токен');
        } catch (JWTException) {
            return $this->fail('token_not_found', 'Токен не передано');
        }

        // щоб $request->user() і Gate бачили цей guard
        auth()->shouldUse('api');

        return $next($request);
    }

    /**
     * @param string $code
     * @param string $message
     * @return JsonResponse
     */
    private function fail(string $code, string $message): JsonResponse
    {
        return response()->json(['error' => $code, 'message' => $message], 401);
    }
}

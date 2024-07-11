<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\HttpUtils;

class ApiEntryPoint implements AuthenticationEntryPointInterface
{
    public function __construct(private HttpUtils $httpUtils)
    {
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        if ($request->isXmlHttpRequest() || $request->isMethod('POST')) {
            // Return a JSON response for AJAX requests or POST methods
            $data = [
                'message' => $authException ? $authException->getMessage() : 'Authentication Required',
            ];
            return new JsonResponse($data, JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Fallback to a default response
        throw new HttpException(JsonResponse::HTTP_UNAUTHORIZED, 'Authentication Required');
    }
}

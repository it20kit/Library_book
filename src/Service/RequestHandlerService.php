<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

class RequestHandlerService
{
    public function getDescriptionFromRequest(Request $request): array
    {
        $jsonStr = $request->getContent();
        return json_decode($jsonStr, true);
    }
}
<?php

namespace App\RequestItem;

class RequestItemFactory
{
    /**
     * @throws \Exception
     */
    public static function getRequestItem(array $requestData): RequestItem
    {
        if (!isset($requestData['method'])) throw new \Exception('Missing request method');
        return match ($requestData['method']) {
            'GET' => GetRequestItem::fromArray($requestData),
            'POST' => PostRequestItem::fromArray($requestData),
            default => throw new \Exception('Request method is not found')
        };

    }
}
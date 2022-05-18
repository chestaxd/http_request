<?php

namespace App\RequestItem;

class RequestItemFactory
{
    public static function getRequestItem(array $requestData): RequestItem
    {
        return match ($requestData['method']) {
            'GET' => GetRequestItem::fromArray($requestData),
            'POST' => PostRequestItem::fromArray($requestData),
        };

    }

}
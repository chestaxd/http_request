<?php

namespace App\RequestItem;

class GetRequest implements InitStrategy
{

    public function init(array $requestData, RequestItem $requestItem)
    {
        $requestItem->setMethod($requestData['method']);
        $requestItem->setUrl($requestData['url']);
        if (isset($requestData['options'])) {
            $requestItem->setOptions($requestData['options']);
        }
    }
}
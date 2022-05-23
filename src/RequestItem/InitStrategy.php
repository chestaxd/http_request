<?php

namespace App\RequestItem;

interface InitStrategy
{
    public function init(array $requestData, RequestItem $requestItem);
}
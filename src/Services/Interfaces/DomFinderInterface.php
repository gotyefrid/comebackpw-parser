<?php

namespace Gotyefrid\ComebackpwParser\Services\Interfaces;

use Gotyefrid\ComebackpwParser\Models\ShopItem;

interface DomFinderInterface
{
    public function getShopsHtml(string $html): array;
}
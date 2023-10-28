<?php

namespace Gotyefrid\ComebackpwParser\Services\Interfaces;

use Gotyefrid\ComebackpwParser\Models\ShopItem;

interface DomFinderInterface
{
    public function getFilledItems(string $html): array;
}
<?php

namespace Gotyefrid\ComebackpwParser\Models;

use Gotyefrid\ComebackpwParser\Base\BaseObject;

/**
 * Координаты
 */
class Coordinates extends BaseObject
{
    public string $region = '';
    public int $x = 0;
    public int $y = 0;

    public function map(string $coordsWithRegion): static
    {
        $this->x = substr($coordsWithRegion, 0, 3);
        $this->y = substr($coordsWithRegion, 4, 3);
        $this->region = substr($coordsWithRegion, 8);

        return $this;
    }
}
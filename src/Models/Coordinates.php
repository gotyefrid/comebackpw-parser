<?php

namespace Gotyefrid\ComebackpwParser\Models;

use Gotyefrid\ComebackpwParser\Base\BaseObject;
use Gotyefrid\ComebackpwParser\Services\Dom\DomService;

/**
 * Координаты
 */
class Coordinates extends BaseObject
{
    public string $region = '';
    public int $x = 0;
    public int $y = 0;

    public function __construct(string $coordsString = '', array $config = [])
    {
        parent::__construct($config);

        if ($coordsString) {
            $this->map($coordsString);
        }
    }

    public function map(string $coordsWithRegion): static
    {
        $this->x = substr($coordsWithRegion, 0, 3);
        $this->y = substr($coordsWithRegion, 4, 3);
        $this->region = substr($coordsWithRegion, 8);

        return $this;
    }

    public static function createFromHtml(string $html): static
    {
        $dom = DomService::createDomDocument($html);
        $coords = $dom->query("//p[@class='coordinate']");

        if (!isset($coords[0])) {
            $coords = $dom->query("//span[@class='coordinate_shop']");
        }

        return new Coordinates($coords[0]->textContent);
    }
}
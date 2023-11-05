<?php

namespace Gotyefrid\ComebackpwParser\Models;

use Gotyefrid\ComebackpwParser\Base\BaseObject;
use Gotyefrid\ComebackpwParser\Services\Dom\DomService;

/**
 * Координаты
 */
class Coordinates extends BaseObject
{
    /**
     * @var string Регион находения магазина
     */
    public string $region = '';

    /**
     * @var int Координата Х
     */
    public int $x = 0;

    /**
     * @var int Координата Y
     */
    public int $y = 0;

    /**
     * @param string $coordsString Если есть готовая строка типа - "345 125 Мир ГДЗ"
     * @param array $config
     */
    public function __construct(string $coordsString = '', array $config = [])
    {
        parent::__construct($config);

        if ($coordsString) {
            $this->map($coordsString);
        }
    }

    /**
     * Маппинг данных из строки типа - "345 125 Мир ГДЗ"
     * @param string $coordsWithRegion
     * @return $this
     */
    public function map(string $coordsWithRegion): static
    {
        $this->x = substr($coordsWithRegion, 0, 3);
        $this->y = substr($coordsWithRegion, 4, 3);
        $this->region = substr($coordsWithRegion, 8);

        return $this;
    }

    /**
     * Создать класс координат из куска кода магазина
     * @param string $html Здесь может прилететь как кусок кода из общей страницы, так и из страницы конкретного магазина
     * @return static
     */
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
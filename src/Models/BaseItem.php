<?php

namespace Gotyefrid\ComebackpwParser\Models;

use Gotyefrid\ComebackpwParser\Base\BaseObject;
use Gotyefrid\ComebackpwParser\Services\Dom\DomService;

/**
 * Базовый класс предмета
 */
class BaseItem extends BaseObject
{
    /**
     * @var string Название предмета
     */
    public string $name = '';

    /**
     * @var float Цена
     */
    public float $price = 0;

    /**
     * @var int Количество предметов в продаже
     */
    public int $count = 1;

    /**
     * Заполнить данные предмета пакетно
     * @param string $html конкретного предмета
     * @return array
     */
    public static function createMultipleFromHtml(string $html): array
    {
        $dom = DomService::createDomDocument($html);
        $items = $dom->query("//img[@data-desc]");

        foreach ($items as $item) {
            $predmet = new static();
            $predmet->name = $item->getAttribute('data-name');
            $predmet->count = $item->getAttribute('data-count');
            $predmet->price = $item->getAttribute('data-price');

            $array[] = $predmet;
        }

        return $array ?? [];
    }
}
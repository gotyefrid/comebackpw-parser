<?php

namespace Gotyefrid\ComebackpwParser\Models;

use Gotyefrid\ComebackpwParser\Base\BaseObject;

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
}
<?php

namespace Gotyefrid\ComebackpwParser\Models;

use Gotyefrid\ComebackpwParser\Base\BaseObject;

/**
 * Результат работы парсера
 */
class ShopItem extends BaseObject
{
    /**
     * @var BaseItem Предмет который был найден
     */
    public BaseItem $item;

    /**
     * @var Shop Магазин который его продает
     */
    public Shop $shop;
}
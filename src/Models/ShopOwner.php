<?php

namespace Gotyefrid\ComebackpwParser\Models;

use Gotyefrid\ComebackpwParser\Base\BaseObject;

/**
 * Владелец магазина
 */
class ShopOwner extends BaseObject
{
    /**
     * @var int
     */
    public int $id;

    /**
     * @var string Ник игрока
     */
    public string $nickname = '';
}
<?php

namespace Gotyefrid\ComebackpwParser\Models;

/**
 * Предмет шмота
 */
class ArmorItem extends BaseItem
{
    /**
     * @var int Кол-во ячеек для камней
     */
    public int $cells = 0;

    /**
     * @var int Enhance Point точка
     */
    public int $ep = 0;

    /**
     * @var array Уникальные характеристики предмета
     */
    public array $stats = [];
}
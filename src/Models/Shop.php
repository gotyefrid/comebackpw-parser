<?php

namespace Gotyefrid\ComebackpwParser\Models;

use DOMNodeList;
use Gotyefrid\ComebackpwParser\Base\BaseObject;
use PhpQuery\PhpQuery;

/**
 * Модель магазина
 */
class Shop extends BaseObject
{
    /**
     * @var int ID магазина
     */
    public int $id;

    /**
     * @var ShopOwner Владелец магазина
     */
    public ShopOwner $owner;

    /**
     * @var string Название магазина
     */
    public string $name = '';

    /**
     * @var Coordinates Координаты магазина
     */
    public Coordinates $coordinates;

    /**
     * @var BaseItem[] Предметы в магазине
     */
    public array $items = [];

    public string $html = '';

    public function isAllItemsVisible(): bool
    {
        if ($this->html) {
            $query = new PhpQuery();
            $query->load_str($this->html);

            /** @var DOMNodeList $existMore */
            $existMore = $query->xpath("//div[@class='its_button']");

            if ($existMore->length) {
                return false;
            }
        }

        return true;
    }
}
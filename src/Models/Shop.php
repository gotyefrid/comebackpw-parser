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
    public bool $isExistMore = false;

    public function __construct(string $html, array $config = [])
    {
        if ($this->isExistMore) {
            $this->mapFromShop();
        } else {
            $this->map();
        }

        parent::__construct($config);
    }

    private function map()
    {

    }

    private function mapFromShop()
    {
    }
}
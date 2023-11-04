<?php

namespace Gotyefrid\ComebackpwParser\Models;

use DOMNodeList;
use Gotyefrid\ComebackpwParser\Base\BaseObject;
use Gotyefrid\ComebackpwParser\Services\Dom\DomService;
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

    public function __construct(string $html, array $config = [])
    {
        parent::__construct($config);

        if ($html) {
            $this->html = $html;
            $this->map();
        } else {
            throw new \Exception('Не передан html код магазина');
        }
    }

    private function map()
    {
        $this->name = $this->parseName();
        $this->coordinates = Coordinates::createFromHtml($this->html);
        $this->owner = ShopOwner::createFromHtml($this->html);
        $this->items = BaseItem::createMultipleFromHtml($this->html);
        $rf = 13;
    }

    private function parseName()
    {
        $dom = DomService::createDomDocument($this->html);
        $name = $dom->query("//p[@class='catname']");

        if (!isset($name[0])) {
            $name = $dom->query("//p[@class='nameshop']");
        }

        return $name[0]->textContent;
    }
}
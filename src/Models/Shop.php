<?php

namespace Gotyefrid\ComebackpwParser\Models;

use Exception;
use Gotyefrid\ComebackpwParser\Base\BaseObject;
use Gotyefrid\ComebackpwParser\Services\Dom\DomService;

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

    /**
     * @var string Код магазина (может быть из общей страницы, а может быть из конкретного магазина)
     */
    public string $html = '';

    /**
     * @param string $html Код магазина (может быть из общей страницы, а может быть из конкретного магазина)
     * @param array $config
     * @throws Exception
     */
    public function __construct(string $html, array $config = [])
    {
        parent::__construct($config);

        if ($html) {
            $this->html = $html;
            $this->map();
        } else {
            throw new Exception('Не передан html код магазина');
        }
    }

    /**
     * Маппинг текущей модели и всех зависимостей
     * @return void
     */
    private function map(): void
    {
        $this->name = $this->parseName();
        $this->coordinates = Coordinates::createFromHtml($this->html);
        $this->owner = ShopOwner::createFromHtml($this->html);
        $this->items = BaseItem::createMultipleFromHtml($this->html);
    }

    /**
     * Получить имя магазина
     * @return string
     */
    private function parseName(): string
    {
        $dom = DomService::createDomDocument($this->html);
        $name = $dom->query("//p[@class='catname']");

        if (!isset($name[0])) {
            $name = $dom->query("//p[@class='nameshop']");
        }

        return $name[0]->textContent;
    }
}
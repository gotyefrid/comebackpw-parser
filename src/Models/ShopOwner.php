<?php

namespace Gotyefrid\ComebackpwParser\Models;

use Gotyefrid\ComebackpwParser\Base\BaseObject;
use Gotyefrid\ComebackpwParser\Services\Dom\DomService;

/**
 * Владелец магазина
 */
class ShopOwner extends BaseObject
{
    /**
     * @var string Ник игрока
     */
    public string $nickname = '';


    /**
     * @param string $html Код магазина (может быть из общей страницы, а может быть из конкретного магазина)
     * @return static
     */
    public static function createFromHtml(string $html): static
    {
        $dom = DomService::createDomDocument($html);
        $nickname = $dom->query("//p[@class='charactername']");

        if (!isset($nickname[0])) {
            $nickname = $dom->query("//p[@class='nameshop']");
        }

        $nickname = $nickname[0]->textContent;

        return new static([
            'nickname' => $nickname
        ]);
    }
}
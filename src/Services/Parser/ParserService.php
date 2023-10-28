<?php

namespace Gotyefrid\ComebackpwParser\Services\Parser;

use Gotyefrid\ComebackpwParser\Base\BaseObject;
use Gotyefrid\ComebackpwParser\Models\BaseItem;
use Gotyefrid\ComebackpwParser\Models\Shop;
use Gotyefrid\ComebackpwParser\Models\ShopItem;
use Gotyefrid\ComebackpwParser\Services\Dom\DomService;
use Gotyefrid\ComebackpwParser\Services\Dom\PhpQueryDomService;
use Gotyefrid\ComebackpwParser\Services\Interfaces\DomFinderInterface;
use PhpQuery\PhpQuery;

class ParserService extends BaseObject
{
    public string $version = '146x';
    public string $mainUrl = 'https://comeback-pw.translate.goog/cats/';
    public int $engine = 1;
    public ?DomFinderInterface $domFinder = null;

    public const TYPE_ALL = 1;
    public const TYPE_ARMOR = 2;
    public const TYPES = [
        self::TYPE_ALL => 'all',
        self::TYPE_ARMOR => 'armor'
    ];
    public const ENGINE_WGET = 1;
    public const ENGINES = [
        self::ENGINE_WGET => 'wget'
    ];

    public bool $isLastPage = true;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        if (!$this->domFinder) {
            $this->domFinder = new DomService();
        }
    }

    public function run(int $type = self::TYPE_ALL): ShopItem
    {
        $page = 1;

        do {
            $shops = $this->getAllShopsFromPage($page);
            $this->getItemsFromShops($shops);

            $this->isLastPage = false;
            $page++;
        } while ($this->isLastPage);
    }

    /**
     * @param int $page
     * @return Shop[]
     */
    public function getAllShopsFromPage(int $page): array
    {
        $html = $this->getHtml(['page' => $page]);

        if ($this->isHtmlValid($html)) {
            return $this->domFinder->getFilledItems($html);
        }

        return [];
    }

    private function getHtml(array $query): string
    {
        $html = match ($this->engine) {
            self::ENGINE_WGET => file_get_contents($this->getUrl($query))
        };

        return $html;
    }

    /**
     * @param array $query
     * @return string
     */
    public function getUrl(array $query): string
    {
        $queryBase = [
            '_x_tr_sl' => 'en',
            '_x_tr_tl' => 'ru',
            '_x_tr_hl' => 'ru',
            '_x_tr_pto' => 'wapp',
        ];

        $query = http_build_query(array_merge($queryBase, $query));

        return $this->mainUrl . $this->version . "?$query";
    }

    private function isHtmlValid(string $html)
    {
        return str_contains($html, '<div id="content">');
    }

    /**
     * @param Shop[] $shops
     * @return void
     */
    private function getItemsFromShops(array $shops)
    {
        foreach ($shops as $shop) {
            if ($shop->isAllItemsVisible()) {
                $shop->items = $this->domFinder->getItemsMain($shop->html);
            }
        }
    }
}
<?php

namespace Gotyefrid\ComebackpwParser\Services\Parser;

use Exception;
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
    public string $shopUrl = 'https://comeback-pw.translate.goog/cats/';
    public ?DomFinderInterface $domFinder = null;

    public const TYPE_ALL = 1;
    public const TYPE_ARMOR = 2;
    public const TYPES = [
        self::TYPE_ALL => 'all',
        self::TYPE_ARMOR => 'armor'
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
            $shopsHtml = $this->getShopsHtml($page);
            $shops = $this->createShops($shopsHtml);

            $this->isLastPage = false;
            $page++;
        } while ($this->isLastPage);
    }

    /**
     * @param int $page
     * @return Shop[]
     * @throws Exception
     */
    public function getShopsHtml(int $page): array
    {
        $html = $this->getHtml($this->getMainUrl(['page' => $page]));

        if ($this->isMainHtmlValid($html)) {
            return $this->domFinder->getShopsHtml($html);
        } else {
            throw new Exception('Основной html невалидный');
        }

        return [];
    }

    private function getHtml(string $url): string
    {
        return file_get_contents($url);
    }

    /**
     * @param array $query
     * @return string
     */
    public function getMainUrl(array $query): string
    {
        $query = http_build_query(array_merge($this->getBaseQuery(), $query));

        return $this->mainUrl . $this->version . "?$query";
    }

    private function getBaseQuery(): array
    {
        return [
            '_x_tr_sl' => 'en',
            '_x_tr_tl' => 'ru',
            '_x_tr_hl' => 'ru',
            '_x_tr_pto' => 'wapp',
        ];
    }

    public function getShopUrl(int $shopId): string
    {
        $query = [
            'shop' => $shopId
        ];

        $query = http_build_query(array_merge($this->getBaseQuery(), $query));

        return $this->shopUrl . $this->version . "?$query";
    }

    private function isMainHtmlValid(string $html): bool
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

    private function createShops(array $shopsHtml)
    {
        foreach ($shopsHtml as $shopHtml) {
            $isExistMore = $this->domFinder->isExistMore($shopHtml);

            if ($isExistMore) {
                $shopId = $this->domFinder->getShopId($shopHtml);
                $shopHtml = $this->getHtml($this->getShopUrl($shopId));
                $shopHtml = $this->domFinder->getShopHtml($shopHtml);
            }

            $shops[] = new Shop($shopHtml, [
                'isExistMore' => $isExistMore
            ]);

            ob_flush();
            var_dump($shopHtml);die;
        }

        return $shops ?? [];
    }
}
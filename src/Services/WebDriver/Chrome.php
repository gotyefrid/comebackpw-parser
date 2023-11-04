<?php

namespace Gotyefrid\ComebackpwParser\Services\WebDriver;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;

class Chrome implements WebDriverInterface
{
    public const HOST = 'http://selenium:4444/wd/hub';
    public string $host = 'http://selenium:4444/wd/hub';

    /**
     * @param string $url
     * @return string|null
     */
    public static function getHtml(string $url): ?string
    {
        // Создаем объект настроек ChromeOptions
        $options = new ChromeOptions();

        // Добавляем параметр --disable-dev-shm-usage в настройки
        $options->addArguments(['--disable-dev-shm-usage', '--no-sandbox']);
        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        $driver = RemoteWebDriver::create(self::HOST, $capabilities);
        $driver->get($url);
        // Добавьте ожидание перед извлечением HTML-кода
        $wait = new WebDriverWait($driver, 10); // Максимальное время ожидания в секундах
        $wait->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::tagName('footer')));

        $html = $driver->getPageSource();
        if (!str_contains($html, 'footer')) {
            throw new \Exception('Не удалось получить полный html за несколько попыток');
        }
        //for ($i = 0; $i <= 2; $i++) {
        //    if (!str_contains('footer', $html = $driver->getPageSource())) {
        //        sleep(2);
        //        if ($i === 2) {
        //            throw new \Exception('Не удалось получить полный html за несколько попыток');
        //        }
        //    } else {
        //        break;
        //    }
        //}



        $driver->quit();

        return $html;
    }
}
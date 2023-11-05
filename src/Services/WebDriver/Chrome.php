<?php

namespace Gotyefrid\ComebackpwParser\Services\WebDriver;

use Exception;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeoutException;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;

/**
 * Класс для работы с Chrome через Selenium
 */
class Chrome implements WebDriverInterface
{
    private RemoteWebDriver $driver;

    /**
     * @param string $host Адрес селениума
     */
    public function __construct(
        public string $host = 'http://selenium:4444/wd/hub'
    )
    {
        $this->deleteOpenSessions();
    }

    /**
     * Получить HTML код страницы
     * @param string $url
     * @return string
     * @throws Exception
     */
    public function getHtml(string $url): string
    {
        $this->createDriver();
        $html = $this->getPageSource($url);
        $this->driver->quit();

        if (!str_contains($html, 'footer')) {
            throw new Exception('Не удалось получить полный html');
        }

        return $html;
    }

    /**
     * Получить HTML код страницы из драйвера
     * @param string $url
     * @return string|null
     * @throws NoSuchElementException
     * @throws TimeoutException
     */
    private function getPageSource(string $url): ?string
    {
        // Загружаем страницу
        $this->driver->get($url);

        // Ожидание перед извлечением HTML-кода
        $wait = new WebDriverWait($this->driver, 10); // Максимальное время ожидания в секундах
        $wait->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::tagName('footer')));

        return $this->driver->getPageSource();
    }

    /**
     * Создать драйвер хрома
     * @return void
     */
    private function createDriver(): void
    {
        // Создаем объект настроек ChromeOptions
        $options = new ChromeOptions();

        // Добавляем параметр --disable-dev-shm-usage в настройки - для стабильной работы
        $options->addArguments(['--disable-dev-shm-usage', '--no-sandbox']);
        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        $this->driver = RemoteWebDriver::create($this->host, $capabilities);
    }

    /**
     * Отключить все работающие сессии, чтобы не зависало ничего.
     * @return void
     */
    private function deleteOpenSessions(): void
    {
        $ids = $this->getOpenSessionIds();

        foreach ($ids as $id) {
            // Формируем URL для закрытия сессии
            $closeSessionUrl = $this->host . '/session/' . $id;

            // Создаем cURL-сессию
            $ch = curl_init($closeSessionUrl);

            // Устанавливаем опции для отправки DELETE-запроса
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Выполняем запрос
            curl_exec($ch);

            // Закрываем cURL-сессию
            curl_close($ch);
        }
    }

    /**
     * Получить ID всех активных сессий
     * @return array
     */
    private function getOpenSessionIds(): array
    {
        // URL адрес Selenium сервера
        $seleniumServerUrl = $this->host . '/status';

        // Отправляем GET-запрос на сервер для получения информации о сессиях
        $response = file_get_contents($seleniumServerUrl);

        if ($response) {
            $status = json_decode($response, true);
            $nodes = $status['value']['nodes'] ?? [];

            foreach ($nodes as $node) {
                $slots = $node['slots'] ?? [];

                foreach ($slots as $slot) {
                    if (isset($slot['session']['sessionId'])) {
                        $sessions[] = $slot['session']['sessionId'];
                    }
                }
            }
        }

        return $sessions ?? [];
    }
}
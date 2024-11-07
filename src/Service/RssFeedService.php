<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use SimpleXMLElement;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RssFeedService extends AbstractExtension
{
    private HttpClientInterface $httpClient;
    private $feedUrl;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->feedUrl = 'https://www.actu-environnement.com/flux/rss/agroecologie/';
    }


    public function getFunctions(): array
    {
        return [
            new TwigFunction('rss_feed', [$this, 'fetchFeed']),
        ];
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws \Exception
     */
    public function fetchFeed(): array
    {
        $response = $this->httpClient->request('GET', $this->feedUrl);
        $content = $response->getContent();

        // Parse le flux RSS
        $rss = new SimpleXMLElement($content);

        $items = [];
        foreach ($rss->channel->item as $item) {
            $items[] = [
                'title' => (string) $item->title,
                'description' => (string) $item->description,
                'link' => (string) $item->link,
                'pubDate' => (string) $item->pubDate,
            ];
        }

        return $items;
    }
}

<?php

namespace src\Integration;

interface Api
{
    public function get(array $request);
}

namespace src\Decorator;

use DateTime;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use src\Integration\Api;
use src\Integration\DataProvider;

class ConcreteApi implements Api
{
    public function get(array $request)
    {
        // returns a response from external service
    }
}

class ApiCacheDecorator implements Api
{
    protected $api;
    protected $cache;

    public function __construct(Api $api, CacheItemPoolInterface $cache)
    {
        $this->api = $api;
        $this->cache = $cache;
    }

    public function get(array $request)
    {
        $cacheKey = $this->getCacheKey($request);
        $cacheItem = $this->cache->getItem($cacheKey);
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }
        $result = $this->api->get($request);
        $cacheItem
            ->set($result)
            ->expiresAt(
                (new DateTime())->modify('+1 day')
            );
        return $result;
    }

    public function getCacheKey(array $request)
    {
        return json_encode($request);
    }
}

class ApiLogDecorator implements Api
{
    protected $api;
    protected $logger;

    public function __construct(Api $api, LoggerInterface $logger)
    {
        $this->api = $api;
        $this->logger = $logger;
    }

    public function get(array $request)
    {
        try {
            return $this->api->get($request);
        } catch (Exception $e) {
            $this->logger->critical('Error');
        }

        return [];
    }
}

$cache = new CacheItemPool();
$logger = new Logger();

// depends on the requirements we can use cached, logged or both decorators
$decorator = new ApiLogDecorator(new ApiCacheDecorator(new ConcreteApi(), $cache), $logger);
$decorator->get($request);

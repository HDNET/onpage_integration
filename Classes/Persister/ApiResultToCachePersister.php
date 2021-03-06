<?php

/**
 * Class ApiResultToCachePersister
 */

namespace HDNET\OnpageIntegration\Persister;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ApiResultToCachePersister
 */
class ApiResultToCachePersister
{
    const DEFAULT_CACHE_LIFETIME = 604800;
    const CACHE_ID_PREFIX = 'HDNET_onpage_extension';

    /**
     * @param string $data
     * @param string $key
     */
    public function persist($data, $key)
    {
        /** @var CacheManager $cacheManager */
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        $cache = $cacheManager->getCache('onpage_extension');
        if (!$cache->get('lastCrawlDate')) {
            $cache->set('lastCrawlDate', date('d.m.Y'));
        }
        $cache->set($this->getIdentifier($key), json_encode($data));
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getIdentifier($key)
    {
        $id = sha1(self::CACHE_ID_PREFIX . $key);
        return $id;
    }
}

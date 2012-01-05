<?php
/*
 * Tikapot Memcached extension
 *
 */

class CacheException extends Exception { }

abstract class TPCache
{
	static private $cache = null;
	
	public static function getCache() {
		global $tp_options;
		if (isset($tp_options['enable_cache']) && !$tp_options['enable_cache'])
			return null;
		
		if (!class_exists("Memcached"))
			throw new CacheException($GLOBALS["i18n"]["cacheerr1"]);
			
		if (isset(TPCache::$cache) && TPCache::$cache !== null)
			return TPCache::$cache;
		
		global $caches;
		$cache = new Memcached();
		foreach ($caches as $server => $arr) {
			$cache->addServer($arr["host"], $arr["port"]);
		}
		$cache->setOption(Memcached::OPT_PREFIX_KEY, $tp_options['cache_prefix']);
		TPCache::$cache = $cache;
		return TPCache::$cache;
	}
}

?>

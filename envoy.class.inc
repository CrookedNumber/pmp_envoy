<?php

class envoy {

  function __construct() {
    
  }

}

class PMPAuth {
 
}

class PMPDoc {
  function __construct($query_result) {
    $this->version = $query_result->version;
    $this->href = $query_result->href;
    $this->attributes = clone $query_result->attributes;
    $this->links = clone $query_result->links;
    $this->guid = $this->attributes->guid;
    $this->profile_href = $this->links->profile[0]->href;
    $pieces = explode('/', $this->profile_href);
    $this->profile = array_pop($pieces);
    // make this better ^^^^^^^^^^^^
  }

  function hasEnclosure() {
    return !empty($this->links->enclosure);
  }

  function getHREF() {
    if ($this->hasEnclosure()) {
      return $this->links->enclosure[0]->href;
    }
  }
  
  function getGUID() {
    return $this->attributes->guid;
  }
}

class PMPCall {

  function __construct($url, $client_id, $client_secret, $cache = FALSE) {
    $this->query = new stdClass();
    $this->query->results = new stdClass();
    $this->base = $url;
    $this->errors = array();

    // Cache
    $this->from_cache = FALSE;
    $this->caching = $cache;
    
    // Auth
    $this->sdkInclude('AuthClient');
    $this->client_id = $client_id;
    $this->client_secret = $client_secret;
    $this->auth_client = NULL;
    $this->auth();
  }
  
  function auth() {
    // See if we can use cached auth_client
    $cache = $this->cacheGet('pmpapi:auth_client'); // don't hardcode this
    if ($cache) {
      $this->auth_client = $cache;
    }
    else {
      try {
        $auth_client = new \Pmp\Sdk\AuthClient($this->base, $this->client_id, $this->client_secret);
        $this->auth_client = $auth_client;
        $cache_key = $this->cacheKey(array('auth_client'));
        $this->cacheSet($cache_key, $auth_client);
      }
      catch (Exception $e) {
        $this->errors['auth'][] = $e->getMessage();
      }
    }
    return $this->auth_client;
  }
  
  function sdkInclude($file = 'CollectionDocJson') {
    include_once 'phpsdk/lib/Pmp/Sdk/' . $file . '.php';
  }

  function pull($options, $type = 'docs') {
    $this->sdkInclude('CollectionDocJson');
    $cache_values = array('collectionType' => $type) + $options;
    $cache_key = $this->cacheKey($cache_values);
    $cache = $this->cacheGet($cache_key);
    if ($cache) {
      $this->query = $cache;
      $this->from_cache = TRUE;
    }
    else {
      try {
        $doc = $this->getDocument();
        $URN = "urn:collectiondoc:query:$type";
        $results = $doc->query($URN)->submit($options);
        $this->query->results->fullDoc = clone $results;
        if (!empty($options['guid'])) {
          $pmpdoc = new PMPDoc($results);
          $this->query->results->docs[] = $pmpdoc;
          $key = $this->cacheKey(array('guid' => $options['guid']));
          $this->cacheSet($key, $pmpdoc);
        }
        else {
          foreach ($results->items as $item) {
            $pmpdoc = new PMPDoc($item);
            $this->query->results->docs[] = $pmpdoc;
            $key = $this->cacheKey(array('guid' => $item->attributes->guid));
            $this->cacheSet($key, $pmpdoc);
          }
          // Add all the query convenience properties, etc.
        }
        //$this->cacheSet($cache_key, $this->query);
      }
      catch (Exception $e) {
        $this->errors['query'][] = $e->getMessage();
      }
    }
  }

  function getDocument($path = '') {
    $url = $this->base . $path;
    $cache_key = $this->cacheKey(array($url));
    $cache = $this->cacheGet($cache_key);
    if ($cache) {
      $this->from_cache = TRUE;
      return $cache;
    }
    else {
      $this->sdkInclude('CollectionDocJson');
      try {
        $doc = new \Pmp\Sdk\CollectionDocJson($url, $this->auth_client);
        $this->cacheSet($cache_key, $doc);
        return $doc;
      }
      catch (Exception $e) {
        $this->errors['getDocument'][] = $e->getMessage();
      }
    }
  }

  function cacheSet($key, $value, $lifetime = 300) {
    if ($this->caching) {
      // If/when this method is overridden
      // Save your cache row here
      // $key, $value, $this->cache_bin, $lifetime + time()
      // return TRUE/FALSE, if cache save was a success or not
      // default = dirt-simple file cache
      $expires = time() . $lifetime;
      $filename = 'cache/' . $key . '.txt';
      return file_put_contents($filename, $expires . "\n" . serialize($value));
    }
  }

  function cacheGet($key) {
    if ($this->caching) {
      // If/when this method is overridden
      // grab row from cache where key==$key
      // make sure it hasn't expired
      // return $cache->value;
      // default = dirt-simple file cache
      $filename = 'cache/' . $key . '.txt';
      if (file_exists($filename) && $file = file_get_contents($filename)) {
        $pieces = explode("\n", $file);
        $expires = array_shift($pieces);
        $value = unserialize(implode("\n", $pieces));
        $now = time();
        if ($expires && $value && $expires < $now) { 
          return $value;
        }
        if ($expires >= $now) {
          unlink($filename);
        }
      }
    }
  }

  function cacheKey($values) {
    // sample key generator (not great; not terrible)
    ksort($values);
    return 'pmpapi|' . md5(implode('|', $values));
  }

}

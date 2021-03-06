<?php

namespace AppBundle\Service;

use Predis;

/**
* Here you have to implement a CacheService with the operations above.
* It should contain a failover, which means that if you cannot retrieve
* data you have to hit the Database.
**/
class CacheService
{
    protected $client;

    /**
     * Initiate the connection instance
     *
     * @param string $host
     * @param string $port
     * @param string $prefix
     */
    public function __construct($host, $port, $prefix)
    {
	
	$this->client = new Predis\Client([
	    'host'   => $host,
	    'port'   => $port
	], [
	    'prefix' => $prefix.":"
	]);  
	
    }

    /**
     * Get the value stored in the key
     *
     * @param string $key
     * @return string
     */
    public function get($key)
    {
        return $this->client->get($key);
    }

    /**
     * Get values of all the keys stored in the redis
     *
     * @return array
     */
    public function getAll()
    {
        try {        
            $keys = $this->client->keys('*');
            $values = [];
            foreach ($keys as $key => $value) {
                $value = substr($value, strpos($value, ':')+1);
                $val = json_decode($this->client->get($value), true);
                $val['_id'] = ['$oid' => $value];
                $values[] = $val;
            }
            return $values;
        } catch(\Exception $e) {
            return false;
        }
    }

    /**
     * Set the value for the given key in to the redis
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function set($key, $value)
    {
        try {        
            return $this->client->set($key, $value);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete the given key from the redis
     *
     * @param string $key
     * @return void
     */
    public function del($key)
    {
        try {        
            return $this->client->delete($key);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Remove the entire redis database
     *
     * @return void
     */
    public function delAll()
    {
        try {        
            return $this->client->flushDb();
        } catch (\Exception $e) {
            return false;
        }
    }
}
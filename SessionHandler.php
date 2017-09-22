<?php
namespace Imarc\CraftDatabaseSessions;

use Craft\DbCache;

/**
 * Craft DB Sessions
 *
 * Sets up a database session handler for Craft CMS. This is useful if
 * redis or memcache is not part of your infrastructure and you are
 * using multiple application servers.
 *
 * This class uses craft's built-in database connection
 *
 * @copyright 2017 Imarc LLC
 * @author Jeff Turcotte [jt] <jeff@imarc.net>
 * @license MIT
 */
class SessionHandler implements \SessionHandlerInterface
{
    protected $cache;
    protected $config;


    /**
     * Register a new session handler
     *
     * @param array $config
     */
    static public function register($config = [])
    {
        $handler = new self($config);
        session_set_save_handler($handler, true);
        ini_set('session.save_handler', 'user');
    }


    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = $config;
    }


    /**
     * Initialize Craft's DbCache class
     *
     * @param array $config
     */
    public function init()
    {
        if ($this->cache !== null) {
            return;
        }

        $this->cache = new DbCache();
        $this->cache->cacheTableName = $this->config['table_name'] ?? 'dbsessions_sessions';
        $this->cache->autoCreateCacheTable = $this->config['table_autocreate'] ?? true;
        $this->cache->init();
    }


    /**
     * Open session handler
     *
     * @param string $save_path
     * @param string $session_name
     *
     * @return boolean
     */
    public function open($save_path, $session_name)
    {
        return true;
    }


    /**
     * Close session handler
     *
     * @return boolean
     */
    public function close()
    {
        return true;
    }


    /**
     * Read session handler
     *
     * @param string $session_id
     *
     * @return mixed
     */
    public function read($session_id)
    {
        $this->init();

        // very important that this
        // returns an empty string when
        // no session data is found.
        return $this->cache->get($session_id) ?: '';
    }


    /**
     * Write session handler
     *
     * @param string $session_id
     * @param mixed $data
     *
     * @return boolean
     */
    public function write($session_id, $data)
    {
        $this->init();

        $seconds = ini_get('session.gc_maxlifetime');

        return $this->cache->set($session_id, $data, $seconds);
    }


    /**
     * Destroy session handler
     *
     * @param string $session_id
     *
     * @return boolean
     */
    public function destroy($session_id)
    {
        $this->init();

        return $this->cache->delete($session_id);
    }


    /**
     * Garbage collection handler
     *
     * @param integer $maxlifetime
     *
     * @return boolean
     */
    public function gc($maxlifetime)
    {
        $this->init();

        // Yii's cache component handles GC
        return true;
    }


    /**
     * Ensure session gets written and closed on __destruct
     */
    public function __destruct()
    {
        session_write_close(true);
    }
}

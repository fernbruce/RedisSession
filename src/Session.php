<?php


namespace fernbruce\RedisSession;


/**
 * Class session
 */
class Session
{
    /**
     * @var \Redis
     */
    private $storage;

    /**
     * @var int
     */
    private $ttl;

    /**
     * @var string
     */
    private $prefix;

    /**
     * session constructor.
     * @param $storage
     * @param string $prefix
     * @param int $ttl
     */
    public function __construct($storage, $prefix = 'PHPSESSID:', $ttl = 3600)
    {
        $this->storage = $storage;
        $this->ttl = $ttl;
        $this->prefix = $prefix;
        session_set_save_handler(
            array(&$this, 'open'),
            array(&$this, 'close'),
            array(&$this, 'read'),
            array(&$this, 'write'),
            array(&$this, 'destroy'),
            array(&$this, 'gc')
        );
        session_start();
    }

    /**
     * @param $savePath
     * @param $sessionName
     * @return bool
     */
    function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * @return bool
     */
    function close()
    {
        return true;
    }

    /**
     * @param $id
     * @return string
     */
    function read($id)
    {
        $id = $this->prefix . $id;
        $sessData = $this->storage->get($id);
        $this->storage->expire($id, $this->ttl);
        return $sessData ?: '';
    }

    /**
     * @param $id
     * @param $sess_data
     * @return bool
     */
    function write($id, $sessionData)
    {
        $id = $this->prefix . $id;
        $this->storage->set($id, $sessionData);
        $this->storage->expire($id, $this->ttl);
        return true;
    }

    /**
     * @param $id
     * @return bool
     */
    function destroy($id)
    {
        $this->storage->del($this->prefix . $id);
        return true;
    }

    /**
     * @param $maxLifeTime
     * @return bool
     */
    function gc($maxLifeTime)
    {
        return true;
    }
}
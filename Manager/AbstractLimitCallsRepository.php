<?php
/**
 * @author Anton U <avtonomspb@gmail.com>
 */
namespace Avtonom\LimitNumberCallsBundle\Manager;

abstract class AbstractLimitCallsRepository implements LimitCallsRepositoryInterface
{
    /**
     * @var mixed
     */
    private $storage;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @param $storage
     * @param string $prefix
     */
    public function __construct($storage, $prefix = 'LNM')
    {
        $this->storage = $storage;
        $this->prefix = $prefix;
    }

    /**
     * @param string $collection
     * @param mixed $value
     *
     * @return string
     */
    public function getKey($collection, $value)
    {
        return vsprintf("%s::%s::%s", array($this->getPrefix(), $collection, md5(serialize($value))));
    }

    /**
     * @param string $collection
     * @param mixed $value
     *
     * @return string
     */
    public function getBlockKey($collection, $value)
    {
        return vsprintf("%s_block::%s::%s", array($this->getPrefix(), $collection, md5(serialize($value))));
    }

    /**
     * @param string $collection
     *
     * @return string
     */
    public function getKeyFind($collection)
    {
        return vsprintf("%s::%s::%s", array($this->getPrefix(), $collection, '*'));
    }

    /**
     * @return string
     */
    public function getKeyBlockFind()
    {
        return vsprintf("%s_block::%s", array($this->getPrefix(), '*'));
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return mixed
     */
    public function getStorage()
    {
        return $this->storage;
    }
}
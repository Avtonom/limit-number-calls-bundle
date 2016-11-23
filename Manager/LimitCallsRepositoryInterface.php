<?php

/**
 * @author Anton U <avtonomspb@gmail.com>
 */
namespace Avtonom\LimitNumberCallsBundle\Manager;

interface LimitCallsRepositoryInterface
{
    const TIME_FORMAT = 'Y-m-d\TH:i:s.uP';

    /**
     * @param string $collection
     * @param mixed $value
     * @param integer $timePeriod - microsecond ( 1 s = 1000000 microsecond)
     *
     * @return integer - count after add
     */
    public function add($collection, $value, $timePeriod);

    /**
     * @param string $collection
     * @param mixed $value
     * @param integer $timePeriod - microsecond
     *
     * @return integer - count after add
     */
    public function count($collection, $value, $timePeriod);

    /**
     * @param string $collection
     * @param integer $timePeriod - microsecond
     *
     * @return array
     */
    public function getAllByCollection($collection, $timePeriod = null);

    /**
     * @param string $collection
     * @param mixed $value
     * @param integer $blockingDuration - second
     *
     * @return string|bool - result add (OK | null | .. ), false- if error
     */
    public function block($collection, $value, $blockingDuration = null);

    /**
     * @param string $collection
     * @param mixed $value
     *
     * @return bool
     */
    public function hasBlock($collection, $value);

    /**
     * @return array
     */
    public function getAllBlock();

    /**
     * @param string $collection
     * @param mixed $value
     *
     * @return integer - The number of keys that were removed
     */
    public function clear($collection, $value);

    /**
     * @return integer - The number of keys that were removed
     */
    public function clearBlock();
}
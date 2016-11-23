<?php

/**
 * @author Anton U <avtonomspb@gmail.com>
 */
namespace Avtonom\LimitNumberCallsBundle\Manager;

class LimitCallsRepository extends AbstractLimitCallsRepository
{
    /**
     * @param string $collection
     * @param mixed $value
     * @param integer $timePeriod - microsecond ( 1 s = 1000000 microsecond)
     *
     * @return integer - count after add
     */
    public function add($collection, $value, $timePeriod)
    {
        if($this->hasBlock($collection, $value)){
            return false;
        }
        $nowMicrosecond = microtime(true);
        $periodMicrosecondFloat = $timePeriod / 1000000;
        $expireSecond = intval($periodMicrosecondFloat);
        $timeLimitMicrosecond = $nowMicrosecond - $timePeriod;

        $now = \DateTime::createFromFormat('U.u', $nowMicrosecond);

        $key = $this->getKey($collection, $value);
        $result = $this->getStorage()->pipeline()
            ->zadd($key, $nowMicrosecond, $this->dataToString(array('date' => $now->format(self::TIME_FORMAT), 'value' => $value)))
            ->expire($key, $expireSecond)
            ->zcount($key, $timeLimitMicrosecond, $nowMicrosecond)
            ->execute()
        ;
        return ($result && is_array($result)) ? $result[2] : false;
    }

    /**
     * @param string $collection
     * @param mixed $value
     * @param integer $timePeriod - microsecond
     *
     * @return integer - count after add
     */
    public function count($collection, $value, $timePeriod)
    {
        $nowMicrosecond = microtime(true);
        $periodMicrosecondFloat = $timePeriod / 1000000;
        $timeLimitMicrosecond = $nowMicrosecond - $periodMicrosecondFloat;

        $key = $this->getKey($collection, $value);
        $count = $this->getStorage()->zcount($key, $timeLimitMicrosecond, $nowMicrosecond);
        return $count;
    }

    /**
     * @param string $collection
     * @param integer $timePeriod - microsecond
     *
     * @return array
     */
    public function getAllByCollection($collection, $timePeriod = null)
    {
        $nowMicrosecond = microtime(true);
        if($timePeriod) {
            $periodMicrosecondFloat = $timePeriod / 1000000;
            $timeLimitMicrosecond = $nowMicrosecond - $periodMicrosecondFloat;
        }
        $data = array();
        $keyFind = $this->getKeyFind($collection);
        foreach ($this->getStorage()->keys($keyFind) as $key) {
            $total = $this->getStorage()->zcount($key, '-inf', $nowMicrosecond);
            $items = $this->getStorage()->zrevrangebyscore($key, 'inf', '-inf');
            $ttl = $this->getStorage()->ttl($key);
            $info = array('total' => $total, 'ttl' => $ttl, 'items' => $items);
            if(isset($timeLimitMicrosecond)){
                $info['current'] = $this->getStorage()->zcount($key, $timeLimitMicrosecond, $nowMicrosecond);
            }
            $data[$key] = $info;
        }
        return $data;
    }

    /**
     * @param string $collection
     * @param mixed $value
     * @param integer $blockingDuration - second
     *
     * @return string|bool - result add (OK | null | .. ), false- if error
     */
    public function block($collection, $value, $blockingDuration = null)
    {
        $now = new \DateTime();
        $key = $this->getBlockKey($collection, $value);

        if(is_null($blockingDuration)){
            return (string)$this->getStorage()->set($key, $this->dataToString(array('date' => $now->format(self::TIME_FORMAT), 'value' => $value)));
        } else {
            $result = $this->getStorage()->pipeline()
                ->set($key, $this->dataToString(array('date' => $now->format(self::TIME_FORMAT), 'value' => $value, 'blocking_duration' => $blockingDuration)))
                ->expire($key, $blockingDuration)
                ->execute()
            ;
            return ($result && is_array($result)) ? (string)$result[0] : false;
        }
    }

    /**
     * @param string $collection
     * @param mixed $value
     *
     * @return bool
     */
    public function hasBlock($collection, $value)
    {
        $key = $this->getBlockKey($collection, $value);
        $result = $this->getStorage()->get($key);
        return !is_null($result);
    }

    /**
     * @return array
     */
    public function getAllBlock()
    {
        $keyBlockFind = $this->getKeyBlockFind();
        $keys = $this->getStorage()->keys($keyBlockFind);
        $data = array('total' => count($keys));
        foreach ($keys as $key) {
            $value = $this->getStorage()->get($key);
            $ttl = $this->getStorage()->ttl($key);
            $info = array('ttl' => $ttl, 'value' => $value);
            $data['items'][$key] = $info;
        }
        return $data;
    }

    /**
     * @param string $collection
     * @param mixed $value
     *
     * @return integer - The number of keys that were removed
     */
    public function clear($collection, $value)
    {
        $key = $this->getKey($collection, $value);
        return $this->getStorage()->del($key);
    }

    /**
     * @return integer - The number of keys that were removed
     */
    public function clearBlock()
    {
        $key = $this->getKeyBlockFind();
        return $this->getStorage()->del($key);
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    protected function dataToString($data){
        $serialize = json_encode($data);
        return $serialize;
    }
}

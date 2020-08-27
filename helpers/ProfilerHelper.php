<?php


namespace app\helpers;


use Yii;
use yii\log\Logger;

class ProfilerHelper
{
    protected $mgu;
    protected $ts;
    protected $dbQueryCount = 0;
    protected $dbQueryTime = 0;

    /**
     * @return static
     */
    public static function start(): self
    {
        return new static();
    }

    /**
     * ProfilerHelper constructor.
     */
    public function __construct()
    {
        Yii::setLogger(new Logger([
            'traceLevel' => 3,
        ]));

        $this->ts = microtime(true);
        $this->dbQueryCount = $this->_getDbQueryCount();
        $this->dbQueryTime = $this->_getDbQueryTime();
        $this->mgu = memory_get_usage();
    }

    /**
     * Подсчитывает выделенное количество памяти
     * @param bool $pretty Приятный глазу формат
     * @return int
     */
    public function getMemoryAllocated(bool $pretty = false)
    {
        $memoryUsage = memory_get_usage() - $this->mgu;

        if ($pretty === false) {
            return $memoryUsage;
        }

        return number_format($memoryUsage, 0, '.', ' ') . ' bytes';
    }

    /**
     * @return float
     */
    public function getTimeSpent(): float
    {
        return microtime(true) - $this->ts;
    }

    /**
     * @return int
     */
    public function getDbQueryCount(): int
    {
        $currCount = $this->_getDbQueryCount();
        return $currCount - $this->dbQueryCount;
    }

    /**
     * @return int
     */
    public function getDbQueryTime(): float
    {
        $currTime = $this->_getDbQueryTime();
        return $currTime - $this->dbQueryTime;
    }

    /**
     * @return int
     */
    protected function _getDbQueryCount(): int
    {
        return Yii::getLogger()->getDbProfiling()[0];
    }

    /**
     * @return int
     */
    protected function _getDbQueryTime(): float
    {
        return Yii::getLogger()->getDbProfiling()[1];
    }
}
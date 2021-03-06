<?php
/**
 * Created by PhpStorm.
 * User: ross
 * Date: 3/11/18
 * Time: 7:40 PM
 */

namespace BoardingCardSort\Service;

/**
 * Class Singleton
 * @package BoardingCardSort\Service
 * @author Ross Ivantsiv <ross@proofpilot.com>
 */
abstract class Singleton
{
    private static $instances = [];
    protected function __construct()
    {
    }

    /**
     * @return mixed
     */
    public static function getInstance()
    {
        if (!isset(self::$instances[static::class])) {
            self::$instances[static::class] = new static();
        }

        return self::$instances[static::class];
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }
}

<?php

declare(strict_types=1);

namespace App\Utils;

trait StaticSingletonUtils
{
    protected static self $instance;

    protected function __construct()
    {
    }

    protected static function getInstance() {
        if (self::$instance) {
            return self::$instance;
        }
        return new static;
    }

    public static function __callStatic($method, $args)
    {
        $instance = static::getInstance();

        return $instance->$method(...$args);
    }
}
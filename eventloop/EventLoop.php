<?php

namespace Lipe\Php;

use Generator;
use Lipe\Php\Promise;

class EventLoop
{
    private static array $generatorList = [];
    private static array $fulfilledList = [];

    public static function add(Generator ...$promises): void
    {
        foreach ($promises as $promise) {
            array_push(self::$generatorList, new Promise($promise));
        }
    }

    public static function hasDone(int $promiseId): bool
    {
        return array_search($promiseId, self::$fulfilledList, true);
    }

    public static function run(Generator ...$generators): void
    {
        if(!empty($generators)) {
            self::add(...$generators);
        }

        while(!empty(self::$generatorList)) {
            $promise = array_shift(self::$generatorList);

            $promise->resolve();

            if($promise->isFulfilled()) {
                array_push(self::$fulfilledList, $promise->getId());
                continue;
            }
            
            array_push(self::$generatorList, $promise);
            unset($promise);
        }
    }
}
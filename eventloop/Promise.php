<?php

namespace Lipe\Php;

use Generator;
use Lipe\Php\States;

class Promise
{
    static private int $lastId = 0;

    private int $id;
    private Generator $gen;
    private States $state = States::Pending;

    function __construct(Generator $call)
    {
        $this->id = self::$lastId++;
        $this->gen = $call;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function resolve(): void
    {
        if($this->isPending()) {
            $this->gen->current();
            $this->state = States::Running;
            return;
        }
        
        $this->gen->next();
        return;
    }

    public function isPending(): bool
    {
        return $this->state === States::Pending;
    }

    public function isRunning(): bool
    {
        return $this->state === States::Running;
    }

    public function isFulfilled(): bool
    {
        return !$this->gen->valid(); 
    }

    public function __toString(): string
    {
        return (string)self::getId() + "{$this->state}";
    }
}
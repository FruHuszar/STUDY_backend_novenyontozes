<?php

declare(strict_types=1);

interface TransactionManagerInterface
{
    public function run(callable $work): mixed;
}

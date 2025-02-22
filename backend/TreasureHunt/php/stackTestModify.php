<?php declare(strict_types=1);
require('TreasureHuntModify.php');
use PHPUnit\Framework\TestCase;

final class StackTestModify extends TestCase
{
    final public function testPushAndPop(): void
    {
        print PHP_EOL;
        for ($i = 0; $i < 10; $i++)
        {
            $tresure = new TreasureHuntModify(random_int(10, 100));
            $tresure->execute();
        }
    }
}

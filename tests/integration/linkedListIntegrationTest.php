<?php

require_once "vendor/autoload.php";

use PHPUnit\Framework\TestCase;

final class linkedListIntegrationTest extends TestCase
{
    /** @test */
    public function linkedListIntegrationTest()
    {
        $linkedList = new app\Data_structures\linkedList();

        $this->assertTrue($linkedList->isEmpty());

        $linkedList->append(2);
        $linkedList->insert_at(1, 4);
        $linkedList->unshift(1);
        $linkedList->insert_Between(2, 4, 3);
        $linkedList->append(5);
        $linkedList->remove($linkedList->get($linkedList->indexof(5)));
        $linkedList->reverse();
        $linkedList->shift();
        $linkedList->append(0);
        $linkedList->removeAt(2);

        $this->assertEquals(320, $linkedList->print());
        $this->assertEquals($linkedList->length(), 3);

        $linkedList->clear();
        $this->assertEquals($linkedList->length(), 0);

    }
}
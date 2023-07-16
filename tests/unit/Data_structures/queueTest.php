<?php

include "app/Data_structures/queue.php";
require_once "vendor/autoload.php";

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;

final class queueTest extends TestCase
{
    protected $queue;

    public function setUp(): void
    {
        $this->queue = new app\Data_structures\queue();
    }

    public static function data_provider()
    {
        return array(array("a", "b", ["a", "b"]), array("c", "d", ["c", "d"]));
    }

    #[DataProvider("data_provider")]
    public function test_enqueueing_the_queue_and_printing_the_it_out($v1, $v2, $expected)
    {
        $this->queue->enqueue($v1);
        $this->queue->enqueue($v2);
        $this->assertEquals($expected, $this->queue->display());
    }

    public function test_dequeueing_the_queue()
    {
        $this->queue->enqueue("a");
        $this->queue->enqueue("b"); 
        $this->queue->enqueue("c"); 

        $this->assertEquals("a", $this->queue->dequeue());
    }

    #[Depends('test_dequeueing_the_queue')]
    public function test_getting_the_queue_front()
    {
        $this->queue->enqueue("a");
        $this->queue->enqueue("b"); 

        $this->assertEquals("a", $this->queue->front());
        return 5;
    }

    #[Depends('test_getting_the_queue_front')]
    public function test_getting_the_queue_rear($value)
    {
        $this->queue->enqueue("a");
        $this->queue->enqueue("b");
        $this->queue->enqueue("c");

        $this->assertEquals("c", $this->queue->rear());
        return $value + 5;
    }

    #[Depends('test_getting_the_queue_front')]
    #[Depends('test_getting_the_queue_rear')]
    public function test_clearing_the_queue($value1, $value2)
    {
        $this->queue->enqueue("a");
        $this->queue->enqueue("b");
        $this->queue->clear();

        $this->assertEquals([] , $this->queue->display());
        $this->assertEquals(15, $value1 + $value2);
    }
}


<?php

include "app/Data_structures/queue.php";
require_once "vendor/autoload.php";

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Depends;

final class queueTest extends TestCase
{
    public $queue;

    public function setUp(): void
    {
        $this->queue = new app\Data_structures\queue();
    }

    public function test_enqueueing_the_queue_and_printing_the_it_out()
    {
        $this->queue->enqueue("a");
        $this->queue->enqueue("b");
        $this->assertEquals(["a", "b"], $this->queue->display());
    }

    public function test_dequeueing_the_queue()
    {
        $this->queue->enqueue("a");
        $this->queue->enqueue("b"); 
        $this->queue->enqueue("c"); 

        $this->assertEquals("a", $this->queue->dequeue());
    }

    public function test_getting_the_queue_front()
    {
        $this->queue->enqueue("a");
        $this->queue->enqueue("b"); 

        $this->assertEquals("a", $this->queue->front());
        return 5;
    }

    public function test_getting_the_queue_rear()
    {
        $this->queue->enqueue("a");
        $this->queue->enqueue("b");
        $this->queue->enqueue("c");

        $this->assertEquals("c", $this->queue->rear());
    }

    public function test_clearing_the_queue()
    {
        $this->queue->enqueue("a");
        $this->queue->enqueue("b");
        $this->queue->clear();

        $this->assertEquals([] , $this->queue->display());
    }

    public function test_enqueueing_an_element_to_the_priority_queue()
    {
        $priority_queue = new app\Data_structures\priority_queue();

        $values = [[50, "anas"], [20, "roger"], [40, "sarah"], [45, "noah"]];
        $priority_queue->enqueueMultipleValues($values);
        $expected_priority_queue = [['priority' => 20, 'value' => "roger"], ['priority' => 45, 'value' => "noah"],
                                    ['priority' => 40, 'value' => 'sarah'], ['priority' => 50, 'value' => "anas"]];
        $this->assertEquals($expected_priority_queue, $priority_queue->data);
    }
    
    public function test_dequeueing_an_element_from_the_priority_queue()
    {
        $priority_queue = new app\Data_structures\priority_queue();
    
        $values = [[50, "anas"], [20, "roger"], [40, "sarah"], [45, "noah"]];

        $priority_queue->enqueueMultipleValues($values);

        $this->assertEquals(['priority' => '20', 'value' => 'roger'], $priority_queue->dequeue());

        $expected_priority_queue = [['priority' => 40, 'value' => "sarah"], ['priority' => 45, 'value' => "noah"],
                                    ['priority' => 50, 'value' => "anas"]];
        $this->assertEquals($expected_priority_queue, $priority_queue->data);
    }
    
    #[DataProviderExternal('queueTestsDataProviders', 'test_updating_the_priority_of_a_certain_element_data_provider')]
    public function test_updating_the_priority_of_a_certain_element($values, $index, $newPriority, $expected_priority_queue)
    {
        $priority_queue = new app\Data_structures\priority_queue();
    
        $priority_queue->enqueueMultipleValues($values);
    
        $priority_queue->updatePriority($index, $newPriority);
        $this->assertEquals($expected_priority_queue, $priority_queue->data);
    }
}

class queueTestsDataProviders
{
    public static function test_updating_the_priority_of_a_certain_element_data_provider()
    {
        return array(
            // Order of input 1:Queue values  2:Index of a value to update its priority  3:The new priority  4:The new expected priority queue

            // Target updating the priority to a less one without changing the structure 
            array([[20, "roger"], [45, "noah"], [40, 'sarah'], [50, "anas"]], 2, 30,
                  [['priority' => 20, 'value' => "roger"], ['priority' => 45, 'value' => "noah"],
                   ['priority' => 30, 'value' => 'sarah'], ['priority' => 50, 'value' => "anas"]]),
            
            // Target updating the priority to a less one with changing the structure 
            array([[20, "roger"], [45, "noah"], [40, 'sarah'], [50, "anas"], [55, "mike"]], 4, 10, 
                  [['priority' => 10, 'value' => "mike"], ['priority' => 20, 'value' => "roger"],
                  ['priority' => 40, 'value' => "sarah"], ['priority' => 50, 'value' => "anas"], ['priority' => 45, 'value' => "noah"]]),

            // Target updating the priority to a greater one without changing the structure 
            array([[20, "roger"], [45, "noah"], [40, 'sarah'], [50, "anas"]], 2, 60,
                  [['priority' => 20, 'value' => "roger"], ['priority' => 45, 'value' => "noah"],
                  ['priority' => 60, 'value' => 'sarah'], ['priority' => 50, 'value' => "anas"]]),
            
            // Target updating the priority to a greater one with changing the structure 
            array([[20, "roger"], [45, "noah"], [40, 'sarah'], [50, "anas"], [55, "mike"]], 0, 60, 
                  [['priority' => 40, 'value' => "sarah"], ['priority' => 45, 'value' => "noah"], ['priority' => 60, 'value' => "roger"],
                   ['priority' => 50, 'value' => "anas"], ['priority' => 55, 'value' => "mike"]])
        );
    }
}
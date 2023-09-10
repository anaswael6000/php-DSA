<?php

require_once "vendor/autoload.php";
require_once "app/Data_structures/heap.php";

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProviderExternal;

final class heapTest extends TestCase
{
    public $MaxHeap;
    public $MinHeap;
    public function setUp():void
    {
        $this->MaxHeap = new app\Data_structures\MaxHeap;
        $this->MinHeap = new app\Data_structures\MinHeap;
    }

    #[DataProviderExternal('heapTestDataProviders', 'test_heapifying_an_array_into_a_max_heap_data_provider')]
    public function test_heapifying_an_array_into_a_max_heap($values, $expected_heap)
    {
        $this->MaxHeap->BuildHeap($values);
        $this->assertEquals($expected_heap, $this->MaxHeap->data);
    }

    #[DataProviderExternal('heapTestDataProviders', 'test_heapifying_an_array_into_a_min_heap_data_provider')]
    public function test_heapifying_an_array_into_a_min_heap($values, $expected_heap)
    {
        $this->MinHeap->BuildHeap($values);
        $this->assertEquals($expected_heap, $this->MinHeap->data);
    }

    #[DataProviderExternal('heapTestDataProviders', 'test_peeking_the_heap_data_provider')]
    public function test_peeking_the_heap($values, $peek)
    {
        $this->MinHeap->BuildHeap($values);
        $this->assertEquals($this->MinHeap->get_peek(), $this->MinHeap->data[0]);
    }

    #[DataProviderExternal('heapTestDataProviders', 'test_removing_a_value_from_the_heap_data_provider')]
    public function test_removing_a_value_from_the_heap($values, $value, $expected_heap)
    {
        $this->MaxHeap->BuildHeap($values);
        $this->MaxHeap->remove($value);
        $this->assertEquals($expected_heap ,$this->MaxHeap->data);
    }

    #[DataProviderExternal('heapTestDataProviders', 'test_heap_sorting_data_provider')]
    public function test_heap_sorting($values, $expected_MaxHeap, $expected_MinHeap)
    {
        $this->MaxHeap->BuildHeap($values);
        $this->MinHeap->BuildHeap($values);
        $this->assertEquals($expected_MaxHeap ,$this->MaxHeap->heapSort());
        $this->assertEquals($expected_MinHeap ,$this->MinHeap->heapSort());
    }

}

class heapTestDataProviders
{
    public static function test_heapifying_an_array_into_a_max_heap_data_provider()
    {
        return [
            // Order of input:  1:array of values  2:expected_heap
            [[1], [1]],

            [[1, 2, 3, 4, 5, 6], [6, 4, 5, 1, 3, 2]],

            [[5, 7, 1, 4, 8, 6], [8, 7, 6, 4, 5, 1]],

            [[5, 4, 3, 2, 1], [5, 4, 3, 2, 1]],

        ];
    }

    public static function test_heapifying_an_array_into_a_min_heap_data_provider()
    {
        return [
            // Order of input:  1:array of values  2:expected_heap
            [[1], [1]],

            [[5, 7, 1, 4, 8, 6], [1, 4, 5, 7, 8, 6]],
            
            [[1, 2, 3, 4, 5, 6], [1, 2, 3, 4, 5, 6]],
        ];
    }

    public static function test_peeking_the_heap_data_provider()
    {
        return [
            // Order of input:  1:array of values  2:peek
            [[1], 1],

            [[5, 7, 1, 4, 8, 6], 1],
            
            [[1, 2, 3, 4, 5, 6], 1],
        ];
    }

    public static function test_removing_a_value_from_the_heap_data_provider()
    {
        return [
            // Order of input:  1:array of values  2:value to be removed  3:expected_heap

            // Does not target anything just adds one test to our tests
            [[1], 1, []],

            // Target a peek value removal
            [[1, 2, 3, 4, 5, 6], 6, [5, 4, 2, 1, 3]],

            // Target a two children value removal
            [[1, 2, 3, 4, 5, 6], 4, [6, 3, 5, 1, 2]],

            // Target a one child value removal
            [[1, 2, 3, 4, 5, 6], 5, [6, 4, 2, 1, 3]],

            // Target a leaf value removal
            [[1, 2, 3, 4, 5, 6], 2, [6, 4, 5, 1, 3]],

        ];        
    }

    public static function test_heap_sorting_data_provider()
    {
        return [
            // Order of input   1:Unsorted values    2: expected max heap     3: expected min heap
            [[5, 7, 1, 4, 8, 6], [8, 7, 6, 5, 4, 1], [1, 4, 5, 6, 7, 8]],
            [[3, 9, 6, 4, 12, 18], [18, 12, 9, 6, 4, 3], [3, 4, 6, 9, 12, 18]]
        ];
    }
}

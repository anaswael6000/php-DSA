<?php

namespace app\Data_structures;

class MaxHeap
{
    public $data = [];

    public function BuildHeap(array $values)
    {
        foreach($values as $value)
        {
            $this->heapPush($value);
        }
    }

    public function heapPush($value)
    {
        // Add the value to the end of the heap
        $this->data[] = $value;

        $this->heapify();
    }

    public function heapify()
    {
        // Start at the last element in the heap and then swap every child with its parent if there is a violation of the heap property
        for ($i = count($this->data) - 1; $i > 0; $i--)
        {
            $parent_index = intdiv($i - 1, 2);
            if ($this->data[$i] > $this->data[$parent_index])
            {
                // Swap
                $temp = $this->data[$i];
                $this->data[$i] = $this->data[$parent_index];
                $this->data[$parent_index] = $temp;
            }
        }
    }

    public function get_peek()
    {
        return $this->data[0];
    }

    public function remove($value)
    {
        $index = array_search($value, $this->data);
        // Swap the value with the farthest right value which in our case the last element in the array
        $this->data[$index] = $this->data[count($this->data) - 1];
        array_pop($this->data);

        // Restore the heap property
        $this->heapify();
    }

    public function heapSort()
    {
        $sorted_array = [];
        while (count($this->data) !== 0)
        {
            $sorted_array[] = $this->data[0];
            array_shift($this->data);
            $this->heapify();
        }
        return $sorted_array;
    }
}

class MinHeap
{
    public $data = [];

    public function BuildHeap(array $values)
    {
        foreach($values as $value)
        {
            $this->heapPush($value);
        }
    }

    public function heapPush($value)
    {
        $this->data[] = $value;

        $this->heapify();
    }

    public function get_peek()
    {
        return $this->data[0];
    }

    public function heapify()
    {
        for ($i = count($this->data) - 1; $i > 0; $i--)
        {
            $parent_index = intdiv($i - 1, 2);
            if ($this->data[$i] < $this->data[$parent_index])
            {
                // Swap
                $temp = $this->data[$i];
                $this->data[$i] = $this->data[$parent_index];
                $this->data[$parent_index] = $temp;
            }
        }
    }

    public function heapSort()
    {
        $sorted_array = [];
        while (count($this->data) !== 0)
        {
            $sorted_array[] = array_shift($this->data);
            $this->heapify();
        }
        return $sorted_array;
    }
}

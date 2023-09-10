<?php

namespace app\Data_structures;

include "app/Data_structures/linkedList.php";

class queue
{
    private $front = NULL;
    private $rear = NULL;
    private $size = 0;

    public function enqueue($value)
    {
        $new_node = new Node($value);
        if ($this->size === 0)
        {
            $this->rear = $new_node;
            $this->front = $new_node;
        }
        else
        {
            $this->rear->next = $new_node;
            $this->rear = $new_node;
        }
        $this->size++;
    }

    public function dequeue()
    {
        if ($this->size === 0)
        {
            throw new Exceptions\itIsEmpty();
        }
        $dequeued_element = $this->front;
        $this->front = $this->front->next;
        return $dequeued_element->data;
    }

    public function front()
    {
        return $this->front->data;
    }

    public function rear()
    {
        return $this->rear->data;
    }

    public function display()
    {
        $current = $this->front;
        $result = [];
        while($current !== null)
        {
            $result[] = $current->data;
            $current = $current->next;
        }
        return $result;
    }

    public function clear()
    {
        $current = $this->front;
        $this->front = null; // no longer needed
        while($current !== null)
        {
            $next = $current->next;
            unset($current);
            $current = $next;
        }
        $this->size = 0;
    }

}

class priority_queue
{
    public $data = [];

    public function enqueueMultipleValues(array $values)
    {
        foreach($values as [$priority, $value])
        {
            $this->enqueue([$priority, $value]);
        }
    }
    
    public function enqueue($array)
    {
        $this->data[] = ['priority' => $array[0], 'value' => $array[1]]; 
        $this->bubbleUp(count($this->data) - 1);
    }

    public function bubbleUp($index)
    {
        $parent_index = intdiv($index - 1, 2);

        while($this->data[$index]['priority'] < $this->data[$parent_index]['priority'])
        {
            $this->swap($index, $parent_index);
            $index = $parent_index;
            $parent_index = intdiv($index - 1, 2);
        }
    }
    
    public function dequeue()
    {
        $dequeued_element = $this->data[0];
        $this->data[0] = $this->data[count($this->data) - 1];
        array_pop($this->data);
        $this->sinkDown(0);
        return $dequeued_element;
    }
    public function sinkDown($index)
    {
        $size = count($this->data);
        $left_child = 2 * $index + 1;
        $right_child = 2 * $index + 2;
        $smallest = $index;

        // Figure out the smallest element of the parent and his children
        $smallest = ($left_child < $size && $this->data[$left_child]['priority'] < $this->data[$smallest]['priority']) ? $left_child : $smallest;
        $smallest = ($right_child < $size && $this->data[$right_child]['priority'] < $this->data[$smallest]['priority']) ? $right_child : $smallest;
        
        // The parent is the smallest element then we are all good move on to the next parent
        if ($smallest === $index) return;

        // if the parent is not the smallest element then swap the parent with the smallest element
        $this->swap($smallest, $index);
        $this->sinkDown($smallest);
    }

    public function swap($index, $parent_index)
    {
        $temp = $this->data[$index];
        $this->data[$index] = $this->data[$parent_index];
        $this->data[$parent_index] = $temp;
    }
 
    public function updatePriority($index, $newPriority)
    {
        if ($newPriority > $this->data[$index]['priority'])       
        {
            $this->data[$index]['priority'] = $newPriority;
            $this->sinkDown($index);
        }
        else
        {
            $this->data[$index]['priority'] = $newPriority;
            $this->bubbleUp($index);
        }
    }
}
<?php

namespace app\Data_structures;

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
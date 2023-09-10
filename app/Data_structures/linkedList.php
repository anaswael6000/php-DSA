<?php

namespace app\Data_structures;

class Node
{
    public $data;
    public $next = null;

    public function __construct($data)
    {
        $this->data = $data;
    }
}

class linkedList
{
    private $head = NULL;
    private $size = 0;

    public function isEmpty()
    {
        return ($this->size == 0);
    }

    public function unshift($value)
    {
        $node = new Node($value);
        $node->next = $this->head;
        $this->head = $node;
        $this->size++;
    }

    public function append($data)
    {
        $new_node = new Node($data);

        if ($this->head === NULL)
        {
            $this->head = $new_node;
        }
        else
        {
            $current = $this->head;

            while($current->next !== NULL)
            {
                $current = $current->next;
            }
            $current->next = $new_node;
        }
        $this->size++;
    }

    public function insert_Between($first, $second, $value)
    {
        $current = $this->head;
        $check = FALSE;
        while($current !== NULL)
        {
            if($current->data !== $first)
            {
                $current = $current->next;
                continue;
            }
            elseif($current->next->data === $second)
            {
                $new_node = new Node($value);
                $new_node->next = $current->next;
                $current->next = $new_node;
                $check = TRUE;
                $this->size++;
                break;
            }
            else
            {
                exit("The second value you provided does not exist or the two values are not consecutive" . "<br>\n");
            }
        }
        if ($check === FALSE)
        {
            exit("The first value you provided does not exist" . "<br>\n");
        }
    }

    public function insert_at($index, $value)
    {
        if ($index === 0 )
        {
            $this->unshift($value);
            exit();
        }
        $new_node = new Node($value);
        $current = $this->head;
        $node_index = $check =  0;
        while ($current !== NULL)
        {
            if ($node_index === $index - 1)
            {
                $new_node->next = $current->next;
                $current->next = $new_node;
                $check = 1;
                $this->size++;
                break;
            }
            else
            {
                $current = $current->next;
                $node_index++;
            }
        }
        if ($check !== 1)
        {
            echo "The index provided is invalid<br>\n";
        }
    }

    public function remove($value)
    {
        $current = $this->head;

        if ($current === NULl)
        {
            throw new Exceptions\itIsEmptyException();
        }
        elseif ($current->data === $value)
        {
            $this->shift();
        }
        else
        {
            while($current->next !== NUll)
            {
                if ($current->next->data === $value)
                {
                    $current->next = $current->next->next;
                    $this->size--;
                    $check = TRUE;
                    break;
                }
                else
                {
                    $current = $current->next;
                }
            }
            if($check === FALSE|NULL)
            {
                exit("The value you provided is not included in the linked list" . "<br>\n");    
            }
        }    
    }

    public function shift()
    {
        $this->head = $this->head->next;
        $this->size--;
    }

    public function removeAt($index)
    {
        if ($this->length() <= $index)
        {
            exit("Index is invalid<br>\n");
        }
        if ($index === 0 )
        {
            $this->shift($value);
            exit();
        }
        $current = $this->head;
        $node_index = 0;
        while ($current !== NULL)
        {
            if ($node_index === $index - 1)
            {
                $current->next = $current->next->next;
                $this->size--;
                break;
            }
            else
            {
                $current = $current->next;
                $node_index++;
            }
        }
    }

    public function indexof($value)
    {
        $current = $this->head;
        $node_index = 0;

        while($current !== NULL)
        {
            if ($current->data === $value)
            {
                return $node_index;
            }
            else
            {
                $current = $current->next;
                $node_index++;
            }
        }
        echo "This value does not exist<br>\n";
        
    }

    public function get(int $index)
    {
        if ($this->length() <= $index)
        {
            exit("Index is invalid<br>\n");
        }
        $current = $this->head;
        $node_index = 0;
        while ($current !== NULL)
        {
            if ($node_index === $index)
            {
                return $current->data;
            }
            else
            {
                $current = $current->next;
                $node_index++;
            }
        }
    }
    
    public function toArray()
    {
        $current = $this->head;
        $array = array();
        while ($current !== NULl)
        {
            $array[] = $current->data;
            $current = $current->next;
        }
        return $array;
    }

    public function length()
    {
        $current = $this->head;
        $length = 0;
        while ($current !== NULL) 
        {
            $length++;
            $current = $current->next;
        }
        return $length;
    }

    public function reverse()
    {
            $current = $this->head;
            $previous = NULL;
            while($current)
            {
                $next = $current->next;
                $current->next = $previous;
                $previous = $current;
                $current = $next;
            }
            $this->head = $previous;
    }

    public function print()
    {
        $data = [];
        $current = $this->head;
        while($current !== NULL)
        {
            $data[] = $current->data;
            $current = $current->next;
        }
        return implode($data);
    }

    public function clear(): void
    {
        $current = $this->head;
        $this->head =  NULL; // No longer needed
        while($current !== NULL)
        {
            $next = $current->next;
            unset($current);
            $current = $next;
        }
    }

}

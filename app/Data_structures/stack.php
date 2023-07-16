<?php

namespace app\Data_structures;
require_once "vendor/autoload.php";

class stack 
{
    private $stack;
    private $size = 0;

    public function __construct()
    {
        $this->stack = array();
    }

    public function push($value)
    {
        $this->stack[] = $value;
    }

    public function pop()
    {
        if (count($this->stack) === 0)
        {
            throw new Exceptions\itIsEmptyException();
        }
        return array_pop($this->stack);
    }

    public function peek()
    {
        return end($this->stack);
    }

    public function isEmpty()
    {
        return (count($this->stack) === 0);
    }

    public function length()
    {
        return count($this->stack);
    }

    public function print()
    {
        return array_reverse($this->stack);
    }

    public function is_balanced($string)
    {
        if (strlen($string) === 0) { throw new Exceptions\itIsEmptyException(); }
        
        $braces = ["(" => ")", "[" => "]", "{" => "}"];
        
        for ($i = 0; $i < strlen($string); $i++)
        {
            if (isset($braces[$string[$i]]))
            {
                $this->stack[] = $string[$i];
            }
            elseif(in_array($string[$i], $braces))
            {
                if (count($this->stack) == 0 || $string[$i] !== $braces[array_pop($this->stack)])
                {
                    return false;
                }
            }
            else
            {
                // In case it is not either an opening and closing brackets so it is a normal letter
                unset($this->queue[$i]);
            }
        }
        return (count($this->stack) === 0);
    }

}
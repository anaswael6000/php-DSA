<?php

namespace app\Data_structures;

class hashTableNode 
{
    public $key;
    public $value;
    public $next;

    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
        $this->next = null;
    }
}

class hashTable
{
   private $hashTable = [];
   private $size = 3;

   public function hash_function($key)
   {
        $sum = 0;
        $n = strlen($key);

        for ($i = 0; $i < $n; $i++)
        {
            $sum += ord($key[$i]);
        }
        return $sum % $this->size;
   }

   public function insert($key, $value)
   {
        $index = $this->hash_function($key);
        $node = new hashTableNode($key, $value);
        
        if (!isset($this->hashTable[$index]))
        {
            $this->hashTable[$index] = $node;
            return;
        }

        $current = $this->hashTable[$index];
        while($current->next !== null)
        {
            $current = $current->next;
        }
        $current->next = $node;
   }

   public function display()
   {
        $values = [];
        for ($i = 0; $i < $this->size; $i++)
        {
            if (isset($this->hashTable[$i])) 
            {
                $current = $this->hashTable[$i];
                while($current !== null)
                {
                    $values[] = $current->value;
                    $current = $current->next;
                }
            }
        }
        return $values;
   }

   public function get($key)
   {
        $index = $this->hash_function($key);

        if (!isset($this->hashTable[$index]))
        {
            throw new Exceptions\doesNotExistException();
        }
        $current = $this->hashTable[$index];
        while($current !== null)
        {
            if ($current->key === $key)
            {
                return $current->value;
            }
            $current = $current->next;
        }
        throw new Exceptions\doesNotExistException();
   }

   public function remove($key)
   {
        $index = $this->hash_function("sha256", $key);
        if (isset($this->hashTable[$index]))
        {
            $current = $this->hashTable[$index];
            if ($current->key === $key)
            {
                $this->hashTable[$index] = $current->next;
                return;
            }
            while($current->next !== null)
            {
                if ($current->next->key === $key)
                {
                    $current->next = $current->next->next;
                    return;
                }
                $current = $current->next;
            }
        }
        throw new Exceptions\doesNotExistException("The key you provided does not exist");
   }

   public function contains($key)
   {
        $index = $this->hash_function($key);
        if (!isset($this->hashTable[$index]))
        {
            return false;
        }
        $current = $this->hashTable[$index];
        while($current !== null)
        {
            if ($current->key === $key)
            {
                return true;
            }
            $current = $current->next;
        }
        return false;
   }

   public function keys()
   {
        $keys = [];
        for ($i = 0; $i < $this->size; $i++)
        {
            if(!isset($this->hashTable[$i]))
            {
                continue;
            }
            $current = $this->hashTable[$i];
            while($current !== null)
            {
                $keys[] = $current->key;
                $current = $current->next;
            }
        }
        return $keys;
   }

   public function values()
   {
        $vales = [];
        for ($i = 0; $i < $this->size; $i++)
        {
            if(!isset($this->hashTable[$i]))
            {
                continue;
            }
            $current = $this->hashTable[$i];
            while($current !== null)
            {
                $values[] = $current->value;
                $current = $current->next;
            }
        }
        return $values;
   }

   public function get_size()
   {
        return $this->size;
   }

   public function clear()
   {
        for ($i = 0; $i < $this->size; $i++)
        {
           unset($this->hashTable[$i]);
        }
   }
}
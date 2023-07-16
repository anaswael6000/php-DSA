<?php declare(strict_types = 1);

namespace app\Data_structures;

class TreeNode
{
    public $value;
    public $left = null;
    public $right = null;

    public function __construct($value)
    {
        $this->value = $value;
    }
}

class BST
{
    public $root;

    public function insert($value, $class = TreeNode::class)
    {
        $node = new $class($value);

        if ($this->root == null)
        {
            $this->root = $node;
            return;
        }

        $current = $this->root;

        while ($current !== null)
        {
            $stack[] = $current;
            if ($value < $current->value)
            {
                $current = $current->left;
            }
            elseif($value > $current->value)
            {
                $current = $current->right;
            }
            else
            {
                throw new Exceptions\alreadyExistsException();
            }
        }

        $parent = array_pop($stack);

        $parent->left = ($node->value < $parent->value) ? $node : $parent->left;
        $parent->right = ($node->value > $parent->value) ? $node : $parent->right;
    }

    public function search($node ,$value)
    {
        if ($node == null)
        {
            return false;
        }
        if ($value < $node->value)
        {
            return $this->search($node->left, $value);
        }
        elseif ($value > $node->value)
        {
            return $this->search($node->right, $value);
        }
        
        return true;
        
    }

    public function print()
    {
        //
    }

    public function insert_multiple_values($array, $class = TreeNode::class)
    {
        $n = count($array);
        for ($i = 0; $i < $n; $i++)
        {
            $this->insert($array[$i], $class);
        }
    }

    public function visit($node)
    {
        echo  $node->value . " " ;
    }

    public function in_order_traversal($node)
    {
        // iterative approach

        // $stack = [];
        // $current = $node;

        // while ($current !== null || !empty($stack))
        // {
        //     while($current !== null)
        //     {
        //         $stack[] = $current;
        //         $current = $current->left;
        //     }

        //     $current = array_pop($stack);
        //     $this->visit($current);
        //     $current = $current->right;
        // }

        // Recursive approach
        if ($node == null) return;
        $this->in_order_traversal($node->left);
        $this->visit($node);
        $this->in_order_traversal($node->right);

    }

    public function reverse_in_order_traversal($node)
    {
        // This is the recursive approach
        // if ($node === null) return;
        // $this->reverse_in_order_traversal($node->right);
        // $this->visit($node);
        // $this->reverse_in_order_traversal($node->left);

        $stack = [];
        $current = $node;

        while($current !== null || !empty($stack))
        {
            while ($current !== null)
            {
                $stack[] = $current;
                $current = $current->right;
            }

            $current = array_pop($stack);
            $this->visit($current);
            $current = $current->left;
        }
    }

    public function pre_order_traversal($node)
    {
        // $stack = [];
        // $current = $node;

        // while($current !== null || !empty($stack))
        // {
        //     while($current !== null)
        //     {
        //         $stack[] = $current;
        //         $this->visit($current);
        //         $current = $current->left;
        //     }
        //     $current = array_pop($stack);
        //     $current = $current->right;
        // }

        if ($node == null) return;
        $this->visit($node);
        $this->pre_order_traversal($node->left);
        $this->pre_order_traversal($node->right);
    }

    public function post_order_traversal($node)
    {
        //     $stack = [$node];
        //     $visited = [];

        //     while (!empty($stack))
            // {
        //         $current = end($stack);

        //         if(isset($current->left) && !in_array($current->left, $visited))
        //         {
        //             $current = $current->left;
        //             $stack[] = $current;
        //             continue;
        //         }
        //         if(isset($current->right) && !in_array($current->right, $visited))
        //         {
        //             $current = $current->right;
        //             $stack[] = $current;
        //             continue;
        //         }

        //         $current = array_pop($stack);
        //         $this->visit($current);
        //         $visited[] = $current;
            // }

        if ($node == null) return;
        $this->post_order_traversal($node->left);
        $this->post_order_traversal($node->right);
        $this->visit($node);
    }

    public function breadth_first_traversal($node)
    {
      if ($node === null)
      {
        return false;
      }
      
      $queue = [$node];

      while(!empty($queue))
      {
          $current = array_shift($queue);
          $this->visit($current);

          if (isset($current->left))
          {
              $queue[] = $current->left;
          }

          if (isset($current->right))
          {
              $queue[] = $current->right;    
          }
	  }

	}

    public function delete($node)
    {
        if ($node === null) return;
        $this->delete($node->left);
        $this->delete($node->right);
        $node->left = null;
        $node->right = null;
        $node->value = null;
    }

    public function get_node($value)
    {
        $current = $this->root;
        while ($current !== null)
        {
            if ($value < $current->value)
            {
                $current = $current->left;    
            }
            elseif ($value > $current->value)
            {
                $current = $current->right;    
            }
            else
            {
                return $current;
            }
        }
        throw new Exceptions\doesNotExistException();
    }

    public function get_parent($node)
    {
        if ($node === $this->root)
        {
            return null;
        }

        $current = $this->root;
        while($current !== null && $current !== $node)
        {
            $parent = $current;
            if ($node->value < $current->value)
            {
                $current = $current->left;
            }
            elseif ($node->value > $current->value)
            {
                $current = $current->right;
            }
        }
        return $parent;
    }

    public function remove($value)
    {
        try 
        {
            $node = $this->get_node($value);
        }
        catch (Exceptions\doesNotExistException)
        {
            return false;
        }
        
        if ($node->left !== null && $node->right !== null)
        {
            // Node has two children

            /* We find the node that is greater than our node but it is also the smallest one in all the nodes that are greater
               than our node as this is the only node that will keep our tree balanced */
            $successor = $node->right;
            while ($successor->left !== null)
            {
                $successor = $successor->left;
            }
            $value = $successor->value;
            $this->remove($successor->value);
            $node->value = $value;
        }
        elseif ($node->left === null && $node->right === null)
        {
            // Node has No children
            if ($node === $this->root)
            {
                $this->root = null;
            }
            else
            {
                $current = $this->root;
                while($current !== null && $current !== $node)
                {
                    $parent = $current;
                    if ($node->value < $current->value)
                    {
                        $current = $current->left;
                        continue;
                    }
                    if ($node->value > $current->value)
                    {
                        $current = $current->right;
                        continue;
                    }
                }
                if ($parent->left === $node)
                {
                    $parent->left = null;
                }
                elseif($parent->right == $node)
                {
                    $parent->right = null;
                }
                unset($node);
            }
        }
        else
        {
            // Node has one child
            $current = $this->root;
            while($current !== null && $current !== $node)
            {
                $parent = $current;
                if ($node->value < $current->value)
                {
                    $current = $current->left;
                    continue;
                }
                if ($node->value > $current->value)
                {
                    $current = $current->right;
                    continue;
                }
            }

            $child = $node->left ?? $node->right;

            if (!isset($parent))
            {
                // If the parent is not set then the the node is the root node so we just set the new root the the child
                $this->root = $child;
            }

            elseif ($parent->left === $node)
            {
                $parent->left = $child;
            }
            else 
            {
                $parent->right = $node;
            }
        }
        return true;
    }

    public function get_height($node)
    {
        // 0 won't affect the addition operation and makes the function function properly
        if ($node == null) return 0;
        
        $left_subtree_height = $this->get_height($node->left);
        $right_subtree_height = $this->get_height($node->right);

        return max($left_subtree_height, $right_subtree_height) + 1;
    }

    public function get_depth($variable)
    {
        $node = ($variable instanceof AVL_treeNode) ? $variable : $this->get_node($variable);

        $depth = 0;
        $current = $this->root;

        while ($current !== null)
        {
            if ($node->value < $current->value)
            {
                $depth++;
                $current = $current->left;
            }
            elseif ($node->value > $current->value)
            {
                $depth++;
                $current = $current->right;
            }
            else
            {
                return $depth;
            }
        }
        // Value is not in the tree
        return false;
    }
}

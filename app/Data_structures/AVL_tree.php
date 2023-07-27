<?php

namespace app\Data_structures;

require_once "app/Data_structures/BST.php";

class AVL_treeNode extends TreeNode
{
  // 
}

class AVL_tree extends BST
{

  public function right_rotate($variable)
  {
    // Check whether the provided variable is a node's value or the node itself
    $node = ($variable instanceof TreeNode) ? $variable : $this->get_node($variable);

    // Get the parent node of the node that will be rotated to set its new pointers after the rotation
    $parent = $this->get_parent($node);
    
    // Rotate
    $newRoot = $node->left;
    $node->left = $newRoot->right;
    $newRoot->right = $node;

    // Check wether the rotated node was the root node
    if (!$parent)
    {
      $this->root = $newRoot;    // Update the root node
    }
    else
    {
      // If the node is not the root then set the parent of the node that was rotated to the new child 
      $parent->left = ($parent->left === $node) ? $newRoot : $parent->left;
      $parent->right = ($parent->right === $node) ? $newRoot : $parent->right;
    }
  }
  
  public function left_right_rotate($variable)
  {
    $node = ($variable instanceof AVL_treeNode) ? $variable : $this->get_node($variable);
    $left_child = $node->left;
    $this->left_rotate($left_child);
    $this->right_rotate($node);
  }

  public function left_rotate($variable)
  {
    // Check whether the provided variable was the  node's value or the node itself
    $node = ($variable instanceof TreeNode) ? $variable : $this->get_node($variable);

    // Get the parent node of the node that will be rotated to set its new pointers after the rotation
    $parent = $this->get_parent($node);

    // Rotate
    $newRoot = $node->right;
    $node->right = $newRoot->left;
    $newRoot->left = $node;

    // Check wether the rotated node was the root node
    if (!$parent)
    {
      $this->root = $newRoot;   // Update the root node
    }
    else
    {
      // If the node is not the root then set the parent of the node that was rotated to the new child 
      if ($parent->left === $node)
      {
        $parent->left = $newRoot;
      }
      else
      {
        $parent->right = $newRoot;
      }
    }

  }
  
  public function right_left_rotate($variable)
  {
    $node = ($variable instanceof AVL_treeNode) ? $variable : $this->get_node($variable);
    $right_child = $node->right;
    $this->right_rotate($right_child);
    $this->left_rotate($variable);
  }

  public function get_balance_factor($variable)
  {
    if ($variable == null) return;
    $node = ($variable instanceof AVL_treeNode) ? $variable : $this->get_node($variable);
    return $this->get_height($node->left) - $this->get_height($node->right);
  }

  public function insertValues(array $input)
  {
      foreach($input as $value)
      {
          $this->insertNode($value);
      }
  }

  public function remove($value)
  {
      try
      {
          $node = $this->get_node($value);
      }
      catch( Exceptions\doesNotExistException )
      {
          return false;
      }
      
      $parent = $this->get_parent($node);

      if ($node->left !== null && $node->right !== null)
      {
          // Node has two children

          $successor = $node->right;

          while($successor->left !== null) 
          {
              $successor = $successor->left;
          }

          $value = $successor->value;
          $this->remove($successor->value);
          $node->value = $value;
      }

      elseif($node->left !== null or $node->right !== null)
      {
          // Node has one child

          $child = $node->left ?? $node->right;

          switch ($parent)
          {
            // The node is the root node
            case null:
              $this->root = $child;
              return;

            default:
              $parent->left = ($parent->left === $node) ? $child : $parent->left; 
              $parent->right = ($parent->right === $node) ? $child : $parent->right; 
          }

          $queue = [$child];

          // Get all the nodes that were affected by the deletion

          while ($parent !== null)
          {
              $queue[] = $parent;
              // Update the child to get the grand parent and then the parent of the grand parent and so on
              $parent = $this->get_parent($parent);
          }
    
          while(!empty($queue))
          {
              $this->balance(array_shift($queue));
          }
      }
      else
      {
        // Node has no children
        switch ($parent)
        {
          // The node is the root node
          case null:
            $this->root = null;
            return;

          default:
            $parent->left = ($parent->left === $node) ? null : $parent->left; 
            $parent->right = ($parent->right === $node) ? null : $parent->right; 
        }

        $queue = [];

        // Get all the nodes that were affected by the deletion

        while ($parent !== null)
        {
            $queue[] = $parent;
            // Update the child to get the grand parent and then the parent of the grand parent and so on
            $parent = $this->get_parent($parent);
        }
  
        while(!empty($queue))
        {
            $this->balance(array_shift($queue));
        }
      }

  }

  public function insertNode($value)
  {
      $node = new AVL_treeNode($value); 

      if ($this->root === null)
      {
          $this->root = $node;
          return;
      }

      $stack = [];
      $current = $this->root;

      while($current !== null)
      {
          $stack[] = $current;

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
              throw new Exceptions\alreadyExistsException;
          }
      }

      $parent = array_pop($stack);

      $parent->right = ($node->value > $parent->value) ? $node : $parent->right;
      $parent->left = ($node->value < $parent->value) ? $node : $parent->left;

      while(!empty($stack))
      {
          $this->balance(array_pop($stack));
      }
  }

  public function balance($node)
  {
      $BF = $this->get_balance_factor($node);

      if (in_array($BF, [0, 1, -1])) return;

      // Else: 
      if ($BF > 1)
      {
          if (($this->get_balance_factor($node->left)) == -1)
          {
              $this->left_rotate($node->left);
          }
          $this->right_rotate($node);
      }
      else
      {
        // Then the balance factor is < -1
        if (($this->get_balance_factor($node->right)) == 1)
        {
            $this->right_rotate($node->right);
        }
        $this->left_rotate($node);
      }
  }
}

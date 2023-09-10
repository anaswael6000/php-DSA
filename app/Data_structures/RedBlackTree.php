<?php

namespace app\Data_structures;

require_once "app/Data_structures/AVL_tree.php";

class RedBlackTreeNode extends TreeNode
{
    public $color = "red";
    public $parent;
}

class RedBlackTree extends AVL_tree
{   

    public function insertValues(array $input)
    {
        foreach($input as $value)
        {
            $this->insert($value);
        }
    }

    public function insert($value, $class = TreeNode::class)
    {
        $node = new RedBlackTreeNode($value);
        
        if ($this->root == null)
        {
            $node->color = "black";
            $this->root = $node;
            return;
        }
        // Else traverse the tree and insert the node
        $current = $this->root;
        while ($current !== null)
        {
            $parent = $current;
            if ($node->value < $current->value)
            {
                $current = $current->left;
            }
            else
            {
                $current = $current->right;
            }
        }
        // Insert the node
        $parent->left = ($node->value < $parent->value) ? $node : $parent->left;
        $parent->right = ($node->value > $parent->value) ? $node : $parent->right;

        $node->parent = $parent;

        $this->FixViolations($node);
    }

    public function remove($node)
    {
        if ($node->left !== null && $node->right !== null)
        {
            // Find the in_order successor
            $successor = $node->right;
            while ($successor->left !== null)
            {
                $successor = $successor->left;
            }
            $value = $successor->value;
            $this->remove($successor);
            $node->value = $value;
        }
        elseif ($node->left === null && $node->right === null)
        {
            // Node has No children
            if ($node === $this->root)
            {
                $this->root = null;
                return;
            }
            // Else:
           $node->parent->left = ($node->parent->left === $node) ? null : $node->parent->left;
           $node->parent->right = ($node->parent->right === $node) ? null : $node->parent->right;
        }
        else
        {
            // Node has one child
            $child = $node->left ?? $node->right;
            if ($node->parent === null)
            {
                // If the parent is not set then the the node is the root node so we just set the new root the the child
                $this->root = $child;
                $child->color = "black";
                $child->parent === null;
                return;
            }
            $node->value = $child->value;
            $node->left = $node->right = null;
        }
    }
    
    public function FixViolations($node)
    {
        while (isset($node->parent) && $node->parent->color === "red")
        {
            $parent = $node->parent;
            $grandparent = $parent->parent;
            $uncle = ($grandparent->left === $parent) ? $grandparent->right : $grandparent->left;
            if ($uncle !== null && $uncle->color === "red")
            {
                $this->recolor($parent);
                $this->recolor($grandparent);
                $this->recolor($uncle);
                $node = $grandparent;
                continue;
            }
            // uncle is black, First check if a triangle is formed
            if (($grandparent->left === $parent && $parent->right === $node) || ($grandparent->right === $parent && $parent->left === $node))
            {
                // This method only rotates the first parameter the second one is only for rotation type determination
                $this->rotate($parent, $node);
                $this->updateParentPropertyOf($node, $parent);
                // Update variable names
                $temp = $parent;
                $parent = $node;
                $node = $temp;
            }
            // A line is formed
            $this->recolor($parent);
            $this->recolor($grandparent);
            $this->rotate($grandparent, $parent);
        }
        $this->root->color = "black";
    }

    public function recolor($node)
    {
        $node->color = ($node->color === "red") ? "black" : "red";
    }    
    
    public function rotate($node, $child)
    {
        if ($node->left === $child)
        {
            $this->right_rotate($node);
        }
        else
        {
            $this->left_rotate($node);
        }
    }

    public function right_rotate($node)
    {
        $parent = $node->parent;
        $newRoot = $node->left;
        $node->left = $newRoot->right;
        $newRoot->right = $node;

        if (!$parent)
        {
            $this->root = $newRoot;
        }
        else
        {
            $parent->left = ($parent->left === $node) ? $newRoot : $parent->left;
            $parent->right = ($parent->right === $node) ? $newRoot : $parent->right;
        }
        $this->updateParentPropertyOf($newRoot, $node);
    }

    public function left_rotate($node)
    {
        $parent = $node->parent;
        $newRoot = $node->right;
        $node->right = $newRoot->left;
        $newRoot->left = $node;

        if (!$parent)
        {
            $this->root = $newRoot;
        }
        else
        {
            $parent->left = ($parent->left === $node) ? $newRoot : $parent->left;
            $parent->right = ($parent->right === $node) ? $newRoot : $parent->right;
        }

        $this->updateParentPropertyOf($newRoot, $node);
    }

    public function updateParentPropertyOf($newParent, $oldParent)
    {
        $newParent->parent = $oldParent->parent;
        $oldParent->parent = $newParent;
    }
}

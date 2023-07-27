<?php

require_once "app/Data_structures/AVL_tree.php";
require_once "vendor/autoload.php";

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProviderExternal;

final class AVL_treeTest extends TestCase
{
  public $AVL_tree;
  
  public function setUp():void
  {
    $this->AVL_tree = new app\Data_structures\AVL_tree;
  }

  #[DataProviderExternal(AVLtreeTestsDataProviders::class, 'right_rotation_data_provider')]
  public function test_right_rotation($input, $value_to_be_rotated, $expected_output)
  {
    $this->AVL_tree->insert_multiple_values($input);
    $this->AVL_tree->right_rotate($value_to_be_rotated);

    $this->expectOutputString($expected_output);
    $this->AVL_tree->breadth_first_traversal($this->AVL_tree->root);
  }

  #[DataProviderExternal(AVLtreeTestsDataProviders::class, 'left_rotation_data_provider')]
  public function test_left_rotation($input, $value_to_be_rotated, $expected_output)
  {
    $this->AVL_tree->insert_multiple_values($input);
    $this->AVL_tree->left_rotate($value_to_be_rotated);

    $this->expectOutputString($expected_output);
    $this->AVL_tree->breadth_first_traversal($this->AVL_tree->root);
  }

  #[DataProviderExternal(AVLtreeTestsDataProviders::class, 'left_right_rotation_data_provider' )]
  public function test_left_right_rotation($input, $value_to_be_rotated, $expected_output)
  {
      $this->AVL_tree->insert_multiple_values($input, app\Data_structures\AVL_treeNode::class);
      $this->AVL_tree->left_right_rotate($value_to_be_rotated);

      $this->expectOutputString($expected_output);
      $this->AVL_tree->breadth_first_traversal($this->AVL_tree->root);
  }

  #[DataProviderExternal(AVLtreeTestsDataProviders::class, 'right_left_rotation_data_provider' )]
  public function test_right_left_rotation($input, $value_to_be_rotated, $expected_output)
  {
    $this->AVL_tree->insert_multiple_values($input, app\Data_structures\AVL_treeNode::class);
    $this->AVL_tree->right_left_rotate($value_to_be_rotated);

    $this->expectOutputString($expected_output);
    $this->AVL_tree->breadth_first_traversal($this->AVL_tree->root);
  }

  #[DataProviderExternal(AVLtreeTestsDataProviders::class, 'getting_the_balance_factor_a_node_data_provider')]
  public function test_getting_the_balance_factor_of_a_node($input, $value, $expected_output)
  {
    $this->AVL_tree->insert_multiple_values($input, app\Data_structures\AVL_treeNode::class);
    $this->assertEquals($expected_output ,$this->AVL_tree->get_balance_factor($value));
  }

  #[DataProviderExternal(AVLtreeTestsDataProviders::class, 'balancing_the_tree_data_provider')]
  public function test_balancing_the_tree($input, $expected_output)
  {
      $this->AVL_tree->insert_multiple_values($input, app\Data_structures\AVL_treeNode::class);
      $this->expectOutputString($expected_output);
      $this->AVL_tree->balance($this->AVL_tree->root);

      $this->AVL_tree->breadth_first_traversal($this->AVL_tree->root);
  } 

  #[DataProviderExternal(AVLtreeTestsDataProviders::class, 'balancing_the_tree_data_provider')]
  public function test_self_balancing_the_tree_when_insertion($input, $expected_output)
  {
      $this->AVL_tree->insertValues($input);
      $this->expectOutputString($expected_output);
      $this->AVL_tree->breadth_first_traversal($this->AVL_tree->root);
  }

  public function test_balancing_the_tree_when_insertion()
  {
      $this->AVL_tree->insertValues([5, 7, 6, 2, 3]);
      $this->expectOutputString("6 3 7 2 5 ");
      $this->AVL_tree->breadth_first_traversal($this->AVL_tree->root);
  }

  public function test_getting_the_depth_of_a_node()
  {
      $this->AVL_tree->insertValues([5, 3, 7, 4, 2]);
      $this->assertEquals(0 ,$this->AVL_tree->get_depth(5));
      $this->assertEquals(2 ,$this->AVL_tree->get_depth(2));
      $this->assertEquals(1 ,$this->AVL_tree->get_depth(7));
      $this->assertEquals(1 ,$this->AVL_tree->get_depth(3));
      $this->assertEquals(2 ,$this->AVL_tree->get_depth(4));
      $this->assertFalse($this->AVL_tree->get_depth(new app\Data_structures\AVL_treeNode(1)));
  }

  #[DataProviderExternal(AVLtreeTestsDataProviders::class, 'removing_a_node_data_provider')]
  public function test_self_balancing_when_deletion($input, $value, $expected_output)
  {
    $this->AVL_tree->insertValues($input);
    $this->AVL_tree->remove($value);
    $this->expectOutputString($expected_output);
    $this->AVL_tree->breadth_first_traversal($this->AVL_tree->root);
  }
}

class AVLtreeTestsDataProviders
{
  public static function right_rotation_data_provider()
  {
    return [
      /* This test only tests the rotation mechanism meaning that the tree 
         does not have to be unbalanced or left heavy to perform the rotation */

      // The returned values are 1:tree input  2:value to be rotated  3: expected output

      // This will target a root node rotation
      [[3, 2, 1], 3, "2 1 3 "],

      // Ensure that the being-rotated node's right children are not affected
      [[4, 3, 1, 5, 7], 4, "3 1 4 5 7 "],

      // This will target a non-root node rotation 
      [[4, 3, 2, 1], 3, "4 2 1 3 "]
    ];
  }

  public static function left_rotation_data_provider()
  {
    return [
      /* This test only tests the rotation mechanism meaning that the tree 
         does not have to be unbalanced or right heavy to perform the rotation */

      // The returned values are 1:tree input  2:value to be rotated  3:expected output 4: expected new root

      // This will target a root node rotation
      [[1, 2, 3 ], 1, "2 1 3 "],

      // Ensure that the being-rotated node's left children are not affected
      [[4, 3, 1, 5, 7], 4, "5 4 7 3 1 "],

      // This will target a non-root node rotation 
      [[1, 2, 3, 4], 2, "1 3 2 4 "]
    ];
  }

  public static function balancing_the_tree_data_provider()
  {
    return [
      /* This test ensures that the correct rotations are performed when the tree is unbalanced
         it does not test the rotations themselves */

      // target a right rotation 
      [[3, 2, 1], "2 1 3 "],

      // Target a left rotation
      [[1, 2, 3 ], "2 1 3 "],

      // Target a left-right rotation
      [[3, 1, 2], "2 1 3 "],

      // Target a right-left rotation
      [[1, 3, 2], "2 1 3 "],

    ];
  }
  
  public static function left_right_rotation_data_provider()
  {
    return [
      // This will target a root node left_right rotation
      [[3, 1, 2], 3, "2 1 3 "],

      // This will target a non-root node left_right rotation
      [[4, 3, 1, 2], 3, "4 2 1 3 "]
    ];
  }
  
  public static function right_left_rotation_data_provider()
  {
    return [
      // This will target a root node right_left rotation
      [[2, 4, 3], 2, "3 2 4 "],

      // This will target a non-root node right_left rotation
      [[5, 18, 20, 19], 18, "5 19 18 20 "]
    ];
  }

  public static function getting_the_balance_factor_a_node_data_provider()
  {
    return[
      // The order of input is 1: tree input 2: node to use 3: expected output

      // This will target a positive balance factor
      [[3, 2, 1], 3, 2],

      // This will target a negative balance factor
      [[1, 5, 6], 1, -2],

      // This will target a zero balance factor
      [[2, 1, 3], 2, 0],

    ];
  }

  public static function removing_a_node_data_provider()
  {
    return[
      // Order of input 1: tree input 2: value to be removed 3: expected Output when traversing

      // Target a two-child root node removal
      [[5, 3, 7, 2], 5, "3 2 7 "],

      // Target a non-root one-child node
      [[9, 18, 7, 5, 20, 15, 22], 7, "18 9 20 5 15 22 "],

      // Target a non-root no-child node
      [[9, 10, 7, 5], 10, "7 5 9 "]
    ];
  }
}
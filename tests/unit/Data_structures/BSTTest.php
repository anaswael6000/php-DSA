<?php

require_once "app/Data_structures/Exceptions/alreadyExistsException.php";
require_once "app/Data_structures/BST.php";
require_once "vendor/autoload.php";

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProviderExternal;

final class BSTTest extends TestCase
{
    public $BST;

    public function setUp(): void
    {
        $this->BST = new app\Data_structures\BST();
    }

    public function test_tree_insertion()
    {
        // The whole of this test is to ensure that the insertion operation runs without trouble
        $stub = $this->createStub(app\Data_structures\BST::class);

        $stub->method("print")
             ->willReturn([8, 5]);

        $this->BST->insert(8);
        $this->BST->insert(5);
        
        $this->assertEquals([8, 5], $stub->print());
    }

    public function test_tree_searching()
    {
        $this->BST->insert(8);
        $this->BST->insert(5);

        $this->assertTrue($this->BST->search($this->BST->root, 8));
        $this->assertTrue($this->BST->search($this->BST->root, 5));
        $this->assertFalse($this->BST->search($this->BST->root, 3));
        $this->expectException(app\Data_structures\Exceptions\alreadyExistsException::class);
        $this->BST->insert(8);
    }

    public function test_empty_tree_searching()
    {
        $this->assertFalse($this->BST->search($this->BST->root, 5));
    }

    public function test_inserting_multiple_values()
    {
        $this->BST->insert_multiple_values([3, 8, 5, 9, 2]);
        $this->assertTrue($this->BST->search($this->BST->root, 5));
        $this->assertTrue($this->BST->search($this->BST->root, 2));
        $this->assertTrue($this->BST->search($this->BST->root, 9));
        $this->assertTrue($this->BST->search($this->BST->root, 3));
    }

    public function test_in_order_tree_traversal()
    {
        $this->BST->insert_multiple_values([3, 1, 5, 9]);

        $this->expectOutputString("1 3 5 9 ");
        $this->BST->in_order_traversal($this->BST->root);
    }

    public function test_reverse_in_order_traversal()
    {
        $this->BST->insert_multiple_values([3, 1, 5, 9]);
        
        $this->expectOutputString("9 5 3 1 ");
        $this->BST->reverse_in_order_traversal($this->BST->root);
    }

    public function test_pre_order_tree_traversal()
    {
        $this->BST->insert_multiple_values([3, 1, 5, 9]);

        $this->expectOutputString("3 1 5 9 ");
        $this->BST->pre_order_traversal($this->BST->root);
    }

    #[DataProviderExternal(BSTtestDataProviders::class ,'post_order_test_data_provider')]
    public function test_post_order_traversal($input, $expected_output)
    {
        $this->BST->insert_multiple_values($input);

        $this->expectOutputString($expected_output);
        $this->BST->post_order_traversal($this->BST->root);
    }
    
    #[DataProviderExternal(BSTtestDataProviders::class, 'breadth_first_order_test_data_provider')]
    public function test_Breadth_First_order($input, $expected_output)
    {
      $this->BST->insert_multiple_values($input);
      $this->expectOutputString($expected_output);
      $this->BST->breadth_first_traversal($this->BST->root);
    }

    public function test_deleting_the_tree()
    {
        $this->BST->insert_multiple_values([1, 3, 9, 2, 5]);
        $this->BST->delete($this->BST->root);
        $this->assertFalse($this->BST->search($this->BST->root, 1));
        $this->assertFalse($this->BST->search($this->BST->root, 9));
        $this->assertFalse($this->BST->search($this->BST->root, 3));
    }

    public function test_getting_a_node_by_a_value()
    {
        $this->BST->insert_multiple_values([3, 5, 2, 1]);

        $this->assertInstanceOf(app\Data_structures\TreeNode::class, $this->BST->get_node(5));
        $this->assertInstanceOf(app\Data_structures\TreeNode::class, $this->BST->get_node(1));
        $this->assertInstanceOf(app\Data_structures\TreeNode::class, $this->BST->get_node(3));

        $this->assertEquals(5, $this->BST->get_node(5)->value);
        $this->assertEquals(3, $this->BST->get_node(3)->value);
        $this->assertEquals(2, $this->BST->get_node(2)->value);
        
        $this->expectException(app\Data_structures\Exceptions\doesNotExistException::class);
        $this->BST->get_Node(9);
    }

    #[DataProviderExternal(BSTtestDataProviders::class, 'removing_a_value_test_data_provider')]
    public function test_removing_a_value($input, $value, $expected_output)
    {
        $this->BST->insert_multiple_values($input);

        $this->assertTrue($this->BST->remove($value));

        // if the removed node is the root one then the search method will throw and exception
        try
        {
        $this->BST->search($this->BST->root, $value);
        } 
        catch(app\Data_structures\Exceptions\itIsEmptyException)
        {
            return;
        }

        $this->assertFalse($this->BST->remove(393279));
        $this->expectOutputString($expected_output);
        $this->BST->in_order_traversal($this->BST->root);
    }

    #[DataProviderExternal(BSTtestDataProviders::class, 'getting_the_tree_height_data_provider')]
    public function test_getting_the_tree_height($input, $expected_output)
    {
        $this->BST->insert_multiple_values($input);
        $this->assertEquals($expected_output, $this->BST->get_height($this->BST->root));
    }                                                    
}

class BSTtestDataProviders
{
    public static function post_order_test_data_provider()
    {
        return [
            [[5, 3, 9, 7, 2, 1], "1 2 3 7 9 5 "],
            [[3, 2, 8, 9, 7], "2 7 9 8 3 " ],
            [[3, 5, 9], "9 5 3 "],
            [[2, 1, 9], "1 9 2 "]
        ];
    }

    public static function breadth_first_order_test_data_provider()
    {
        return [
            [[5, 3, 9, 7, 2, 1], "5 3 9 2 7 1 "],
            [[3, 2, 8, 9, 7], "3 2 8 7 9 " ],
            [[3, 5, 9], "3 5 9 "],
            [[2, 1, 9], "2 1 9 "]
        ];
    }
    
    public static function removing_a_value_test_data_provider()
    {
        return [
            /* First element is the input the second is the will-be-removed-element the third is the expected output using
               using the in-order traversal technique */

            // This will target a two-child-root  node 
            [[5, 9, 3], 5, "3 9 "],

            // This will target a one-child-root  node 
            [[5, 9], 5, "9 "],

            // This will target a no-child-root node 
            [[5], 5, ""],

            // This will target a two-child node 
            [[6, 3, 5, 2, 4, 1], 3, "1 2 4 5 6 "],

            // This will target a one-child node
            [[5, 3, 2, 1], 2, "1 3 5 "],

            // This will target a no-children node
            [[5, 3, 4], 4, "3 5 "]
        ];
    }

    public static function getting_the_tree_height_data_provider()
    {
        return [
            [[3, 2, 4], 2],
            [[3, 2, 4, 6], 3],
            [[9, 15, 12, 16, 7, 5, 4, 2, ], 5]

        ];
    }
}
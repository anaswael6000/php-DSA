<?php

require_once "vendor/autoload.php";
require_once "app/Data_structures/RedBlackTree.php";

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProviderExternal;

final class RedBlackTreeTest extends TestCase 
{
    public $tree;

    public function setUp(): void
    {
        $this->tree = new app\Data_structures\RedBlackTree;
    }

    #[DataProviderExternal('RedBlackTreeTestsDataProviders' ,'test_fixing_up_the_tree_when_insertion_data_provider')]
    public function test_fixing_up_the_tree_when_insertion($Tree_input, $Black_values, $Red_values, $expected_output)
    {
        $this->tree->insertValues($Tree_input);

        foreach($Black_values as $Black_value)
        {
            $this->assertEquals("black", $this->tree->get_node($Black_value)->color);
        }
        foreach($Red_values as $Red_value)
        {
            $this->assertEquals("red", $this->tree->get_node($Red_value)->color);
        }

        $this->expectOutputString($expected_output);
        $this->tree->breadth_first_traversal($this->tree->root);
    }

    #[DataProviderExternal('RedBlackTreeTestsDataProviders' ,'test_fixing_up_the_tree_when_deletion_data_provider')]
    public function test_fixing_up_the_tree_when_deletion($Tree_input, $value ,$Black_values, $Red_values, $expected_output)
    {
        $this->tree->insertValues($Tree_input);
        $this->tree->remove($this->tree->get_node($value));

        foreach($Black_values as $Black_value)
        {
            $this->assertEquals("black", $this->tree->get_node($Black_value)->color);
        }
        foreach($Red_values as $Red_value)
        {
            $this->assertEquals("red", $this->tree->get_node($Red_value)->color);
        }

        $this->expectOutputString($expected_output);
        $this->tree->breadth_first_traversal($this->tree->root);
    }
}

class RedBlackTreeTestsDataProviders
{
    public static function test_fixing_up_the_tree_when_insertion_data_provider()
    {
        return [
            // Order of input  1:Tree input  2:Black values  3:Red values  4: Expected Output

            // Target a root node insertion 
            [[5, 3], [5], [3], "5 3 "],

            // Target a red-uncle node insertion 
            [[5, 3, 7, 9], [5, 7, 3], [9], "5 3 7 9 "],
            
            // Target a black-uncle node insertion that forms a right line
            [[5, 10, 18], [10], [5, 18], "10 5 18 "],

            // Target a black-uncle node insertion that forms a left line
            [[5, 4, 3], [4], [5, 3], "4 3 5 "],

            // Target a black-uncle node insertion that forms a right triangle
            [[5, 7, 6], [6], [7, 5], "6 5 7 "],

            // Target a black-uncle node insertion that forms a left triangle
            [[5, 3, 4], [4], [5, 3], "4 3 5 "],
            
            // Target a more complex tree
            [[5, 4, 18, 30, 3, 40, 25, 17, 27], [18, 4, 17, 25, 40], [30, 5, 27, 3], "18 5 30 4 17 25 40 3 27 "],
        ];
    }

    public static function test_fixing_up_the_tree_when_deletion_data_provider()
    {
        return [
            // Order of input  1:Tree input  2:value-to-be-removed  3:Black values  4:Red values  5: Expected Output

            // Target a root-left-child node removal
            [[5, 3], 5, [3], [], "3 "],

            // Target a root-right-child node removal
            [[5, 6], 5, [6], [], "6 "],

            // Target a two-children root node removal
            [[5, 3, 7], 5, [7], [3], "7 3 "],

            // Target a no-children root node removal
            [[5], 5, [], [], ""],
            
            // Target a black-non-root-left-child node removal
            [[5, 4, 3, 2], 3, [4, 5, 2], [], "4 2 5 "],

            // Target a black-non-root-right-child node removal
            [[5, 10, 18, 22], 18, [10, 5, 22], [], "10 5 22 "],

            // Target a non-root-no-children node removal
            [[5, 4, 7], 7, [5], [4], "5 4 "],

            // Target a black-non-root-two-children node removal
            [[5, 4, 18, 30, 3, 40], 30, [5, 4, 40], [18, 3], "5 4 40 3 18 "],

            // Target a red-non-root-two-children node removal
            [[5, 4, 18, 30, 3, 40, 25, 17, 27], 30, [18, 4, 17, 25], [40, 5, 27, 3], "18 5 40 4 17 25 3 27 "],
        ];
    }

}
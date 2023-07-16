<?php

include "app/Data_structures/linkedList.php";
include "app/Data_structures/Exceptions/itIsEmptyException.php"; 
require_once "vendor/autoload.php";

use PHPUnit\Framework\TestCase;

final class linkedListUnitTest extends TestCase
{
    private $linkedList;

    public function setUp(): void
    {
        $this->linkedList = new app\Data_structures\linkedList();
    }

    public function tearDown(): void
    {
        $this->linkedList->clear();
    }

    public function test_that_the_linked_list_is_empty_when_we_do_not_add_any_elements()
    {
        $this->assertTrue($this->linkedList->isEmpty());
    }

    public function test_that_we_can_create_a_node_and_set_and_get_its_value()
    {
        $node = new app\Data_structures\Node("I adore writing code");
        $this->assertEquals($node->data, "I adore writing code");
    }

    public function test_that_we_can_append_new_elements_to_the_end_and_print_them_out()
    {
        $this->linkedList->append("a");
        $this->linkedList->append("b");
        $this->linkedList->append("c");
        $this->assertEquals($this->linkedList->print(), "abc");
    }

    public function test_that_we_can_insert_a_new_element_to_the_middle()
    {
        $this->linkedList->append("a");
        $this->linkedList->append("c");
        $this->linkedList->insert_Between("a", "c", "b");
        $this->assertEquals($this->linkedList->print(), "abc");
    }

    public function test_that_we_can_insert_new_elements_to_the_beginning()
    {
        $this->linkedList->append("b");
        $this->linkedList->append("c");
        $this->linkedList->unshift("a");
        $this->assertEquals($this->linkedList->print(), "abc");
    }

    public function test_that_we_can_remove_the_first_node()
    {
        $this->linkedList->append("a");
        $this->linkedList->append("b");
        $this->linkedList->append("c");

        $this->linkedList->shift();

        $this->assertEquals($this->linkedList->print(), "bc");
    }

    public function test_removing_a_node_with_a_certain_value()
    {
        $this->linkedList->append("a");
        $this->linkedList->append("b");
        $this->linkedList->append("c");

        $this->linkedList->remove("c");

        $this->assertEquals($this->linkedList->print(), "ab");
        $this->linkedList->clear();
        $this->expectException(app\Data_structures\Exceptions\itIsEmptyException::class);
        $this->linkedList->remove("b");
    }

    public function test_inserting_an_element_by_a_specific_index()
    {
        $this->linkedList->append("a");
        $this->linkedList->append("c");
        $this->linkedList->insert_at(1, "b");

        $this->assertEquals($this->linkedList->print(), "abc");
    }

    public function test_getting_the_length_of_the_linked_list()
    {
        $this->linkedList->append("1");
        $this->linkedList->append("2");
        $this->linkedList->append("3");

        $this->assertEquals(3, $this->linkedList->length());
    }

    public function test_removing_a_value_with_a_certain_index()
    {
        $this->linkedList->append("a");
        $this->linkedList->append("b");
        $this->linkedList->append("c");;
        $this->linkedList->append("d");
        $this->linkedList->removeAt(2);
        $this->assertEquals($this->linkedList->print(), "abd");
    }

    public function test_getting_the_value_of_a_certain_index()
    {
        $this->linkedList->append("a");
        $this->linkedList->append("b");
        $this->linkedList->append("c");

        $this->assertEquals($this->linkedList->get(1), "b");
    }

    public function test_clearing_the_linkedList()
    {
        $this->linkedList->append("a");
        $this->linkedList->append("b");

        $this->linkedList->clear();
        $this->assertEquals($this->linkedList->print(), "");
    }

    public function test_converting_the_linked_list_to_an_array()
    {
        $this->linkedList->append("a");
        $this->linkedList->append("b");
        $this->linkedList->append("c");

        $this->assertEquals($this->linkedList->toArray(), ["a", "b", "c"]);
    }

    public function test_getting_the_index_of_a_specific_value()
    {
        $this->linkedList->append("a");
        $this->linkedList->append("b");
        $this->linkedList->append("c");

        $this->assertEquals($this->linkedList->indexof("b"), 1);
    }

    public function test_reversing_the_linked_list()
    {
        $this->linkedList->append("a");
        $this->linkedList->append("b");
        $this->linkedList->append("c");
        $this->linkedList->reverse();
        $this->assertEquals($this->linkedList->print(), "cba");
    }

}
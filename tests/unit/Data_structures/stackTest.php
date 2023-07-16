<?php

include "app/Data_structures/stack.php";
require_once "vendor/autoload.php";

use PHPUnit\Framework\TestCase;

final class stackTest extends TestCase
{
    protected $stack;

    public function setUp(): void
    {
        $this->stack = new app\Data_structures\stack();
        $this->stack->push(1);
        $this->stack->push(2);
    }

    public function test_pushing_an_element_to_the_stack_and_printing_it_out()
    {
        $this->assertEquals([2, 1], $this->stack->print());
    }

    public function test_popping_the_stack()
    {
        $this->stack->push(3);
        $this->stack->pop();

        $this->assertEquals([2,1], $this->stack->print());

        $this->stack->pop();
        $this->stack->pop();
        
        $this->expectException(app\Data_structures\Exceptions\itIsEmptyException::class);
        $this->stack->pop();

    }

    public function test_getting_the_length_of_the_stack()
    {
        $this->assertEquals(2, $this->stack->length());
    }

    public function test_checking_if_the_stack_is_empty()
    {
        $this->stack->pop();
        $this->stack->pop();
        $this->assertTrue($this->stack->isEmpty());
    }

    public function test_getting_the_top_element()
    {
        $this->assertEquals(2, $this->stack->peek());
    }

   public function test_if_the_string_is_balanced()
    {
        // Delete the elements pushed by default
        $this->stack->pop();
        $this->stack->pop();

        
        $this->assertFalse($this->stack->is_balanced("([]"));
        $this->assertFalse($this->stack->is_balanced("([])}"));
        $this->assertTrue($this->stack->is_balanced("()d"));
        $this->assertTrue($this->stack->is_balanced("(I adore writing code)"));

        $this->expectException(app\Data_structures\Exceptions\itIsEmptyException::class);
        $this->stack->is_balanced("");
    }

}
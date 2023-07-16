<?php

include "app\Data_structures\Exceptions\doesNotExistException.php";
include "app\Data_structures\hashTable.php";
require_once "vendor/autoload.php";

use PHPUnit\Framework\TestCase;

final class hashTableTest extends TestCase
{
    protected $hashTable;

    public function setUp(): void
    {
        $this->hashTable = new app\Data_structures\hashTable();
    }

    public function test_inserting_a_value_and_printing_it_out()
    {
        $this->hashTable->insert("rachel", "is pretty");
        $this->assertEquals(["is pretty"], $this->hashTable->display());
    }

    public function test_getting_a_value()
    {
        $this->hashTable->insert("rachel", "is pretty");
        $this->assertEquals("is pretty", $this->hashTable->get("rachel"));
    }

    public function test_getting_a_non_existing_value()
    {
        $this->expectException(app\Data_structures\Exceptions\doesNotExistException::class);
        $this->hashTable->get("rachel");
    }

    public function test_that_collisions_are_resolved()
    {
        $this->hashTable->insert("act", 24);
        $this->hashTable->insert("cat", 25);
        $this->hashTable->insert("tac", 26);

        // This will result in a collision because cat and act are the same letters in different order
        $this->assertEquals(24, $this->hashTable->get("act"));        
        $this->assertEquals(25, $this->hashTable->get("cat"));        
        $this->assertEquals(26, $this->hashTable->get("tac"));        
        $this->assertEquals([24, 25, 26], $this->hashTable->display());        
    }

    public function test_removing_a_key_value_pair()
    {
        $this->hashTable->insert("rachel", "is pretty");

        $this->hashTable->remove("rachel");

        $this->assertEmpty($this->hashTable->display());

        $this->expectException(app\Data_structures\Exceptions\doesNotExistException::class);
        $this->hashTable->get("rachel");
    }

    public function test_checking_if_key_value_pair_is_included_in_the_hashTable()
    {
        $this->hashTable->insert("anas", "is handsome");
        $this->assertTrue($this->hashTable->contains("anas"));
        $this->assertFalse($this->hashTable->contains("rachel"));
    }

    public function test_clearing_the_hashTable()
    {
        $this->hashTable->insert("act", 24);
        $this->hashTable->insert("cat", 25);
        $this->hashTable->insert("tac", 26);

        $this->hashTable->clear();

        $this->assertEmpty($this->hashTable->display());
    }

    public function test_getting_all_key()
    {
        $this->hashTable->insert("act", 24);
        $this->hashTable->insert("cat", 25);
        $this->hashTable->insert("tac", 26);

        $this->assertEquals(["act", "cat", "tac"], $this->hashTable->keys());
    }

    public function test_getting_values()
    {
        $this->hashTable->insert("act", 24);
        $this->hashTable->insert("cat", 25);
        $this->hashTable->insert("tac", 26);

        $this->assertEquals([24, 25, 26], $this->hashTable->values());
    }

    public function test_getting_size()
    {
        $this->assertEquals(3, $this->hashTable->get_size());
    }
}
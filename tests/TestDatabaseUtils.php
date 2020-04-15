<?php

require_once __DIR__ . '/../includes/database/utils.php';
require_once __DIR__ . '/TestRecord.php';
require_once __DIR__ . '/TestIdRecord.php';
require_once __DIR__ . '/../includes/database/Database.php';

use PHPUnit\Framework\TestCase;

class TestDatabaseUtils extends TestCase {

    private $table;

    public function __construct() {
        parent::__construct();
        $this->table = new Table('test', 'TestRecord');
    }

    public function test_generate_upsert_sql() {
        $expected =
            "INSERT INTO test (`one`, `two`, `id`) VALUES (:ins_one, :ins_two, :ins_id) "
            . "ON DUPLICATE KEY UPDATE `one`=:up_one, `two`=:up_two";

        $actual = generate_upsert_sql($this->table, new TestRecord());
        $this->assertSame($expected, $actual);
    }

    public function test_generate_upsert_execute_array() {
        $expected = [
            ':ins_one' => 'aa',
            ':ins_two' => 'bb',
            ':ins_id' => null,
            ':up_one' => 'aa',
            ':up_two' => 'bb'
        ];
        $record = new TestRecord();
        $record->one = 'aa';
        $record->two = 'bb';
        $actual = create_upsert_execute_array($record);
        $this->assertSame($expected, $actual);
    }

    public function test_generate_upsert_sql_id() {
        $expected =
            "INSERT INTO test (`id`, `two`) VALUES (:ins_id, :ins_two) "
            . "ON DUPLICATE KEY UPDATE `two`=:up_two";
        $actual = generate_upsert_sql($this->table, new TestIdRecord());
        $this->assertSame($expected, $actual);
    }

    public function test_generate_upsert_execute_array_id() {
        $expected = [
            ':ins_id' => 'aa',
            ':ins_two' => 'bb',
            ':up_two' => 'bb'
        ];
        $record = new TestIdRecord();
        $record->id = 'aa';
        $record->two = 'bb';
        $actual = create_upsert_execute_array($record);
        $this->assertSame($expected, $actual);
    }

    public function test_generate_insert_sql() {
        $table = new Table('test', 'TestRecord');
        $expected = "INSERT INTO test (`two`) VALUES (:ins_two)";
        $actual = generate_insert_sql($table, new TestIdRecord());
        $this->assertSame($expected, $actual);
    }

}

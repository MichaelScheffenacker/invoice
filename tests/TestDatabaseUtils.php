<?php

require_once __DIR__ . '/../includes/database/utils.php';
require_once __DIR__ . '/TestRecord.php';
require_once __DIR__ . '/TestIdRecord.php';

use PHPUnit\Framework\TestCase;

class TestDatabaseUtils extends TestCase {

    public function test_generate_upsert_sql() {
        $expected =
            "INSERT INTO test (`one`, `two`) VALUES (:ins_one, :ins_two) "
            . "ON DUPLICATE KEY UPDATE `one`=:up_one, `two`=:up_two";
        $actual = generate_upsert_sql('test', new TestRecord());
        $this->assertSame($expected, $actual);
    }

    public function test_generate_upsert_execute_array() {
        $expected = [
            ':ins_one' => 'aa',
            ':ins_two' => 'bb',
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
        $actual = generate_upsert_sql('test', new TestIdRecord());
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
}

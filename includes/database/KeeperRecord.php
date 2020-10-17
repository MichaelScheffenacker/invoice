<?php


abstract class KeeperRecord extends Record
{
    protected const _wards = [];

    public static function select_all(): array {
        $records = parent::select_all();
        foreach ($records as $record) {
            self::select_wards($record);
        }
        return $records;
    }

    public static function construct_from_id(int $id): Record {
        $record = parent::construct_from_id($id);
        self::select_wards($record);
        return $record;
    }

    public static function construct_from_alien_array(array $array): Record {
        $record = parent::construct_from_alien_array($array);
        /** @var Record $Ward */
        foreach (static::_wards as $Ward => $property) {
            $raw_wards = $array[$property];
            foreach ($raw_wards as $raw_ward) {
                $ward = $Ward::construct_from_alien_array($raw_ward);
                $record->$property[] = $ward;
            }
        }
        return $record;
    }

    private static function select_wards(Record $record) {
        foreach (static::_wards as $Ward => $property) {
            $wards = self::$_db->select_wards_by_keeper($Ward, $record);
            $record->$property = $wards;
        }
    }


    public function insert() : void {
        parent::insert();
        $this->insert_wards();
    }

    public function upsert(): void {
        parent::upsert();
        foreach (static::_wards as $Ward => $property) {
            self::$_db->delete_wards_by_keeper($Ward, $this);
        }
    }

    private function insert_wards() {
        foreach (static::_wards as $Ward => $property) {
            foreach ($this->$property as $ward) {
                /** @var Record $ward */
                $ward->insert();
            }
        }
    }

}

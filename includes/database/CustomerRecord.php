<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 14.01.18
 * Time: 00:04
 */

require_once __DIR__ . '/Record.php';
require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/../html/utils.php';

class CustomerRecord extends Record
{
    protected $_table_name = 'customers';
    public $id = '';
    public $gender = '';
    public $title = '';
    public $forename = '';
    public $surname = '';
    public $company = '';
    public $street = '';
    public $city = '';
    public $country = '';
    public $vatin = '';

}

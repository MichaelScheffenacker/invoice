<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 14.01.18
 * Time: 00:04
 */

require_once 'Record.php';
require_once 'utils.php';
require_once 'includes/html/utils.php';

class CustomerRecord extends Record
{
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

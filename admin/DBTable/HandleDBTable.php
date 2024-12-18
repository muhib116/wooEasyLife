<?php
namespace WooEasyLife\Admin\DBTable;

use WooEasyLife\Admin\DBTable\FraudTable;
use WooEasyLife\Admin\DBTable\SMSConfigTable;

if(!class_exists('HandleDBTable')) :
class HandleDBTable{
    public $fraudTable;
    public $smsConfigTable;
    public function __construct()
    {
        $this->fraudTable = new FraudTable();
        $this->smsConfigTable = new SMSConfigTable();
    }

    public function create() {
        $this->fraudTable->create();
        $this->smsConfigTable->create();
    }

    public function delete() {
        $this->fraudTable->delete();
        $this->smsConfigTable->delete();
    }
}
endif;
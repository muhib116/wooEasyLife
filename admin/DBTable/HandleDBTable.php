<?php
namespace WooEasyLife\Admin\DBTable;

use WooEasyLife\Admin\DBTable\FraudTable;

if(!class_exists('HandleDBTable')) :
class HandleDBTable{
    public $fraudTable;
    public function __construct()
    {
        $this->fraudTable = new FraudTable();
    }

    public function create() {
        $this->fraudTable->create();
    }

    public function delete() {
        $this->fraudTable->delete();
    }
}
endif;
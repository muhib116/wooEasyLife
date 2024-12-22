<?php
namespace WooEasyLife\Admin\DBTable;

if(!class_exists('HandleDBTable')) :
class HandleDBTable{
    public $fraudTable;
    public $smsConfigTable;
    public $blockListTable;

    public function __construct()
    {
        $this->fraudTable = new FraudTable();
        $this->smsConfigTable = new SMSConfigTable();
        $this->blockListTable = new BlockListTable();
    }

    public function create() {
        $this->fraudTable->create();
        $this->smsConfigTable->create();
        $this->blockListTable->create();
    }

    public function delete() {
        $this->fraudTable->delete();
        $this->smsConfigTable->delete();
        $this->blockListTable->delete();
    }
}
endif;
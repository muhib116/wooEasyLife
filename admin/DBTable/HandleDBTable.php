<?php
namespace WooEasyLife\Admin\DBTable;

if(!class_exists('HandleDBTable')) :
class HandleDBTable{
    public $fraudTable;
    public $smsConfigTable;
    public $blockListTable;
    public $smsHistoryTable;

    public function __construct()
    {
        $this->fraudTable = new FraudTable();
        $this->smsConfigTable = new SMSConfigTable();
        $this->blockListTable = new BlockListTable();
        $this->smsHistoryTable = new SMSHistoryTable();
    }

    public function create() {
        $this->fraudTable->create();
        $this->smsConfigTable->create();
        $this->blockListTable->create();
        $this->smsHistoryTable->create();
    }

    public function delete() {
        $this->fraudTable->delete();
        $this->smsConfigTable->delete();
        $this->blockListTable->delete();
        $this->smsHistoryTable->delete();
    }
}
endif;
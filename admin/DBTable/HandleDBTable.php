<?php
namespace WooEasyLife\Admin\DBTable;

if(!class_exists('HandleDBTable')) :
class HandleDBTable{
    public $fraudTable;
    public $smsConfigTable;
    public $blockListTable;
    public $smsHistoryTable;
    public $abandonCartTable;

    public function __construct()
    {
        $this->fraudTable = new FraudTable();
        $this->smsConfigTable = new SMSConfigTable();
        $this->blockListTable = new BlockListTable();
        $this->smsHistoryTable = new SMSHistoryTable();
        $this->abandonCartTable = new AbandonCartTable();
    }

    public function create() {
        $this->fraudTable->create();
        $this->smsConfigTable->create();
        $this->blockListTable->create();
        $this->smsHistoryTable->create();
        $this->abandonCartTable->create();
    }

    public function delete() {
        $this->fraudTable->delete();
        $this->smsConfigTable->delete();
        $this->blockListTable->delete();
        $this->smsHistoryTable->delete();
        $this->abandonCartTable->delete();
    }
}
endif;
<?php
namespace WooEasyLife\CRUD;

class SMSConfigTable {
    private $table_name;

    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . __PREFIX . 'sms_config';
    }

    /**
     * Create a new SMS configuration record.
     */
    public function create($data) {
        global $wpdb;

        $inserted = $wpdb->insert(
            $this->table_name,
            [
                'status'      => $data['status'],
                'message'     => $data['message'],
                'message_for' => $data['message_for'],
                'settings'    => isset($data['settings']) ? json_encode($data['settings']) : null,
                'is_active'   => isset($data['is_active']) ? (int)$data['is_active'] : 1,
                'created_at'  => isset($data['created_at']) ? $data['created_at'] : current_time('mysql'),
                'updated_at'  => isset($data['updated_at']) ? $data['updated_at'] : current_time('mysql'),
            ],
            [
                '%s',
                '%s',
                '%s',
                '%s', // Handles null for optional fields.
                '%d',
                '%s',
                '%s'
            ]
        );

        if ($inserted === false) {
            echo ("Database Insertion Failed: " . $wpdb->last_error);
            return false;
        }

        return $wpdb->insert_id;
    }

    /**
     * Read an SMS configuration record by ID.
     */
    public function get_by_id($id) {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $this->table_name WHERE id = %d",
                $id
            ),
            ARRAY_A
        );
    }

    /**
     * Update an SMS configuration record by ID.
     */
    public function update($id, $data) {
        global $wpdb;

        return $wpdb->update(
            $this->table_name,
            [
                'status'      => $data['status'],
                'message'     => $data['message'],
                'message_for' => $data['message_for'],
                'settings'    => isset($data['settings']) ? json_encode($data['settings']) : null,
                'is_active'   => isset($data['is_active']) ? (int)$data['is_active'] : 1,
                'updated_at'  => isset($data['updated_at']) ? $data['updated_at'] : current_time('mysql'),
            ],
            ['id' => $id],
            [
                '%s',
                '%s',
                '%s',
                '%s', // Handles null for optional fields.
                '%d',
                '%s'
            ],
            ['%d']
        );
    }

    /**
     * Delete an SMS configuration record by ID.
     */
    public function delete($id) {
        global $wpdb;

        return $wpdb->delete(
            $this->table_name,
            ['id' => $id],
            ['%d']
        );
    }

    /**
     * Get all SMS configuration records.
     */
    public function get_all() {
        global $wpdb;

        return $wpdb->get_results("SELECT * FROM $this->table_name", ARRAY_A);
    }

    /**
     * Check if an SMS configuration record exists by ID.
     */
    public function exists($id) {
        global $wpdb;

        $count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $this->table_name WHERE id = %d",
                $id
            )
        );

        return $count > 0;
    }
}
<?php
namespace WooEasyLife\Frontend;

class AbandonedCronJob {

    public function __construct() {
        // Register hooks
        add_filter( 'cron_schedules', [$this, 'add_custom_cron_schedule'] );
        add_action( 'wp', [$this, 'schedule_mark_abandoned_carts_cron'] );
        add_action( 'mark_abandoned_carts_cron', [$this, 'mark_abandoned_carts'] );

        // Schedule the cron on plugin activation
        register_activation_hook(__FILE__, [$this, 'activate_cron']);
        // Clear scheduled cron on plugin deactivation
        register_deactivation_hook(__FILE__, [$this, 'deactivate_cron']);
        do_action('mark_abandoned_carts_cron');
    }

    // Function to add a custom schedule (every 5 minutes)
    public function add_custom_cron_schedule( $schedules ) {
        $schedules['every_five_minutes'] = array(
            'interval' => 5 * 60, // 5 minutes in seconds
            'display'  => __( 'Every Five Minutes' ),
        );
        return $schedules;
    }

    // Function to schedule the cron job
    public function schedule_mark_abandoned_carts_cron() {
        if ( ! wp_next_scheduled( 'mark_abandoned_carts_cron' ) ) {
            wp_schedule_event( time(), 'every_five_minutes', 'mark_abandoned_carts_cron' );
        }
    }

    // Function to unschedule the cron job on deactivation
    public function deactivate_cron() {
        $timestamp = wp_next_scheduled( 'mark_abandoned_carts_cron' );
        if ( $timestamp ) {
            wp_unschedule_event( $timestamp, 'mark_abandoned_carts_cron' );
        }
    }

    // Activation hook to ensure cron is scheduled
    public function activate_cron() {
        $this->schedule_mark_abandoned_carts_cron();
    }

    // Function to mark carts as abandoned
    public function mark_abandoned_carts() {
        global $wpdb;

        $cutoff_time = strtotime( '-15 minutes' ); // 15 minutes ago
        $cutoff_date = date( 'Y-m-d H:i:s', $cutoff_time );

        $table_name = $wpdb->prefix . 'abandon_cart'; // Ensure this matches your actual table

        $wpdb->query(
            $wpdb->prepare(
                "UPDATE $table_name 
                SET status = 'abandoned' 
                WHERE status = 'active' AND updated_at < %s",
                $cutoff_date
            )
        );
    }
}
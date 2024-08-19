<?php
/*
Plugin Name: Comprehensive Content Sync Logger with Meta Data
Description: Logs all stages of the content and meta data lifecycle (creation, updates, trashing, restoring, deleting) for replay on another environment.
Version: 0.1
Author: Ross Mulcahy
*/

if (!defined('ABSPATH')) {
    exit;
}

function csl_log_content_change($post_id, $post, $update) {
    // Capture the creation or update details
    $log_entry = array(
        'timestamp' => date('Y-m-d H:i:s'),
        'post_id' => $post_id,
        'status' => $post->post_status,
        'title' => $post->post_title,
        'content' => $post->post_content,
        'meta_data' => get_post_meta($post_id),
        'action' => $update ? 'update' : 'create' // Check if this is an update or creation
    );

    // Convert the log entry to JSON
    $log_entry_json = wp_json_encode($log_entry) . "\n";

    // Write the log entry to the log file
    $log_file = dirname(__FILE__).'/../private/' . 'content_changes.log';
    file_put_contents($log_file, $log_entry_json, FILE_APPEND);
}

function csl_log_post_status_change($new_status, $old_status, $post) {
    if ($new_status !== $old_status) {
        $log_entry = array(
            'timestamp' => date('Y-m-d H:i:s'),
            'post_id' => $post->ID,
            'old_status' => $old_status,
            'new_status' => $new_status,
            'action' => 'status_change'
        );

        $log_entry_json = wp_json_encode($log_entry) . "\n";

        $log_file = dirname(__FILE__).'/../private/' . 'content_changes.log';
        file_put_contents($log_file, $log_entry_json, FILE_APPEND);
    }
}

function csl_log_post_deletion($post_id) {
    $log_entry = array(
        'timestamp' => date('Y-m-d H:i:s'),
        'post_id' => $post_id,
        'action' => 'delete'
    );

    $log_entry_json = wp_json_encode($log_entry) . "\n";

    $log_file = dirname(__FILE__).'/../private/' . 'content_changes.log';
    file_put_contents($log_file, $log_entry_json, FILE_APPEND);
}

// Log meta data changes
function csl_log_meta_change($meta_id, $post_id, $meta_key, $_meta_value) {
    $log_entry = array(
        'timestamp' => date('Y-m-d H:i:s'),
        'post_id' => $post_id,
        'meta_key' => $meta_key,
        'meta_value' => $_meta_value,
        'action' => 'meta_update'
    );

    $log_entry_json = wp_json_encode($log_entry) . "\n";

    $log_file = dirname(__FILE__).'/../private/' . 'meta_changes.log';
    file_put_contents($log_file, $log_entry_json, FILE_APPEND);
}

function csl_log_meta_delete($meta_ids, $post_id, $meta_key, $_meta_value) {
    $log_entry = array(
        'timestamp' => date('Y-m-d H:i:s'),
        'post_id' => $post_id,
        'meta_key' => $meta_key,
        'action' => 'meta_delete'
    );

    $log_entry_json = wp_json_encode($log_entry) . "\n";

    $log_file = dirname(__FILE__).'/../private/' . 'meta_changes.log';
    file_put_contents($log_file, $log_entry_json, FILE_APPEND);
}

// Hook into post creation and updates
add_action('save_post', 'csl_log_content_change', 10, 3);

// Hook into post status transitions (for trashing, restoring, publishing, unpublishing)
add_action('transition_post_status', 'csl_log_post_status_change', 10, 3);

// Hook into post deletions
add_action('before_delete_post', 'csl_log_post_deletion');

// Hook into meta data changes (adding, updating, deleting)
add_action('updated_post_meta', 'csl_log_meta_change', 10, 4);
add_action('added_post_meta', 'csl_log_meta_change', 10, 4);
add_action('deleted_post_meta', 'csl_log_meta_delete', 10, 4);
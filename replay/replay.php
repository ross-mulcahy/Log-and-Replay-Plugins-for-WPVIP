<?php
/*
Plugin Name: Content Sync Replay on Activation
Description: Replays content and meta changes from log files in /wp-content/private when the plugin is activated.
Version: 0.1
Author: Ross Mulcahy
*/

if (!defined('ABSPATH')) {
    exit;
}

class Content_Sync_Replay_On_Activation {

    const CONTENT_LOG_FILE = WPCOM_VIP_PRIVATE_DIR . '/content_changes.log';
    const META_LOG_FILE = WPCOM_VIP_PRIVATE_DIR  . '/meta_changes.log';

    public function __construct() {
        // Register the activation hook
        register_activation_hook(__FILE__, array($this, 'replay_content_changes'));
    }

    public function replay_content_changes() {
        if (file_exists(self::CONTENT_LOG_FILE)) {
            $content_log_entries = file(self::CONTENT_LOG_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($content_log_entries as $log_entry_json) {
                $log_entry = json_decode($log_entry_json);

                if ($log_entry) {
                    $post_id = $log_entry->post_id;
                    $action = $log_entry->action;

                    if ($action === 'delete') {
                        wp_delete_post($post_id, true);
                        error_log("Replayed delete action for Post ID: {$post_id}");
                    } elseif ($action === 'status_change') {
                        $post_data = array(
                            'ID' => $post_id,
                            'post_status' => $log_entry->new_status
                        );
                        wp_update_post($post_data);
                        error_log("Replayed status change for Post ID: {$post_id}, New Status: {$log_entry->new_status}");
                    } else {
                        $post_data = array(
                            'ID' => $post_id,
                            'post_title' => $log_entry->title,
                            'post_content' => $log_entry->content,
                            'post_status' => $log_entry->status
                        );

                        // Update or create the post with the new content
                        wp_update_post($post_data);

                        error_log("Replayed {$action} action for Post ID: {$post_id}");
                    }
                }
            }
        }

        if (file_exists(self::META_LOG_FILE)) {
            $meta_log_entries = file(self::META_LOG_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($meta_log_entries as $log_entry_json) {
                $log_entry = json_decode($log_entry_json);

                if ($log_entry) {
                    $post_id = $log_entry->post_id;
                    $meta_key = $log_entry->meta_key;
                    $meta_value = isset($log_entry->meta_value) ? $log_entry->meta_value : '';

                    if ($log_entry->action === 'meta_update') {
                        update_post_meta($post_id, $meta_key, $meta_value);
                        error_log("Replayed meta update for Post ID: {$post_id}, Meta Key: {$meta_key}");
                    } elseif ($log_entry->action === 'meta_delete') {
                        delete_post_meta($post_id, $meta_key);
                        error_log("Replayed meta delete for Post ID: {$post_id}, Meta Key: {$meta_key}");
                    }
                }
            }
        }

        error_log("Replay completed.");
    }
}

// Instantiate the class to hook into WordPress
new Content_Sync_Replay_On_Activation();

?>
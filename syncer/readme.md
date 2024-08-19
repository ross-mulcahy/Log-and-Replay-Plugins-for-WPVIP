# Comprehensive Content Sync Logger with Meta Data

The `Content Sync Logger` plugin is designed to track and log all changes made to WordPress posts, including post creation, updates, status changes (e.g., published, trashed), and deletions, as well as changes to post meta data (custom fields). These changes are logged into files that can later be replayed in a different environment.

**How It Works:**
- **Logging Changes:** The plugin hooks into various WordPress actions (`save_post`, `transition_post_status`, `updated_post_meta`, `added_post_meta`, `deleted_post_meta`, and `before_delete_post`) to log changes in content and meta data.
- **Log Files:** All logged changes are saved as JSON entries in two log files:
  - `content_changes.log` for changes to post content (creation, updates, status changes, deletions).
  - `meta_changes.log` for changes to post meta data.
- **Log Location:** These log files are stored in a secure location, typically in `/wp-content/private/`, to ensure they are not publicly accessible.

**Use Case:** The `Content Sync Logger` plugin is intended to be used in your local development. As you make changes to content, the plugin logs these changes, creating a record that can be transported to another environment (e.g., production) to replay the changes and keep environments in sync.
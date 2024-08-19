# Content Logger and Replay for WPVIP

*This plugin has not been comprehensively tested, please use at your own discretion.*

The `Content Sync Logger` plugin is designed to track and log all changes made to WordPress posts, including post creation, updates, status changes (e.g., published, trashed), and deletions, as well as changes to post meta data (custom fields). These changes are logged into files that can later be replayed in a different environment.

The `Content Sync Replay on Activation` plugin is designed to automatically replay the logged changes captured by the `Content Sync Logger` plugin in a different environment, such as production. When this plugin is activated, it reads the log files and applies all the changes to the target environment.

## How to Use the Plugins Between Local Development and Production

Step 1

1. Use *git clone* to get the latest code from Production.  
2. Use [VIP CLI](https://docs.wpvip.com/vip-cli/) to create a [local development environment](https://docs.wpvip.com/vip-local-development-environment/).  
3. [Sync](https://docs.wpvip.com/vip-cli/commands/dev-env/sync/sql/) latest version of database. (DO NOT RUN IF DATABASES ARE NOT IN SYNC)

Step 2

1. Install and activate the *Content Sync Logger* plugin on your local development.  
2. As you create, update, delete posts, or modify meta data in your local environment, the plugin logs all changes to the content\_changes.log and meta\_changes.log files located in /wp-content/private/.  
3. Optionally, you can review the logs to ensure all necessary changes have been captured.

Step 3

1. The code uses the [VIP Private Directory](https://docs.wpvip.com/wordpress-skeleton/private-directory/) to ensure the files are *not* web accessible, but can only be accessed by the application’s theme, plugins, or CLI commands.   
2. Use git to push log files into the target environment’s repository.

Step 4:

1. Install the *Content Sync Replay on Activation* plugin on your target environment.  
2. Activate the plugin from the WordPress admin dashboard. The plugin will automatically detect the log files and apply all changes to your target environment.  
3. Check the PHP error log on your target environment to monitor the replay process and ensure all changes were applied successfully.

Step 5:

1. After the changes have been successfully applied, you can deactivate the *Content Sync Replay on Activation* plugin.  
2. To prevent accidental reprocessing, delete the log files in /wp-content/private/.

## Limitations of the Current Approach

1. **Limited Content Types**:  
   * The current plugins primarily handle posts and their meta data but do not cover all types of WordPress content, such as:  
     * **Pages**: These are technically similar to posts but may require specific handling depending on your site structure.  
     * **Custom Post Types**: These might have unique fields or behaviors that aren’t captured by the generic approach.  
     * **Taxonomies**: Changes to categories, tags, and custom taxonomies are not logged or replayed.  
     * **Menus**: Changes to navigation menus are not tracked.  
     * **Widgets**: Modifications to widgets and widget areas are not logged.  
     * **Media**: Uploads, deletions, and edits of media files (e.g., images, videos) are not covered.  
     * **Users**: User creation, deletion, and role changes are not logged.  
     * **Settings**: Changes to site settings, options, or configurations are not captured.  
2. **Potential for Conflicts**:  
   * If the same post or meta data is modified in both environments before the logs are replayed, conflicts can occur, leading to unintended overwrites or data loss.  
3. **Dependency on Log Files**:  
   * The system relies on log files being transferred accurately between environments. If a log file is missing or corrupted, some changes may not be applied correctly.  
4. **Error Handling**:  
   * Error handling is minimal, primarily relying on `error_log()`. This may not provide sufficient visibility into what happens during the replay process, especially in cases of failure.  
5. **Security Concerns**:  
   * Log files could potentially contain sensitive data. If they are not securely managed (e.g., proper file permissions), there could be security risks.  
6. **Lack of Version Control**:  
   * The approach does not handle version control of content. If multiple changes to the same piece of content are logged, the last change in the log file will overwrite earlier ones without considering any intermediate states.  
7. **One-Time Replay**:  
   * The replay is triggered only once upon plugin activation. If there are new log entries after activation, those won't be applied unless the plugin is deactivated and reactivated.

### **Next Steps for Improvement**

1. **Extend to All Content Types**:  
   * **Custom Post Types**: Extend logging to handle all custom post types by dynamically capturing these types rather than just posts.  
   * **Taxonomies**: Add hooks to log changes to categories, tags, and any custom taxonomies.  
   * **Menus and Widgets**: Develop additional hooks or functions to capture changes in navigation menus and widgets.  
   * **Media Handling**: Log uploads, updates, and deletions of media files. This might require copying media files as part of the transfer process.  
   * **User Management**: Capture and replay changes to users, including role assignments and profile updates.  
   * **Settings and Options**: Log changes to site settings and options that may affect the site’s behavior or appearance.  
2. **Conflict Detection and Resolution**:  
   * Implement a mechanism to detect and handle conflicts when the same piece of content is modified in both environments. This could involve checking timestamps or introducing a locking mechanism.  
3. **Enhanced Error Handling**:  
   * Improve error handling with more robust logging, alerts, and potential rollback mechanisms if an error occurs during the replay process.  
   * Consider using a WordPress notification system (e.g., admin notices) or a dashboard interface to report the status of the replay process.  
4. **Version Control and Incremental Sync**:  
   * Introduce versioning for each log entry to handle multiple changes to the same content. This could be achieved by using unique identifiers for each change and tracking which changes have been applied.  
   * Develop an incremental sync mechanism where new changes can be detected and applied without requiring a full replay every time.  
5. **User Interface for Logs Management**:  
   * Create a WordPress admin interface to manage and review logs, manually trigger replays, and monitor the status of content synchronization.  
   * Provide options to selectively replay certain changes or exclude specific log entries.  
6. **Scheduled and Automated Replays**:  
   * Allow the replay plugin to run on a schedule (e.g., daily, weekly) to automatically apply any new changes without manual activation.  
   * Provide options to run the replay process manually via a button in the WordPress admin or via WP-CLI commands.  
7. **Comprehensive Testing**:  
   * Test the plugins in different scenarios, such as high-content websites, sites with complex custom post types, and multisite environments, to ensure they handle all edge cases and scale effectively.


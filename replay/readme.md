# Content Sync Replay on Activation

The `Content Sync Replay on Activation` plugin is designed to automatically replay the logged changes captured by the `Content Sync Logger` plugin in a different environment, such as production. When this plugin is activated, it reads the log files and applies all the changes to the target environment.

**How It Works:**
- **Replaying Changes:** Upon activation, the plugin reads the `content_changes.log` and `meta_changes.log` files and applies the logged changes to the WordPress site.
- **Error Logging:** The plugin uses `error_log()` to log the actions it performs during the replay process, which can be reviewed in the server's PHP error log.
- **One-Time Replay:** The replay occurs only once when the plugin is activated. After the changes are applied, the log files can be manually deleted or archived to prevent reprocessing.

**Use Case:** The `Content Sync Replay on Activation` plugin is intended to be used in your production environment. After transferring the log files from your development environment, activating this plugin will apply all changes to production, ensuring it mirrors the development environment.
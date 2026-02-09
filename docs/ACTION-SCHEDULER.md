# Action Scheduler Integration

WPSeed includes **Action Scheduler** - the battle-tested background processing library used by WooCommerce and trusted by millions of WordPress sites.

## Why Action Scheduler?

- ✅ **Battle-Tested**: Powers WooCommerce, MailPoet, AutomateWoo
- ✅ **Reliable**: Handles millions of tasks daily
- ✅ **Scalable**: Works on shared hosting to enterprise
- ✅ **Admin UI**: Built-in tools for monitoring tasks
- ✅ **Fault Tolerant**: Automatic retry on failure
- ✅ **No Cron Issues**: Works even if WP-Cron is disabled

## Quick Start

### Schedule a Single Task

```php
$scheduler = WPSeed_Task_Scheduler::instance();

// Run in 1 hour
$scheduler->schedule_single(
    'my_custom_task',              // Hook name
    array('user_id' => 123),       // Arguments
    time() + HOUR_IN_SECONDS       // When to run
);

// Handle the task
add_action('my_custom_task', function($user_id) {
    // Your code here
});
```

### Schedule a Recurring Task

```php
// Run every hour
$scheduler->schedule_recurring(
    'hourly_cleanup',
    HOUR_IN_SECONDS,               // Interval
    array(),                       // Arguments
    time()                         // Start time
);
```

### Schedule with Cron Expression

```php
// Run every day at 3am
$scheduler->schedule_cron(
    'daily_report',
    '0 3 * * *',                   // Cron expression
    array()
);
```

## API Reference

### schedule_single($hook, $args, $timestamp, $group)

Schedule a one-time action.

**Parameters:**
- `$hook` (string) - Action hook name
- `$args` (array) - Arguments to pass to the hook
- `$timestamp` (int) - Unix timestamp when to run (default: now)
- `$group` (string) - Group name for organization (default: 'wpseed')

**Returns:** (int) Action ID

### schedule_recurring($hook, $interval, $args, $timestamp, $group)

Schedule a recurring action.

**Parameters:**
- `$hook` (string) - Action hook name
- `$interval` (int) - Seconds between runs
- `$args` (array) - Arguments to pass to the hook
- `$timestamp` (int) - Unix timestamp for first run (default: now)
- `$group` (string) - Group name (default: 'wpseed')

**Returns:** (int) Action ID

### schedule_cron($hook, $cron_expression, $args, $timestamp, $group)

Schedule using cron expression.

**Parameters:**
- `$hook` (string) - Action hook name
- `$cron_expression` (string) - Cron expression (e.g., '0 3 * * *')
- `$args` (array) - Arguments to pass to the hook
- `$timestamp` (int) - Unix timestamp for first run (default: now)
- `$group` (string) - Group name (default: 'wpseed')

**Returns:** (int) Action ID

### unschedule($hook, $args, $group)

Unschedule a specific action.

### unschedule_all($hook, $args, $group)

Unschedule all actions for a hook.

### is_scheduled($hook, $args, $group)

Check if an action is scheduled.

**Returns:** (bool) True if scheduled

### next_scheduled($hook, $args, $group)

Get next scheduled time for an action.

**Returns:** (int|false) Unix timestamp or false

### get_scheduled($args)

Get list of scheduled actions.

**Parameters:**
- `$args` (array) - Query arguments
  - `group` (string) - Filter by group
  - `status` (string) - Filter by status (pending, complete, failed)
  - `per_page` (int) - Number of results

**Returns:** (array) Array of ActionScheduler_Action objects

## Common Use Cases

### 1. Email Queue

```php
// Schedule email
$scheduler->schedule_single('send_email', array(
    'to' => 'user@example.com',
    'subject' => 'Welcome!',
    'message' => 'Thanks for signing up!'
));

// Process email
add_action('send_email', function($to, $subject, $message) {
    wp_mail($to, $subject, $message);
}, 10, 3);
```

### 2. Data Import

```php
// Schedule import in batches
$items = range(1, 1000);
$batches = array_chunk($items, 50);

foreach ($batches as $index => $batch) {
    $scheduler->schedule_single(
        'import_batch',
        array('items' => $batch),
        time() + ($index * 60) // Stagger by 1 minute
    );
}
```

### 3. Cleanup Tasks

```php
// Daily cleanup at 2am
$scheduler->schedule_cron(
    'daily_cleanup',
    '0 2 * * *',
    array()
);

add_action('daily_cleanup', function() {
    // Delete old logs
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->prefix}wpseed_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
});
```

### 4. API Sync

```php
// Sync every 15 minutes
$scheduler->schedule_recurring(
    'api_sync',
    15 * MINUTE_IN_SECONDS,
    array('endpoint' => 'users')
);

add_action('api_sync', function($endpoint) {
    // Fetch from API
    $response = wp_remote_get("https://api.example.com/{$endpoint}");
    // Process response
});
```

## Migration from WPSeed_Background_Process

### Old Way

```php
class My_Process extends WPSeed_Background_Process {
    protected $action = 'my_process';
    
    protected function task($item) {
        // Process item
        return false;
    }
}

$process = new My_Process();
$process->push_to_queue(array('id' => 1));
$process->save()->dispatch();
```

### New Way

```php
$scheduler = WPSeed_Task_Scheduler::instance();

$scheduler->schedule_single(
    'my_process_task',
    array('id' => 1)
);

add_action('my_process_task', function($id) {
    // Process item
});
```

## Admin Interface

Action Scheduler includes a built-in admin interface:

**WordPress Admin → Tools → Scheduled Actions**

Features:
- View all scheduled actions
- Filter by status (pending, complete, failed, canceled)
- Search by hook name
- View action details and arguments
- Cancel pending actions
- View execution logs

## Best Practices

### 1. Use Unique Hook Names

```php
// ❌ Bad - generic name
$scheduler->schedule_single('process_data', $args);

// ✅ Good - specific name
$scheduler->schedule_single('wpseed_process_user_data', $args);
```

### 2. Check Before Scheduling

```php
// Avoid duplicate scheduling
if (!$scheduler->is_scheduled('my_task', $args)) {
    $scheduler->schedule_single('my_task', $args);
}
```

### 3. Use Groups for Organization

```php
// Group related tasks
$scheduler->schedule_single('task1', $args, time(), 'email_queue');
$scheduler->schedule_single('task2', $args, time(), 'data_import');
$scheduler->schedule_single('task3', $args, time(), 'cleanup');
```

### 4. Handle Errors Gracefully

```php
add_action('my_task', function($data) {
    try {
        // Your code
    } catch (Exception $e) {
        error_log('Task failed: ' . $e->getMessage());
        // Action Scheduler will mark as failed
    }
});
```

### 5. Batch Large Operations

```php
// Process 1000 items in batches of 50
$items = range(1, 1000);
$batches = array_chunk($items, 50);

foreach ($batches as $batch) {
    $scheduler->schedule_single('process_batch', array('items' => $batch));
}
```

## Cron Expression Examples

```php
'* * * * *'      // Every minute
'0 * * * *'      // Every hour
'0 0 * * *'      // Daily at midnight
'0 2 * * *'      // Daily at 2am
'0 0 * * 0'      // Weekly on Sunday
'0 0 1 * *'      // Monthly on 1st
'*/15 * * * *'   // Every 15 minutes
'0 */6 * * *'    // Every 6 hours
'0 9-17 * * *'   // Every hour from 9am-5pm
```

## Troubleshooting

### Tasks Not Running

1. **Check WP-Cron**: Visit `yoursite.com/wp-cron.php`
2. **Check Scheduled Actions**: WordPress Admin → Tools → Scheduled Actions
3. **Enable Debug Mode**: Add to wp-config.php:
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   ```

### High Server Load

- Reduce batch sizes
- Increase delay between tasks
- Use cron expressions for off-peak hours

### Failed Actions

- Check error logs: `wp-content/debug.log`
- View failed actions in admin interface
- Add error handling to your action callbacks

## Resources

- **Action Scheduler Docs**: https://actionscheduler.org/
- **GitHub**: https://github.com/woocommerce/action-scheduler
- **WPSeed Examples**: `/examples/task-scheduler-examples.php`

## Support

For issues specific to WPSeed's Action Scheduler integration:
- GitHub Issues: https://github.com/ryanbayne/wpseed/issues
- Documentation: https://github.com/ryanbayne/wpseed/wiki

# Advanced Features Usage Guide

## Background Processing System

Process large tasks in the background without blocking requests.

### Basic Usage

```php
// Create your background process class
class My_Background_Process extends WPSeed_Background_Process {
    protected $action = 'my_process';
    
    protected function task( $item ) {
        // Process item
        // Return false to remove from queue
        return false;
    }
}

// Use it
$process = new My_Background_Process();
$process->push_to_queue( array( 'id' => 1 ) );
$process->push_to_queue( array( 'id' => 2 ) );
$process->save()->dispatch();
```

### Advanced Features

- Automatic memory and time limit management
- Cron healthcheck for reliability
- Queue persistence across requests
- Multisite compatible

## Object Registry

Store and access objects globally without using globals.

### Usage

```php
// Store object
$my_object = new My_Class();
WPSeed_Object_Registry::add( 'my_object', $my_object );

// Retrieve object
$obj = WPSeed_Object_Registry::get( 'my_object' );

// Update object property
WPSeed_Object_Registry::update_var( 'my_object', 'property', 'new_value' );

// Check existence
if ( WPSeed_Object_Registry::exists( 'my_object' ) ) {
    // Object exists
}

// Remove object
WPSeed_Object_Registry::remove( 'my_object' );
```

## Data Freshness Manager

Validate cache freshness and ensure data quality.

### Basic Usage

```php
// Check if data is fresh
$validation = WPSeed_Data_Freshness_Manager::validate_freshness( 
    'my_cache_key', 
    'hourly' 
);

if ( $validation['needs_update'] ) {
    // Fetch fresh data
}

// Store fresh data
WPSeed_Data_Freshness_Manager::set_fresh_data( 
    'my_cache_key', 
    $data, 
    3600 
);

// Get fresh data
$data = WPSeed_Data_Freshness_Manager::get_fresh_data( 'my_cache_key' );
```

### Ensure Freshness with Callback

```php
$data = WPSeed_Data_Freshness_Manager::ensure_freshness( 
    'my_cache_key',
    'hourly',
    function() {
        // Fetch data if stale
        return fetch_data_from_api();
    }
);
```

### Freshness Requirements

- `realtime`: 60 seconds
- `hourly`: 3600 seconds (1 hour)
- `daily`: 86400 seconds (24 hours)
- `default`: 3600 seconds

## Developer Flow Logger

Track decision flows for debugging (developer mode only).

### Usage

```php
// Start flow
WPSeed_Developer_Flow_Logger::start_flow( 'data_processing', 'Processing user data' );

// Log decision
WPSeed_Developer_Flow_Logger::log_decision( 
    'Check cache', 
    'HIT', 
    'Found cached data', 
    array( 'age' => 300 ) 
);

// Log action
WPSeed_Developer_Flow_Logger::log_action( 
    'Fetch API', 
    'Calling external API', 
    array( 'endpoint' => '/users' ) 
);

// Log cache operation
WPSeed_Developer_Flow_Logger::log_cache( 
    'GET', 
    'user_123', 
    'HIT', 
    array( 'ttl' => 3600 ) 
);

// End flow
WPSeed_Developer_Flow_Logger::end_flow( 'Success', null );
```

### Output

Displays detailed flow breakdown with:
- Step-by-step execution
- Timing information
- Memory usage
- Decision points
- Data context

## Integration Examples

### Background Processing + Object Registry

```php
class Data_Import_Process extends WPSeed_Background_Process {
    protected $action = 'data_import';
    
    protected function task( $item ) {
        // Get shared object
        $importer = WPSeed_Object_Registry::get( 'data_importer' );
        
        // Process item
        $importer->import( $item );
        
        return false;
    }
}
```

### Data Freshness + Flow Logger

```php
WPSeed_Developer_Flow_Logger::start_flow( 'api_request' );

$data = WPSeed_Data_Freshness_Manager::ensure_freshness(
    'api_data',
    'hourly',
    function() {
        WPSeed_Developer_Flow_Logger::log_action( 'API Call', 'Fetching fresh data' );
        return fetch_from_api();
    }
);

WPSeed_Developer_Flow_Logger::end_flow( 'Data retrieved' );
```

## Best Practices

1. **Background Processing**
   - Use for tasks taking > 5 seconds
   - Keep task() method lightweight
   - Return false to remove completed items

2. **Object Registry**
   - Use for shared objects across requests
   - Avoid storing large data sets
   - Clean up when done

3. **Data Freshness**
   - Choose appropriate freshness requirements
   - Use callbacks for automatic refresh
   - Consider API rate limits

4. **Flow Logger**
   - Only active in developer mode
   - Log key decision points
   - Include relevant context data

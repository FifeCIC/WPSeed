# Duplicate Classes Analysis & Resolution

## Issue Summary
WPSeed has duplicate background processing implementations that need consolidation:

1. **Original classes** in `/includes/classes/`
2. **Library versions** in `/includes/libraries/`
3. **Action Scheduler** (recommended replacement) already bundled

## Duplicate Files Identified

### Async Request
- `includes/classes/async-request.php` (Original)
- `includes/libraries/library.async-request.php` (Library version)
- `includes/libraries/action-scheduler/lib/WP_Async_Request.php` (Action Scheduler's version)

### Background Process
- `includes/classes/background-process.php` (Original)
- `includes/libraries/library.background-process.php` (Library version)

## Current Usage

### Files Using These Classes
1. `loader.php` - Loads original classes
2. `examples/background-process-example.php` - Example using WPSeed_Background_Process
3. `examples/task-scheduler-examples.php` - References both systems

## Recommended Solution

### Keep Action Scheduler (Primary)
- Already bundled and loaded
- Battle-tested (used by WooCommerce)
- Better performance and reliability
- Active maintenance

### Remove Duplicates
1. Delete `includes/classes/async-request.php`
2. Delete `includes/classes/background-process.php`
3. Keep library versions as legacy fallback (optional)
4. Update loader.php to use Action Scheduler

### Migration Path
- Update examples to use Action Scheduler
- Add migration guide in documentation
- Deprecate old classes with notices

## Implementation Steps

1. Remove loader.php includes for old classes
2. Update background-process-example.php to use Action Scheduler
3. Add deprecation notices if keeping library versions
4. Update THIRD-PARTY-LIBRARIES.md with migration guide
5. Test all background processing features

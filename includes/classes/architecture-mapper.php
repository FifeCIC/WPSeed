<?php
/**
 * WPSeed Architecture Mapper
 * 
 * Visual guide to plugin structure for developers and AI
 * 
 * @package WPSeed/Development
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_Architecture_Mapper {
    
    public static function get_architecture_map() {
        return array(
            'core_systems' => array(
                'name' => 'Core Systems',
                'type' => 'system',
                'children' => array(
                    'asset_system' => array(
                        'name' => 'Asset Management System',
                        'type' => 'system',
                        'file' => 'includes/classes/asset-manager.php',
                        'description' => 'Centralized asset loading with metadata tracking',
                        'children' => array(
                            'asset_manager' => array(
                                'name' => 'WPSeed_Asset_Manager',
                                'type' => 'class',
                                'file' => 'includes/classes/asset-manager.php',
                                'methods' => array('enqueue_assets', 'get_all_assets', 'asset_exists', 'get_missing_assets'),
                                'usage_pattern' => 'Automatically enqueues assets based on current page',
                                'purpose' => 'Manages all CSS/JS with automatic page-based loading'
                            ),
                            'css_registry' => array(
                                'name' => 'CSS Registry',
                                'type' => 'registry',
                                'file' => 'assets/css-registry.php',
                                'purpose' => 'Defines all CSS assets with metadata (path, purpose, pages, dependencies)'
                            ),
                            'js_registry' => array(
                                'name' => 'JS Registry',
                                'type' => 'registry',
                                'file' => 'assets/js-registry.php',
                                'purpose' => 'Defines all JS assets with metadata (path, purpose, pages, dependencies)'
                            )
                        )
                    ),
                    'ai_system' => array(
                        'name' => 'AI Integration System',
                        'type' => 'system',
                        'file' => 'includes/ai-system/',
                        'description' => 'Multi-provider AI assistant with usage tracking',
                        'children' => array(
                            'ai_assistant' => array(
                                'name' => 'WPSeed_AI_Assistant',
                                'type' => 'class',
                                'file' => 'includes/ai-system/ai-assistant.php',
                                'methods' => array('process_request', 'get_conversation_history'),
                                'purpose' => 'Main AI interface for code generation and debugging'
                            ),
                            'provider_factory' => array(
                                'name' => 'WPSeed_AI_Provider_Factory',
                                'type' => 'factory',
                                'file' => 'includes/ai-system/ai-provider-factory.php',
                                'methods' => array('create_provider'),
                                'usage_pattern' => 'Creates AI provider instances (Amazon Q, Gemini)',
                                'supported_providers' => array('amazonq', 'gemini')
                            ),
                            'usage_tracker' => array(
                                'name' => 'WPSeed_AI_Usage_Tracker',
                                'type' => 'class',
                                'file' => 'includes/ai-system/ai-usage-tracker.php',
                                'methods' => array('track_request', 'get_usage_stats'),
                                'tables' => array('wpseed_ai_usage'),
                                'purpose' => 'Tracks AI API usage and costs'
                            )
                        )
                    ),
                    'api_system' => array(
                        'name' => 'API Client System',
                        'type' => 'system',
                        'file' => 'api/',
                        'description' => 'Generic API client architecture',
                        'children' => array(
                            'base_api' => array(
                                'name' => 'WPSeed_Base_API',
                                'type' => 'class',
                                'file' => 'api/base-api.php',
                                'methods' => array('make_request', 'get', 'post', 'handle_response'),
                                'usage_pattern' => 'Extend this class for custom API integrations',
                                'purpose' => 'Base class for all API clients with error handling'
                            ),
                            'api_factory' => array(
                                'name' => 'WPSeed_API_Factory',
                                'type' => 'factory',
                                'file' => 'api/api-factory.php',
                                'methods' => array('create'),
                                'usage_pattern' => '$api = WPSeed_API_Factory::create("provider_name", $args);'
                            ),
                            'api_logging' => array(
                                'name' => 'API Logging',
                                'type' => 'component',
                                'file' => 'includes/api-logging.php',
                                'tables' => array('wpseed_api_calls', 'wpseed_api_errors'),
                                'purpose' => 'Logs all API requests and responses'
                            )
                        )
                    ),
                    'rest_api' => array(
                        'name' => 'REST API Framework',
                        'type' => 'system',
                        'file' => 'includes/classes/rest-controller.php',
                        'description' => 'Secure REST API endpoints',
                        'children' => array(
                            'rest_controller' => array(
                                'name' => 'WPSeed_REST_Controller',
                                'type' => 'class',
                                'file' => 'includes/classes/rest-controller.php',
                                'methods' => array('register_routes', 'get_items_permissions_check'),
                                'usage_pattern' => 'Extend for custom endpoints - secure by default',
                                'namespace' => 'wpseed/v1',
                                'security' => 'Requires manage_options capability by default'
                            ),
                            'rest_example' => array(
                                'name' => 'WPSeed_REST_Example_Controller',
                                'type' => 'class',
                                'file' => 'includes/classes/rest-example.php',
                                'extends' => 'WPSeed_REST_Controller',
                                'endpoint' => '/wp-json/wpseed/v1/example'
                            )
                        )
                    ),
                    'ecosystem_framework' => array(
                        'name' => 'Plugin Ecosystem Framework',
                        'type' => 'system',
                        'file' => 'includes/classes/ecosystem-registry.php',
                        'description' => 'Inter-plugin communication and shared resources',
                        'children' => array(
                            'ecosystem_registry' => array(
                                'name' => 'WPSeed_Ecosystem_Registry',
                                'type' => 'class',
                                'file' => 'includes/classes/ecosystem-registry.php',
                                'methods' => array('register_plugin', 'get_plugins', 'is_ecosystem_mode'),
                                'purpose' => 'Detects and manages multiple Ryan Bayne plugins'
                            ),
                            'menu_manager' => array(
                                'name' => 'WPSeed_Ecosystem_Menu_Manager',
                                'type' => 'class',
                                'file' => 'includes/classes/ecosystem-menu-manager.php',
                                'purpose' => 'Dynamic menu placement - single plugin vs ecosystem mode',
                                'behavior' => 'Moves shared views to Tools/Settings when 2+ plugins detected'
                            ),
                            'installer' => array(
                                'name' => 'WPSeed_Ecosystem_Installer',
                                'type' => 'class',
                                'file' => 'includes/classes/ecosystem-installer.php',
                                'purpose' => 'One-click installation of related plugins'
                            )
                        )
                    ),
                    'logging_system' => array(
                        'name' => 'Logging System',
                        'type' => 'system',
                        'file' => 'includes/logging-helper.php',
                        'description' => 'File and database logging',
                        'children' => array(
                            'file_logging' => array(
                                'name' => 'File Logging',
                                'type' => 'component',
                                'file' => 'includes/logging-helper.php',
                                'functions' => array('wpseed_log', 'wpseed_log_error'),
                                'log_location' => 'wp-content/uploads/wpseed-logs/'
                            ),
                            'database_logging' => array(
                                'name' => 'Database Logging',
                                'type' => 'component',
                                'tables' => array('wpseed_logs'),
                                'purpose' => 'Persistent logging with filtering and search'
                            )
                        )
                    ),
                    'background_processing' => array(
                        'name' => 'Background Processing System',
                        'type' => 'system',
                        'file' => 'includes/classes/background-process.php',
                        'description' => 'Queue system for long-running tasks',
                        'children' => array(
                            'async_request' => array(
                                'name' => 'WPSeed_Async_Request',
                                'type' => 'class',
                                'file' => 'includes/classes/async-request.php',
                                'methods' => array('dispatch', 'handle'),
                                'purpose' => 'Base class for async operations'
                            ),
                            'background_process' => array(
                                'name' => 'WPSeed_Background_Process',
                                'type' => 'class',
                                'file' => 'includes/classes/background-process.php',
                                'methods' => array('push_to_queue', 'save', 'dispatch', 'task'),
                                'usage_pattern' => 'Extend and implement task() method',
                                'purpose' => 'Process large tasks without blocking requests'
                            )
                        )
                    ),
                    'object_registry' => array(
                        'name' => 'Object Registry',
                        'type' => 'system',
                        'file' => 'includes/classes/object-registry.php',
                        'description' => 'Global object access without globals',
                        'children' => array(
                            'registry' => array(
                                'name' => 'WPSeed_Object_Registry',
                                'type' => 'class',
                                'file' => 'includes/classes/object-registry.php',
                                'methods' => array('add', 'get', 'update_var', 'remove', 'exists'),
                                'usage_pattern' => 'WPSeed_Object_Registry::add("key", $object);',
                                'purpose' => 'Store and retrieve objects globally'
                            )
                        )
                    ),
                    'data_freshness' => array(
                        'name' => 'Data Freshness Manager',
                        'type' => 'system',
                        'file' => 'includes/classes/data-freshness-manager.php',
                        'description' => 'Cache validation and auto-refresh',
                        'children' => array(
                            'freshness_manager' => array(
                                'name' => 'WPSeed_Data_Freshness_Manager',
                                'type' => 'class',
                                'file' => 'includes/classes/data-freshness-manager.php',
                                'methods' => array('validate_freshness', 'ensure_freshness', 'set_fresh_data'),
                                'usage_pattern' => 'ensure_freshness("key", "hourly", $callback);',
                                'purpose' => 'Ensure data quality with automatic refresh'
                            )
                        )
                    ),
                    'developer_flow_logger' => array(
                        'name' => 'Developer Flow Logger',
                        'type' => 'system',
                        'file' => 'includes/classes/developer-flow-logger.php',
                        'description' => 'Detailed decision tracking for debugging',
                        'children' => array(
                            'flow_logger' => array(
                                'name' => 'WPSeed_Developer_Flow_Logger',
                                'type' => 'class',
                                'file' => 'includes/classes/developer-flow-logger.php',
                                'methods' => array('start_flow', 'log_decision', 'log_action', 'log_cache', 'end_flow'),
                                'usage_pattern' => 'Developer mode only - tracks execution flows',
                                'purpose' => 'Visual flow breakdown with timing and memory'
                            )
                        )
                    )
                )
            ),
            'admin_pages' => array(
                'name' => 'Admin Pages',
                'type' => 'system',
                'children' => array(
                    'development_page' => array(
                        'name' => 'Development Page',
                        'type' => 'page',
                        'file' => 'admin/page/development/development-tabs.php',
                        'menu_slug' => 'wpseed-development',
                        'children' => array(
                            'assets_tab' => array(
                                'name' => 'Assets Tab',
                                'type' => 'tab',
                                'file' => 'admin/page/development/view/assets-tracker.php',
                                'purpose' => 'Track all CSS/JS assets with found/missing status'
                            ),
                            'theme_tab' => array(
                                'name' => 'Theme Tab',
                                'type' => 'tab',
                                'purpose' => 'View active theme info and template hierarchy'
                            ),
                            'debug_log_tab' => array(
                                'name' => 'Debug Log Tab',
                                'type' => 'tab',
                                'purpose' => 'View WordPress debug.log with filtering'
                            ),
                            'database_tab' => array(
                                'name' => 'Database Tab',
                                'type' => 'tab',
                                'purpose' => 'Inspect tables, run queries, optimize database'
                            ),
                            'php_info_tab' => array(
                                'name' => 'PHP Info Tab',
                                'type' => 'tab',
                                'purpose' => 'Server configuration and PHP settings'
                            ),
                            'ai_assistant_tab' => array(
                                'name' => 'AI Assistant Tab',
                                'type' => 'tab',
                                'purpose' => 'Chat with AI for code help and debugging'
                            ),
                            'dev_checklist_tab' => array(
                                'name' => 'Dev Checklist Tab',
                                'type' => 'tab',
                                'purpose' => 'Pre-release checklist with industry tools'
                            ),
                            'tasks_tab' => array(
                                'name' => 'Tasks Tab',
                                'type' => 'tab',
                                'file' => 'admin/page/development/view/tasks.php',
                                'purpose' => 'GitHub issues integration for task management'
                            ),
                            'layouts_tab' => array(
                                'name' => 'Layouts Tab',
                                'type' => 'tab',
                                'file' => 'admin/page/development/view/layouts.php',
                                'purpose' => 'Visual layout examples and CSS reference'
                            ),
                            'diagrams_tab' => array(
                                'name' => 'Diagrams Tab',
                                'type' => 'tab',
                                'file' => 'admin/page/development/view/diagrams.php',
                                'purpose' => 'Interactive Mermaid.js system diagrams'
                            ),
                            'architecture_tab' => array(
                                'name' => 'Architecture Tab',
                                'type' => 'tab',
                                'file' => 'admin/page/development/view/architecture.php',
                                'purpose' => 'Visual plugin structure for developers and AI'
                            )
                        )
                    ),
                    'settings_page' => array(
                        'name' => 'Settings Page',
                        'type' => 'page',
                        'file' => 'includes/admin/settings/',
                        'menu_slug' => 'wpseed-settings',
                        'children' => array(
                            'general_tab' => array(
                                'name' => 'General Tab',
                                'type' => 'tab',
                                'purpose' => 'General plugin settings'
                            ),
                            'ai_tab' => array(
                                'name' => 'AI Tab',
                                'type' => 'tab',
                                'purpose' => 'AI provider configuration (Amazon Q, Gemini)'
                            )
                        )
                    ),
                    'github_sync' => array(
                        'name' => 'GitHub Sync',
                        'type' => 'page',
                        'file' => 'includes/classes/github-sync.php',
                        'menu_slug' => 'wpseed-github-sync',
                        'purpose' => 'Sync documentation to GitHub repository',
                        'developer_only' => true
                    ),
                    'learning_centre' => array(
                        'name' => 'Learning Centre',
                        'type' => 'page',
                        'file' => 'includes/classes/education.php',
                        'menu_slug' => 'wpseed-learning',
                        'purpose' => 'Database-driven lessons with REST API export',
                        'tables' => array('wpseed_lessons')
                    )
                )
            ),
            'wordpress_features' => array(
                'name' => 'WordPress Features',
                'type' => 'system',
                'children' => array(
                    'custom_post_types' => array(
                        'name' => 'Custom Post Types',
                        'type' => 'feature',
                        'file' => 'includes/classes/install.php',
                        'example' => 'wpseed_example_cpt',
                        'purpose' => 'Example CPT implementation'
                    ),
                    'taxonomies' => array(
                        'name' => 'Taxonomies',
                        'type' => 'feature',
                        'file' => 'includes/classes/install.php',
                        'example' => 'wpseed_example_taxonomy',
                        'purpose' => 'Example taxonomy implementation'
                    ),
                    'shortcodes' => array(
                        'name' => 'Shortcodes',
                        'type' => 'feature',
                        'file' => 'shortcodes/shortcodes.php',
                        'example' => '[wpseed_example]',
                        'purpose' => 'Template-based shortcode system'
                    ),
                    'user_roles' => array(
                        'name' => 'User Roles',
                        'type' => 'feature',
                        'file' => 'includes/classes/install.php',
                        'example' => 'wpseed_custom_role',
                        'purpose' => 'Custom role and capability management'
                    )
                )
            ),
            'developer_tools' => array(
                'name' => 'Developer Tools',
                'type' => 'system',
                'children' => array(
                    'wp_cli' => array(
                        'name' => 'WP-CLI Commands',
                        'type' => 'tool',
                        'file' => 'includes/classes/cli-commands.php',
                        'commands' => array('wp wpseed info', 'wp wpseed cache clear'),
                        'usage_pattern' => 'Run from command line for plugin management'
                    ),
                    'developer_mode' => array(
                        'name' => 'Developer Mode',
                        'type' => 'tool',
                        'file' => 'includes/classes/developer-mode.php',
                        'purpose' => 'Detects localhost/dev environments to show dev-only features',
                        'detection' => 'Checks for localhost, custom domains, or WPSEED_DEV_MODE constant'
                    ),
                    'uninstall_feedback' => array(
                        'name' => 'Uninstall Feedback',
                        'type' => 'tool',
                        'file' => 'includes/classes/uninstall-feedback.php',
                        'purpose' => 'Collects user feedback on plugin deactivation',
                        'tables' => array('wpseed_uninstall_feedback')
                    ),
                    'dependencies_checker' => array(
                        'name' => 'Dependencies Checker',
                        'type' => 'tool',
                        'file' => 'includes/classes/dependencies.php',
                        'purpose' => 'Checks for required plugins and PHP extensions'
                    ),
                    'multisite_support' => array(
                        'name' => 'Multisite Support',
                        'type' => 'tool',
                        'file' => 'includes/classes/multisite.php',
                        'purpose' => 'Network activation detection and site-specific helpers'
                    )
                )
            ),
            'testing_framework' => array(
                'name' => 'Testing Framework',
                'type' => 'system',
                'children' => array(
                    'phpunit' => array(
                        'name' => 'PHPUnit Tests',
                        'type' => 'testing',
                        'file' => 'tests/',
                        'purpose' => 'Unit testing framework with examples'
                    ),
                    'github_actions' => array(
                        'name' => 'GitHub Actions CI/CD',
                        'type' => 'testing',
                        'file' => '.github/workflows/',
                        'purpose' => 'Automated testing on push/PR'
                    )
                )
            )
        );
    }
    
    public static function render_tree() {
        $map = self::get_architecture_map();
        echo '<div class="wpseed-architecture-tree">';
        echo '<style>' . esc_html( self::get_tree_styles() ) . '</style>';
        echo wp_kses_post( self::render_tree_node($map, 0) );
        echo '</div>';
        echo '<script>' . esc_js( self::get_tree_scripts() ) . '</script>';
    }
    
    private static function render_tree_node($nodes, $level = 0) {
        $html = '';
        
        foreach ($nodes as $key => $node) {
            $type_class = 'tree-' . ($node['type'] ?? 'item');
            
            $html .= "<div class='tree-node {$type_class}' data-level='{$level}'>";
            $html .= "<div class='tree-item-header'>";
            
            if (isset($node['children'])) {
                $html .= "<span class='tree-toggle'>▼</span>";
            } else {
                $html .= "<span class='tree-spacer'></span>";
            }
            
            $icon = self::get_type_icon($node['type'] ?? 'item');
            $html .= "<span class='tree-icon'>{$icon}</span>";
            $html .= "<span class='tree-name'>{$node['name']}</span>";
            
            if (isset($node['file'])) {
                $html .= "<span class='tree-file'>{$node['file']}</span>";
            }
            
            $html .= "</div>";
            
            if (isset($node['description'])) {
                $html .= "<div class='tree-description'>{$node['description']}</div>";
            }
            
            if (isset($node['purpose'])) {
                $html .= "<div class='tree-purpose'><strong>Purpose:</strong> {$node['purpose']}</div>";
            }
            
            if (isset($node['methods'])) {
                $html .= "<div class='tree-methods'><strong>Methods:</strong> " . implode(', ', $node['methods']) . "</div>";
            }
            
            if (isset($node['usage_pattern'])) {
                $html .= "<div class='tree-usage'><strong>Usage:</strong> {$node['usage_pattern']}</div>";
            }
            
            if (isset($node['tables'])) {
                $html .= "<div class='tree-tables'><strong>Tables:</strong> " . implode(', ', $node['tables']) . "</div>";
            }
            
            if (isset($node['children'])) {
                $html .= "<div class='tree-children'>";
                $html .= self::render_tree_node($node['children'], $level + 1);
                $html .= "</div>";
            }
            
            $html .= "</div>";
        }
        
        return $html;
    }
    
    private static function get_type_icon($type) {
        $icons = array(
            'system' => '🏗️',
            'class' => '📦',
            'factory' => '🏭',
            'registry' => '📋',
            'page' => '📄',
            'tab' => '📑',
            'feature' => '⚙️',
            'tool' => '🔧',
            'testing' => '✅',
            'component' => '🧩'
        );
        return $icons[$type] ?? '📄';
    }
    
    private static function get_tree_styles() {
        return "
        .wpseed-architecture-tree {
            font-family: 'Courier New', monospace;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 20px;
        }
        .tree-node {
            margin: 2px 0;
            border-left: 2px solid transparent;
        }
        .tree-system { border-left-color: #007cba; }
        .tree-class { border-left-color: #00a32a; }
        .tree-factory { border-left-color: #8c44c4; }
        .tree-page { border-left-color: #ff6900; }
        .tree-tab { border-left-color: #ff9500; }
        .tree-item-header {
            display: flex;
            align-items: center;
            padding: 4px 8px;
            cursor: pointer;
            border-radius: 3px;
        }
        .tree-item-header:hover {
            background: #e8f4f8;
        }
        .tree-toggle {
            width: 16px;
            font-size: 12px;
            cursor: pointer;
            user-select: none;
        }
        .tree-spacer {
            width: 16px;
        }
        .tree-icon {
            margin: 0 8px;
            font-size: 16px;
        }
        .tree-name {
            font-weight: bold;
            color: #333;
        }
        .tree-file {
            margin-left: 12px;
            color: #666;
            font-size: 12px;
            font-style: italic;
        }
        .tree-description, .tree-purpose, .tree-methods, .tree-usage, .tree-tables {
            margin: 4px 0 4px 40px;
            font-size: 12px;
            color: #555;
        }
        .tree-children {
            margin-left: 20px;
        }
        .tree-node[data-level='0'] {
            margin: 10px 0;
            padding: 8px;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        ";
    }
    
    private static function get_tree_scripts() {
        return "
        document.addEventListener('DOMContentLoaded', function() {
            const toggles = document.querySelectorAll('.tree-toggle');
            toggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const node = this.closest('.tree-node');
                    const children = node.querySelector('.tree-children');
                    if (children) {
                        if (children.style.display === 'none') {
                            children.style.display = 'block';
                            this.textContent = '▼';
                        } else {
                            children.style.display = 'none';
                            this.textContent = '▶';
                        }
                    }
                });
            });
        });
        ";
    }
}

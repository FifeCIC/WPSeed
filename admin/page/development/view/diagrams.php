<?php
/**
 * WPSeed Development Diagrams View
 *
 * @package WPSeed/Admin/Views
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_Admin_Development_Diagrams {
    
    public static function output() {
        // Enqueue Mermaid.js
        wp_enqueue_script('mermaid', 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js', array(), '10.0.0', true);
        ?>
        <div class="wpseed-diagrams-container">
            <div class="diagrams-header">
                <h2><?php _e('WPSeed System Diagrams', 'wpseed'); ?></h2>
                <p><?php _e('Visual diagrams showing plugin architecture, data flow, and system processes.', 'wpseed'); ?></p>
            </div>
            
            <div class="diagram-controls">
                <select id="diagram-selector" class="regular-text">
                    <optgroup label="Architecture">
                        <option value="plugin-structure"><?php _e('Plugin Structure', 'wpseed'); ?></option>
                        <option value="class-hierarchy"><?php _e('Class Hierarchy', 'wpseed'); ?></option>
                        <option value="hook-system"><?php _e('Hook System', 'wpseed'); ?></option>
                    </optgroup>
                    <optgroup label="Data Flow">
                        <option value="data-flow"><?php _e('Data Flow Architecture', 'wpseed'); ?></option>
                        <option value="rest-api-flow"><?php _e('REST API Flow', 'wpseed'); ?></option>
                        <option value="admin-flow"><?php _e('Admin Interface Flow', 'wpseed'); ?></option>
                    </optgroup>
                    <optgroup label="Systems">
                        <option value="logging-system"><?php _e('Logging System', 'wpseed'); ?></option>
                        <option value="ai-integration"><?php _e('AI Integration', 'wpseed'); ?></option>
                        <option value="asset-management"><?php _e('Asset Management', 'wpseed'); ?></option>
                    </optgroup>
                </select>
                <button id="fullscreen-btn" class="button"><?php _e('Fullscreen', 'wpseed'); ?></button>
                <button id="export-btn" class="button"><?php _e('Export SVG', 'wpseed'); ?></button>
            </div>
            
            <div id="diagram-container" class="diagram-viewer">
                <div id="mermaid-diagram"></div>
            </div>
            
            <div class="diagram-info">
                <h3 id="diagram-title"></h3>
                <p id="diagram-description"></p>
            </div>
        </div>
        
        <style>
        .wpseed-diagrams-container { max-width: 100%; margin: 20px 0; }
        .diagrams-header { margin-bottom: 20px; }
        .diagram-controls { display: flex; gap: 10px; margin-bottom: 20px; align-items: center; }
        .diagram-viewer { border: 1px solid #ddd; border-radius: 4px; padding: 20px; background: #fff; min-height: 400px; overflow: auto; }
        #mermaid-diagram { text-align: center; }
        .diagram-info { margin-top: 20px; padding: 15px; background: #f9f9f9; border-left: 4px solid #0073aa; }
        .diagram-viewer.fullscreen { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 999999; background: white; border: none; border-radius: 0; }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            if (typeof mermaid === 'undefined') {
                console.error('Mermaid.js not loaded');
                return;
            }
            
            mermaid.initialize({ 
                startOnLoad: false,
                theme: 'default',
                flowchart: { useMaxWidth: true, htmlLabels: true },
                securityLevel: 'loose'
            });
            
            const diagrams = {
                'plugin-structure': {
                    title: 'Plugin Structure',
                    description: 'File and folder organization of WPSeed plugin',
                    mermaid: `
                        flowchart TD
                            A[wpseed/] --> B[includes/]
                            A --> C[admin/]
                            A --> D[assets/]
                            A --> E[api/]
                            A --> F[templates/]
                            
                            B --> G[classes/]
                            B --> H[functions/]
                            B --> I[ai-system/]
                            B --> J[admin/]
                            
                            C --> K[page/]
                            C --> L[settings/]
                            C --> M[notices/]
                            
                            D --> N[css/]
                            D --> O[js/]
                            D --> P[images/]
                            
                            style A fill:#e3f2fd
                            style B fill:#fff3e0
                            style C fill:#e8f5e8
                            style D fill:#f3e5f5
                    `
                },
                'class-hierarchy': {
                    title: 'Class Hierarchy',
                    description: 'Core classes and their relationships',
                    mermaid: `
                        flowchart TD
                            A[WordPressPluginSeed] --> B[WPSeed_Install]
                            A --> C[WPSeed_Logger]
                            A --> D[WPSeed_REST_Controller]
                            A --> E[WPSeed_AI_Assistant]
                            
                            D --> F[WPSeed_REST_Example]
                            D --> G[Custom REST Controllers]
                            
                            E --> H[WPSeed_AI_Provider_Factory]
                            H --> I[WPSeed_AI_Provider_Gemini]
                            H --> J[WPSeed_AI_Provider_AmazonQ]
                            
                            style A fill:#e3f2fd
                            style D fill:#fff3e0
                            style E fill:#e8f5e8
                    `
                },
                'hook-system': {
                    title: 'WordPress Hook System',
                    description: 'Actions and filters used throughout WPSeed',
                    mermaid: `
                        flowchart LR
                            A[WordPress Init] --> B[wpseed_loaded]
                            B --> C[wpseed_init]
                            C --> D[admin_menu]
                            C --> E[rest_api_init]
                            C --> F[wp_enqueue_scripts]
                            
                            D --> G[Add Admin Pages]
                            E --> H[Register REST Routes]
                            F --> I[Enqueue Assets]
                            
                            style A fill:#e3f2fd
                            style B fill:#fff3e0
                            style C fill:#e8f5e8
                    `
                },
                'data-flow': {
                    title: 'Data Flow Architecture',
                    description: 'How data flows through the plugin',
                    mermaid: `
                        flowchart TD
                            A[User Input] --> B[Sanitization]
                            B --> C[Validation]
                            C --> D[Processing]
                            D --> E[Database Storage]
                            E --> F[Cache Layer]
                            F --> G[Output]
                            
                            H[External API] --> I[API Client]
                            I --> J[Response Handler]
                            J --> E
                            
                            style A fill:#e3f2fd
                            style E fill:#fff3e0
                            style G fill:#e8f5e8
                    `
                },
                'rest-api-flow': {
                    title: 'REST API Flow',
                    description: 'Request handling in REST API endpoints',
                    mermaid: `
                        flowchart TD
                            A[REST Request] --> B[Route Matching]
                            B --> C[Permission Check]
                            C --> D{Authorized?}
                            D -->|Yes| E[Controller Method]
                            D -->|No| F[401 Unauthorized]
                            
                            E --> G[Process Request]
                            G --> H[Prepare Response]
                            H --> I[Return JSON]
                            
                            style A fill:#e3f2fd
                            style C fill:#fff3e0
                            style E fill:#e8f5e8
                            style F fill:#ffebee
                    `
                },
                'admin-flow': {
                    title: 'Admin Interface Flow',
                    description: 'Navigation and page rendering in admin',
                    mermaid: `
                        flowchart TD
                            A[Admin Menu] --> B[Development]
                            A --> C[Settings]
                            
                            B --> D[Assets Tab]
                            B --> E[Theme Tab]
                            B --> F[Debug Log Tab]
                            B --> G[Database Tab]
                            B --> H[AI Assistant Tab]
                            
                            C --> I[General Settings]
                            C --> J[GitHub Settings]
                            C --> K[Custom Settings]
                            
                            style A fill:#e3f2fd
                            style B fill:#fff3e0
                            style C fill:#e8f5e8
                    `
                },
                'logging-system': {
                    title: 'Logging System',
                    description: 'File-based and database logging architecture',
                    mermaid: `
                        flowchart TD
                            A[Event Occurs] --> B[WPSeed_Logger]
                            B --> C{Log Level}
                            C -->|Error| D[Error Log]
                            C -->|Info| E[Info Log]
                            C -->|Debug| F[Debug Log]
                            
                            D --> G[File: wpseed-errors.log]
                            E --> H[File: wpseed-info.log]
                            F --> I[File: wpseed-debug.log]
                            
                            B --> J[Database Logger]
                            J --> K[wp_wpseed_logs table]
                            
                            style A fill:#e3f2fd
                            style B fill:#fff3e0
                            style K fill:#e8f5e8
                    `
                },
                'ai-integration': {
                    title: 'AI Integration System',
                    description: 'AI provider management and request routing',
                    mermaid: `
                        flowchart TD
                            A[AI Request] --> B[WPSeed_AI_Assistant]
                            B --> C[WPSeed_AI_Router]
                            C --> D{Task Type}
                            D -->|Code Gen| E[Amazon Q]
                            D -->|Analysis| F[Gemini]
                            D -->|General| G[Default Provider]
                            
                            E --> H[Usage Tracker]
                            F --> H
                            G --> H
                            
                            H --> I{Rate Limit?}
                            I -->|OK| J[Return Response]
                            I -->|Exceeded| K[Fallback Provider]
                            K --> J
                            
                            style A fill:#e3f2fd
                            style C fill:#fff3e0
                            style H fill:#e8f5e8
                            style K fill:#ffebee
                    `
                },
                'asset-management': {
                    title: 'Asset Management System',
                    description: 'CSS/JS asset tracking and enqueueing',
                    mermaid: `
                        flowchart TD
                            A[Page Load] --> B[Asset Manager]
                            B --> C[Get Assets for Page]
                            C --> D[Check File Exists]
                            D --> E{File Found?}
                            E -->|Yes| F[Enqueue Asset]
                            E -->|No| G[Log Missing Asset]
                            
                            F --> H[wp_enqueue_script/style]
                            G --> I[Admin Notice]
                            
                            B --> J[Asset Tracker]
                            J --> K[Track Usage]
                            K --> L[Generate Report]
                            
                            style A fill:#e3f2fd
                            style B fill:#fff3e0
                            style F fill:#e8f5e8
                            style G fill:#ffebee
                    `
                }
            };
            
            function renderDiagram(diagramKey) {
                const diagram = diagrams[diagramKey];
                if (!diagram) return;
                
                $('#diagram-title').text(diagram.title);
                $('#diagram-description').text(diagram.description);
                
                const element = document.getElementById('mermaid-diagram');
                element.innerHTML = '';
                
                try {
                    mermaid.render('diagram-' + Date.now(), diagram.mermaid).then(function(result) {
                        element.innerHTML = result.svg;
                    }).catch(function(error) {
                        console.error('Mermaid render error:', error);
                        element.innerHTML = '<p>Error rendering diagram: ' + error.message + '</p>';
                    });
                } catch (error) {
                    console.error('Mermaid error:', error);
                    element.innerHTML = '<p>Error initializing diagram</p>';
                }
            }
            
            $('#diagram-selector').on('change', function() {
                renderDiagram($(this).val());
            });
            
            $('#fullscreen-btn').on('click', function() {
                $('.diagram-viewer').toggleClass('fullscreen');
                $(this).text($('.diagram-viewer').hasClass('fullscreen') ? 'Exit Fullscreen' : 'Fullscreen');
            });
            
            $('#export-btn').on('click', function() {
                const svg = $('#mermaid-diagram svg')[0];
                if (svg) {
                    const serializer = new XMLSerializer();
                    const svgString = serializer.serializeToString(svg);
                    const blob = new Blob([svgString], {type: 'image/svg+xml'});
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'wpseed-diagram.svg';
                    a.click();
                    URL.revokeObjectURL(url);
                }
            });
            
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $('.diagram-viewer').hasClass('fullscreen')) {
                    $('.diagram-viewer').removeClass('fullscreen');
                    $('#fullscreen-btn').text('Fullscreen');
                }
            });
            
            renderDiagram('plugin-structure');
        });
        </script>
        <?php
    }
}

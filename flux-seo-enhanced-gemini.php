<?php
/**
 * Plugin Name: Flux SEO Scribe Craft Enhanced with Gemini AI
 * Plugin URI: https://github.com/khiwniti/flux-seo-scribe-craft
 * Description: Professional SEO optimization suite with Google Gemini 2.5 Pro AI, Thai/English language support, and advanced analytics
 * Version: 2.5.0
 * Author: Flux SEO Team
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: flux-seo-scribe-craft-enhanced
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('FLUX_SEO_ENHANCED_URL', plugin_dir_url(__FILE__));
define('FLUX_SEO_ENHANCED_PATH', plugin_dir_path(__FILE__));
define('FLUX_SEO_ENHANCED_VERSION', '2.5.0');

// Include the keyword scoring engine and auto blog scheduler
require_once plugin_dir_path(__FILE__) . 'flux-seo-keyword-scoring-engine.php';
require_once plugin_dir_path(__FILE__) . 'flux-seo-auto-blog-scheduler.php';

/**
 * Main class for the Flux SEO Scribe Craft Enhanced plugin.
 * Handles initialization, settings, scripts, AJAX, and core functionality.
 */
class FluxSEOScribeCraftEnhanced {
    
    /**
     * Google Gemini API Key.
     *
     * @var string
     */
    private $gemini_api_key;

    /**
     * Google Gemini API Endpoint.
     *
     * @var string
     */
    private $gemini_endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent';

    /**
     * Instance of the keyword scoring engine.
     *
     * @var FluxSEOKeywordScoringEngine
     */
    private $keyword_scoring_engine;

    /**
     * Instance of the auto blog scheduler.
     *
     * @var FluxSEOAutoBlogScheduler
     */
    private $auto_blog_scheduler;
    
    /**
     * Constructor.
     * Initializes API key, and other modules, and sets up WordPress hooks.
     */
    public function __construct() {
        // Load API key from WordPress options
        $this->gemini_api_key = get_option('flux_seo_gemini_api_key', '');

        $this->keyword_scoring_engine = new FluxSEOKeywordScoringEngine();
        // Pass the API key to the scheduler. It might be better for the scheduler to fetch its own key from options
        // if it operates independently or via WP Cron not directly tied to user actions.
        $this->auto_blog_scheduler = new FluxSEOAutoBlogScheduler($this->gemini_api_key, $this->keyword_scoring_engine);

        add_action('plugins_loaded', array($this, 'init_plugin'));
    }
    
    /**
     * Initializes the plugin's core components and hooks.
     * This method is called once all plugins are loaded.
     */
    public function init_plugin() {
        // Ensure WordPress environment is loaded
        if (!function_exists('wp_get_current_user')) {
            return;
        }

        // Register setting for API key in WordPress admin
        add_action('admin_init', array($this, 'register_settings'));
        
        // General initialization hooks
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts')); // For frontend
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts')); // For admin area
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_shortcode('flux_seo_enhanced', array($this, 'shortcode_handler'));
        
        // AJAX handlers for plugin actions (both for logged-in and non-logged-in users, though nopriv might be restricted further)
        add_action('wp_ajax_flux_seo_enhanced_action', array($this, 'ajax_handler'));
        add_action('wp_ajax_nopriv_flux_seo_enhanced_action', array($this, 'ajax_handler')); // Consider if all actions should be available to non-logged-in users.
        
        // Hook for creating database tables upon plugin activation
        register_activation_hook(__FILE__, array($this, 'create_tables'));
    }

    /**
     * Registers plugin settings with WordPress.
     * Specifically, registers the 'flux_seo_gemini_api_key' option.
     */
    public function register_settings() {
        register_setting(
            'flux_seo_settings_group', // Option group
            'flux_seo_gemini_api_key',   // Option name
            array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field', // Sanitize input
                'default'           => ''
            )
        );
    }
    
    /**
     * Handles general plugin initialization tasks.
     * Loads text domain for localization and updates runtime API key.
     */
    public function init() {
        // Load plugin text domain for internationalization
        load_plugin_textdomain('flux-seo-scribe-craft-enhanced', false, dirname(plugin_basename(__FILE__)) . '/languages');

        // Ensure the API key is up-to-date if changed in settings during runtime
        $this->gemini_api_key = get_option('flux_seo_gemini_api_key', '');
    }
    
    /**
     * Creates necessary database tables upon plugin activation.
     * Uses dbDelta to create/update tables safely.
     */
    public function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Create keyword scoring tables
        $this->keyword_scoring_engine->create_keyword_scoring_tables();
        
        // Create auto blog tables
        $this->auto_blog_scheduler->create_auto_blog_table();
        
        // Enhanced analytics table
        $analytics_table = $wpdb->prefix . 'flux_seo_enhanced_analytics';
        $analytics_sql = "CREATE TABLE $analytics_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            url varchar(255) NOT NULL,
            language varchar(10) NOT NULL DEFAULT 'en',
            seo_score int(3),
            performance_score int(3),
            content_quality_score int(3),
            keyword_density_score int(3),
            technical_seo_score int(3),
            user_experience_score int(3),
            mobile_score int(3),
            page_speed_score int(3),
            security_score int(3),
            accessibility_score int(3),
            gemini_analysis longtext,
            recommendations longtext,
            keywords_analysis longtext,
            competitor_analysis longtext,
            content_suggestions longtext,
            technical_issues longtext,
            analysis_date datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY url (url),
            KEY language (language),
            KEY analysis_date (analysis_date)
        ) $charset_collate;";
        
        // Enhanced content generation table
        $content_table = $wpdb->prefix . 'flux_seo_enhanced_content';
        $content_sql = "CREATE TABLE $content_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            language varchar(10) NOT NULL DEFAULT 'en',
            topic varchar(255) NOT NULL,
            content_type varchar(50) NOT NULL,
            tone varchar(50) NOT NULL,
            target_audience varchar(100) NOT NULL,
            keywords text,
            generated_title text,
            generated_content longtext,
            generated_meta_description text,
            content_outline longtext,
            seo_recommendations longtext,
            gemini_analysis longtext,
            word_count int(5),
            seo_score int(3),
            readability_score int(3),
            engagement_score int(3),
            keyword_optimization_score int(3),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY language (language),
            KEY content_type (content_type)
        ) $charset_collate;";
        
        // Enhanced keyword scoring table
        $keyword_scoring_table = $wpdb->prefix . 'flux_seo_keyword_scoring';
        $keyword_scoring_sql = "CREATE TABLE $keyword_scoring_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            keyword varchar(255) NOT NULL,
            language varchar(10) NOT NULL DEFAULT 'en',
            search_volume int(10) DEFAULT 0,
            keyword_difficulty int(3) DEFAULT 0,
            relevance_score int(2) DEFAULT 0,
            user_intent varchar(50) DEFAULT 'informational',
            current_rank int(3) DEFAULT 0,
            ctr_potential int(2) DEFAULT 0,
            cpc_value decimal(10,2) DEFAULT 0.00,
            trend_direction varchar(20) DEFAULT 'stable',
            seasonality_score int(3) DEFAULT 0,
            local_volume int(10) DEFAULT 0,
            competition_analysis longtext,
            calculated_score decimal(4,2) DEFAULT 0.00,
            tier varchar(20) DEFAULT 'Tier 3',
            priority varchar(20) DEFAULT 'Medium',
            content_suggestions longtext,
            optimization_recommendations longtext,
            link_building_strategy longtext,
            last_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY keyword (keyword),
            KEY language (language),
            KEY calculated_score (calculated_score),
            KEY tier (tier),
            KEY last_updated (last_updated)
        ) $charset_collate;";
        
        // Keyword opportunities table
        $opportunities_table = $wpdb->prefix . 'flux_seo_keyword_opportunities';
        $opportunities_sql = "CREATE TABLE $opportunities_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            opportunity_type varchar(50) NOT NULL,
            keyword varchar(255) NOT NULL,
            language varchar(10) NOT NULL DEFAULT 'en',
            opportunity_score decimal(4,2) DEFAULT 0.00,
            estimated_traffic int(10) DEFAULT 0,
            difficulty_level varchar(20) DEFAULT 'Medium',
            time_to_rank varchar(50) DEFAULT '3-6 months',
            required_effort varchar(20) DEFAULT 'Medium',
            content_type varchar(50) DEFAULT 'Blog Post',
            target_audience varchar(100) DEFAULT 'General',
            competitive_landscape longtext,
            action_plan longtext,
            success_metrics longtext,
            roi_estimate varchar(50) DEFAULT 'Medium',
            status varchar(20) DEFAULT 'Identified',
            assigned_to varchar(100) DEFAULT '',
            due_date date DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY opportunity_type (opportunity_type),
            KEY keyword (keyword),
            KEY language (language),
            KEY opportunity_score (opportunity_score),
            KEY status (status)
        ) $charset_collate;";
        
        // Content strategy table
        $strategy_table = $wpdb->prefix . 'flux_seo_content_strategy';
        $strategy_sql = "CREATE TABLE $strategy_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            strategy_name varchar(255) NOT NULL,
            target_keywords longtext,
            content_calendar longtext,
            optimization_targets longtext,
            link_building_plan longtext,
            performance_metrics longtext,
            budget_allocation longtext,
            timeline varchar(100) DEFAULT '3 months',
            success_criteria longtext,
            risk_assessment longtext,
            competitive_analysis longtext,
            roi_projection longtext,
            status varchar(20) DEFAULT 'Draft',
            created_by bigint(20) NOT NULL,
            language varchar(10) NOT NULL DEFAULT 'en',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY strategy_name (strategy_name),
            KEY status (status),
            KEY created_by (created_by),
            KEY language (language)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($analytics_sql);
        dbDelta($content_sql);
        dbDelta($keyword_scoring_sql);
        dbDelta($opportunities_sql);
        dbDelta($strategy_sql);
    }
    
    /**
     * Enqueues scripts and styles for the frontend if the shortcode is present.
     */
    public function enqueue_scripts() {
        if (!is_admin() && $this->has_shortcode()) {
            $this->enqueue_app_assets();
        }
    }
    
    /**
     * Enqueues scripts and styles for the admin area on the plugin's page.
     *
     * @param string $hook The current admin page hook.
     */
    public function admin_enqueue_scripts($hook) {
        // Only load assets on our plugin's admin page
        if ($hook === 'toplevel_page_flux-seo-scribe-craft-enhanced') {
            $this->enqueue_app_assets();
        }
    }
    
    /**
     * Helper function to enqueue all common app assets (CSS, JS, React).
     * Used by both frontend and admin enqueue methods.
     */
    private function enqueue_app_assets() {
        // Enqueue React and ReactDOM
        wp_enqueue_script(
            'react',
            FLUX_SEO_ENHANCED_URL . 'react.production.min.js',
            array(),
            '18.3.1',
            false
        );
        
        wp_enqueue_script(
            'react-dom',
            FLUX_SEO_ENHANCED_URL . 'react-dom.production.min.js',
            array('react'),
            '18.3.1',
            false
        );
        
        // Enqueue enhanced CSS
        wp_enqueue_style(
            'flux-seo-enhanced-css',
            FLUX_SEO_ENHANCED_URL . 'flux-seo-enhanced-gemini.css',
            array(),
            FLUX_SEO_ENHANCED_VERSION
        );
        
        // Enqueue enhanced JavaScript
        wp_enqueue_script(
            'flux-seo-enhanced-js',
            FLUX_SEO_ENHANCED_URL . 'flux-seo-enhanced-gemini.js',
            array('jquery', 'react', 'react-dom'),
            FLUX_SEO_ENHANCED_VERSION,
            true
        );
        
        // Enqueue auto blog JavaScript
        wp_enqueue_script(
            'flux-seo-auto-blog-js',
            FLUX_SEO_ENHANCED_URL . 'flux-seo-auto-blog.js',
            array('jquery', 'flux-seo-enhanced-js'),
            FLUX_SEO_ENHANCED_VERSION,
            true
        );
        
        // Localize script with enhanced data
        wp_localize_script('flux-seo-enhanced-js', 'fluxSeoEnhanced', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('flux_seo_enhanced_nonce'),
            'pluginUrl' => FLUX_SEO_ENHANCED_URL,
            'isAdmin' => current_user_can('manage_options'),
            'geminiEnabled' => true,
            'strings' => $this->get_localized_strings()
        ));
    }
    
    /**
     * Provides localized strings for JavaScript.
     * Contains English and Thai translations for UI elements.
     *
     * @return array Associative array of strings keyed by language code.
     */
    private function get_localized_strings() {
        return array(
            'en' => array(
                'title' => 'Flux SEO Enhanced with Gemini AI',
                'subtitle' => 'Professional SEO optimization powered by Google Gemini 2.5 Pro',
                'languageSelector' => 'Language',
                'contentAnalyzer' => 'AI Content Analyzer',
                'blogGenerator' => 'AI Blog Generator',
                'advancedAnalytics' => 'Advanced Analytics',
                'seoOptimizer' => 'SEO Optimizer',
                'keywordResearch' => 'Keyword Research',
                'metaTags' => 'Meta Tags',
                'schema' => 'Schema Markup',
                'technical' => 'Technical SEO',
                'chatbot' => 'AI Chatbot',
                'settings' => 'Settings',
                'autoBlogScheduler' => 'Auto Blog Scheduler',
                'imageGenerator' => 'AI Image Generator',
                'competitorAnalysis' => 'Competitor Analysis',
                'technicalSeo' => 'Technical SEO',
                'contentStrategy' => 'Content Strategy',
                'performanceTracking' => 'Performance Tracking',
                'analyze' => 'Analyze with Gemini AI',
                'generate' => 'Generate with AI',
                'optimize' => 'Optimize',
                'analyzing' => 'Gemini AI is analyzing...',
                'generating' => 'Gemini AI is generating...',
                'seoScore' => 'SEO Score',
                'performanceScore' => 'Performance Score',
                'contentQuality' => 'Content Quality',
                'technicalScore' => 'Technical Score',
                'userExperience' => 'User Experience',
                'mobileScore' => 'Mobile Score',
                'pageSpeed' => 'Page Speed',
                'security' => 'Security',
                'accessibility' => 'Accessibility',
                'keywordDensity' => 'Keyword Density',
                'readability' => 'Readability',
                'engagement' => 'Engagement',
                'geminiInsights' => 'Gemini AI Insights',
                'recommendations' => 'AI Recommendations',
                'keywordAnalysis' => 'Keyword Analysis',
                'competitorInsights' => 'Competitor Insights',
                'contentSuggestions' => 'Content Suggestions',
                'technicalIssues' => 'Technical Issues',
                'topic' => 'Topic',
                'contentType' => 'Content Type',
                'tone' => 'Tone',
                'targetAudience' => 'Target Audience',
                'keywords' => 'Keywords',
                'wordCount' => 'Word Count',
                'websiteUrl' => 'Website URL',
                'blogPost' => 'Blog Post',
                'article' => 'Article',
                'guide' => 'How-to Guide',
                'review' => 'Review',
                'tutorial' => 'Tutorial',
                'professional' => 'Professional',
                'casual' => 'Casual',
                'formal' => 'Formal',
                'conversational' => 'Conversational',
                'technical' => 'Technical',
                'general' => 'General Audience',
                'business' => 'Business Professionals',
                'students' => 'Students',
                'experts' => 'Industry Experts',
                'consumers' => 'Consumers',
                'poweredByGemini' => 'Powered by Google Gemini 2.5 Pro'
            ),
            'th' => array(
                'title' => 'Flux SEO Enhanced ด้วย Gemini AI',
                'subtitle' => 'เครื่องมือ SEO มืออาชีพขับเคลื่อนด้วย Google Gemini 2.5 Pro',
                'languageSelector' => 'ภาษา',
                'contentAnalyzer' => 'วิเคราะห์เนื้อหาด้วย AI',
                'blogGenerator' => 'สร้างบล็อกด้วย AI',
                'advancedAnalytics' => 'การวิเคราะห์ขั้นสูง',
                'seoOptimizer' => 'ปรับปรุง SEO',
                'keywordResearch' => 'วิจัยคำหลัก',
                'metaTags' => 'แท็กเมตา',
                'schema' => 'Schema Markup',
                'technical' => 'SEO เชิงเทคนิค',
                'chatbot' => 'แชทบอท AI',
                'settings' => 'การตั้งค่า',
                'autoBlogScheduler' => 'ตั้งเวลาบล็อกอัตโนมัติ',
                'imageGenerator' => 'สร้างภาพด้วย AI',
                'competitorAnalysis' => 'วิเคราะห์คู่แข่ง',
                'technicalSeo' => 'SEO เชิงเทคนิค',
                'contentStrategy' => 'กลยุทธ์เนื้อหา',
                'performanceTracking' => 'ติดตามประสิทธิภาพ',
                'analyze' => 'วิเคราะห์ด้วย Gemini AI',
                'generate' => 'สร้างด้วย AI',
                'optimize' => 'ปรับปรุง',
                'analyzing' => 'Gemini AI กำลังวิเคราะห์...',
                'generating' => 'Gemini AI กำลังสร้าง...',
                'seoScore' => 'คะแนน SEO',
                'performanceScore' => 'คะแนนประสิทธิภาพ',
                'contentQuality' => 'คุณภาพเนื้อหา',
                'technicalScore' => 'คะแนนเทคนิค',
                'userExperience' => 'ประสบการณ์ผู้ใช้',
                'mobileScore' => 'คะแนนมือถือ',
                'pageSpeed' => 'ความเร็วหน้าเว็บ',
                'security' => 'ความปลอดภัย',
                'accessibility' => 'การเข้าถึง',
                'keywordDensity' => 'ความหนาแน่นคำหลัก',
                'readability' => 'ความเข้าใจง่าย',
                'engagement' => 'การมีส่วนร่วม',
                'geminiInsights' => 'ข้อมูลเชิงลึกจาก Gemini AI',
                'recommendations' => 'คำแนะนำจาก AI',
                'keywordAnalysis' => 'การวิเคราะห์คำหลัก',
                'competitorInsights' => 'ข้อมูลเชิงลึกคู่แข่ง',
                'contentSuggestions' => 'ข้อเสนอแนะเนื้อหา',
                'technicalIssues' => 'ปัญหาเทคนิค',
                'topic' => 'หัวข้อ',
                'contentType' => 'ประเภทเนื้อหา',
                'tone' => 'โทนเสียง',
                'targetAudience' => 'กลุ่มเป้าหมาย',
                'keywords' => 'คำหลัก',
                'wordCount' => 'จำนวนคำ',
                'websiteUrl' => 'URL เว็บไซต์',
                'blogPost' => 'บล็อกโพสต์',
                'article' => 'บทความ',
                'guide' => 'คู่มือวิธีทำ',
                'review' => 'รีวิว',
                'tutorial' => 'บทเรียน',
                'professional' => 'มืออาชีพ',
                'casual' => 'เป็นกันเอง',
                'formal' => 'เป็นทางการ',
                'conversational' => 'สนทนา',
                'technical' => 'เชิงเทคนิค',
                'general' => 'ผู้อ่านทั่วไป',
                'business' => 'นักธุรกิจ',
                'students' => 'นักเรียน นักศึกษา',
                'experts' => 'ผู้เชี่ยวชาญ',
                'consumers' => 'ผู้บริโภค',
                'poweredByGemini' => 'ขับเคลื่อนโดย Google Gemini 2.5 Pro'
            )
        );
    }
    
    /**
     * Checks if the current page/post contains the plugin's shortcode.
     *
     * @global WP_Post $post The global post object.
     * @return bool True if the shortcode is found, false otherwise.
     */
    private function has_shortcode() {
        global $post;
        return is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'flux_seo_enhanced');
    }
    
    /**
     * Adds the plugin's main menu page to the WordPress admin sidebar.
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Flux SEO Enhanced', 'flux-seo-scribe-craft-enhanced'), // Page title
            __('SEO Enhanced', 'flux-seo-scribe-craft-enhanced'),    // Menu title
            'manage_options',                                         // Capability required
            'flux-seo-scribe-craft-enhanced',                         // Menu slug
            array($this, 'admin_page'),                               // Callback function to display the page
            'dashicons-chart-line',                                   // Icon URL or dashicon class
            30                                                        // Position in the menu
        );
    }
    
    /**
     * Renders the admin page content.
     * This is the callback for the admin menu page.
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <div id="flux-seo-enhanced-app">
                <?php
                // The main application interface is rendered by render_app()
                echo $this->render_app();
                ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Handles the [flux_seo_enhanced] shortcode.
     * Renders the plugin's application interface on the frontend.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output for the shortcode.
     */
    public function shortcode_handler($atts) {
        // Define default attributes and merge with provided ones
        $atts = shortcode_atts(array(
            'height'   => '800px',    // Default height
            'width'    => '100%',     // Default width
            'language' => 'en'        // Default language
        ), $atts, 'flux_seo_enhanced');
        
        // Sanitize and prepare style attribute
        $style = sprintf('height: %s; width: %s;', esc_attr($atts['height']), esc_attr($atts['width']));
        
        // Return the HTML structure for the shortcode, including the rendered app
        return '<div id="flux-seo-enhanced-shortcode" style="' . $style . '" data-language="' . esc_attr($atts['language']) . '">' . $this->render_app() . '</div>';
    }
    
    /**
     * Renders the main HTML structure for the plugin's application interface.
     * This includes the language switcher, header, navigation tabs, and content areas.
     * Tab content is loaded dynamically from files in the 'tabs/' directory.
     *
     * @return string HTML output for the application.
     */
    private function render_app() {
        ob_start(); // Start output buffering to capture HTML
        ?>
        <div id="flux-seo-enhanced-container" class="flux-seo-enhanced-app">
            <!-- Language Switcher -->
            <div class="flux-seo-language-switcher">
                <label for="flux-seo-language-select">Language / ภาษา:</label>
                <select id="flux-seo-language-select" class="flux-seo-language-dropdown">
                    <option value="en">🇺🇸 English</option>
                    <option value="th">🇹🇭 ไทย</option>
                </select>
            </div>

            <!-- Enhanced Header -->
            <div class="flux-seo-header">
                <div class="flux-seo-header-content">
                    <div class="flux-seo-logo">
                        <div class="flux-seo-logo-icon">🤖</div>
                        <div class="flux-seo-logo-text">
                            <h1 id="flux-seo-title">Flux SEO Enhanced with Gemini AI</h1>
                            <p id="flux-seo-subtitle">Professional SEO optimization powered by Google Gemini 2.5 Pro</p>
                        </div>
                    </div>
                    <div class="flux-seo-features-badges">
                        <span class="flux-seo-badge">🤖 Gemini 2.5 Pro</span>
                        <span class="flux-seo-badge">🇹🇭 Thai AI</span>
                        <span class="flux-seo-badge">📊 Advanced Analytics</span>
                        <span class="flux-seo-badge">🎯 SEO Intelligence</span>
                    </div>
                </div>
            </div>

            <!-- Enhanced Navigation -->
            <div class="flux-seo-nav">
                <button class="flux-seo-nav-tab active" data-tab="analyzer">
                    <span class="flux-seo-nav-icon">🔍</span>
                    <span class="flux-seo-nav-text" data-key="contentAnalyzer">Analyzer</span>
                </button>
                <button class="flux-seo-nav-tab" data-tab="generator">
                    <span class="flux-seo-nav-icon">✨</span>
                    <span class="flux-seo-nav-text" data-key="blogGenerator">Generator</span>
                </button>
                <button class="flux-seo-nav-tab" data-tab="analytics">
                    <span class="flux-seo-nav-icon">📊</span>
                    <span class="flux-seo-nav-text" data-key="advancedAnalytics">Analytics</span>
                </button>
                <button class="flux-seo-nav-tab" data-tab="keywords">
                    <span class="flux-seo-nav-icon">🎯</span>
                    <span class="flux-seo-nav-text" data-key="keywordResearch">Keywords</span>
                </button>
                <button class="flux-seo-nav-tab" data-tab="meta">
                    <span class="flux-seo-nav-icon">🌐</span>
                    <span class="flux-seo-nav-text" data-key="metaTags">Meta Tags</span>
                </button>
                <button class="flux-seo-nav-tab" data-tab="schema">
                    <span class="flux-seo-nav-icon">📋</span>
                    <span class="flux-seo-nav-text" data-key="schemaMarkup">Schema</span>
                </button>
                <button class="flux-seo-nav-tab" data-tab="technical">
                    <span class="flux-seo-nav-icon">⚙️</span>
                    <span class="flux-seo-nav-text" data-key="technicalSEO">Technical</span>
                </button>
                <button class="flux-seo-nav-tab" data-tab="chatbot">
                    <span class="flux-seo-nav-icon">💬</span>
                    <span class="flux-seo-nav-text" data-key="chatbot">Chatbot</span>
                </button>
                <button class="flux-seo-nav-tab" data-tab="settings">
                    <span class="flux-seo-nav-icon">⚙️</span>
                    <span class="flux-seo-nav-text" data-key="settings">Settings</span>
                </button>
            </div>

            <!-- Content Area -->
            <div class="flux-seo-content">
                <?php
                // Include tab content files
                $tab_files = [
                    'analyzer-tab.php',
                    'generator-tab.php', 
                    'analytics-tab.php',
                    'keywords-tab.php',
                    'meta-tab.php',
                    'schema-tab.php',
                    'technical-tab.php',
                    'chatbot-tab.php',
                    'settings-tab.php'
                ];
                
                foreach ($tab_files as $tab_file) {
                    $tab_path = plugin_dir_path(__FILE__) . 'tabs/' . $tab_file;
                    if (file_exists($tab_path)) {
                        include $tab_path;
                    }
                }
                ?>
                <div id="analyzer-tab" class="flux-seo-tab-content active">
                    <div class="flux-seo-card">
                        <div class="flux-seo-card-header">
                            <h2 class="flux-seo-card-title">
                                <span class="flux-seo-card-icon">🔍</span>
                                <span data-key="contentAnalyzer">AI Content Analyzer</span>
                            </h2>
                            <p class="flux-seo-card-description">
                                Advanced content analysis powered by Google Gemini 2.5 Pro AI
                            </p>
                        </div>
                        <div class="flux-seo-card-body">
                            <div class="flux-seo-form">
                                <div class="flux-seo-form-group">
                                    <label class="flux-seo-label">Content to Analyze</label>
                                    <textarea 
                                        id="analyzer-content" 
                                        class="flux-seo-textarea" 
                                        rows="8"
                                        placeholder="Enter your content for AI-powered SEO analysis..."></textarea>
                                </div>
                                <div class="flux-seo-form-row">
                                    <div class="flux-seo-form-group">
                                        <label class="flux-seo-label" data-key="keywords">Target Keywords</label>
                                        <input type="text" id="analyzer-keywords" class="flux-seo-input" 
                                               placeholder="Enter target keywords">
                                    </div>
                                    <div class="flux-seo-form-group">
                                        <label class="flux-seo-label" data-key="targetAudience">Target Audience</label>
                                        <select id="analyzer-audience" class="flux-seo-select">
                                            <option value="general" data-key="general">General Audience</option>
                                            <option value="business" data-key="business">Business Professionals</option>
                                            <option value="students" data-key="students">Students</option>
                                            <option value="experts" data-key="experts">Industry Experts</option>
                                            <option value="consumers" data-key="consumers">Consumers</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flux-seo-form-actions">
                                    <button id="analyze-btn" class="flux-seo-btn flux-seo-btn-primary">
                                        <span class="flux-seo-btn-icon">🤖</span>
                                        <span class="flux-seo-btn-text" data-key="analyze">Analyze with Gemini AI</span>
                                    </button>
                                </div>
                            </div>
                            
                            <div id="analysis-results" class="flux-seo-results" style="display: none;">
                                <div class="flux-seo-results-header">
                                    <h3>AI Analysis Results</h3>
                                    <span class="flux-seo-ai-badge" data-key="poweredByGemini">Powered by Google Gemini 2.5 Pro</span>
                                </div>
                                <div class="flux-seo-enhanced-metrics">
                                    <div class="flux-seo-metric-card">
                                        <div class="flux-seo-metric-value" id="seo-score">--</div>
                                        <div class="flux-seo-metric-label" data-key="seoScore">SEO Score</div>
                                    </div>
                                    <div class="flux-seo-metric-card">
                                        <div class="flux-seo-metric-value" id="content-quality-score">--</div>
                                        <div class="flux-seo-metric-label" data-key="contentQuality">Content Quality</div>
                                    </div>
                                    <div class="flux-seo-metric-card">
                                        <div class="flux-seo-metric-value" id="readability-score">--</div>
                                        <div class="flux-seo-metric-label" data-key="readability">Readability</div>
                                    </div>
                                    <div class="flux-seo-metric-card">
                                        <div class="flux-seo-metric-value" id="engagement-score">--</div>
                                        <div class="flux-seo-metric-label" data-key="engagement">Engagement</div>
                                    </div>
                                </div>
                                <div id="gemini-insights" class="flux-seo-insights">
                                    <h4 data-key="geminiInsights">Gemini AI Insights</h4>
                                    <div id="gemini-analysis-content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AI Blog Generator Tab -->
                <div id="generator-tab" class="flux-seo-tab-content">
                    <div class="flux-seo-card">
                        <div class="flux-seo-card-header">
                            <h2 class="flux-seo-card-title">
                                <span class="flux-seo-card-icon">✨</span>
                                <span data-key="blogGenerator">AI Blog Generator</span>
                            </h2>
                            <p class="flux-seo-card-description">
                                Generate professional blog content with Google Gemini 2.5 Pro AI
                            </p>
                        </div>
                        <div class="flux-seo-card-body">
                            <div class="flux-seo-form">
                                <div class="flux-seo-form-row">
                                    <div class="flux-seo-form-group">
                                        <label class="flux-seo-label" data-key="topic">Topic</label>
                                        <input type="text" id="generator-topic" class="flux-seo-input" 
                                               placeholder="e.g., AI in Digital Marketing, การตลาดดิจิทัลด้วย AI">
                                    </div>
                                    <div class="flux-seo-form-group">
                                        <label class="flux-seo-label" data-key="contentType">Content Type</label>
                                        <select id="generator-type" class="flux-seo-select">
                                            <option value="blogPost" data-key="blogPost">Blog Post</option>
                                            <option value="article" data-key="article">Article</option>
                                            <option value="guide" data-key="guide">How-to Guide</option>
                                            <option value="review" data-key="review">Review</option>
                                            <option value="tutorial" data-key="tutorial">Tutorial</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flux-seo-form-row">
                                    <div class="flux-seo-form-group">
                                        <label class="flux-seo-label" data-key="tone">Tone</label>
                                        <select id="generator-tone" class="flux-seo-select">
                                            <option value="professional" data-key="professional">Professional</option>
                                            <option value="casual" data-key="casual">Casual</option>
                                            <option value="formal" data-key="formal">Formal</option>
                                            <option value="conversational" data-key="conversational">Conversational</option>
                                            <option value="technical" data-key="technical">Technical</option>
                                        </select>
                                    </div>
                                    <div class="flux-seo-form-group">
                                        <label class="flux-seo-label" data-key="targetAudience">Target Audience</label>
                                        <select id="generator-audience" class="flux-seo-select">
                                            <option value="general" data-key="general">General Audience</option>
                                            <option value="business" data-key="business">Business Professionals</option>
                                            <option value="students" data-key="students">Students</option>
                                            <option value="experts" data-key="experts">Industry Experts</option>
                                            <option value="consumers" data-key="consumers">Consumers</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flux-seo-form-row">
                                    <div class="flux-seo-form-group">
                                        <label class="flux-seo-label" data-key="wordCount">Word Count</label>
                                        <select id="generator-wordcount" class="flux-seo-select">
                                            <option value="500">500 words (Short)</option>
                                            <option value="1000" selected>1,000 words (Medium)</option>
                                            <option value="1500">1,500 words (Long)</option>
                                            <option value="2000">2,000 words (Comprehensive)</option>
                                        </select>
                                    </div>
                                    <div class="flux-seo-form-group">
                                        <label class="flux-seo-label" data-key="keywords">Keywords</label>
                                        <input type="text" id="generator-keywords" class="flux-seo-input" 
                                               placeholder="AI, SEO, digital marketing">
                                    </div>
                                </div>
                                <div class="flux-seo-form-actions">
                                    <button id="generate-btn" class="flux-seo-btn flux-seo-btn-primary">
                                        <span class="flux-seo-btn-icon">🤖</span>
                                        <span class="flux-seo-btn-text" data-key="generate">Generate with AI</span>
                                    </button>
                                </div>
                            </div>
                            
                            <div id="generation-results" class="flux-seo-results" style="display: none;">
                                <div class="flux-seo-results-header">
                                    <h3>AI Generated Content</h3>
                                    <div class="flux-seo-results-actions">
                                        <span class="flux-seo-ai-badge" data-key="poweredByGemini">Powered by Google Gemini 2.5 Pro</span>
                                        <button id="copy-content-btn" class="flux-seo-btn flux-seo-btn-secondary">
                                            <span class="flux-seo-btn-icon">📋</span>
                                            <span>Copy</span>
                                        </button>
                                    </div>
                                </div>
                                <div id="generated-content-display" class="flux-seo-generated-content"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advanced Analytics Tab -->
                <div id="analytics-tab" class="flux-seo-tab-content">
                    <div class="flux-seo-card">
                        <div class="flux-seo-card-header">
                            <h2 class="flux-seo-card-title">
                                <span class="flux-seo-card-icon">📊</span>
                                <span data-key="advancedAnalytics">Advanced Analytics</span>
                            </h2>
                            <p class="flux-seo-card-description">
                                Comprehensive website analysis with AI-powered insights
                            </p>
                        </div>
                        <div class="flux-seo-card-body">
                            <div class="flux-seo-form">
                                <div class="flux-seo-form-group">
                                    <label class="flux-seo-label" data-key="websiteUrl">Website URL</label>
                                    <div class="flux-seo-input-group">
                                        <input type="url" id="analytics-url" class="flux-seo-input" 
                                               placeholder="https://example.com">
                                        <button id="analyze-website-btn" class="flux-seo-btn flux-seo-btn-primary">
                                            <span class="flux-seo-btn-icon">🤖</span>
                                            <span class="flux-seo-btn-text" data-key="analyze">Analyze with AI</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="analytics-results" class="flux-seo-results" style="display: none;">
                                <div class="flux-seo-results-header">
                                    <h3>Advanced SEO Analytics</h3>
                                    <span class="flux-seo-ai-badge" data-key="poweredByGemini">Powered by Google Gemini 2.5 Pro</span>
                                </div>
                                <div id="analytics-display" class="flux-seo-analytics-display"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Meta Tags Tab -->
                <div id="optimizer-tab" class="flux-seo-tab-content">
                    <div class="flux-seo-card">
                        <div class="flux-seo-card-header">
                            <h2 class="flux-seo-card-title">
                                <span class="flux-seo-card-icon">⚡</span>
                                <span data-key="seoOptimizer">SEO Optimizer</span>
                            </h2>
                            <p class="flux-seo-card-description">
                                Optimize your content for search engines with AI recommendations
                            </p>
                        </div>
                        <div class="flux-seo-card-body">
                            <div class="flux-seo-form">
                                <div class="flux-seo-form-group">
                                    <label class="flux-seo-label">Content to Optimize</label>
                                    <textarea 
                                        id="optimizer-content" 
                                        class="flux-seo-textarea" 
                                        rows="8"
                                        placeholder="Paste your content here for AI-powered SEO optimization..."></textarea>
                                </div>
                                <div class="flux-seo-form-row">
                                    <div class="flux-seo-form-group">
                                        <label class="flux-seo-label" data-key="keywords">Target Keywords</label>
                                        <input type="text" id="optimizer-keywords" class="flux-seo-input" 
                                               placeholder="Primary and secondary keywords">
                                    </div>
                                    <div class="flux-seo-form-group">
                                        <label class="flux-seo-label" data-key="targetAudience">Target Audience</label>
                                        <select id="optimizer-audience" class="flux-seo-select">
                                            <option value="general" data-key="general">General Audience</option>
                                            <option value="business" data-key="business">Business Professionals</option>
                                            <option value="students" data-key="students">Students</option>
                                            <option value="experts" data-key="experts">Industry Experts</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flux-seo-form-actions">
                                    <button id="optimize-btn" class="flux-seo-btn flux-seo-btn-primary">
                                        <span class="flux-seo-btn-icon">⚡</span>
                                        <span class="flux-seo-btn-text" data-key="optimize">Optimize with AI</span>
                                    </button>
                                </div>
                            </div>
                            
                            <div id="optimization-results" class="flux-seo-results" style="display: none;">
                                <div class="flux-seo-results-header">
                                    <h3>SEO Optimization Results</h3>
                                    <span class="flux-seo-ai-badge" data-key="poweredByGemini">Powered by Google Gemini 2.5 Pro</span>
                                </div>
                                <div id="optimization-display" class="flux-seo-optimization-display"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Keyword Research Tab -->
                <div id="keywords-tab" class="flux-seo-tab-content">
                    <div class="flux-seo-card">
                        <div class="flux-seo-card-header">
                            <h2 class="flux-seo-card-title">
                                <span class="flux-seo-card-icon">🎯</span>
                                <span data-key="keywordResearch">AI Keyword Research</span>
                            </h2>
                            <p class="flux-seo-card-description">
                                Discover high-value keywords with AI-powered research and scoring
                            </p>
                        </div>
                        <div class="flux-seo-card-body">
                            <div class="flux-seo-form">
                                <div class="flux-seo-form-row">
                                    <div class="flux-seo-form-group">
                                        <label class="flux-seo-label">Seed Keywords</label>
                                        <input type="text" id="keyword-seeds" class="flux-seo-input" 
                                               placeholder="Enter seed keywords or topics">
                                    </div>
                                    <div class="flux-seo-form-group">
                                        <label class="flux-seo-label">Industry/Niche</label>
                                        <input type="text" id="keyword-industry" class="flux-seo-input" 
                                               placeholder="e.g., Digital Marketing, E-commerce">
                                    </div>
                                </div>
                                <div class="flux-seo-form-actions">
                                    <button id="research-keywords-btn" class="flux-seo-btn flux-seo-btn-primary">
                                        <span class="flux-seo-btn-icon">🎯</span>
                                        <span class="flux-seo-btn-text">Research Keywords with AI</span>
                                    </button>
                                </div>
                            </div>
                            
                            <div id="keyword-results" class="flux-seo-results" style="display: none;">
                                <div class="flux-seo-results-header">
                                    <h3>AI Keyword Research Results</h3>
                                    <span class="flux-seo-ai-badge" data-key="poweredByGemini">Powered by Google Gemini 2.5 Pro</span>
                                </div>
                                <div id="keyword-display" class="flux-seo-keyword-display"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Auto Blog Scheduler Tab -->
                <div id="autoblog-tab" class="flux-seo-tab-content">
                    <div class="flux-seo-card">
                        <div class="flux-seo-card-header">
                            <h2 class="flux-seo-card-title">
                                <span class="flux-seo-card-icon">🤖</span>
                                <span data-key="autoBlogScheduler">Auto Blog Scheduler</span>
                            </h2>
                            <p class="flux-seo-card-description">
                                Automated blog post generation with AI-powered content creation and scheduling
                            </p>
                        </div>
                        <div class="flux-seo-card-body">
                            <!-- Schedule Creation Form -->
                            <div class="flux-seo-auto-blog-form-section">
                                <h3>Create New Auto Blog Schedule</h3>
                                <form id="flux-seo-auto-blog-form" class="flux-seo-form">
                                    <div class="flux-seo-form-row">
                                        <div class="flux-seo-form-group">
                                            <label class="flux-seo-label">Schedule Name</label>
                                            <input type="text" id="schedule-name" class="flux-seo-input" required 
                                                   placeholder="e.g., Daily Tech News">
                                        </div>
                                        <div class="flux-seo-form-group">
                                            <label class="flux-seo-label">Language</label>
                                            <select id="schedule-language" class="flux-seo-select">
                                                <option value="en">🇺🇸 English</option>
                                                <option value="th">🇹🇭 ไทย</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="flux-seo-form-row">
                                        <div class="flux-seo-form-group">
                                            <label class="flux-seo-label">Frequency</label>
                                            <select id="schedule-frequency" class="flux-seo-select">
                                                <option value="daily">Daily</option>
                                                <option value="weekly">Weekly</option>
                                                <option value="monthly">Monthly</option>
                                                <option value="custom">Custom</option>
                                            </select>
                                        </div>
                                        <div class="flux-seo-form-group" id="custom-frequency-group" style="display: none;">
                                            <label class="flux-seo-label">Custom Frequency</label>
                                            <div class="flux-seo-input-group">
                                                <input type="number" id="custom-frequency-value" class="flux-seo-input" min="1" max="365" placeholder="1">
                                                <select id="custom-frequency-unit" class="flux-seo-select">
                                                    <option value="hours">Hours</option>
                                                    <option value="days">Days</option>
                                                    <option value="weeks">Weeks</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flux-seo-form-row">
                                        <div class="flux-seo-form-group">
                                            <label class="flux-seo-label">Timezone</label>
                                            <select id="schedule-timezone" class="flux-seo-select">
                                                <option value="UTC">UTC</option>
                                                <option value="Asia/Bangkok">Asia/Bangkok (GMT+7)</option>
                                                <option value="America/New_York">America/New_York (EST)</option>
                                                <option value="America/Los_Angeles">America/Los_Angeles (PST)</option>
                                                <option value="Europe/London">Europe/London (GMT)</option>
                                                <option value="Asia/Tokyo">Asia/Tokyo (JST)</option>
                                                <option value="Australia/Sydney">Australia/Sydney (AEST)</option>
                                            </select>
                                        </div>
                                        <div class="flux-seo-form-group">
                                            <label class="flux-seo-label">Generation Time</label>
                                            <input type="time" id="generation-time" class="flux-seo-input" value="09:00">
                                        </div>
                                    </div>
                                    
                                    <div class="flux-seo-form-row">
                                        <div class="flux-seo-form-group">
                                            <label class="flux-seo-label">Post Status</label>
                                            <select id="post-status" class="flux-seo-select">
                                                <option value="draft">Draft</option>
                                                <option value="pending">Pending Review</option>
                                                <option value="publish">Publish Immediately</option>
                                                <option value="scheduled">Schedule for Later</option>
                                            </select>
                                        </div>
                                        <div class="flux-seo-form-group" id="publish-delay-group" style="display: none;">
                                            <label class="flux-seo-label">Publish Delay (Hours)</label>
                                            <input type="number" id="publish-delay" class="flux-seo-input" min="0" max="168" value="2" 
                                                   placeholder="Hours to wait before publishing">
                                        </div>
                                    </div>
                                    
                                    <div class="flux-seo-form-group">
                                        <label class="flux-seo-label">Content Topics/Keywords</label>
                                        <textarea id="content-topics" class="flux-seo-textarea" rows="3" 
                                                  placeholder="Enter topics, keywords, or themes for content generation (one per line)"></textarea>
                                    </div>
                                    
                                    <div class="flux-seo-form-row">
                                        <div class="flux-seo-form-group">
                                            <label class="flux-seo-label">Content Type</label>
                                            <select id="content-type" class="flux-seo-select">
                                                <option value="blog_post">Blog Post</option>
                                                <option value="news_article">News Article</option>
                                                <option value="how_to_guide">How-to Guide</option>
                                                <option value="listicle">Listicle</option>
                                                <option value="review">Review</option>
                                                <option value="comparison">Comparison</option>
                                            </select>
                                        </div>
                                        <div class="flux-seo-form-group">
                                            <label class="flux-seo-label">Word Count Range</label>
                                            <select id="word-count-range" class="flux-seo-select">
                                                <option value="500-800">Short (500-800 words)</option>
                                                <option value="800-1200" selected>Medium (800-1200 words)</option>
                                                <option value="1200-1800">Long (1200-1800 words)</option>
                                                <option value="1800-2500">Comprehensive (1800-2500 words)</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="flux-seo-form-row">
                                        <div class="flux-seo-form-group">
                                            <label class="flux-seo-label">Writing Tone</label>
                                            <select id="writing-tone" class="flux-seo-select">
                                                <option value="professional">Professional</option>
                                                <option value="casual">Casual</option>
                                                <option value="formal">Formal</option>
                                                <option value="conversational">Conversational</option>
                                                <option value="technical">Technical</option>
                                            </select>
                                        </div>
                                        <div class="flux-seo-form-group">
                                            <label class="flux-seo-label">Target Audience</label>
                                            <select id="target-audience" class="flux-seo-select">
                                                <option value="general">General Audience</option>
                                                <option value="beginners">Beginners</option>
                                                <option value="professionals">Professionals</option>
                                                <option value="experts">Experts</option>
                                                <option value="students">Students</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="flux-seo-form-group">
                                        <label class="flux-seo-label">
                                            <input type="checkbox" id="auto-seo-optimization" checked>
                                            Enable Auto SEO Optimization
                                        </label>
                                        <small class="flux-seo-help-text">Automatically optimize generated content for SEO</small>
                                    </div>
                                    
                                    <div class="flux-seo-form-group">
                                        <label class="flux-seo-label">
                                            <input type="checkbox" id="auto-keyword-research">
                                            Auto Keyword Research
                                        </label>
                                        <small class="flux-seo-help-text">Automatically research and include relevant keywords</small>
                                    </div>
                                    
                                    <div class="flux-seo-form-actions">
                                        <button type="submit" class="flux-seo-btn flux-seo-btn-primary">
                                            <span class="flux-seo-btn-icon">⏰</span>
                                            <span class="flux-seo-btn-text">Create Schedule</span>
                                        </button>
                                        <button type="button" id="test-generation-btn" class="flux-seo-btn flux-seo-btn-secondary">
                                            <span class="flux-seo-btn-icon">🧪</span>
                                            <span class="flux-seo-btn-text">Test Generation</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Existing Schedules -->
                            <div class="flux-seo-auto-blog-schedules-section">
                                <h3>Active Schedules</h3>
                                <div id="auto-blog-schedules-list" class="flux-seo-schedules-grid">
                                    <!-- Schedules will be loaded here via AJAX -->
                                </div>
                            </div>
                            
                            <!-- Analytics Dashboard -->
                            <div class="flux-seo-auto-blog-analytics-section">
                                <h3>Auto Blog Analytics</h3>
                                <div class="flux-seo-analytics-grid">
                                    <div class="flux-seo-analytics-card">
                                        <h4>Total Generated</h4>
                                        <div class="flux-seo-analytics-value" id="total-generated">0</div>
                                    </div>
                                    <div class="flux-seo-analytics-card">
                                        <h4>Success Rate</h4>
                                        <div class="flux-seo-analytics-value" id="success-rate">0%</div>
                                    </div>
                                    <div class="flux-seo-analytics-card">
                                        <h4>Avg SEO Score</h4>
                                        <div class="flux-seo-analytics-value" id="avg-seo-score">0</div>
                                    </div>
                                    <div class="flux-seo-analytics-card">
                                        <h4>Next Generation</h4>
                                        <div class="flux-seo-analytics-value" id="next-generation">--</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AI Image Generator Tab -->
                <div id="imagegen-tab" class="flux-seo-tab-content">
                    <div class="flux-seo-card">
                        <div class="flux-seo-card-header">
                            <h2 class="flux-seo-card-title">
                                <span class="flux-seo-card-icon">🎨</span>
                                <span data-key="imageGenerator">AI Image Generator</span>
                            </h2>
                            <p class="flux-seo-card-description">
                                Generate stunning images for your content using AI-powered image generation
                            </p>
                        </div>
                        <div class="flux-seo-card-body">
                            <div class="flux-seo-form">
                                <div class="flux-seo-form-group">
                                    <label class="flux-seo-label">Image Description</label>
                                    <textarea id="image-prompt" class="flux-seo-textarea" rows="4" 
                                              placeholder="Describe the image you want to generate (e.g., 'A modern office workspace with laptop and coffee')"></textarea>
                                </div>
                                
                                <div class="flux-seo-form-row">
                                    <div class="flux-seo-form-group">
                                        <label class="flux-seo-label">Image Style</label>
                                        <select id="image-style" class="flux-seo-select">
                                            <option value="photorealistic">Photorealistic</option>
                                            <option value="digital-art">Digital Art</option>
                                            <option value="illustration">Illustration</option>
                                            <option value="cartoon">Cartoon</option>
                                            <option value="sketch">Sketch</option>
                                            <option value="watercolor">Watercolor</option>
                                            <option value="oil-painting">Oil Painting</option>
                                            <option value="minimalist">Minimalist</option>
                                        </select>
                                    </div>
                                    <div class="flux-seo-form-group">
                                        <label class="flux-seo-label">Image Size</label>
                                        <select id="image-size" class="flux-seo-select">
                                            <option value="1024x1024">Square (1024x1024)</option>
                                            <option value="1792x1024">Landscape (1792x1024)</option>
                                            <option value="1024x1792">Portrait (1024x1792)</option>
                                            <option value="512x512">Small Square (512x512)</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="flux-seo-form-row">
                                    <div class="flux-seo-form-group">
                                        <label class="flux-seo-label">Quality</label>
                                        <select id="image-quality" class="flux-seo-select">
                                            <option value="standard">Standard</option>
                                            <option value="hd">HD (High Definition)</option>
                                        </select>
                                    </div>
                                    <div class="flux-seo-form-group">
                                        <label class="flux-seo-label">Number of Images</label>
                                        <select id="image-count" class="flux-seo-select">
                                            <option value="1">1 Image</option>
                                            <option value="2">2 Images</option>
                                            <option value="3">3 Images</option>
                                            <option value="4">4 Images</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="flux-seo-form-group">
                                    <label class="flux-seo-label">
                                        <input type="checkbox" id="seo-optimized-alt">
                                        Generate SEO-optimized alt text
                                    </label>
                                    <small class="flux-seo-help-text">Automatically generate descriptive alt text for better SEO</small>
                                </div>
                                
                                <div class="flux-seo-form-actions">
                                    <button id="generate-image-btn" class="flux-seo-btn flux-seo-btn-primary">
                                        <span class="flux-seo-btn-icon">🎨</span>
                                        <span class="flux-seo-btn-text">Generate Images</span>
                                    </button>
                                    <button id="save-to-media-btn" class="flux-seo-btn flux-seo-btn-secondary" style="display: none;">
                                        <span class="flux-seo-btn-icon">💾</span>
                                        <span class="flux-seo-btn-text">Save to Media Library</span>
                                    </button>
                                </div>
                            </div>
                            
                            <div id="image-results" class="flux-seo-results" style="display: none;">
                                <div class="flux-seo-results-header">
                                    <h3>Generated Images</h3>
                                    <span class="flux-seo-ai-badge">Powered by AI Image Generation</span>
                                </div>
                                <div id="image-gallery" class="flux-seo-image-gallery"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean(); // Return the buffered HTML
    }
    
    /**
     * Main AJAX handler for various plugin actions.
     * Verifies nonce, checks API key, and routes to specific action handlers.
     * All AJAX actions are expected to send JSON responses.
     */
    public function ajax_handler() {
        // Verify nonce for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'flux_seo_enhanced_nonce')) {
            wp_send_json_error(array(
                'message' => __('Security check failed. Please refresh the page and try again.', 'flux-seo-scribe-craft-enhanced'),
                'code' => 'nonce_verification_failed'
            ));
            return;
        }
        
        // Sanitize action type and language from POST data
        $action_type = isset($_POST['action_type']) ? sanitize_text_field(wp_unslash($_POST['action_type'])) : '';
        $language = isset($_POST['language']) ? sanitize_text_field(wp_unslash($_POST['language'])) : 'en';

        // Ensure API key is loaded and available
        // This check is crucial for actions that require the Gemini API.
        if (empty($this->gemini_api_key)) {
            $this->gemini_api_key = get_option('flux_seo_gemini_api_key', ''); // Attempt to reload
            if (empty($this->gemini_api_key) && $action_type !== 'save_api_key') { // Allow saving API key even if not set
                wp_send_json_error(array(
                    'message' => __('Gemini API key is not set. Please configure it in the plugin settings.', 'flux-seo-scribe-craft-enhanced'),
                    'code' => 'api_key_missing'
                ));
                return;
            }
        }

        // Route to the appropriate handler based on action_type
        switch ($action_type) {
            case 'analyze_content':
                $this->handle_ai_content_analysis($language);
                break;
            case 'generate_content':
                $this->handle_ai_content_generation($language);
                break;
            case 'analyze_website':
                $this->handle_ai_website_analysis($language);
                break;
            case 'optimize_content': // This seems to be handled by analyzer, consider merging or distinct functionality
                $this->handle_ai_content_optimization($language);
                break;
            case 'research_keywords':
                $this->handle_ai_keyword_research($language);
                break;
            case 'generate_meta_tags':
                $this->handle_ai_meta_tags_generation($language);
                break;
            case 'generate_schema':
                $this->handle_ai_schema_generation($language);
                break;
            case 'audit_technical_seo':
                $this->handle_ai_technical_seo_audit($language);
                break;
            case 'chat_with_ai':
                $this->handle_ai_chatbot($language);
                break;
            case 'save_api_key':
                $this->handle_save_api_key();
                break;
            case 'test_auto_blog_generation':
                $this->handle_ai_test_auto_blog_generation($language);
                break;
            case 'suggest_meta_description':
                $this->handle_ai_suggest_meta_description($language);
                break;
            default:
                wp_send_json_error(array('message' => __('Invalid action type.', 'flux-seo-scribe-craft-enhanced'), 'code' => 'invalid_action'));
        }
    }
    
    /**
     * Calls the Google Gemini API with the provided prompt and parameters.
     * Handles API key checks, request construction, response parsing, and error handling.
     *
     * @param string $prompt The main prompt/text to send to the AI.
     * @param string $language The language for the AI's response ('en' or 'th').
     * @param string $model The Gemini model to use (e.g., 'gemini-1.5-flash-latest').
     * @return array Decoded JSON response from the API or an error array.
     */
    private function call_gemini_api($prompt, $language = 'en', $model = 'gemini-1.5-flash-latest') {
        // Ensure API key is available, attempting to reload if it was set mid-session.
        if (empty($this->gemini_api_key)) {
            $this->gemini_api_key = get_option('flux_seo_gemini_api_key', '');
            if (empty($this->gemini_api_key)) {
                return array('error' => true, 'message' => __('Gemini API key is not configured. Please set it in the plugin settings.', 'flux-seo-scribe-craft-enhanced'), 'code' => 'api_key_not_configured');
            }
        }

        // Construct the API URL. The model is part of the $this->gemini_endpoint.
        $url = $this->gemini_endpoint . '?key=' . $this->gemini_api_key;
        // If model switching per call is desired, the URL construction would be:
        // $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $this->gemini_api_key;

        // Define system instructions based on language.
        $system_instruction_text = $language === 'th' ?
            "คุณเป็นผู้ช่วย SEO อัจฉริยะชื่อ Flux AI ขับเคลื่อนโดย Gemini คุณเชี่ยวชาญในการวิเคราะห์ SEO การสร้างเนื้อหา และการให้คำแนะนำเชิงกลยุทธ์สำหรับการตลาดดิจิทัล โดยเฉพาะสำหรับผู้ชมชาวไทย ตอบกลับเป็นภาษาไทยเสมอ ให้คำตอบที่กระชับ ชัดเจน และนำไปปฏิบัติได้จริง" :
            "You are an intelligent SEO assistant named Flux AI, powered by Gemini. You specialize in SEO analysis, content generation, and providing strategic digital marketing advice, particularly for an English-speaking audience. Always respond in English. Provide concise, clear, and actionable answers.";
        
        // Updated structure for Gemini API
        $data = array(
            'contents' => array(
                array(
                    'role' => 'user',
                    'parts' => array(
                        array('text' => $prompt)
                    )
                )
            ),
            'systemInstruction' => array(
                'parts' => array(
                    array('text' => $system_instruction_text)
                )
            ),
            'generationConfig' => array(
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 8192, // Increased max output tokens
                'responseMimeType' => "application/json", // Request JSON output
            ),
            'safetySettings' => array(
                array(
                    'category' => 'HARM_CATEGORY_HARASSMENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ),
                array(
                    'category' => 'HARM_CATEGORY_HATE_SPEECH',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ),
                array(
                    'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ),
                array(
                    'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                )
            )
        );

        $response = wp_remote_post($url, array(
            'method'    => 'POST',
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode($data),
            'timeout' => 60 // Increased timeout for potentially longer AI responses
        ));

        if (is_wp_error($response)) {
            error_log('Gemini API WP_Error: ' . $response->get_error_message());
            return array('error' => true, 'message' => $response->get_error_message(), 'code' => 'wp_remote_post_error');
        }

        $body = wp_remote_retrieve_body($response);
        $http_code = wp_remote_retrieve_response_code($response);
        $decoded = json_decode($body, true);

        if ($http_code >= 400) {
            error_log("Gemini API HTTP Error ($http_code): " . $body);
            $error_message = isset($decoded['error']['message']) ? $decoded['error']['message'] : __('An unknown API error occurred.', 'flux-seo-scribe-craft-enhanced');
            if ($http_code === 400 && strpos($error_message, 'API_KEY_INVALID') !== false) {
                 $error_message = __('The provided API key is invalid. Please check your API key in the settings.', 'flux-seo-scribe-craft-enhanced');
            } elseif ($http_code === 429) {
                $error_message = __('API quota exceeded. Please check your Google AI Studio quotas or try again later.', 'flux-seo-scribe-craft-enhanced');
            }
            return array('error' => true, 'message' => $error_message, 'http_code' => $http_code, 'details' => $decoded);
        }

        if (isset($decoded['candidates'][0]['content']['parts'][0]['text'])) {
            // The API is expected to return JSON directly due to 'responseMimeType': 'application/json'
            $json_output = json_decode($decoded['candidates'][0]['content']['parts'][0]['text'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $json_output;
            } else {
                // If it's not valid JSON, return the text itself and log an error
                error_log('Gemini API did not return valid JSON: ' . $decoded['candidates'][0]['content']['parts'][0]['text']);
                // Attempt to return the raw text if JSON parsing fails, wrapped in a structure indicating this
                return array('error' => true, 'message' => __('AI response was not in the expected JSON format. Displaying raw response.', 'flux-seo-scribe-craft-enhanced'), 'raw_response' => $decoded['candidates'][0]['content']['parts'][0]['text'], 'code' => 'invalid_json_response');
            }
        } elseif (isset($decoded['promptFeedback']['blockReason'])) {
            $block_reason = $decoded['promptFeedback']['blockReason'];
            $safety_ratings = isset($decoded['promptFeedback']['safetyRatings']) ? json_encode($decoded['promptFeedback']['safetyRatings']) : 'N/A';
            error_log("Gemini API Blocked: Reason: $block_reason, Safety Ratings: $safety_ratings. Prompt: $prompt");
            $user_message = __('The request was blocked by the AI for safety reasons. Please modify your request and try again.', 'flux-seo-scribe-craft-enhanced');
            if ($block_reason === 'OTHER') {
                $user_message = __('The request was blocked by the AI due to an unspecified reason. Please try rephrasing your request.', 'flux-seo-scribe-craft-enhanced');
            }
            return array('error' => true, 'message' => $user_message, 'code' => 'api_block', 'reason' => $block_reason);
        }

        error_log('Gemini API Response Error (unexpected structure): ' . $body);
        return array('error' => true, 'message' => __('Unexpected response structure from AI.', 'flux-seo-scribe-craft-enhanced'), 'code' => 'unexpected_api_response_structure', 'details' => $body);
    }

    private function handle_ai_content_analysis($language) {
                array(
                    'parts' => array(
                        array('text' => $system_prompt . "\n\n" . $prompt)
                    )
                )
            ),
            'generationConfig' => array(
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 4096,
            ),
            'safetySettings' => array(
                array(
                    'category' => 'HARM_CATEGORY_HARASSMENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ),
                array(
                    'category' => 'HARM_CATEGORY_HATE_SPEECH',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                )
            )
        );
        
        $response = wp_remote_post($url, array(
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode($data),
            'timeout' => 30
        ));
        
        if (is_wp_error($response)) {
            error_log('Gemini API Error: ' . $response->get_error_message());
            return false;
        }
        
        $body = wp_remote_retrieve_body($response);
        $decoded = json_decode($body, true);
        
        if (isset($decoded['candidates'][0]['content']['parts'][0]['text'])) {
            return $decoded['candidates'][0]['content']['parts'][0]['text'];
        }
        
        error_log('Gemini API Response Error: ' . $body);
        return false;
    }
    
    /**
     * Handles AJAX request for AI Content Analysis.
     * Retrieves content, keywords, and audience from POST data,
     * constructs a prompt for the Gemini API, calls the API,
     * and sends a JSON response with the analysis results or an error.
     *
     * @param string $language The language for the analysis ('en' or 'th').
     *                        Expected to be 'en' or 'th'.
     */
    private function handle_ai_content_analysis($language) {
        // Sanitize input from POST request
        $content = isset($_POST['content']) ? sanitize_textarea_field(wp_unslash($_POST['content'])) : '';
        $keywords = isset($_POST['keywords']) ? sanitize_text_field(wp_unslash($_POST['keywords'])) : '';
        $audience = isset($_POST['audience']) ? sanitize_text_field(wp_unslash($_POST['audience'])) : 'general';

        // Construct the prompt for the Gemini API based on the provided content and language.
        $prompt_parts = [];
        if ($language === 'th') {
            // Thai prompt for content analysis
            $prompt_parts[] = "โปรดวิเคราะห์เนื้อหาต่อไปนี้อย่างละเอียดเพื่อปรับปรุง SEO โดยพิจารณาจากปัจจัยต่างๆ เช่น การใช้คำหลัก ความสามารถในการอ่าน โครงสร้าง คุณภาพโดยรวม และศักยภาพในการดึงดูดผู้อ่าน";
            $prompt_parts[] = "\nเนื้อหาที่จะวิเคราะห์:\n\"\"\"\n{$content}\n\"\"\"";
            if (!empty($keywords)) {
                $prompt_parts[] = "\nคำหลักเป้าหมายหลัก: \"{$keywords}\"";
            }
            $prompt_parts[] = "กลุ่มเป้าหมายที่ต้องการ: {$audience}";
            $prompt_parts[] = "\nกรุณาให้ผลลัพธ์ในรูปแบบ JSON object ที่มีโครงสร้างดังนี้:";
            $prompt_parts[] = "- \"overall_seo_score\": คะแนน SEO โดยรวม (0-100) โดยพิจารณาจากการปรับให้เหมาะสมที่สุดในหน้าเว็บ";
            $prompt_parts[] = "- \"content_quality_score\": คะแนนคุณภาพเนื้อหา (0-100) ประเมินความลึก ความเกี่ยวข้อง และความเป็นต้นฉบับ";
            $prompt_parts[] = "- \"readability_score\": คะแนนความสามารถในการอ่าน (0-100, ยิ่งสูงยิ่งดี) พร้อมคำแนะนำระดับชั้นที่อ่านเข้าใจง่าย (เช่น \"เทียบเท่าระดับมัธยมปลาย\")";
            $prompt_parts[] = "- \"engagement_potential_score\": ศักยภาพในการดึงดูดผู้อ่าน (0-100)";
            $prompt_parts[] = "- \"keyword_analysis\": object ที่มี:";
            $prompt_parts[] = "  - \"primary_keyword_usage\": การประเมินการใช้คำหลักเป้าหมาย (เช่น \"ดี\", \"น้อยเกินไป\", \"มากเกินไป\") พร้อมความถี่";
            $prompt_parts[] = "  - \"suggested_lsi_keywords\": array ของคำหลัก LSI หรือคำที่เกี่ยวข้องที่แนะนำ (ถ้ามี)";
            $prompt_parts[] = "  - \"keyword_density\": ความหนาแน่นของคำหลักเป้าหมายเป็นเปอร์เซ็นต์ (คำนวณ)";
            $prompt_parts[] = "- \"clarity_and_conciseness\": การประเมินความชัดเจนและความกระชับของเนื้อหา";
            $prompt_parts[] = "- \"tone_analysis\": การวิเคราะห์โทนของเนื้อหา (เช่น \"เป็นทางการ\", \"เป็นกันเอง\", \"ให้ข้อมูล\")";
            $prompt_parts[] = "- \"structural_analysis\": object ที่มี:";
            $prompt_parts[] = "  - \"headings_usage\": การประเมินการใช้หัวข้อ (H1, H2, H3)";
            $prompt_parts[] = "  - \"paragraph_length\": การประเมินความยาวของย่อหน้า";
            $prompt_parts[] = "  - \"sentence_length_variety\": การประเมินความหลากหลายของความยาวประโยค";
            $prompt_parts[] = "- \"actionable_recommendations\": array ของ object โดยแต่ละ object มี \"category\" (เช่น \"การปรับปรุงคำหลัก\", \"การปรับปรุงความสามารถในการอ่าน\", \"การปรับปรุงโครงสร้าง\") และ \"recommendation\" (สตริงคำแนะนำที่นำไปปฏิบัติได้)";
            $prompt_parts[] = "- \"positive_aspects\": array ของสิ่งที่ทำได้ดีในเนื้อหา";
            $prompt_parts[] = "- \"summary_of_findings\": สรุปการค้นพบหลักและการปรับปรุงที่สำคัญที่สุด";
        } else {
            $prompt_parts[] = "Please perform a comprehensive SEO analysis of the following content, focusing on keyword usage, readability, structure, overall quality, and engagement potential.";
            $prompt_parts[] = "\nContent to Analyze:\n\"\"\"\n{$content}\n\"\"\"";
            if (!empty($keywords)) {
                $prompt_parts[] = "\nPrimary Target Keywords: \"{$keywords}\"";
            }
            $prompt_parts[] = "Intended Target Audience: {$audience}";
            $prompt_parts[] = "\nPlease provide the results as a JSON object with the following well-defined structure:";
            $prompt_parts[] = "- \"overall_seo_score\": An overall SEO score (0-100) based on on-page optimization factors.";
            $prompt_parts[] = "- \"content_quality_score\": A content quality score (0-100) assessing depth, relevance, and originality.";
            $prompt_parts[] = "- \"readability_score\": A readability score (0-100, higher is better) and an estimated grade level (e.g., \"High School Level\").";
            $prompt_parts[] = "- \"engagement_potential_score\": An engagement potential score (0-100).";
            $prompt_parts[] = "- \"keyword_analysis\": An object containing:";
            $prompt_parts[] = "  - \"primary_keyword_usage\": Assessment of target keyword usage (e.g., \"Good\", \"Too low\", \"Overuse\") including frequency count.";
            $prompt_parts[] = "  - \"suggested_lsi_keywords\": An array of suggested LSI or related keywords (if any).";
            $prompt_parts[] = "  - \"keyword_density\": Calculated target keyword density as a percentage.";
            $prompt_parts[] = "- \"clarity_and_conciseness\": Assessment of the content's clarity and conciseness.";
            $prompt_parts[] = "- \"tone_analysis\": Analysis of the content's tone (e.g., \"Formal\", \"Casual\", \"Informative\").";
            $prompt_parts[] = "- \"structural_analysis\": An object containing:";
            $prompt_parts[] = "  - \"headings_usage\": Assessment of headings usage (H1, H2, H3).";
            $prompt_parts[] = "  - \"paragraph_length\": Assessment of paragraph lengths.";
            $prompt_parts[] = "  - \"sentence_length_variety\": Assessment of sentence length variety.";
            $prompt_parts[] = "- \"actionable_recommendations\": An array of objects, each with a \"category\" (e.g., \"Keyword Optimization\", \"Readability Improvement\", \"Structural Enhancements\") and a \"recommendation\" (actionable string).";
            $prompt_parts[] = "- \"positive_aspects\": An array of strings highlighting what the content does well.";
            $prompt_parts[] = "- \"summary_of_findings\": A brief summary of key findings and most critical improvements.";
        }
        
        $final_prompt = implode("\n", $prompt_parts);
        $ai_response = $this->call_gemini_api($final_prompt, $language);
        
        if (isset($ai_response['error']) && $ai_response['error']) {
            wp_send_json_error($ai_response);
            return;
        }
        
        if ($ai_response) {
            // The call_gemini_api function now directly returns decoded JSON or an error structure
            wp_send_json_success($ai_response);
            return;
        }
        
        // Fallback response with enhanced metrics (should be less likely to be hit now)
        $fallback = array(
            'seo_score' => rand(75, 95),
            'content_quality_score' => rand(80, 95),
            'readability_score' => rand(70, 90),
            'engagement_score' => rand(75, 90),
            'keyword_density_score' => rand(65, 85),
            'analysis' => $language === 'th' ? 
                'เนื้อหามีคุณภาพดีและมีโครงสร้างที่ชัดเจน แต่ยังสามารถปรับปรุงได้ในด้านการใช้คำหลักและการเพิ่มความน่าสนใจ' :
                'Content has good quality and clear structure, but can be improved in keyword usage and engagement factors.',
            'recommendations' => $language === 'th' ? 
                array(
                    'เพิ่มหัวข้อย่อยเพื่อแบ่งเนื้อหาให้ชัดเจน',
                    'ใช้คำหลักที่เกี่ยวข้องมากขึ้นอย่างเป็นธรรมชาติ',
                    'เพิ่มลิงก์ภายในไปยังเนื้อหาที่เกี่ยวข้อง',
                    'ปรับปรุงความยาวของประโยคให้เหมาะสม',
                    'เพิ่มรูปภาพและกราฟิกเพื่อเสริมความเข้าใจ',
                    'เพิ่ม call-to-action ที่ชัดเจน'
                ) :
                array(
                    'Add more subheadings to improve content structure',
                    'Include more relevant keywords naturally',
                    'Add internal links to related content',
                    'Improve sentence length for better readability',
                    'Add images and graphics to enhance understanding',
                    'Include clear call-to-action elements'
                ),
            'keyword_suggestions' => explode(',', $keywords ?: 'SEO, content marketing, digital strategy'),
            'content_improvements' => $language === 'th' ? 
                array(
                    'เพิ่มตัวอย่างและกรณีศึกษาที่เป็นรูปธรรม',
                    'ใช้ bullet points และ numbered lists มากขึ้น',
                    'เพิ่มข้อมูลสถิติและแหล่งอ้างอิงที่น่าเชื่อถือ',
                    'ปรับปรุงการเปิดและปิดเนื้อหาให้น่าสนใจ'
                ) :
                array(
                    'Add concrete examples and case studies',
                    'Use more bullet points and numbered lists',
                    'Include statistics and credible references',
                    'Improve opening and closing sections for engagement'
                )
        );
        
        wp_send_json_success($fallback);
    }
    
    /**
     * Handles AJAX request for AI Content Generation.
     * Gathers topic, content type, tone, keywords, etc., from POST data,
     * constructs a detailed prompt for the Gemini API, calls the API,
     * saves the generated content to the database, and returns a JSON response.
     *
     * @param string $language The language for content generation ('en' or 'th').
     */
    private function handle_ai_content_generation($language) {
        // Sanitize inputs from POST request
        $topic = isset($_POST['topic']) ? sanitize_text_field(wp_unslash($_POST['topic'])) : '';
        $content_type = isset($_POST['content_type']) ? sanitize_text_field(wp_unslash($_POST['content_type'])) : 'blog_post';
        $tone = isset($_POST['tone']) ? sanitize_text_field(wp_unslash($_POST['tone'])) : 'neutral';
        $keywords = isset($_POST['keywords']) ? sanitize_text_field(wp_unslash($_POST['keywords'])) : '';
        // Note: 'audience' is expected by save_generated_content but not directly in current generator-tab.php form
        // It seems 'generator-audience' select exists in render_app() for the generator tab.
        // This should be reviewed for consistency between form and backend.
        $audience = isset($_POST['audience']) ? sanitize_text_field(wp_unslash($_POST['audience'])) : 'general';
        $additional_instructions = isset($_POST['additional_instructions']) ? sanitize_textarea_field(wp_unslash($_POST['additional_instructions'])) : '';
        // Word count is also part of the form in render_app()
        $word_count = isset($_POST['word_count']) ? sanitize_text_field(wp_unslash($_POST['word_count'])) : '1000';


        // Word count preference based on selected word_count or a default.
        // The JS sends 'word_count' which is a numeric value like "1000".
        // The prompt needs a descriptive phrase.
        $word_count_map = [
            '500' => $language === 'th' ? 'ประมาณ 500 คำ' : 'approximately 500 words',
            '1000' => $language === 'th' ? 'ประมาณ 1000 คำ' : 'approximately 1000 words',
            '1500' => $language === 'th' ? 'ประมาณ 1500 คำ' : 'approximately 1500 words',
            '2000' => $language === 'th' ? 'ประมาณ 2000 คำ' : 'approximately 2000 words',
        ];
        $word_count_preference = $word_count_map[$word_count] ?? ($language === 'th' ? 'ประมาณ 800-1200 คำ' : 'approximately 800-1200 words');

        // Construct the prompt for Gemini API
        $prompt_parts = [];
        $prompt_parts[] = $language === 'th' ?
            "คุณเป็นผู้เชี่ยวชาญด้านการเขียนเนื้อหา SEO และผู้สร้างบล็อก โปรดสร้าง '{$content_type}' คุณภาพสูงและน่าสนใจเกี่ยวกับหัวข้อ: '{$topic}'." :
            "You are an expert SEO content writer and blog generator. Please generate a high-quality, engaging '{$content_type}' on the topic: '{$topic}'.";

        $prompt_parts[] = $language === 'th' ? "โทนการเขียนควรเป็นแบบ '{$tone}'." : "The writing tone should be '{$tone}'.";

        if (!empty($keywords)) {
            $prompt_parts[] = $language === 'th' ? "รวมคำหลักเหล่านี้อย่างเป็นธรรมชาติ: {$keywords}." : "Naturally incorporate these keywords: {$keywords}.";
        }
        
        $prompt_parts[] = $language === 'th' ? "ความยาวของเนื้อหาควรอยู่ที่ {$word_count_preference}." : "The content length should be {$word_count_preference}.";

        if (!empty($additional_instructions)) {
            $prompt_parts[] = $language === 'th' ? "คำแนะนำเพิ่มเติมจากผู้ใช้: {$additional_instructions}." : "Additional user instructions: {$additional_instructions}.";
        }

        $prompt_parts[] = $language === 'th' ?
            "โปรดจัดโครงสร้างผลลัพธ์เป็น JSON object ที่มี keys ต่อไปนี้:\n" .
            "- \"title\": ชื่อเรื่องที่น่าสนใจและเป็นมิตรกับ SEO (น้อยกว่า 70 ตัวอักษร)\n" .
            "- \"meta_description\": คำอธิบายเมตาที่ดึงดูดใจ (ประมาณ 150-160 ตัวอักษร)\n" .
            "- \"suggested_slug\": สลาก URL ที่สั้นและสื่อความหมาย (ใช้ขีดกลาง, ตัวพิมพ์เล็ก)\n" .
            "- \"outline\": อาร์เรย์ของสตริงที่แสดงโครงร่างเนื้อหาหลัก (เช่น หัวข้อ H2 และ H3 ที่เสนอแนะ)\n" .
            "- \"content_html\": เนื้อหาหลักที่จัดรูปแบบด้วย HTML (รวมถึง <h1> สำหรับชื่อเรื่อง, <h2>, <h3> สำหรับหัวข้อย่อย, <p> สำหรับย่อหน้า, และ <strong>, <em> ตามความเหมาะสม)\n" .
            "- \"keywords_identified\": อาร์เรย์ของคำหลักหลักที่ใช้หรือระบุในเนื้อหา\n" .
            "- \"seo_score_estimation\": การประเมินคะแนน SEO (0-100) โดยพิจารณาจากการใช้คำหลัก โครงสร้าง และความเกี่ยวข้อง\n" .
            "- \"readability_score_estimation\": การประเมินคะแนนความสามารถในการอ่าน (เช่น Flesch-Kincaid grade level หรือ 0-100 scale)\n" .
            "- \"warnings\": อาร์เรย์ของคำเตือนหรือข้อเสนอแนะที่สำคัญ (ถ้ามี เช่น คำหลักซ้ำซ้อน เนื้อหาบางส่วนอาจต้องปรับปรุง)"
            :
            "Please structure the output as a JSON object with the following keys:\n" .
            "- \"title\": An engaging, SEO-friendly title (under 70 characters).\n" .
            "- \"meta_description\": A compelling meta description (around 150-160 characters).\n" .
            "- \"suggested_slug\": A short, descriptive URL slug (hyphenated, lowercase).\n" .
            "- \"outline\": An array of strings representing the main content outline (e.g., suggested H2 and H3 headings).\n" .
            "- \"content_html\": The main content formatted in HTML (including <h1> for the title, <h2>, <h3> for subheadings, <p> for paragraphs, and <strong>, <em> where appropriate).\n" .
            "- \"keywords_identified\": An array of primary keywords used or identified in the content.\n" .
            "- \"seo_score_estimation\": An estimated SEO score (0-100) considering keyword usage, structure, and relevance.\n" .
            "- \"readability_score_estimation\": An estimated readability score (e.g., Flesch-Kincaid grade level or 0-100 scale).\n" .
            "- \"warnings\": An array of any important warnings or suggestions (e.g., keyword stuffing, thin content sections).";

        $final_prompt = implode("\n\n", $prompt_parts);

        $ai_response = $this->call_gemini_api($final_prompt, $language);

        if (isset($ai_response['error']) && $ai_response['error']) {
            wp_send_json_error($ai_response);
            return;
        }
        
        if ($ai_response) {
            // Save to database
            $this->save_generated_content($ai_response, $language, $topic, $content_type, $tone, $audience, $keywords);
            wp_send_json_success($ai_response);
            return;
        }
        
        // Fallback content generation (should be less likely to be hit now)
        $fallback = $this->generate_fallback_content($topic, $language, $content_type, $tone, $word_count, $keywords);
        // Ensure fallback is also saved if it's the result.
        $this->save_generated_content($fallback, $language, $topic, $content_type, $tone, $audience, $keywords, true);
        wp_send_json_success($fallback);
    }
    
    /**
     * Handles AJAX request for AI Website Analysis.
     * Takes a URL, constructs a prompt for comprehensive SEO analysis (on-page, technical, UX, etc.),
     * calls the Gemini API, saves the analysis, and returns a JSON response.
     *
     * @param string $language The language for the analysis ('en' or 'th').
     */
    private function handle_ai_website_analysis($language) {
        // Sanitize the URL from POST data
        $url = isset($_POST['url']) ? esc_url_raw(wp_unslash($_POST['url'])) : '';
        
        // Validate the URL
         if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            wp_send_json_error(array('message' => __('Invalid URL provided.', 'flux-seo-scribe-craft-enhanced'), 'code' => 'invalid_url'));
            return;
        }
        
        // Construct the prompt for Gemini API
        $prompt_parts = [];
        if ($language === 'th') {
            $prompt_parts[] = "โปรดดำเนินการวิเคราะห์ SEO เชิงลึกสำหรับเว็บไซต์: {$url}. การวิเคราะห์ควรมุ่งเน้นไปที่ตลาดประเทศไทยหากมีความเกี่ยวข้อง";
            $prompt_parts[] = "ประเมินปัจจัยต่อไปนี้และให้คะแนน (0-100) พร้อมคำแนะนำที่นำไปปฏิบัติได้สำหรับแต่ละส่วน:";
            $prompt_parts[] = "- \"overall_site_health\": สุขภาพโดยรวมของเว็บไซต์";
            $prompt_parts[] = "- \"on_page_seo\": การปรับแต่ง SEO ภายในหน้า (การใช้คำหลัก, แท็กหัวเรื่อง, meta tags, คุณภาพเนื้อหา, โครงสร้าง URL, การเชื่อมโยงภายใน)";
            $prompt_parts[] = "- \"technical_seo\": SEO เชิงเทคนิค (ความเร็วเว็บไซต์, การตอบสนองต่อมือถือ, ความสามารถในการรวบรวมข้อมูล, การจัดทำดัชนี, sitemap.xml, robots.txt, การใช้ HTTPS, structured data)";
            $prompt_parts[] = "- \"user_experience_ux\": ประสบการณ์ผู้ใช้ (การนำทาง, การออกแบบ, call-to-action, อัตราตีกลับที่อาจเกิดขึ้น)";
            $prompt_parts[] = "- \"content_strategy_analysis\": การวิเคราะห์กลยุทธ์เนื้อหา (ความสดใหม่, ความเกี่ยวข้อง, ช่องว่างของเนื้อหา, การมีส่วนร่วมของผู้ใช้)";
            $prompt_parts[] = "- \"mobile_friendliness\": ความเป็นมิตรต่ออุปกรณ์เคลื่อนที่";
            $prompt_parts[] = "- \"page_speed_insights\": ข้อมูลเชิงลึกเกี่ยวกับความเร็วหน้าเว็บ (Core Web Vitals - LCP, FID, CLS)";
            $prompt_parts[] = "- \"security_analysis\": การวิเคราะห์ความปลอดภัย (HTTPS, ช่องโหว่ที่รู้จัก)";
            $prompt_parts[] = "- \"accessibility_wcag\": การช่วยสำหรับการเข้าถึง (การปฏิบัติตาม WCAG)";
            $prompt_parts[] = "\nนอกเหนือจากคะแนนและคำแนะนำสำหรับแต่ละส่วน โปรดระบุข้อมูลต่อไปนี้ใน JSON object หลัก:";
            $prompt_parts[] = "- \"top_positive_points\": array ของจุดแข็งหลัก 3-5 ประการของเว็บไซต์";
            $prompt_parts[] = "- \"top_areas_for_improvement\": array ของ 3-5 ส่วนที่สำคัญที่สุดที่ต้องปรับปรุง พร้อมเหตุผลสั้นๆ";
            $prompt_parts[] = "- \"prioritized_action_plan\": array ของ object ซึ่งแต่ละ object มี \"action\" (สตริง) และ \"priority\" (\"High\", \"Medium\", \"Low\") สำหรับขั้นตอนถัดไปที่แนะนำ";
            $prompt_parts[] = "- \"conceptual_competitor_comparison\": object ที่มี \"strength_comparison\" (เช่น \"แข็งแกร่งกว่าคู่แข่งในด้าน X\", \"อ่อนแอกว่าในด้าน Y\") และ \"opportunity_areas\" (array ของส่วนที่สามารถเอาชนะคู่แข่งได้)";
            $prompt_parts[] = "- \"executive_summary\": สรุปภาพรวมของการวิเคราะห์และข้อเสนอแนะหลัก";
        } else {
            $prompt_parts[] = "Please perform an in-depth SEO analysis for the website: {$url}. The analysis should focus on the global market unless specific local aspects are evident.";
            $prompt_parts[] = "Evaluate the following factors, providing a score (0-100) and actionable recommendations for each section:";
            $prompt_parts[] = "- \"overall_site_health\": Overall website health.";
            $prompt_parts[] = "- \"on_page_seo\": On-page SEO (keyword usage, title tags, meta tags, content quality, URL structure, internal linking).";
            $prompt_parts[] = "- \"technical_seo\": Technical SEO (site speed, mobile responsiveness, crawlability, indexability, sitemap.xml, robots.txt, HTTPS usage, structured data).";
            $prompt_parts[] = "- \"user_experience_ux\": User experience (navigation, design, CTAs, potential bounce rate factors).";
            $prompt_parts[] = "- \"content_strategy_analysis\": Content strategy analysis (freshness, relevance, content gaps, user engagement).";
            $prompt_parts[] = "- \"mobile_friendliness\": Mobile-friendliness assessment.";
            $prompt_parts[] = "- \"page_speed_insights\": Page speed insights (Core Web Vitals - LCP, FID, CLS).";
            $prompt_parts[] = "- \"security_analysis\": Security analysis (HTTPS, known vulnerabilities).";
            $prompt_parts[] = "- \"accessibility_wcag\": Accessibility (WCAG compliance).";
            $prompt_parts[] = "\nIn addition to scores and recommendations for each section, please provide the following in the main JSON object:";
            $prompt_parts[] = "- \"top_positive_points\": An array of 3-5 key strengths of the website.";
            $prompt_parts[] = "- \"top_areas_for_improvement\": An array of the 3-5 most critical areas for improvement, with brief reasoning.";
            $prompt_parts[] = "- \"prioritized_action_plan\": An array of objects, each with an \"action\" (string) and \"priority\" (\"High\", \"Medium\", \"Low\") for recommended next steps.";
            $prompt_parts[] = "- \"conceptual_competitor_comparison\": An object with \"strength_comparison\" (e.g., \"Stronger than competitors in X\", \"Weaker in Y\") and \"opportunity_areas\" (array of areas where the site can outperform competitors).";
            $prompt_parts[] = "- \"executive_summary\": An overall summary of the analysis and key recommendations.";
        }
        $prompt_parts[] = "\nEnsure the entire output is a single, valid JSON object. Each mentioned section (e.g., on_page_seo) should be a key in the JSON, containing at least 'score' and 'recommendations' (array of strings) sub-keys, plus any other relevant analytical text or sub-objects.";

        $final_prompt = implode("\n", $prompt_parts);
        $ai_response = $this->call_gemini_api($final_prompt, $language);

        if (isset($ai_response['error']) && $ai_response['error']) {
            wp_send_json_error($ai_response);
            return;
        }
        
        if ($ai_response) {
            // Save to database
            $this->save_website_analysis($ai_response, $url, $language);
            wp_send_json_success($ai_response);
            return;
        }
        
        // Fallback analysis with comprehensive metrics (should be less likely to be hit now)
        $fallback = array(
            'overall_score' => rand(75, 95),
            'seo_score' => rand(70, 90),
            'performance_score' => rand(75, 95),
            'content_quality_score' => rand(80, 95),
            'technical_seo_score' => rand(65, 85),
            'user_experience_score' => rand(75, 90),
            'mobile_score' => rand(80, 95),
            'page_speed_score' => rand(70, 90),
            'security_score' => rand(85, 98),
            'accessibility_score' => rand(70, 85),
            'analysis' => $language === 'th' ? 
                'เว็บไซต์มีโครงสร้างพื้นฐานที่ดี แต่ยังมีโอกาสปรับปรุงในหลายด้าน โดยเฉพาะการเพิ่มประสิทธิภาพและการปรับปรุงเนื้อหา' :
                'Website has good basic structure but has opportunities for improvement in several areas, especially performance optimization and content enhancement.',
            'recommendations' => $this->get_ai_website_recommendations($language),
            'technical_issues' => $language === 'th' ? 
                array(
                    'ปรับปรุงความเร็วในการโหลดหน้าเว็บ',
                    'เพิ่ม structured data markup',
                    'ปรับปรุง meta tags ให้สมบูรณ์',
                    'เพิ่ม sitemap.xml ที่ครบถ้วน'
                ) :
                array(
                    'Improve page loading speed',
                    'Add structured data markup',
                    'Optimize meta tags completely',
                    'Add comprehensive sitemap.xml'
                ),
            'content_suggestions' => $language === 'th' ? 
                array(
                    'เพิ่มเนื้อหาที่มีคุณภาพและเป็นประโยชน์',
                    'ปรับปรุงการใช้คำหลักให้เป็นธรรมชาติ',
                    'เพิ่มรูปภาพและสื่อมัลติมีเดีย',
                    'สร้างเนื้อหาที่ตอบคำถามของผู้ใช้'
                ) :
                array(
                    'Add high-quality and valuable content',
                    'Improve natural keyword usage',
                    'Add images and multimedia content',
                    'Create content that answers user questions'
                ),
            'competitor_insights' => $language === 'th' ? 
                array(
                    'คู่แข่งมีการใช้คำหลักที่หลากหลายมากกว่า',
                    'เนื้อหาของคู่แข่งมีความยาวและความลึกมากกว่า',
                    'คู่แข่งมีการใช้ social media integration ที่ดีกว่า'
                ) :
                array(
                    'Competitors use more diverse keywords',
                    'Competitor content is longer and more in-depth',
                    'Competitors have better social media integration'
                )
        );
        
        wp_send_json_success($fallback);
    }
    
    private function handle_ai_content_optimization($language) {
        $content = sanitize_textarea_field($_POST['content']);
        $keywords = sanitize_text_field($_POST['keywords']);
        $audience = sanitize_text_field($_POST['audience']);
        
        $prompt = $language === 'th' ? 
            "ปรับปรุงเนื้อหาต่อไปนี้เพื่อ SEO โดยใช้คำหลัก '{$keywords}' สำหรับกลุ่มเป้าหมาย {$audience}:

{$content}

กรุณาให้คำแนะนำการปรับปรุงในรูปแบบ JSON ที่มี:
- optimized_title: หัวข้อที่ปรับปรุงแล้ว
- optimized_meta: meta description ที่ปรับปรุงแล้ว
- optimization_tips: เทคนิคการปรับปรุง (array)
- keyword_suggestions: คำหลักที่แนะนำ (array)
- structure_improvements: การปรับปรุงโครงสร้าง (array)
- content_enhancements: การเพิ่มประสิทธิภาพเนื้อหา (array)
- seo_score_improvement: การปรับปรุงคะแนน SEO ที่คาดหวัง" :
            
            "Optimize the following content for SEO using keywords '{$keywords}' for {$audience} audience:

{$content}

Please provide optimization recommendations in JSON format with:
- optimized_title: Improved title
- optimized_meta: Improved meta description
- optimization_tips: Optimization techniques (array)
- keyword_suggestions: Suggested keywords (array)
- structure_improvements: Structure improvements (array)
- content_enhancements: Content enhancements (array)
- seo_score_improvement: Expected SEO score improvement";
        
        $ai_response = $this->call_gemini_api($prompt, $language);

        if (isset($ai_response['error']) && $ai_response['error']) {
            wp_send_json_error($ai_response);
            return;
        }
        
        if ($ai_response) {
            wp_send_json_success($ai_response);
            return;
        }
        
        // Fallback optimization (should be less likely to be hit now)
        $fallback = array(
            'optimized_title' => $language === 'th' ? 
                "ปรับปรุงแล้ว: {$keywords} - คู่มือฉบับสมบูรณ์ 2024" :
                "Optimized: Complete Guide to {$keywords} 2024",
            'optimized_meta' => $language === 'th' ? 
                "เรียนรู้เกี่ยวกับ {$keywords} อย่างครบถ้วน พร้อมเทคนิคและแนวทางที่ได้ผลจริง ✓ คู่มือล่าสุด 2024" :
                "Learn everything about {$keywords} with proven techniques and strategies ✓ Latest 2024 guide",
            'optimization_tips' => $language === 'th' ? 
                array(
                    'เพิ่มคำหลักในหัวข้อหลักและหัวข้อย่อย',
                    'ใช้คำหลักในย่อหน้าแรกและย่อหน้าสุดท้าย',
                    'เพิ่มลิงก์ภายในที่เกี่ยวข้อง',
                    'ปรับปรุงโครงสร้างเนื้อหาให้ชัดเจน',
                    'เพิ่ม schema markup ที่เหมาะสม'
                ) :
                array(
                    'Include keywords in main headings and subheadings',
                    'Use keywords in first and last paragraphs',
                    'Add relevant internal links',
                    'Improve content structure for clarity',
                    'Add appropriate schema markup'
                ),
            'keyword_suggestions' => explode(',', $keywords ?: 'SEO optimization, content marketing, digital strategy'),
            'structure_improvements' => $language === 'th' ? 
                array(
                    'เพิ่มสารบัญในตอนต้น',
                    'แบ่งเนื้อหาเป็นหัวข้อย่อยที่ชัดเจน',
                    'เพิ่มสรุปในตอนท้าย',
                    'ใช้ bullet points และ numbered lists',
                    'เพิ่ม FAQ section'
                ) :
                array(
                    'Add table of contents at the beginning',
                    'Divide content into clear subsections',
                    'Add summary at the end',
                    'Use bullet points and numbered lists',
                    'Add FAQ section'
                ),
            'content_enhancements' => $language === 'th' ? 
                array(
                    'เพิ่มตัวอย่างและกรณีศึกษา',
                    'ใช้ข้อมูลสถิติและแหล่งอ้างอิง',
                    'เพิ่มรูปภาพและกราฟิก',
                    'สร้าง call-to-action ที่ชัดเจน'
                ) :
                array(
                    'Add examples and case studies',
                    'Use statistics and references',
                    'Add images and graphics',
                    'Create clear call-to-action'
                ),
            'seo_score_improvement' => '+15-25 points'
        );
        
        wp_send_json_success($fallback);
    }
    
    /**
     * Handles AJAX request for AI Keyword Research.
     * Takes seed keywords and industry, generates a prompt for the Gemini API,
     * processes the AI response, scores keywords using FluxSEOKeywordScoringEngine,
     * generates opportunity analysis and content strategy, saves results, and returns a comprehensive JSON response.
     *
     * @param string $language The language for keyword research ('en' or 'th').
     */
    private function handle_ai_keyword_research($language) {
        // Sanitize inputs from POST
        $seeds = isset($_POST['seeds']) ? sanitize_text_field(wp_unslash($_POST['seeds'])) : '';
        $industry = isset($_POST['industry']) ? sanitize_text_field(wp_unslash($_POST['industry'])) : '';
        
        // Construct the prompt for Gemini API for comprehensive keyword research
        $prompt = $language === 'th' ? 
            "ทำการวิจัยคำหลักแบบครอบคลุมสำหรับ seed keywords: '{$seeds}' ในอุตสาหกรรม: '{$industry}'\n\nกรุณาให้ผลลัพธ์ในรูปแบบ JSON ที่มี:" :
            "Conduct comprehensive keyword research for seed keywords: '{$seeds}' in industry: '{$industry}'\n\nPlease provide results in JSON format with:";

        // Define expected JSON structure for the prompt
        $json_structure_prompt = [
            "- primary_keywords: คำหลักหลัก (array with keyword, search_volume, difficulty, intent, relevance_score, ctr_potential)",
            "- long_tail_keywords: คำหลักแบบ long-tail (array with scoring data)",
            "- related_keywords: คำหลักที่เกี่ยวข้อง (array)",
            "- competitor_keywords: คำหลักของคู่แข่ง (array)",
            "- seasonal_keywords: คำหลักตามฤดูกาล (array)",
            "- local_keywords: คำหลักท้องถิ่น (array สำหรับตลาดไทย)",
            "- trending_keywords: คำหลักที่กำลังเป็นที่นิยม (array)",
            "- opportunity_analysis: การวิเคราะห์โอกาส (object)",
            "- content_strategy: กลยุทธ์เนื้อหา (object)",
            "- competitive_landscape: ภูมิทัศน์การแข่งขัน (object)"
        ];
        $prompt .= "\n" . implode("\n", $json_structure_prompt);

        $ai_response = $this->call_gemini_api($prompt, $language);

        // Log API errors but proceed if it's not a configuration issue, as some fallback data might still be generated.
        if (isset($ai_response['error']) && $ai_response['error'] && $ai_response['code'] !== 'api_key_not_configured') {
            error_log('Gemini API error during keyword research: ' . esc_html($ai_response['message']));
        }

        // Generate keyword data, potentially enhanced by AI response
        $keyword_data = $this->generate_enhanced_keyword_data($seeds, $industry, $language, (isset($ai_response['error']) ? null : $ai_response));

        // Score keywords using the dedicated engine
        $scored_keywords = $this->keyword_scoring_engine->score_keyword_batch($keyword_data);

        // Analyze opportunities and generate content strategy
        $opportunities = $this->keyword_scoring_engine->analyze_keyword_opportunities($keyword_data); // Requires keyword_data with all metrics
        $content_strategy = $this->keyword_scoring_engine->generate_content_strategy($scored_keywords);

        // Save results to the database
        $this->save_keyword_research_results($scored_keywords, $opportunities, $content_strategy, $language);

        // Prepare a comprehensive response for the frontend
        $response = array(
            'scored_keywords' => $scored_keywords,
            'opportunities' => $opportunities,
            'content_strategy' => $content_strategy,
            'model_config' => $this->keyword_scoring_engine->get_model_config(), // Configuration of the scoring model
            'total_keywords' => count($scored_keywords),
            'tier_distribution' => $this->calculate_tier_distribution($scored_keywords),
            'priority_breakdown' => $this->calculate_priority_breakdown($scored_keywords),
            'estimated_timeline' => $this->estimate_implementation_timeline($scored_keywords),
            'roi_projection' => $this->calculate_roi_projection($scored_keywords),
            'competitive_analysis' => $this->generate_competitive_analysis($keyword_data, $language), // Based on generated/simulated data
            'success_metrics' => $this->define_success_metrics($scored_keywords, $language)
        );

        wp_send_json_success($response);
    }

    /**
     * Generates a base set of keyword data and variations from seed keywords and industry.
     * This data is then scored by the keyword scoring engine.
     * If an AI response is provided, it attempts to incorporate AI-suggested keywords.
     * Note: Many metrics here are simulated/estimated.
     *
     * @param string $seeds Comma-separated seed keywords.
     * @param string $industry The industry/niche.
     * @param string $language Language for the keywords.
     * @param array|null $ai_response Optional AI response to enhance keyword list.
     * @return array Associative array of keyword data.
     */
    private function generate_enhanced_keyword_data($seeds, $industry, $language, $ai_response = null) {
- primary_keywords: คำหลักหลัก (array with keyword, search_volume, difficulty, intent, relevance_score, ctr_potential)
- long_tail_keywords: คำหลักแบบ long-tail (array with scoring data)
- related_keywords: คำหลักที่เกี่ยวข้อง (array)
- competitor_keywords: คำหลักของคู่แข่ง (array)
- seasonal_keywords: คำหลักตามฤดูกาล (array)
- local_keywords: คำหลักท้องถิ่น (array สำหรับตลาดไทย)
- trending_keywords: คำหลักที่กำลังเป็นที่นิยม (array)
- opportunity_analysis: การวิเคราะห์โอกาส (object)
- content_strategy: กลยุทธ์เนื้อหา (object)
- competitive_landscape: ภูมิทัศน์การแข่งขัน (object)" :
            
            "Conduct comprehensive keyword research for seed keywords: '{$seeds}' in industry: '{$industry}'

Please provide results in JSON format with:
- primary_keywords: Primary keywords (array with keyword, search_volume, difficulty, intent, relevance_score, ctr_potential)
- long_tail_keywords: Long-tail keywords (array with scoring data)
- related_keywords: Related keywords (array)
- competitor_keywords: Competitor keywords (array)
- seasonal_keywords: Seasonal keywords (array)
- local_keywords: Local keywords (array)
- trending_keywords: Trending keywords (array)
- opportunity_analysis: Opportunity analysis (object)
- content_strategy: Content strategy recommendations (object)
- competitive_landscape: Competitive landscape analysis (object)";
        
        $ai_response = $this->call_gemini_api($prompt, $language);

        if (isset($ai_response['error']) && $ai_response['error'] && $ai_response['code'] !== 'api_key_not_configured') {
            // Log the error but proceed with fallback if it's not an API key issue,
            // as keyword research might still provide some value without AI enrichment.
            error_log('Gemini API error during keyword research: ' . $ai_response['message']);
        }
        
        // Generate comprehensive keyword data with AI enhancement
        // Pass the AI response, it might be an error structure or actual data
        $keyword_data = $this->generate_enhanced_keyword_data($seeds, $industry, $language, (isset($ai_response['error']) ? null : $ai_response));
        
        // Use the keyword scoring engine for accurate scoring
        $scored_keywords = $this->keyword_scoring_engine->score_keyword_batch($keyword_data);
        
        // Generate opportunity analysis
        $opportunities = $this->keyword_scoring_engine->analyze_keyword_opportunities($keyword_data);
        
        // Generate content strategy
        $content_strategy = $this->keyword_scoring_engine->generate_content_strategy($scored_keywords);
        
        // Save to database for future reference
        $this->save_keyword_research_results($scored_keywords, $opportunities, $content_strategy, $language);
        
        // Prepare comprehensive response
        $response = array(
            'scored_keywords' => $scored_keywords,
            'opportunities' => $opportunities,
            'content_strategy' => $content_strategy,
            'model_config' => $this->keyword_scoring_engine->get_model_config(),
            'total_keywords' => count($scored_keywords),
            'tier_distribution' => $this->calculate_tier_distribution($scored_keywords),
            'priority_breakdown' => $this->calculate_priority_breakdown($scored_keywords),
            'estimated_timeline' => $this->estimate_implementation_timeline($scored_keywords),
            'roi_projection' => $this->calculate_roi_projection($scored_keywords),
            'competitive_analysis' => $this->generate_competitive_analysis($keyword_data, $language),
            'success_metrics' => $this->define_success_metrics($scored_keywords, $language)
        );
        
        wp_send_json_success($response);
    }
    
    private function generate_enhanced_keyword_data($seeds, $industry, $language, $ai_response = null) {
        $keyword_data = array();
        $seed_array = array_map('trim', explode(',', $seeds));
        
        // Parse AI response if available
        $ai_keywords = array();
        if ($ai_response) {
            preg_match('/\{.*\}/s', $ai_response, $matches);
            if (!empty($matches)) {
                $ai_data = json_decode($matches[0], true);
                if ($ai_data && isset($ai_data['primary_keywords'])) {
                    $ai_keywords = $ai_data['primary_keywords'];
                }
            }
        }
        
        // Generate comprehensive keyword variations
        $all_keywords = array();
        
        // Primary seed keywords
        foreach ($seed_array as $seed) {
            $all_keywords[] = $seed;
            $all_keywords[] = $seed . ' guide';
            $all_keywords[] = $seed . ' tips';
            $all_keywords[] = 'best ' . $seed;
            $all_keywords[] = $seed . ' 2024';
            
            if ($language === 'th') {
                $all_keywords[] = $seed . ' คือ';
                $all_keywords[] = $seed . ' วิธีการ';
                $all_keywords[] = $seed . ' ประโยชน์';
                $all_keywords[] = $seed . ' สำหรับคนไทย';
            } else {
                $all_keywords[] = 'what is ' . $seed;
                $all_keywords[] = 'how to ' . $seed;
                $all_keywords[] = $seed . ' benefits';
                $all_keywords[] = $seed . ' for beginners';
            }
        }
        
        // Add industry-specific variations
        if ($industry) {
            foreach ($seed_array as $seed) {
                $all_keywords[] = $seed . ' ' . $industry;
                $all_keywords[] = $industry . ' ' . $seed;
            }
        }
        
        // Generate scoring data for each keyword
        foreach (array_unique($all_keywords) as $keyword) {
            $keyword_data[$keyword] = array(
                'search_volume' => $this->estimate_search_volume($keyword, $language),
                'keyword_difficulty' => $this->estimate_keyword_difficulty($keyword, $language),
                'relevance' => $this->calculate_relevance_score($keyword, $seeds, $industry),
                'user_intent' => $this->determine_user_intent($keyword),
                'current_rank' => $this->get_current_ranking($keyword),
                'ctr_potential' => $this->estimate_ctr_potential($keyword),
                'cpc_value' => $this->estimate_cpc_value($keyword),
                'trend_direction' => $this->analyze_trend_direction($keyword),
                'seasonality_score' => $this->calculate_seasonality_score($keyword),
                'local_volume' => $language === 'th' ? $this->estimate_local_volume($keyword) : 0,
                'competition_analysis' => $this->analyze_competition($keyword, $language),
                'content_suggestions' => $this->generate_content_suggestions($keyword, $language),
                'optimization_recommendations' => $this->generate_optimization_recommendations($keyword, $language)
            );
        }
        
        return $keyword_data;
    }
    
    /**
     * Estimates search volume for a given keyword.
     * Placeholder: Simulates volume based on keyword length and language.
     *
     * @param string $keyword The keyword.
     * @param string $language Language of the keyword.
     * @return int Estimated search volume.
     */
    private function estimate_search_volume($keyword, $language) {
        // Simulate search volume based on keyword characteristics
        $base_volume = 1000;
        
        // Adjust based on keyword length (shorter = higher volume)
        $word_count = str_word_count($keyword);
        if ($word_count <= 2) {
            $base_volume *= 3;
        } elseif ($word_count <= 3) {
            $base_volume *= 2;
        }
        
        // Adjust for language
        if ($language === 'th') {
            $base_volume *= 0.3; // Assuming Thai market is smaller for general terms
        }
        
        // Add randomization for realism
        return rand(intval($base_volume * 0.5), intval($base_volume * 2));
    }
    
    /**
     * Estimates keyword difficulty.
     * Placeholder: Simulates difficulty based on keyword length and common modifiers.
     *
     * @param string $keyword The keyword.
     * @param string $language Language of the keyword.
     * @return int Estimated keyword difficulty (0-100).
     */
    private function estimate_keyword_difficulty($keyword, $language) {
        // Simulate difficulty based on keyword characteristics
        $base_difficulty = 50;
        
        // Shorter keywords are typically more competitive
        $word_count = str_word_count($keyword);
        if ($word_count <= 2) {
            $base_difficulty += 20;
        } elseif ($word_count >= 4) {
            $base_difficulty -= 15;
        }
        
        // Commercial intent keywords are more competitive
        if (strpos($keyword, 'best') !== false || strpos($keyword, 'buy') !== false) {
            $base_difficulty += 15;
        }
        
        // Question keywords are less competitive
        if (strpos($keyword, 'what') !== false || strpos($keyword, 'how') !== false) {
            $base_difficulty -= 10;
        }
        
        return min(100, max(10, $base_difficulty + rand(-10, 10)));
    }
    
    /**
     * Calculates a relevance score for the keyword based on seed keywords and industry.
     * Placeholder: Basic string matching.
     *
     * @param string $keyword The keyword.
     * @param string $seeds Comma-separated seed keywords.
     * @param string $industry The industry.
     * @return int Relevance score (1-10).
     */
    private function calculate_relevance_score($keyword, $seeds, $industry) {
        $relevance = 5; // Base score
        
        // Check if keyword contains seed terms
        $seed_array = explode(',', $seeds);
        foreach ($seed_array as $seed_term) {
            if (stripos($keyword, trim($seed_term)) !== false) {
                $relevance += 3;
                break; // Found a seed term, add score and exit loop
            }
        }
        
        // Check industry relevance
        if (!empty($industry) && stripos($keyword, $industry) !== false) {
            $relevance += 2;
        }
        
        return min(10, max(1, $relevance)); // Ensure score is within 1-10 range
    }
    
    /**
     * Determines the likely user intent behind a keyword.
     * Placeholder: Uses regex to identify common intent patterns.
     *
     * @param string $keyword The keyword.
     * @return string User intent (e.g., 'transactional', 'commercial', 'informational', 'navigational').
     */
    private function determine_user_intent($keyword) {
        $keyword_lower = strtolower($keyword);
        
        // Transactional intent (buy, purchase, order, price, cost, cheap, discount, deal)
        if (preg_match('/\b(buy|purchase|order|price|cost|cheap|discount|deal)\b/i', $keyword_lower)) {
            return 'transactional';
        }
        
        // Commercial investigation (best, top, review, compare, vs, versus, alternative)
        if (preg_match('/\b(best|top|review|compare|vs|versus|alternative)\b/i', $keyword_lower)) {
            return 'commercial';
        }
        
        // Informational intent (what, how, why, when, where, guide, tutorial, tips, learn)
        if (preg_match('/\b(what|how|why|when|where|guide|tutorial|tips|learn)\b/i', $keyword_lower)) {
            return 'informational';
        }
        
        // Navigational intent (login, sign in, official, website, homepage)
        if (preg_match('/\b(login|sign in|official|website|homepage)\b/i', $keyword_lower)) {
            return 'navigational';
        }
        
        return 'informational'; // Default intent
    }
    
    /**
     * Gets the current ranking for a keyword.
     * Placeholder: Simulates ranking. In a real implementation, this would use Search Console API or a rank tracker.
     *
     * @param string $keyword The keyword.
     * @return int Current ranking (0 if not ranking, 1-100+ otherwise).
     */
    private function get_current_ranking($keyword) {
        // Simulate current ranking (0 means not ranking)
        return rand(0, 100);
    }
    
    /**
     * Estimates Click-Through Rate (CTR) potential for a keyword.
     * Placeholder: Basic estimation based on keyword type.
     *
     * @param string $keyword The keyword.
     * @return int Estimated CTR potential (1-10 scale or percentage).
     */
    private function estimate_ctr_potential($keyword) {
        $base_ctr = 7; // Base CTR potential (arbitrary scale 1-10)
        
        // Question keywords often have higher CTR
        if (preg_match('/\b(what|how|why|when|where)\b/i', strtolower($keyword))) {
            $base_ctr += 1;
        }
        
        // Commercial keywords may have lower CTR due to ads and competition on SERP
        if (preg_match('/\b(buy|price|cheap|discount)\b/i', strtolower($keyword))) {
            $base_ctr -= 2;
        }
        
        return min(10, max(1, $base_ctr + rand(-1, 1))); // Ensure score is within 1-10
    }
    
    /**
     * Estimates Cost Per Click (CPC) value for a keyword.
     * Placeholder: Simulates CPC based on user intent.
     *
     * @param string $keyword The keyword.
     * @return float Estimated CPC value.
     */
    private function estimate_cpc_value($keyword) {
        // Simulate CPC based on commercial intent
        $intent = $this->determine_user_intent($keyword);
        
        switch ($intent) {
            case 'transactional':
                return rand(200, 1000) / 100.0; // $2.00 - $10.00
            case 'commercial':
                return rand(100, 500) / 100.0;  // $1.00 - $5.00
            case 'informational':
                return rand(20, 100) / 100.0;   // $0.20 - $1.00
            default: // Navigational or other
                return rand(10, 50) / 100.0;    // $0.10 - $0.50
        }
    }
    
    /**
     * Analyzes the trend direction for a keyword.
     * Placeholder: Simulates trend (up, stable, down).
     *
     * @param string $keyword The keyword.
     * @return string Trend direction.
     */
    private function analyze_trend_direction($keyword) {
        // Simulate trend analysis
        $trends = array('up', 'stable', 'down');
        // Weighted random selection: 30% up, 50% stable, 20% down
        $rand_val = rand(1, 100);
        if ($rand_val <= 30) return 'up';
        if ($rand_val <= 80) return 'stable'; // 30 (up) + 50 (stable) = 80
        return 'down';
    }
    
    /**
     * Calculates a seasonality score for a keyword.
     * Placeholder: Checks for common seasonal terms.
     *
     * @param string $keyword The keyword.
     * @return int Seasonality score (0-100).
     */
    private function calculate_seasonality_score($keyword) {
        // Check for seasonal keywords
        $seasonal_terms = array(
            'christmas', 'holiday', 'summer', 'winter', 'spring', 'fall', 'autumn',
            'new year', 'valentine', 'easter', 'halloween', 'thanksgiving', 'black friday', 'cyber monday'
        );
        
        foreach ($seasonal_terms as $term) {
            if (stripos($keyword, $term) !== false) {
                return rand(70, 100); // High seasonality
            }
        }
        
        return rand(0, 30); // Low seasonality for non-matching terms
    }
    
    /**
     * Estimates local search volume.
     * Placeholder: Specific to Thai market in this simulation.
     *
     * @param string $keyword The keyword.
     * @return int Estimated local search volume.
     */
    private function estimate_local_volume($keyword) {
        // Estimate local search volume for Thai market (example)
        return rand(50, 500);
    }
    
    /**
     * Analyzes competition for a keyword.
     * Placeholder: Returns a generic statement.
     *
     * @param string $keyword The keyword.
     * @param string $language Language of the keyword.
     * @return string Competition analysis statement.
     */
    private function analyze_competition($keyword, $language) {
        // This is a very basic placeholder. Real analysis would involve SERP scraping/analysis.
        return $language === 'th' ? 
            "การแข่งขันปานกลาง มีโอกาสในการจัดอันดับดีหากมีเนื้อหาที่มีคุณภาพสูงและตรงเป้าหมาย" :
            "Moderate competition with good ranking opportunities for high-quality, targeted content";
    }
    
    /**
     * Generates content suggestions based on keyword intent.
     * Placeholder: Provides generic suggestions.
     *
     * @param string $keyword The keyword.
     * @param string $language Language for suggestions.
     * @return string Content suggestions.
     */
    private function generate_content_suggestions($keyword, $language) {
        $intent = $this->determine_user_intent($keyword);
        
        if ($language === 'th') {
            switch ($intent) {
                case 'informational':
                    return "สร้างบทความคู่มือที่ครอบคลุม, เพิ่มตัวอย่าง, กรณีศึกษา, และตอบคำถามที่เกี่ยวข้อง (FAQ).";
                case 'commercial':
                    return "สร้างเนื้อหาเปรียบเทียบผลิตภัณฑ์/บริการ, รีวิวอย่างละเอียด, และเน้นข้อดีข้อเสีย.";
                case 'transactional':
                    return "สร้างหน้าผลิตภัณฑ์หรือบริการที่ชัดเจน, มีข้อมูลครบถ้วน, call-to-action ที่โดดเด่น, และรีวิวจากลูกค้า.";
                default: // Navigational or other
                    return "ตรวจสอบให้แน่ใจว่าหน้าเป้าหมายสำหรับคำหลักนี้ใช้งานง่ายและให้ข้อมูลที่ผู้ใช้ค้นหาโดยตรง.";
            }
        } else { // English
            switch ($intent) {
                case 'informational':
                    return "Create comprehensive guide articles, add examples, case studies, and answer related questions (FAQs).";
                case 'commercial':
                    return "Develop product/service comparison content, detailed reviews, and highlight pros and cons.";
                case 'transactional':
                    return "Build clear product or service pages with complete information, strong CTAs, and customer reviews.";
                default: // Navigational or other
                    return "Ensure the target page for this keyword is user-friendly and directly provides what the user is searching for.";
            }
        }
    }
    
    /**
     * Generates optimization recommendations for a keyword.
     * Placeholder: Provides generic on-page SEO tips.
     *
     * @param string $keyword The keyword.
     * @param string $language Language for recommendations.
     * @return array Array of optimization recommendation strings.
     */
    private function generate_optimization_recommendations($keyword, $language) {
        // Generic recommendations, can be expanded based on keyword analysis
        if ($language === 'th') {
            return array(
                "ใช้คำหลัก '{$keyword}' ในแท็ก Title และ Meta Description อย่างเป็นธรรมชาติ.",
                "รวมคำหลัก '{$keyword}' ในหัวข้อหลัก (H1) และหัวข้อย่อย (H2, H3) ที่เกี่ยวข้อง.",
                "กระจายคำหลัก '{$keyword}' และคำที่เกี่ยวข้องในเนื้อหาอย่างสม่ำเสมอ.",
                "สร้างลิงก์ภายในไปยังหน้านี้จากเนื้อหาที่เกี่ยวข้องโดยใช้ anchor text ที่มีความหมาย.",
                "ปรับปรุงรูปภาพด้วย alt text ที่มีคำหลัก '{$keyword}' หากเหมาะสม."
            );
        } else { // English
            return array(
                "Use the keyword '{$keyword}' naturally in the Title tag and Meta Description.",
                "Include '{$keyword}' in the main heading (H1) and relevant subheadings (H2, H3).",
                "Distribute '{$keyword}' and related terms throughout the content body.",
                "Build internal links to this page from related content using descriptive anchor text.",
                "Optimize images with alt text that includes '{$keyword}' where appropriate."
            );
        }
    }
    
    /**
     * Saves keyword research results (scored keywords, opportunities, content strategy) to the database.
     *
     * @param array $scored_keywords Array of scored keyword items.
     * @param array $opportunities Array of keyword opportunities.
     * @param array $content_strategy Content strategy data.
     * @param string $language Language of the research.
     */
    private function save_keyword_research_results($scored_keywords, $opportunities, $content_strategy, $language) {
        global $wpdb;
        
        // Define table names
        $keyword_table = $wpdb->prefix . 'flux_seo_keyword_scoring';
        $opportunities_table = $wpdb->prefix . 'flux_seo_keyword_opportunities';
        $strategy_table = $wpdb->prefix . 'flux_seo_content_strategy';
        
        // Save scored keywords
        if (is_array($scored_keywords)) {
            foreach ($scored_keywords as $item) {
                if (!is_array($item) || !isset($item['keyword']) || !isset($item['data']) || !is_array($item['data'])) {
                    // Log or skip invalid item structure
                    error_log('Flux SEO: Invalid item structure in $scored_keywords: ' . print_r($item, true));
                    continue;
                }
                $data = $item['data'];
                $wpdb->replace( // Use replace to insert or update based on primary/unique key
                    $keyword_table,
                    array(
                        'keyword' => sanitize_text_field($item['keyword']),
                        'language' => sanitize_text_field($language),
                        'search_volume' => intval($data['search_volume'] ?? 0),
                        'keyword_difficulty' => intval($data['keyword_difficulty'] ?? 0),
                        'relevance_score' => intval($data['relevance'] ?? 0),
                        'user_intent' => sanitize_text_field($data['user_intent'] ?? 'informational'),
                        'current_rank' => intval($data['current_rank'] ?? 0),
                        'ctr_potential' => intval($data['ctr_potential'] ?? 0),
                        'cpc_value' => floatval($data['cpc_value'] ?? 0.00),
                        'trend_direction' => sanitize_text_field($data['trend_direction'] ?? 'stable'),
                        'seasonality_score' => intval($data['seasonality_score'] ?? 0),
                        'local_volume' => intval($data['local_volume'] ?? 0),
                        'competition_analysis' => sanitize_textarea_field($data['competition_analysis'] ?? ''),
                        'calculated_score' => floatval($item['score'] ?? 0.00),
                        'tier' => sanitize_text_field($item['tier'] ?? 'Tier 3'),
                        'priority' => sanitize_text_field($item['priority'] ?? 'Medium'),
                        'content_suggestions' => wp_json_encode($data['content_suggestions'] ?? array()),
                        'optimization_recommendations' => wp_json_encode($data['optimization_recommendations'] ?? array())
                    )
                );
            }
        }
        
        // Save opportunities
        if (is_array($opportunities)) {
            foreach ($opportunities as $type => $items) {
                if (is_array($items)) {
                    foreach ($items as $opportunity) {
                         if (!is_array($opportunity) || !isset($opportunity['keyword'])) {
                            error_log('Flux SEO: Invalid item structure in $opportunities: ' . print_r($opportunity, true));
                            continue;
                        }
                        $wpdb->insert(
                            $opportunities_table,
                            array(
                                'opportunity_type' => sanitize_text_field($type),
                                'keyword' => sanitize_text_field($opportunity['keyword']),
                                'language' => sanitize_text_field($language),
                                'opportunity_score' => floatval($opportunity['score'] ?? 0.00),
                                'estimated_traffic' => intval(rand(100, 1000)), // Placeholder
                                'difficulty_level' => sanitize_text_field($this->map_score_to_difficulty($opportunity['score'] ?? 0)),
                                'time_to_rank' => sanitize_text_field($this->estimate_time_to_rank($opportunity['score'] ?? 0)),
                                'required_effort' => sanitize_text_field($this->estimate_required_effort($opportunity['score'] ?? 0)),
                                'action_plan' => sanitize_textarea_field($opportunity['reason'] ?? ''),
                                'status' => 'Identified' // Default status
                            )
                        );
                    }
                }
            }
        }

        // Save content strategy
        if (is_array($content_strategy) && !empty($scored_keywords)) {
            $wpdb->insert(
                $strategy_table,
                array(
                    'strategy_name' => sanitize_text_field('AI Generated Strategy - ' . current_time('mysql')),
                    'target_keywords' => wp_json_encode(array_column($scored_keywords, 'keyword')),
                    'content_calendar' => wp_json_encode($content_strategy['content_calendar'] ?? array()),
                    'optimization_targets' => wp_json_encode($content_strategy['optimization_targets'] ?? array()),
                    'link_building_plan' => wp_json_encode($content_strategy['link_building_priorities'] ?? array()),
                    'performance_metrics' => wp_json_encode($this->define_success_metrics($scored_keywords, $language)),
                    'timeline' => '3-6 months', // Default timeline
                    'status' => 'Draft',        // Default status
                    'created_by' => get_current_user_id(),
                    'language' => sanitize_text_field($language)
                )
            );
        }
    }
    
    /**
     * Calculates the distribution of keywords across different tiers.
     *
     * @param array $scored_keywords Array of scored keyword items.
     * @return array Associative array with tier counts (Tier 1, Tier 2, Tier 3).
     */
    private function calculate_tier_distribution($scored_keywords) {
        $distribution = array('Tier 1' => 0, 'Tier 2' => 0, 'Tier 3' => 0);
        if (!is_array($scored_keywords)) return $distribution;

        foreach ($scored_keywords as $item) {
            if (isset($item['tier']) && array_key_exists($item['tier'], $distribution)) {
                $distribution[$item['tier']]++;
            }
        }
        return $distribution;
    }
    
    /**
     * Calculates the breakdown of keywords by priority level.
     *
     * @param array $scored_keywords Array of scored keyword items.
     * @return array Associative array with priority counts.
     */
    private function calculate_priority_breakdown($scored_keywords) {
        $breakdown = array();
        if (!is_array($scored_keywords)) return $breakdown;

        foreach ($scored_keywords as $item) {
            if (isset($item['priority'])) {
                $priority = sanitize_text_field($item['priority']);
                if (!isset($breakdown[$priority])) {
                    $breakdown[$priority] = 0;
                }
                $breakdown[$priority]++;
            }
        }
        return $breakdown;
    }
    
    /**
     * Estimates the implementation timeline for keyword strategy based on tiers.
     *
     * @param array $scored_keywords Array of scored keyword items.
     * @return array Associative array with timeline estimates (immediate, short_term, long_term).
     */
    private function estimate_implementation_timeline($scored_keywords) {
        if (!is_array($scored_keywords)) {
            return array('immediate' => '0 keywords', 'short_term' => '0 keywords', 'long_term' => '0 keywords');
        }
        $tier1_count = 0;
        $tier2_count = 0;
        foreach($scored_keywords as $item) {
            if (isset($item['tier'])) {
                if ($item['tier'] === 'Tier 1') $tier1_count++;
                elseif ($item['tier'] === 'Tier 2') $tier2_count++;
            }
        }
        $total_keywords = count($scored_keywords);
        
        return array(
            'immediate' => $tier1_count . ' keywords (1-2 weeks)',
            'short_term' => $tier2_count . ' keywords (1-3 months)',
            'long_term' => ($total_keywords - $tier1_count - $tier2_count) . ' keywords (3-6 months)'
        );
    }
    
    /**
     * Calculates a rough ROI projection based on potential traffic and estimated values.
     * Placeholder: Uses simplified assumptions.
     *
     * @param array $scored_keywords Array of scored keyword items.
     * @return array Associative array with ROI projection details.
     */
    private function calculate_roi_projection($scored_keywords) {
        $total_potential_traffic = 0;
        $estimated_conversion_rate = 0.02; // 2% - configurable or dynamic in future
        $average_order_value = 100; // $100 - configurable or dynamic in future

        if (is_array($scored_keywords)) {
            foreach ($scored_keywords as $item) {
                if (isset($item['data']['search_volume'])) {
                    // Assume a conservative potential CTR (e.g., 10% of search volume if ranked well)
                    $potential_traffic_for_keyword = intval($item['data']['search_volume']) * 0.10;
                    $total_potential_traffic += $potential_traffic_for_keyword;
                }
            }
        }
        
        $estimated_conversions = $total_potential_traffic * $estimated_conversion_rate;
        $estimated_revenue = $estimated_conversions * $average_order_value;
        
        return array(
            'potential_monthly_traffic' => round($total_potential_traffic),
            'estimated_monthly_conversions' => round($estimated_conversions),
            'estimated_monthly_revenue' => '$' . number_format($estimated_revenue, 2),
            'roi_timeline' => '6-12 months' // Generic timeline
        );
    }
    
    /**
     * Generates a conceptual competitive analysis.
     * Placeholder: Returns predefined data.
     *
     * @param array $keyword_data Base keyword data (not directly used in this placeholder).
     * @param string $language Language for the analysis.
     * @return array Associative array with competitive analysis details.
     */
    private function generate_competitive_analysis($keyword_data, $language) {
        // This is a placeholder. Real competitive analysis would involve actual SERP data.
        if ($language === 'th') {
            return array(
                'market_saturation' => 'ปานกลาง',
                'top_competitors' => array('คู่แข่งหลัก ก', 'คู่แข่งหลัก ข', 'คู่แข่งหลัก ค'),
                'competitive_advantages' => array(
                    'โอกาสในการสร้างเนื้อหาเฉพาะทางและมีคุณภาพสูง',
                    'การใช้ภาษาไทยที่สละสลวยและเป็นธรรมชาติ',
                    'ความเข้าใจในวัฒนธรรมและพฤติกรรมผู้บริโภคชาวไทย'
                ),
                'market_gaps' => array(
                    'เนื้อหาเชิงลึกเฉพาะอุตสาหกรรมในภาษาไทย',
                    'คำแนะนำที่ปรับให้เข้ากับตลาดท้องถิ่นอย่างแท้จริง',
                    'กรณีศึกษาและตัวอย่างจากธุรกิจในประเทศไทย'
                )
            );
        } else { // English
            return array(
                'market_saturation' => 'Moderate',
                'top_competitors' => array('Major Competitor A', 'Major Competitor B', 'Major Competitor C'),
                'competitive_advantages' => array(
                    'Opportunity for niche, high-quality content creation',
                    'Focus on superior user experience and site speed',
                    'Building authority through comprehensive topic coverage'
                ),
                'market_gaps' => array(
                    'In-depth technical content for advanced users',
                    'Beginner-friendly guides with clear, actionable steps',
                    'Localized content for specific regions or demographics'
                )
            );
        }
    }
    
    /**
     * Defines success metrics for the keyword strategy.
     * Placeholder: Returns predefined metrics.
     *
     * @param array $scored_keywords Array of scored keyword items (not directly used in this placeholder).
     * @param string $language Language for the metrics.
     * @return array Associative array with primary, secondary, and business metrics.
     */
    private function define_success_metrics($scored_keywords, $language) {
        // These are example metrics. Real metrics should be tailored to business goals.
        if ($language === 'th') {
            return array(
                'primary_metrics' => array(
                    'การเพิ่มขึ้นของ organic traffic 20-30% ภายใน 6 เดือน',
                    'คำหลัก Tier 1 อย่างน้อย 50% ติด Top 10 ภายใน 3-4 เดือน',
                    'การปรับปรุง average ranking โดยรวม 10-15 อันดับ'
                ),
                'secondary_metrics' => array(
                    'เพิ่ม click-through rate (CTR) เฉลี่ย 5-10%',
                    'ลด bounce rate หน้าหลักลง 10-15%',
                    'เพิ่ม time on page เฉลี่ย 15-20%'
                ),
                'business_metrics' => array(
                    'เพิ่มจำนวน leads จาก organic search 15-25%',
                    'ปรับปรุง conversion rate จาก organic traffic 3-5%',
                    'เพิ่ม brand visibility และ mentions ในช่องทางออนไลน์'
                )
            );
        } else { // English
            return array(
                'primary_metrics' => array(
                    '20-30% increase in organic traffic within 6 months',
                    'At least 50% of Tier 1 keywords in Top 10 within 3-4 months',
                    '10-15 position improvement in overall average ranking'
                ),
                'secondary_metrics' => array(
                    '5-10% increase in average click-through rate (CTR)',
                    '10-15% reduction in key landing page bounce rates',
                    '15-20% increase in average time on page'
                ),
                'business_metrics' => array(
                    '15-25% increase in lead generation from organic search',
                    '3-5% improvement in conversion rate from organic traffic',
                    'Increased brand visibility and online mentions'
                )
            );
        }
    }
    
    /**
     * Maps a keyword opportunity score to a difficulty level.
     *
     * @param float $score The opportunity score.
     * @return string Difficulty level ('Easy', 'Medium', 'Hard').
     */
    private function map_score_to_difficulty($score) {
        if ($score >= 8) return 'Easy';
        if ($score >= 6) return 'Medium';
        return 'Hard';
    }
    
    /**
     * Estimates time to rank based on an opportunity score.
     *
     * @param float $score The opportunity score.
     * @return string Estimated time to rank.
     */
    private function estimate_time_to_rank($score) {
        if ($score >= 8) return '1-3 months';
        if ($score >= 6) return '3-6 months';
        return '6-12 months';
    }
    
    /**
     * Estimates required effort based on an opportunity score.
     *
     * @param float $score The opportunity score.
     * @return string Estimated required effort ('Low', 'Medium', 'High').
     */
    private function estimate_required_effort($score) {
        if ($score >= 8) return 'Low';
        if ($score >= 6) return 'Medium';
        return 'High';
    }
    
    /**
     * Saves generated content data to the database.
     *
     * @param array $data Associative array of generated content data (title, content, meta, etc.).
     * @param string $language Language of the content.
     * @param string $topic Original topic for generation.
     * @param string $content_type Type of content generated.
     * @param string $tone Tone of the content.
     * @param string $audience Target audience.
     * @param string $keywords Keywords used for generation.
     */
    private function save_generated_content($data, $language, $topic, $content_type, $tone, $audience, $keywords) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'flux_seo_enhanced_content';
        
        $wpdb->insert(
            $table_name,
            array(
                'user_id' => get_current_user_id(),
                'language' => sanitize_text_field($language),
                'topic' => sanitize_text_field($topic),
                'content_type' => sanitize_text_field($content_type),
                'tone' => sanitize_text_field($tone),
                'target_audience' => sanitize_text_field($audience), // Ensure this is consistently passed
                'keywords' => sanitize_text_field($keywords),
                'generated_title' => sanitize_text_field($data['title'] ?? ''),
                'generated_content' => wp_kses_post($data['content_html'] ?? ($data['content'] ?? '')), // Allow safe HTML
                'generated_meta_description' => sanitize_textarea_field($data['meta_description'] ?? ''),
                'content_outline' => wp_json_encode($data['outline'] ?? array()),
                'seo_recommendations' => wp_json_encode($data['seo_recommendations'] ?? array()),
                'gemini_analysis' => wp_json_encode($data), // Store the whole AI response for reference
                'word_count' => intval(str_word_count(wp_strip_all_tags($data['content_html'] ?? ($data['content'] ?? '')))),
                'seo_score' => intval($data['seo_score_estimation'] ?? ($data['seo_score'] ?? 0)),
                'readability_score' => intval($data['readability_score_estimation'] ?? ($data['readability_score'] ?? 0)),
                'engagement_score' => intval($data['engagement_score'] ?? 0), // If available from AI
                'keyword_optimization_score' => intval(rand(70, 95)) // Placeholder score
            )
        );
    }
    
    /**
     * Saves website analysis data to the database.
     *
     * @param array $data Associative array of website analysis data.
     * @param string $url The URL analyzed.
     * @param string $language Language of the analysis.
     */
    private function save_website_analysis($data, $url, $language) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'flux_seo_enhanced_analytics';
        
        // Extract scores from nested structure if present (e.g. data.on_page_seo.score)
        // This depends on the exact structure returned by the AI for website analysis.
        // For now, assuming top-level scores for simplicity or that AI provides them flattened.
        $wpdb->insert(
            $table_name,
            array(
                'url' => esc_url_raw($url),
                'language' => sanitize_text_field($language),
                'seo_score' => intval($data['on_page_seo']['score'] ?? ($data['overall_site_health']['score'] ?? ($data['seo_score'] ?? 0))),
                'performance_score' => intval($data['page_speed_insights']['score'] ?? ($data['performance_score'] ?? 0)),
                'content_quality_score' => intval($data['content_strategy_analysis']['score'] ?? ($data['content_quality_score'] ?? 0)),
                'technical_seo_score' => intval($data['technical_seo']['score'] ?? ($data['technical_seo_score'] ?? 0)),
                'user_experience_score' => intval($data['user_experience_ux']['score'] ?? ($data['user_experience_score'] ?? 0)),
                'mobile_score' => intval($data['mobile_friendliness']['score'] ?? ($data['mobile_score'] ?? 0)),
                'page_speed_score' => intval($data['page_speed_insights']['score'] ?? ($data['page_speed_score'] ?? 0)), // Can be redundant with performance
                'security_score' => intval($data['security_analysis']['score'] ?? ($data['security_score'] ?? 0)),
                'accessibility_score' => intval($data['accessibility_wcag']['score'] ?? ($data['accessibility_score'] ?? 0)),
                'gemini_analysis' => wp_json_encode($data), // Store the whole AI response
                'recommendations' => wp_json_encode($data['prioritized_action_plan'] ?? ($data['recommendations'] ?? array())),
                'keywords_analysis' => wp_json_encode($data['keywords_analysis'] ?? array()), // If AI provides this
                'competitor_analysis' => wp_json_encode($data['conceptual_competitor_comparison'] ?? array()),
                'content_suggestions' => wp_json_encode($data['content_suggestions'] ?? array()), // If AI provides this
                'technical_issues' => wp_json_encode($data['technical_seo']['recommendations'] ?? ($data['technical_issues'] ?? array()))
            )
        );
    }
    
    /**
     * Provides a generic list of AI website recommendations.
     * Placeholder: Used if AI fails to provide specific recommendations.
     *
     * @param string $language Language for recommendations.
     * @return array Array of recommendation strings.
     */
    private function get_ai_website_recommendations($language) {
        if ($language === 'th') {
            return array(
                'ปรับปรุงความเร็วในการโหลดหน้าเว็บด้วยการบีบอัดรูปภาพและใช้ CDN',
                'เพิ่มประสิทธิภาพการใช้คำหลักในเนื้อหาและ Meta Tags อย่างเป็นธรรมชาติ',
                'ตรวจสอบและปรับปรุงโครงสร้างหัวข้อ (H1-H6) ให้มีลำดับชั้นที่ชัดเจนและสื่อความหมาย',
                'สร้างลิงก์ภายในที่มีคุณภาพเพื่อเชื่อมโยงเนื้อหาที่เกี่ยวข้องและกระจาย PageRank',
                'ใช้งาน Structured Data Markup (Schema.org) เพื่อช่วยให้เครื่องมือค้นหาเข้าใจเนื้อหาได้ดีขึ้น',
                'ตรวจสอบและปรับปรุงการแสดงผลบนอุปกรณ์เคลื่อนที่ (Mobile-Friendly) ให้สมบูรณ์แบบ',
                'สร้างเนื้อหาใหม่ที่มีคุณภาพสูง เป็นประโยชน์ และตอบโจทย์ผู้ใช้อยู่เสมอ',
                'ปรับปรุง Meta Descriptions ทั้งหมดให้น่าสนใจ กระตุ้นการคลิก และมีคำหลักสำคัญ',
                'ตรวจสอบให้แน่ใจว่าเว็บไซต์ใช้ HTTPS เพื่อความปลอดภัยและการจัดอันดับที่ดีขึ้น',
                'ปรับปรุงการเข้าถึงเว็บไซต์ (Accessibility) ตามมาตรฐาน WCAG เพื่อผู้ใช้ทุกคน'
            );
        }
        
        // English recommendations
        return array(
            'Improve page loading speed through image compression, browser caching, and using a CDN.',
            'Optimize keyword usage naturally within content, title tags, and meta descriptions.',
            'Review and improve heading structure (H1-H6) for clear hierarchy and relevance.',
            'Build quality internal links to connect related content and distribute link equity.',
            'Implement Structured Data Markup (Schema.org) to enhance search engine understanding.',
            'Ensure the website is fully mobile-responsive and provides an excellent mobile UX.',
            'Regularly create high-quality, valuable content that meets user intent.',
            'Optimize all meta descriptions to be compelling, click-worthy, and include target keywords.',
            'Ensure the entire website uses HTTPS for security and SEO benefits.',
            'Improve website accessibility according to WCAG standards for all users.'
        );
    }
    
    /**
     * Generates fallback content if the AI generation fails or is not available.
     * Provides a structured array with title, meta description, content, outline, etc.
     *
     * @param string $topic The topic for content generation.
     * @param string $language Language of the content ('en' or 'th').
     * @param string $content_type Type of content (e.g., 'blogPost').
     * @param string $tone Desired tone of the content.
     * @param string $word_count Approximate desired word count (not strictly enforced by fallback).
     * @param string $keywords Target keywords.
     * @return array Associative array of fallback content.
     */
    private function generate_fallback_content($topic, $language, $content_type, $tone, $word_count, $keywords) {
        $topic_placeholder = empty($topic) ? ($language === 'th' ? 'หัวข้อทั่วไป' : 'General Topic') : $topic;
        $keywords_placeholder = empty($keywords) ? ($language === 'th' ? 'SEO, การตลาดเนื้อหา' : 'SEO, content marketing') : $keywords;

        if ($language === 'th') {
            return array(
                'title' => "{$topic_placeholder}: คู่มือฉบับสมบูรณ์สำหรับปี " . date('Y'),
                'meta_description' => "เรียนรู้ทุกสิ่งเกี่ยวกับ {$topic_placeholder} พร้อมเทคนิคและกลยุทธ์ที่พิสูจน์แล้วว่าได้ผลจริง ✓ อัปเดตล่าสุดปี " . date('Y'),
                'content_html' => $this->generate_thai_content($topic_placeholder, $tone, $word_count, $keywords_placeholder),
                'outline' => array(
                    "บทนำ: ความสำคัญของ {$topic_placeholder} ในยุคปัจจุบัน",
                    "ความเข้าใจพื้นฐานเกี่ยวกับ {$topic_placeholder}",
                    "การประยุกต์ใช้ {$topic_placeholder} อย่างมีประสิทธิภาพ",
                    "เทคนิคและกลยุทธ์ขั้นสูงสำหรับ {$topic_placeholder}",
                    "ตัวอย่างการใช้งานจริงและกรณีศึกษาที่ประสบความสำเร็จ",
                    "เครื่องมือและแหล่งข้อมูลสำหรับ {$topic_placeholder}",
                    "แนวโน้มอนาคตของ {$topic_placeholder} และสิ่งที่ต้องเตรียมพร้อม",
                    "สรุปและคำแนะนำในการเริ่มต้นกับ {$topic_placeholder}"
                ),
                'seo_recommendations' => array(
                    'ใช้คำหลักหลักและคำหลักรองในหัวข้อหลักและหัวข้อย่อยอย่างเป็นธรรมชาติ',
                    'เพิ่มลิงก์ภายในไปยังเนื้อหาที่เกี่ยวข้องและลิงก์ภายนอกไปยังแหล่งข้อมูลที่น่าเชื่อถือ',
                    'ปรับรูปภาพให้เหมาะสม (ขนาด, alt text) และใช้สื่อผสมเพื่อเพิ่มการมีส่วนร่วม',
                    'ตรวจสอบให้แน่ใจว่าเนื้อหามีโครงสร้างที่ดี อ่านง่าย และตอบคำถามผู้ใช้อย่างครบถ้วน',
                    'เพิ่ม Schema Markup ที่เกี่ยวข้อง (เช่น Article, FAQPage) เพื่อเพิ่มการมองเห็นใน SERPs'
                ),
                'keywords_identified' => array_map('trim', explode(',', $keywords_placeholder)),
                'seo_score_estimation' => rand(70, 85), // Placeholder score
                'readability_score_estimation' => rand(65, 80), // Placeholder score
                'warnings' => array('นี่คือเนื้อหาตัวอย่างที่สร้างขึ้นโดยระบบสำรอง โปรดตรวจสอบและปรับปรุงก่อนเผยแพร่')
            );
        }
        
        // English fallback content
        return array(
            'title' => "The Ultimate Guide to {$topic_placeholder} for " . date('Y'),
            'meta_description' => "Discover everything about {$topic_placeholder} with proven techniques and strategies that deliver real results. ✓ Updated for " . date('Y'),
            'content_html' => $this->generate_english_content($topic_placeholder, $tone, $word_count, $keywords_placeholder),
            'outline' => array(
                "Introduction: The Importance of {$topic_placeholder} Today",
                "Understanding the Fundamentals of {$topic_placeholder}",
                "Effective Applications of {$topic_placeholder}",
                "Advanced Techniques and Strategies for {$topic_placeholder}",
                "Real-World Examples and Successful Case Studies",
                "Tools and Resources for Mastering {$topic_placeholder}",
                "Future Trends in {$topic_placeholder} and How to Prepare",
                "Conclusion and Getting Started with {$topic_placeholder}"
            ),
            'seo_recommendations' => array(
                'Naturally incorporate primary and secondary keywords in headings and subheadings.',
                'Include relevant internal links to related content and external links to authoritative sources.',
                'Optimize images (size, alt text) and use multimedia to enhance engagement.',
                'Ensure content is well-structured, easy to read, and comprehensively answers user queries.',
                'Add relevant Schema Markup (e.g., Article, FAQPage) to improve SERP visibility.'
            ),
            'keywords_identified' => array_map('trim', explode(',', $keywords_placeholder)),
            'seo_score_estimation' => rand(70, 85), // Placeholder score
            'readability_score_estimation' => rand(65, 80), // Placeholder score
            'warnings' => array('This is sample content generated by the fallback system. Please review and refine before publishing.')
        );
    }
    
    /**
     * Generates sample Thai content for fallback purposes.
     *
     * @param string $topic The topic.
     * @param string $tone Desired tone (not strictly used in this basic fallback).
     * @param string $word_count Desired word count (not strictly enforced).
     * @param string $keywords Target keywords.
     * @return string HTML formatted Thai content.
     */
    private function generate_thai_content($topic, $tone, $word_count, $keywords) {
        $content = "<h1>{$topic}: คู่มือฉบับสมบูรณ์ด้วยเทคโนโลยี AI สำหรับปี " . date('Y') . "</h1>\n\n";
        $content .= "<p>ในยุคดิจิทัลที่เปลี่ยนแปลงอย่างรวดเร็วนี้ <strong>{$topic}</strong> ได้กลายเป็นสิ่งที่สำคัญมากขึ้นเรื่อยๆ สำหรับคนไทยที่ต้องการก้าวทันโลกและสร้างความสำเร็จในชีวิต การเข้าใจและประยุกต์ใช้ {$topic} อย่างถูกต้องจะช่วยให้เราสามารถปรับตัวและเติบโตได้อย่างมีประสิทธิภาพ โดยเฉพาะเมื่อใช้ร่วมกับคำหลักเช่น <em>{$keywords}</em>.</p>\n\n";
        
        $content .= "<h2>ความสำคัญของ {$topic} ในยุคปัจจุบัน</h2>\n\n";
        $content .= "<p>{$topic} มีบทบาทสำคัญในการขับเคลื่อนการเปลี่ยนแปลงและพัฒนาในหลายๆ ด้าน ไม่ว่าจะเป็นในภาคธุรกิจ การศึกษา หรือการดำเนินชีวิตประจำวัน การเข้าใจและประยุกต์ใช้ {$topic} อย่างถูกต้องจะช่วยให้เราสามารถปรับตัวและเติบโตได้อย่างมีประสิทธิภาพ.</p>\n\n";
        
        $content .= "<h2>การประยุกต์ใช้ {$topic} ในการทำงาน</h2>\n\n";
        $content .= "<p>การนำ {$topic} มาใช้ในการทำงานต้องอาศัยความเข้าใจที่ถูกต้องและการวางแผนที่ดี เริ่มต้นจากการศึกษาหาข้อมูลที่เชื่อถือได้ จากนั้นทดลองประยุกต์ใช้ในขอบเขตเล็กๆ ก่อน แล้วค่อยขยายผลเมื่อเห็นผลลัพธ์ที่ดี.</p>\n\n";
        
        $content .= "<h3>เทคนิคและกลยุทธ์ที่มีประสิทธิภาพสำหรับ {$topic}</h3>\n\n";
        $content .= "<ul>\n";
        $content .= "<li><strong>การวางแผนอย่างเป็นระบบ</strong>: กำหนดเป้าหมายที่ชัดเจนและวัดผลได้</li>\n";
        $content .= "<li><strong>การเรียนรู้อย่างต่อเนื่อง</strong>: ติดตามความเปลี่ยนแปลงและแนวโน้มใหม่ๆ ที่เกี่ยวข้องกับ {$topic}</li>\n";
        $content .= "<li><strong>การประยุกต์ใช้เทคโนโลยี</strong>: ใช้เครื่องมือที่เหมาะสมเพื่อเพิ่มประสิทธิภาพในการทำงานกับ {$topic}</li>\n";
        $content .= "<li><strong>การวิเคราะห์ข้อมูล</strong>: ใช้ข้อมูลในการตัดสินใจและปรับปรุงกลยุทธ์สำหรับ {$topic}</li>\n";
        $content .= "</ul>\n\n";
        
        $content .= "<h3>ตัวอย่างการใช้งานจริงของ {$topic}</h3>\n\n";
        $content .= "<p>ในการประยุกต์ใช้ {$topic} ในสถานการณ์จริง เราสามารถเห็นผลลัพธ์ที่เป็นรูปธรรมได้หลายวิธี เช่น การเพิ่มประสิทธิภาพในการทำงาน การลดต้นทุน และการสร้างโอกาสใหม่ๆ ในธุรกิจที่เกี่ยวข้องกับ {$keywords}.</p>\n\n";
        
        $content .= "<h2>แนวโน้มและอนาคตของ {$topic}</h2>\n\n";
        $content .= "<p>ในอนาคต {$topic} จะมีบทบาทที่สำคัญมากขึ้น โดยเฉพาะในยุคของ AI และเทคโนโลยีดิจิทัล การเตรียมตัวและการเรียนรู้อย่างต่อเนื่องจะเป็นกุญแจสำคัญในการประสบความสำเร็จในด้าน {$topic}.</p>\n\n";
        
        $content .= "<h2>สรุปเกี่ยวกับ {$topic}</h2>\n\n";
        $content .= "<p>สรุปแล้ว {$topic} เป็นเรื่องที่น่าสนใจและมีประโยชน์มากสำหรับเราทุกคน การเรียนรู้และประยุกต์ใช้อย่างถูกวิธีจะช่วยให้เราก้าวไปข้างหน้าได้อย่างมั่นใจและประสบความสำเร็จในสิ่งที่ตั้งใจไว้ ด้วยเทคโนโลยี AI ที่ก้าวหน้า เราสามารถเข้าถึงข้อมูลและเครื่องมือที่มีคุณภาพได้ง่ายขึ้น และสร้างโอกาสใหม่ๆ ในการพัฒนาตนเองและธุรกิจด้วย {$topic}.</p>";
        
        // Simple replacement, for a real fallback, more sophisticated template might be needed.
        return str_replace('{$topic}', esc_html($topic), $content);
    }
    
    /**
     * Generates sample English content for fallback purposes.
     *
     * @param string $topic The topic.
     * @param string $tone Desired tone (not strictly used in this basic fallback).
     * @param string $word_count Desired word count (not strictly enforced).
     * @param string $keywords Target keywords.
     * @return string HTML formatted English content.
     */
    private function generate_english_content($topic, $tone, $word_count, $keywords) {
        $content = "<h1>The Complete AI-Powered Guide to {$topic} " . date('Y') . "</h1>\n\n";
        $content .= "<p>In today's rapidly evolving digital landscape, <strong>{$topic}</strong> has become increasingly important for businesses and individuals looking to stay competitive and achieve success. Understanding and properly implementing {$topic} strategies, especially focusing on keywords like <em>{$keywords}</em>, can help organizations adapt and grow efficiently in an ever-changing market.</p>\n\n";

        $content .= "<h2>Understanding the Fundamentals of {$topic}</h2>\n\n";
        $content .= "<p>{$topic} plays a crucial role in driving change and development across multiple sectors. Whether in business, education, or daily life operations, understanding and properly implementing {$topic} can help us adapt and grow efficiently.</p>\n\n";
        
        $content .= "<h2>Practical Applications of {$topic} in Modern Workflows</h2>\n\n";
        $content .= "<p>Implementing {$topic} in your workflow requires proper understanding and strategic planning. Start by researching reliable sources, then experiment with small-scale applications before expanding your efforts when you see positive results.</p>\n\n";
        
        $content .= "<h3>Effective Techniques and Strategies for {$topic}</h3>\n\n";
        $content .= "<ul>\n";
        $content .= "<li><strong>Systematic Planning</strong>: Set clear, measurable goals and objectives for your {$topic} efforts.</li>\n";
        $content .= "<li><strong>Continuous Learning</strong>: Stay updated with changes and new trends related to {$topic}.</li>\n";
        $content .= "<li><strong>Technology Integration</strong>: Use appropriate tools to enhance efficiency when working with {$topic}.</li>\n";
        $content .= "<li><strong>Data-Driven Decisions</strong>: Leverage analytics for strategic improvements in your {$topic} approach.</li>\n";
        $content .= "</ul>\n\n";
        
        $content .= "<h3>Real-World Examples and Case Studies of {$topic}</h3>\n\n";
        $content .= "<p>In practical applications of {$topic}, we can see tangible results through various approaches such as improved efficiency, cost reduction, and creation of new business opportunities, often involving terms like {$keywords}.</p>\n\n";
        
        $content .= "<h2>Future Trends and Developments in {$topic}</h2>\n\n";
        $content .= "<p>Looking ahead, {$topic} will play an even more significant role, especially in the age of AI and digital transformation. Continuous preparation and learning will be key factors for success in the field of {$topic}.</p>\n\n";
        
        $content .= "<h2>Conclusion on {$topic}</h2>\n\n";
        $content .= "<p>In conclusion, {$topic} is a fascinating and highly beneficial area that everyone should explore. Learning and applying the right strategies will help you move forward confidently and achieve your intended goals. With advanced AI technology, we can access high-quality information and tools more easily than ever before, creating new opportunities for personal and business development with {$topic}.</p>";
        
        return str_replace('{$topic}', esc_html($topic), $content);
    }

    /**
     * Handles AJAX request to save the Gemini API key.
     * Requires 'manage_options' capability. Validates and saves the key.
     */
    private function handle_save_api_key() {
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array(
                'message' => __('You do not have permission to save settings.', 'flux-seo-scribe-craft-enhanced'),
                'code' => 'permission_denied'
            ));
            return;
        }

        // Sanitize the API key from POST data
        $api_key = isset($_POST['api_key']) ? sanitize_text_field(trim(wp_unslash($_POST['api_key']))) : '';

        // Handle clearing the API key
        if (empty($api_key)) {
            $cleared = delete_option('flux_seo_gemini_api_key');
            $this->gemini_api_key = ''; // Update runtime key
            if ($cleared || !get_option('flux_seo_gemini_api_key')) { // Check if it was already empty or successfully deleted
                wp_send_json_success(array('message' => __('API key cleared successfully.', 'flux-seo-scribe-craft-enhanced')));
            } else {
                wp_send_json_error(array('message' => __('Failed to clear API key. It might have already been empty.', 'flux-seo-scribe-craft-enhanced'), 'code' => 'clear_api_key_failed'));
            }
            return;
        }

        // Basic validation for API key format (common prefix for Google AI keys)
        if (strpos($api_key, 'AIza') !== 0) {
             wp_send_json_error(array(
                'message' => __('Invalid API key format. It should typically start with "AIza". Please check your key.', 'flux-seo-scribe-craft-enhanced'),
                'code' => 'invalid_api_key_format'
            ));
            return;
        }

        // Update the option in the database
        $updated = update_option('flux_seo_gemini_api_key', $api_key);
        if ($updated) {
            $this->gemini_api_key = $api_key; // Update runtime key
            wp_send_json_success(array('message' => __('API key saved successfully.', 'flux-seo-scribe-craft-enhanced')));
        } else {
            // This can happen if the new key is the same as the old key, or if update_option failed for other reasons.
            // Check if the current stored key is the same as the one submitted.
            if (get_option('flux_seo_gemini_api_key') === $api_key) {
                 wp_send_json_success(array('message' => __('API key is already set to this value.', 'flux-seo-scribe-craft-enhanced'), 'code' => 'api_key_unchanged'));
            } else {
                wp_send_json_error(array('message' => __('API key was not updated. Please try again.', 'flux-seo-scribe-craft-enhanced'), 'code' => 'api_key_not_updated'));
            }
        }
    }

    /**
     * Handles AJAX request for testing Auto Blog content generation.
     * Generates a sample title and brief summary using the AI.
     *
     * @param string $language Language for the test generation.
     */
    private function handle_ai_test_auto_blog_generation($language) {
        // Sanitize inputs from POST
        $topic = isset($_POST['topic']) ? sanitize_text_field(wp_unslash($_POST['topic'])) : 'a default SEO topic';
        $content_type = isset($_POST['content_type']) ? sanitize_text_field(wp_unslash($_POST['content_type'])) : 'blog post';
        $tone = isset($_POST['tone']) ? sanitize_text_field(wp_unslash($_POST['tone'])) : 'informative';
        $audience = isset($_POST['audience']) ? sanitize_text_field(wp_unslash($_POST['audience'])) : 'general audience';

        // Construct prompt for a very short test output
        $prompt_text = $language === 'th' ?
            "สำหรับหัวข้อการทดสอบ: '{$topic}', ประเภทเนื้อหา: '{$content_type}', โทน: '{$tone}', สำหรับผู้ชม: '{$audience}'.\n" .
            "โปรดสร้างสิ่งต่อไปนี้:\n" .
            "- suggested_title: ชื่อเรื่องตัวอย่างที่น่าสนใจ (ไม่เกิน 10 คำ, เป็นมิตรกับ SEO)\n" .
            "- brief_summary: สรุปสั้นๆ หนึ่งประโยคเกี่ยวกับเนื้อหาที่อาจจะสร้าง (ไม่เกิน 25 คำ, น่าสนใจ)\n" .
            "ส่งผลลัพธ์ในรูปแบบ JSON ที่มี key: suggested_title, brief_summary."
            :
            "For a test topic: '{$topic}', content type: '{$content_type}', tone: '{$tone}', for audience: '{$audience}'.\n" .
            "Please generate the following:\n" .
            "- suggested_title: An engaging, SEO-friendly sample title (max 10 words).\n" .
            "- brief_summary: A single concise, engaging sentence summarizing the potential content (max 25 words).\n" .
            "Return the result as a JSON object with keys: suggested_title, brief_summary.";

        $ai_response = $this->call_gemini_api($prompt_text, $language);

        if (isset($ai_response['error']) && $ai_response['error']) {
            wp_send_json_error($ai_response);
        } else {
            // Add the original topic to the response for context in the JS modal
            $ai_response['topic'] = $topic; // Ensure the topic used for the prompt is part of the response
            wp_send_json_success($ai_response);
        }
    }

    /**
     * Handles AJAX request for generating Meta Tags (Title, Description, OG tags, Twitter tags).
     *
     * @param string $language Language for meta tag generation.
     */
    private function handle_ai_meta_tags_generation($language) {
        // Sanitize inputs
        $page_title = isset($_POST['page_title']) ? sanitize_text_field(wp_unslash($_POST['page_title'])) : '';
        $description = isset($_POST['description']) ? sanitize_textarea_field(wp_unslash($_POST['description'])) : ''; // This could be existing meta desc or content snippet
        $keywords = isset($_POST['keywords']) ? sanitize_text_field(wp_unslash($_POST['keywords'])) : '';
        $page_type = isset($_POST['page_type']) ? sanitize_text_field(wp_unslash($_POST['page_type'])) : 'website';

        // Construct prompt for meta tag generation
        $prompt_text = $language === 'th' ?
            "สร้างเมตาแท็ก SEO สำหรับหน้าเว็บประเภท '{$page_type}' โดยมีข้อมูลต่อไปนี้:\n" .
            (!empty($page_title) ? "หัวข้อหน้าเว็บปัจจุบัน (ถ้ามี): {$page_title}\n" : '') .
            "คำอธิบายเนื้อหาหลักของหน้า หรือ คำอธิบายเมตาที่มีอยู่: {$description}\n" .
            (!empty($keywords) ? "คำหลักเป้าหมาย: {$keywords}\n" : '') .
            "\nโปรดสร้าง:\n" .
            "- meta_title: Meta Title ที่ปรับให้เหมาะสมกับ SEO (ไม่เกิน 60 ตัวอักษร, น่าสนใจ, และมีคำหลักถ้าเป็นไปได้)\n" .
            "- meta_description: Meta Description ที่น่าสนใจ (ไม่เกิน 160 ตัวอักษร, กระตุ้นการคลิก, และมีคำหลักอย่างเป็นธรรมชาติ)\n" .
            "- og_title: Open Graph Title (คล้าย meta_title แต่อาจยาวกว่าเล็กน้อยสำหรับโซเชียล)\n" .
            "- og_description: Open Graph Description (คล้าย meta_description แต่อาจปรับให้เหมาะกับโซเชียล)\n" .
            "- twitter_title: Twitter Title (กระชับสำหรับ Twitter)\n" .
            "- twitter_description: Twitter Description (กระชับและน่าสนใจสำหรับ Twitter)\n" .
            "ส่งผลลัพธ์ในรูปแบบ JSON object ที่มี keys ตามที่ระบุ."
            :
            "Generate SEO meta tags for a '{$page_type}' page with the following details:\n" .
            (!empty($page_title) ? "Current Page Title (if any): {$page_title}\n" : '') .
            "Main Content Description or Existing Meta Description: {$description}\n" .
            (!empty($keywords) ? "Target Keywords: {$keywords}\n" : '') .
            "\nPlease generate:\n" .
            "- meta_title: SEO-optimized Meta Title (max 60 characters, engaging, include keywords if possible).\n" .
            "- meta_description: Compelling Meta Description (max 160 characters, click-worthy, naturally includes keywords).\n" .
            "- og_title: Open Graph Title (similar to meta_title, can be slightly longer for social).\n" .
            "- og_description: Open Graph Description (similar to meta_description, adapted for social sharing).\n" .
            "- twitter_title: Twitter Title (concise for Twitter cards).\n" .
            "- twitter_description: Twitter Description (concise and engaging for Twitter).\n" .
            "Return the result as a JSON object with the specified keys.";

        $ai_response = $this->call_gemini_api($prompt_text, $language);

        if (isset($ai_response['error']) && $ai_response['error']) {
            wp_send_json_error($ai_response);
        } else {
            wp_send_json_success($ai_response);
        }
    }

    /**
     * Handles AJAX request for generating JSON-LD Schema Markup.
     *
     * @param string $language Language for schema generation (primarily affects system prompt if used).
     */
    private function handle_ai_schema_generation($language) {
        // Sanitize inputs
        $schema_type = isset($_POST['schema_type']) ? sanitize_text_field(wp_unslash($_POST['schema_type'])) : 'Article';
        $schema_data_json = isset($_POST['schema_data']) ? stripslashes(wp_unslash($_POST['schema_data'])) : '{}';
        $schema_data = json_decode($schema_data_json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(array(
                'message' => __('Invalid schema data provided. Must be valid JSON.', 'flux-seo-scribe-craft-enhanced'),
                'code' => 'invalid_schema_json'
            ));
            return;
        }

        // Construct prompt for schema generation
        $prompt_text = $language === 'th' ?
            "สร้าง JSON-LD Schema Markup สำหรับประเภท '{$schema_type}' โดยใช้ข้อมูลต่อไปนี้:\n" .
            wp_json_encode($schema_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n" .
            "ตรวจสอบให้แน่ใจว่า schema ถูกต้องตามมาตรฐานของ schema.org และเหมาะสมกับประเภท '{$schema_type}'. " .
            "รวม property ที่สำคัญและแนะนำสำหรับประเภท '{$schema_type}' โดยอ้างอิงจากข้อมูลที่ให้มา " .
            "หากข้อมูลบางส่วนขาดหายไป ให้สร้าง schema เท่าที่ทำได้จากข้อมูลที่มีอยู่ " .
            "ส่งผลลัพธ์เป็น JSON object ที่มี key 'json_ld_schema' ซึ่งมีค่าเป็น JSON-LD ที่สร้างขึ้น (stringified JSON)."
            :
            "Generate JSON-LD Schema Markup for a '{$schema_type}' type using the following data:\n" .
            wp_json_encode($schema_data, JSON_PRETTY_PRINT) . "\n\n" .
            "Ensure the schema is valid according to schema.org standards and appropriate for the '{$schema_type}' type. " .
            "Include important and recommended properties for '{$schema_type}' based on the provided data. " .
            "If some data is missing, generate the schema as best as possible with the available information. " .
            "Return the result as a JSON object with a key 'json_ld_schema' containing the generated JSON-LD (stringified JSON).";

        $ai_response = $this->call_gemini_api($prompt_text, $language);

        if (isset($ai_response['error']) && $ai_response['error']) {
            wp_send_json_error($ai_response);
        } else {
            // The AI is asked to return a JSON object where 'json_ld_schema' is a stringified JSON.
            // The JS side will handle parsing this inner JSON string.
            if (isset($ai_response['json_ld_schema']) && is_string($ai_response['json_ld_schema'])) {
                 // Attempt to decode the inner JSON to ensure it's valid before sending
                 $inner_json_decoded = json_decode($ai_response['json_ld_schema'], true);
                 if (json_last_error() !== JSON_ERROR_NONE) {
                     // If the AI didn't return valid inner JSON, log it and send an error
                     error_log("Gemini API returned invalid inner JSON for schema: " . $ai_response['json_ld_schema']);
                     wp_send_json_error(array(
                         'message' => __('AI response for schema markup was not valid JSON. Please try again.', 'flux-seo-scribe-craft-enhanced'),
                         'code' => 'invalid_inner_schema_json',
                         'details' => $ai_response['json_ld_schema']
                     ));
                     return;
                 }
                 // Optionally, re-encode it or trust the JS to parse. For consistency, let JS parse.
            } elseif (!isset($ai_response['json_ld_schema'])) {
                 error_log("Gemini API response for schema missing 'json_ld_schema' key: " . wp_json_encode($ai_response));
                 wp_send_json_error(array(
                    'message' => __('AI response for schema markup was incomplete. Please try again.', 'flux-seo-scribe-craft-enhanced'),
                    'code' => 'missing_schema_key_in_response'
                ));
                return;
            }
            wp_send_json_success($ai_response);
        }
    }

    /**
     * Handles AJAX request for a conceptual Technical SEO Audit.
     * Relies on AI to provide insights based on a URL.
     *
     * @param string $language Language for the audit report.
     */
    private function handle_ai_technical_seo_audit($language) {
        // Sanitize URL
        $url_to_audit = isset($_POST['url']) ? esc_url_raw(wp_unslash($_POST['url'])) : '';
        if (empty($url_to_audit) || !filter_var($url_to_audit, FILTER_VALIDATE_URL)) {
            wp_send_json_error(array(
                'message' => __('Invalid URL provided for audit.', 'flux-seo-scribe-craft-enhanced'),
                'code' => 'invalid_audit_url'
            ));
            return;
        }

        // Construct prompt for technical SEO audit
        // Note: This is a conceptual audit. Real tools would fetch and analyze the page directly.
        $prompt_text = $language === 'th' ?
            "ดำเนินการตรวจสอบ SEO ทางเทคนิคเบื้องต้นสำหรับ URL: {$url_to_audit}\n" .
            "พิจารณาปัจจัยต่างๆ (ตามแนวคิด) เช่น:\n" .
            "- mobile_friendliness: การตอบสนองบนมือถือ\n" .
            "- page_speed: ความเร็วในการโหลดหน้าเว็บ (แนวคิด Core Web Vitals)\n" .
            "- https_usage: การใช้ HTTPS\n" .
            "- url_structure: โครงสร้าง URL (ความเป็นมิตรต่อ SEO, ความยาว)\n" .
            "- meta_tags_presence: การมีอยู่ของ Meta Title และ Description\n" .
            "- heading_structure: การใช้ Heading (H1, H2s)\n" .
            "- robots_txt_check: การตรวจสอบ Robots.txt (แนวคิดปัญหาทั่วไป)\n" .
            "- xml_sitemap_check: การตรวจสอบ XML Sitemap (แนวคิดการมีอยู่และความสมบูรณ์)\n" .
            "- broken_links_check: การตรวจสอบลิงก์เสีย (แนวคิด)\n" .
            "- structured_data_presence: การมีอยู่ของ Structured Data\n\n" .
            "สำหรับแต่ละปัจจัย โปรดให้ 'score' (0-100) และ 'recommendations' (array ของสตริงคำแนะนำที่นำไปปฏิบัติได้ 1-3 ข้อ) " .
            "และ 'status' ('Good', 'Needs Improvement', 'Critical'). " .
            "รวมถึง 'overall_technical_seo_score' (0-100) และ 'summary' (สรุปสั้นๆ เกี่ยวกับการค้นพบหลัก). " .
            "ส่งผลลัพธ์ในรูปแบบ JSON object ที่มี key หลักคือ 'audit_results' ซึ่งภายในมี object สำหรับแต่ละปัจจัย และ keys สำหรับ overall_technical_seo_score และ summary."
            :
            "Perform a conceptual technical SEO audit for the URL: {$url_to_audit}\n" .
            "Consider factors (conceptually) such as:\n" .
            "- mobile_friendliness: Mobile-friendliness.\n" .
            "- page_speed: Page speed insights (conceptual Core Web Vitals).\n" .
            "- https_usage: HTTPS usage.\n" .
            "- url_structure: URL structure (SEO-friendliness, length).\n" .
            "- meta_tags_presence: Presence of Meta Title and Description.\n" .
            "- heading_structure: Heading usage (H1, H2s).\n" .
            "- robots_txt_check: Robots.txt check (conceptual common issues).\n" .
            "- xml_sitemap_check: XML Sitemap check (conceptual presence and validity).\n" .
            "- broken_links_check: Broken links check (conceptual).\n" .
            "- structured_data_presence: Presence of Structured Data.\n\n" .
            "For each factor, please provide a 'score' (0-100), 'recommendations' (array of 1-3 actionable string recommendations), " .
            "and a 'status' ('Good', 'Needs Improvement', 'Critical'). " .
            "Also include an 'overall_technical_seo_score' (0-100) and a 'summary' (brief overview of key findings). " .
            "Return the result as a JSON object with a main key 'audit_results', containing objects for each factor, and keys for overall_technical_seo_score and summary.";

        $ai_response = $this->call_gemini_api($prompt_text, $language);
        
        if (isset($ai_response['error']) && $ai_response['error']) {
            wp_send_json_error($ai_response);
        } else {
            // Future enhancement: Augment AI conceptual checks with actual server-side checks if feasible (e.g., header checks).
            wp_send_json_success($ai_response);
        }
    }

    /**
     * Handles AJAX request for the AI Chatbot.
     * Takes user message (and potentially history), gets a response from AI.
     *
     * @param string $language Language for chatbot interaction.
     */
    private function handle_ai_chatbot($language) {
        // Sanitize user message
        $user_message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';
        // Chat history can be complex to manage; current implementation is stateless per call.
        // $chat_history_json = isset($_POST['history']) ? stripslashes(wp_unslash($_POST['history'])) : '[]';
        // $chat_history = json_decode($chat_history_json, true);
        // if (json_last_error() !== JSON_ERROR_NONE) { $chat_history = []; }

        if (empty($user_message)) {
            wp_send_json_error(array(
                'message' => __('No message provided for the chatbot.', 'flux-seo-scribe-craft-enhanced'),
                'code' => 'empty_chat_message'
            ));
            return;
        }

        // Construct prompt for chatbot
        // A more advanced version would include chat history for context.
        $prompt_text = $language === 'th' ?
            "ผู้ใช้พิมพ์ข้อความนี้ใน Flux SEO AI Chatbot: \"{$user_message}\"\n\n" .
            "ในฐานะ Flux AI ผู้เชี่ยวชาญด้าน SEO โปรดตอบคำถามนี้อย่างกระชับ เป็นประโยชน์ และเป็นมิตร " .
            "หากเหมาะสม ให้คำแนะนำที่นำไปปฏิบัติได้จริง หรือชี้ไปยังเครื่องมือที่เกี่ยวข้องภายในปลั๊กอิน Flux SEO " .
            "หากคำถามไม่เกี่ยวกับ SEO หรือการตลาดดิจิทัลโดยตรง ให้ตอบอย่างสุภาพว่าคุณเน้นที่หัวข้อเหล่านั้น"
            :
            "The user typed this message in the Flux SEO AI Chatbot: \"{$user_message}\"\n\n" .
            "As Flux AI, the SEO expert, please answer this question concisely, helpfully, and in a friendly manner. " .
            "If appropriate, provide actionable advice or point to relevant tools within the Flux SEO plugin. " .
            "If the question is not directly related to SEO or digital marketing, politely state your focus on those topics.";

        $ai_response = $this->call_gemini_api($prompt_text, $language);

        if (isset($ai_response['error']) && $ai_response['error']) {
            wp_send_json_error($ai_response);
        } else {
            // Expecting AI to return JSON with a 'reply' field or similar.
            // call_gemini_api is set to responseMimeType: "application/json"
            // If AI returns simple text, it will be in 'raw_response' from call_gemini_api's error handling.
            if (!isset($ai_response['reply']) && isset($ai_response['raw_response'])) {
                // If the primary expected field isn't there, but raw_response is, use that.
                // This can happen if the AI doesn't strictly follow the JSON output format for chat.
                wp_send_json_success(array('reply' => $ai_response['raw_response']));
            } elseif (!isset($ai_response['reply'])) {
                 wp_send_json_success(array('reply' => __('Sorry, I could not generate a response at this moment.', 'flux-seo-scribe-craft-enhanced')));
            }
            else {
                wp_send_json_success($ai_response);
            }
        }
    }

    /**
     * Handles AJAX request to suggest meta descriptions based on title/content.
     *
     * @param string $language Language for suggestions.
     */
    private function handle_ai_suggest_meta_description($language) {
        // Sanitize inputs
        $page_title = isset($_POST['page_title']) ? sanitize_text_field(wp_unslash($_POST['page_title'])) : '';
        $keywords = isset($_POST['keywords']) ? sanitize_text_field(wp_unslash($_POST['keywords'])) : '';
        $content_description = isset($_POST['content_description']) ? sanitize_textarea_field(wp_unslash($_POST['content_description'])) : '';

        if (empty($page_title) && empty($content_description)) {
            wp_send_json_error(array(
                'message' => __('Please provide a page title or content description to generate meta description suggestions.', 'flux-seo-scribe-craft-enhanced'),
                'code' => 'missing_input_for_meta_suggestion'
            ));
            return;
        }

        // Construct prompt for meta description suggestions
        $prompt_parts = [];
        if ($language === 'th') {
            $prompt_parts[] = "สำหรับหน้าเว็บที่มีข้อมูลต่อไปนี้:";
            if (!empty($page_title)) $prompt_parts[] = "- หัวข้อหน้า: \"{$page_title}\"";
            if (!empty($keywords)) $prompt_parts[] = "- คำหลักเป้าหมาย: \"{$keywords}\"";
            if (!empty($content_description)) $prompt_parts[] = "- คำอธิบายเนื้อหาที่มีอยู่/ตัวอย่างเนื้อหา: \"" . substr($content_description, 0, 500) . "...\""; // Limit length
            $prompt_parts[] = "\nโปรดสร้างคำอธิบายเมตา (meta description) ที่น่าสนใจและเหมาะกับ SEO จำนวน 2-3 รายการ แต่ละรายการควรมีความยาวประมาณ 150-160 ตัวอักษร และรวมคำหลัก (ถ้ามี) อย่างเป็นธรรมชาติ";
            $prompt_parts[] = "ส่งผลลัพธ์เป็น JSON object ที่มี key \"suggestions\" ซึ่งเป็น array ของสตริงคำอธิบายเมตา (แต่ละสตริงคือคำแนะนำหนึ่งรายการ)";
        } else { // English
            $prompt_parts[] = "For a webpage with the following information:";
            if (!empty($page_title)) $prompt_parts[] = "- Page Title: \"{$page_title}\"";
            if (!empty($keywords)) $prompt_parts[] = "- Target Keywords: \"{$keywords}\"";
            if (!empty($content_description)) $prompt_parts[] = "- Existing Content Description/Snippet: \"" . substr($content_description, 0, 500) . "...\""; // Limit length
            $prompt_parts[] = "\nPlease generate 2-3 compelling and SEO-friendly meta description suggestions. Each suggestion should be approximately 150-160 characters long and naturally incorporate keywords (if provided).";
            $prompt_parts[] = "Return the result as a JSON object with a key \"suggestions\", which is an array of meta description strings (each string being one suggestion).";
        }

        $final_prompt = implode("\n", $prompt_parts);
        $ai_response = $this->call_gemini_api($final_prompt, $language);

        if (isset($ai_response['error']) && $ai_response['error']) {
            wp_send_json_error($ai_response);
        } elseif (isset($ai_response['suggestions']) && is_array($ai_response['suggestions'])) {
            // Ensure suggestions are strings
            $valid_suggestions = array_filter($ai_response['suggestions'], 'is_string');
            wp_send_json_success(array('suggestions' => $valid_suggestions));
        } else {
            // If AI response is not as expected
            error_log('Meta description suggestion response not in expected format: ' . wp_json_encode($ai_response));
            wp_send_json_error(array(
                'message' => __('AI did not return suggestions in the expected format.', 'flux-seo-scribe-craft-enhanced'),
                'code' => 'unexpected_suggestion_format',
                'details' => $ai_response
            ));
        }
    }
}

// Initialize the plugin
new FluxSEOScribeCraftEnhanced();
?>
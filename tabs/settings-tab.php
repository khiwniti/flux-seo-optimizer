<?php
// Settings Tab Content
?>
<div id="settings-tab" class="flux-seo-tab-content">
    <div class="flux-seo-card">
        <div class="flux-seo-card-header">
            <h2 class="flux-seo-card-title">
                <span class="flux-seo-card-icon">⚙️</span>
                <span data-key="settings"><?php _e('Settings & Configuration', 'flux-seo-enhanced'); ?></span>
            </h2>
            <p class="flux-seo-card-description">
                <?php _e('Configure your SEO tools and API settings', 'flux-seo-enhanced'); ?>
            </p>
        </div>
        <div class="flux-seo-card-body">
            <!-- API Configuration -->
            <div class="flux-seo-settings-section">
                <h3>🔑 <?php _e('API Configuration', 'flux-seo-enhanced'); ?></h3>
                <div class="flux-seo-form-group">
                    <label class="flux-seo-label"><?php _e('Google Gemini API Key', 'flux-seo-enhanced'); ?></label>
                    <div class="flux-seo-input-group">
                        <input type="password" id="gemini-api-key" class="flux-seo-input" 
                               placeholder="<?php _e('Enter your Gemini API key', 'flux-seo-enhanced'); ?>"
                               value="<?php echo esc_attr(get_option('flux_seo_gemini_api_key', '')); ?>">
                        <button id="save-api-key" class="flux-seo-btn flux-seo-btn-secondary">
                            <span class="flux-seo-btn-text"><?php _e('Save', 'flux-seo-enhanced'); ?></span>
                        </button>
                        <button id="test-api-key" class="flux-seo-btn flux-seo-btn-outline">
                            <span class="flux-seo-btn-text"><?php _e('Test', 'flux-seo-enhanced'); ?></span>
                        </button>
                    </div>
                    <p class="flux-seo-help-text">
                        <?php _e('Get your API key from', 'flux-seo-enhanced'); ?> 
                        <a href="https://makersuite.google.com/app/apikey" target="_blank">Google AI Studio</a>
                    </p>
                </div>
                
                <div class="flux-seo-form-group">
                    <label class="flux-seo-label"><?php _e('API Model', 'flux-seo-enhanced'); ?></label>
                    <select id="gemini-model" class="flux-seo-select">
                        <option value="gemini-2.5-pro"><?php _e('Gemini 2.5 Pro (Recommended)', 'flux-seo-enhanced'); ?></option>
                        <option value="gemini-1.5-pro"><?php _e('Gemini 1.5 Pro', 'flux-seo-enhanced'); ?></option>
                        <option value="gemini-1.5-flash"><?php _e('Gemini 1.5 Flash (Faster)', 'flux-seo-enhanced'); ?></option>
                    </select>
                </div>
            </div>
            
            <!-- Language Settings -->
            <div class="flux-seo-settings-section">
                <h3>🌐 <?php _e('Language Settings', 'flux-seo-enhanced'); ?></h3>
                <div class="flux-seo-form-group">
                    <label class="flux-seo-label"><?php _e('Default Language', 'flux-seo-enhanced'); ?></label>
                    <select id="default-language" class="flux-seo-select">
                        <option value="en"><?php _e('English', 'flux-seo-enhanced'); ?></option>
                        <option value="th"><?php _e('ไทย (Thai)', 'flux-seo-enhanced'); ?></option>
                        <option value="es"><?php _e('Español (Spanish)', 'flux-seo-enhanced'); ?></option>
                        <option value="fr"><?php _e('Français (French)', 'flux-seo-enhanced'); ?></option>
                        <option value="de"><?php _e('Deutsch (German)', 'flux-seo-enhanced'); ?></option>
                        <option value="ja"><?php _e('日本語 (Japanese)', 'flux-seo-enhanced'); ?></option>
                        <option value="ko"><?php _e('한국어 (Korean)', 'flux-seo-enhanced'); ?></option>
                        <option value="zh"><?php _e('中文 (Chinese)', 'flux-seo-enhanced'); ?></option>
                    </select>
                </div>
                <div class="flux-seo-form-group">
                    <label class="flux-seo-checkbox-label">
                        <input type="checkbox" id="auto-detect-language" checked>
                        <?php _e('Auto-detect content language', 'flux-seo-enhanced'); ?>
                    </label>
                </div>
            </div>
            
            <!-- Analytics Settings -->
            <div class="flux-seo-settings-section">
                <h3>📊 <?php _e('Analytics Settings', 'flux-seo-enhanced'); ?></h3>
                <div class="flux-seo-form-group">
                    <label class="flux-seo-checkbox-label">
                        <input type="checkbox" id="enable-analytics" checked>
                        <?php _e('Enable advanced analytics tracking', 'flux-seo-enhanced'); ?>
                    </label>
                </div>
                <div class="flux-seo-form-group">
                    <label class="flux-seo-checkbox-label">
                        <input type="checkbox" id="auto-suggestions" checked>
                        <?php _e('Enable automatic SEO suggestions', 'flux-seo-enhanced'); ?>
                    </label>
                </div>
                <div class="flux-seo-form-group">
                    <label class="flux-seo-checkbox-label">
                        <input type="checkbox" id="performance-monitoring">
                        <?php _e('Enable performance monitoring', 'flux-seo-enhanced'); ?>
                    </label>
                </div>
            </div>
            
            <!-- Content Settings -->
            <div class="flux-seo-settings-section">
                <h3>📝 <?php _e('Content Settings', 'flux-seo-enhanced'); ?></h3>
                <div class="flux-seo-form-group">
                    <label class="flux-seo-label"><?php _e('Default Content Tone', 'flux-seo-enhanced'); ?></label>
                    <select id="default-tone" class="flux-seo-select">
                        <option value="professional"><?php _e('Professional', 'flux-seo-enhanced'); ?></option>
                        <option value="casual"><?php _e('Casual', 'flux-seo-enhanced'); ?></option>
                        <option value="friendly"><?php _e('Friendly', 'flux-seo-enhanced'); ?></option>
                        <option value="authoritative"><?php _e('Authoritative', 'flux-seo-enhanced'); ?></option>
                    </select>
                </div>
                <div class="flux-seo-form-group">
                    <label class="flux-seo-label"><?php _e('Default Target Audience', 'flux-seo-enhanced'); ?></label>
                    <select id="default-audience" class="flux-seo-select">
                        <option value="general"><?php _e('General Audience', 'flux-seo-enhanced'); ?></option>
                        <option value="business"><?php _e('Business Professionals', 'flux-seo-enhanced'); ?></option>
                        <option value="students"><?php _e('Students', 'flux-seo-enhanced'); ?></option>
                        <option value="experts"><?php _e('Industry Experts', 'flux-seo-enhanced'); ?></option>
                    </select>
                </div>
            </div>
            
            <!-- Security Settings -->
            <div class="flux-seo-settings-section">
                <h3>🔒 <?php _e('Security Settings', 'flux-seo-enhanced'); ?></h3>
                <div class="flux-seo-form-group">
                    <label class="flux-seo-checkbox-label">
                        <input type="checkbox" id="encrypt-api-keys" checked>
                        <?php _e('Encrypt stored API keys', 'flux-seo-enhanced'); ?>
                    </label>
                </div>
                <div class="flux-seo-form-group">
                    <label class="flux-seo-checkbox-label">
                        <input type="checkbox" id="log-api-requests">
                        <?php _e('Log API requests for debugging', 'flux-seo-enhanced'); ?>
                    </label>
                </div>
                <div class="flux-seo-form-group">
                    <label class="flux-seo-label"><?php _e('Session Timeout (minutes)', 'flux-seo-enhanced'); ?></label>
                    <input type="number" id="session-timeout" class="flux-seo-input" value="30" min="5" max="120">
                </div>
            </div>
            
            <!-- Export/Import Settings -->
            <div class="flux-seo-settings-section">
                <h3>💾 <?php _e('Backup & Restore', 'flux-seo-enhanced'); ?></h3>
                <div class="flux-seo-form-group">
                    <label class="flux-seo-label"><?php _e('Export Settings', 'flux-seo-enhanced'); ?></label>
                    <div class="flux-seo-export-buttons">
                        <button id="export-settings" class="flux-seo-btn flux-seo-btn-secondary">
                            <span class="flux-seo-btn-icon">📤</span>
                            <span class="flux-seo-btn-text"><?php _e('Export Settings', 'flux-seo-enhanced'); ?></span>
                        </button>
                        <button id="import-settings" class="flux-seo-btn flux-seo-btn-outline">
                            <span class="flux-seo-btn-icon">📥</span>
                            <span class="flux-seo-btn-text"><?php _e('Import Settings', 'flux-seo-enhanced'); ?></span>
                        </button>
                    </div>
                    <input type="file" id="import-file" accept=".json" style="display: none;">
                </div>
            </div>
            
            <!-- Advanced Settings -->
            <div class="flux-seo-settings-section">
                <h3>🔧 <?php _e('Advanced Settings', 'flux-seo-enhanced'); ?></h3>
                <div class="flux-seo-form-group">
                    <label class="flux-seo-label"><?php _e('API Request Timeout (seconds)', 'flux-seo-enhanced'); ?></label>
                    <input type="number" id="api-timeout" class="flux-seo-input" value="30" min="10" max="120">
                </div>
                <div class="flux-seo-form-group">
                    <label class="flux-seo-label"><?php _e('Max Concurrent Requests', 'flux-seo-enhanced'); ?></label>
                    <input type="number" id="max-requests" class="flux-seo-input" value="3" min="1" max="10">
                </div>
                <div class="flux-seo-form-group">
                    <label class="flux-seo-checkbox-label">
                        <input type="checkbox" id="debug-mode">
                        <?php _e('Enable debug mode', 'flux-seo-enhanced'); ?>
                    </label>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flux-seo-settings-actions">
                <button id="save-all-settings" class="flux-seo-btn flux-seo-btn-primary">
                    <span class="flux-seo-btn-icon">💾</span>
                    <span class="flux-seo-btn-text"><?php _e('Save All Settings', 'flux-seo-enhanced'); ?></span>
                </button>
                <button id="reset-settings" class="flux-seo-btn flux-seo-btn-danger">
                    <span class="flux-seo-btn-icon">🔄</span>
                    <span class="flux-seo-btn-text"><?php _e('Reset All Settings', 'flux-seo-enhanced'); ?></span>
                </button>
            </div>
            
            <!-- System Information -->
            <div class="flux-seo-settings-section">
                <h3>ℹ️ <?php _e('System Information', 'flux-seo-enhanced'); ?></h3>
                <div class="flux-seo-system-info">
                    <div class="flux-seo-info-item">
                        <span class="flux-seo-info-label"><?php _e('Plugin Version:', 'flux-seo-enhanced'); ?></span>
                        <span class="flux-seo-info-value">7.0.0</span>
                    </div>
                    <div class="flux-seo-info-item">
                        <span class="flux-seo-info-label"><?php _e('WordPress Version:', 'flux-seo-enhanced'); ?></span>
                        <span class="flux-seo-info-value"><?php echo get_bloginfo('version'); ?></span>
                    </div>
                    <div class="flux-seo-info-item">
                        <span class="flux-seo-info-label"><?php _e('PHP Version:', 'flux-seo-enhanced'); ?></span>
                        <span class="flux-seo-info-value"><?php echo PHP_VERSION; ?></span>
                    </div>
                    <div class="flux-seo-info-item">
                        <span class="flux-seo-info-label"><?php _e('API Status:', 'flux-seo-enhanced'); ?></span>
                        <span class="flux-seo-info-value" id="api-status">
                            <span class="flux-seo-status-indicator flux-seo-status-unknown">●</span>
                            <?php _e('Not tested', 'flux-seo-enhanced'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
// Technical Tab Content
?>
<div id="technical-tab" class="flux-seo-tab-content">
    <div class="flux-seo-card">
        <div class="flux-seo-card-header">
            <h2 class="flux-seo-card-title">
                <span class="flux-seo-card-icon">⚙️</span>
                <span data-key="technicalSEO"><?php _e('Technical SEO Audit', 'flux-seo-enhanced'); ?></span>
            </h2>
            <p class="flux-seo-card-description">
                <?php _e('Comprehensive technical SEO analysis and recommendations', 'flux-seo-enhanced'); ?>
            </p>
        </div>
        <div class="flux-seo-card-body">
            <div class="flux-seo-form">
                <div class="flux-seo-form-group">
                    <label class="flux-seo-label"><?php _e('Website URL', 'flux-seo-enhanced'); ?></label>
                    <input type="url" id="technical-url" class="flux-seo-input" 
                           placeholder="https://example.com">
                </div>
                <div class="flux-seo-form-row">
                    <div class="flux-seo-form-group">
                        <label class="flux-seo-label"><?php _e('Audit Type', 'flux-seo-enhanced'); ?></label>
                        <select id="audit-type" class="flux-seo-select">
                            <option value="full"><?php _e('Full Technical Audit', 'flux-seo-enhanced'); ?></option>
                            <option value="speed"><?php _e('Page Speed Analysis', 'flux-seo-enhanced'); ?></option>
                            <option value="mobile"><?php _e('Mobile Optimization', 'flux-seo-enhanced'); ?></option>
                            <option value="security"><?php _e('Security Check', 'flux-seo-enhanced'); ?></option>
                            <option value="crawlability"><?php _e('Crawlability Analysis', 'flux-seo-enhanced'); ?></option>
                            <option value="indexability"><?php _e('Indexability Check', 'flux-seo-enhanced'); ?></option>
                        </select>
                    </div>
                    <div class="flux-seo-form-group">
                        <label class="flux-seo-label"><?php _e('Device Type', 'flux-seo-enhanced'); ?></label>
                        <select id="device-type" class="flux-seo-select">
                            <option value="desktop"><?php _e('Desktop', 'flux-seo-enhanced'); ?></option>
                            <option value="mobile"><?php _e('Mobile', 'flux-seo-enhanced'); ?></option>
                            <option value="both"><?php _e('Both', 'flux-seo-enhanced'); ?></option>
                        </select>
                    </div>
                </div>
                <button id="run-audit-btn" class="flux-seo-btn flux-seo-btn-primary">
                    <span class="flux-seo-btn-icon">🔍</span>
                    <span class="flux-seo-btn-text"><?php _e('Run Technical Audit', 'flux-seo-enhanced'); ?></span>
                </button>
            </div>
            
            <div id="technical-results" class="flux-seo-results" style="display: none;">
                <div class="flux-seo-results-header">
                    <h3><?php _e('Technical SEO Audit Results', 'flux-seo-enhanced'); ?></h3>
                    <span class="flux-seo-ai-badge"><?php _e('Powered by Google Gemini 2.5 Pro', 'flux-seo-enhanced'); ?></span>
                </div>
                <div id="technical-display" class="flux-seo-technical-display">
                    <!-- Overall Score -->
                    <div class="flux-seo-audit-score">
                        <div class="flux-seo-score-circle">
                            <span class="flux-seo-score-number" id="overall-score">85</span>
                            <span class="flux-seo-score-label"><?php _e('SEO Score', 'flux-seo-enhanced'); ?></span>
                        </div>
                        <div class="flux-seo-score-breakdown">
                            <div class="flux-seo-score-item">
                                <span class="flux-seo-score-category"><?php _e('Performance', 'flux-seo-enhanced'); ?></span>
                                <span class="flux-seo-score-value" id="performance-score">90</span>
                            </div>
                            <div class="flux-seo-score-item">
                                <span class="flux-seo-score-category"><?php _e('Accessibility', 'flux-seo-enhanced'); ?></span>
                                <span class="flux-seo-score-value" id="accessibility-score">85</span>
                            </div>
                            <div class="flux-seo-score-item">
                                <span class="flux-seo-score-category"><?php _e('Best Practices', 'flux-seo-enhanced'); ?></span>
                                <span class="flux-seo-score-value" id="practices-score">80</span>
                            </div>
                            <div class="flux-seo-score-item">
                                <span class="flux-seo-score-category"><?php _e('SEO', 'flux-seo-enhanced'); ?></span>
                                <span class="flux-seo-score-value" id="seo-score">85</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Audit Sections -->
                    <div class="flux-seo-audit-sections">
                        <div class="flux-seo-audit-section">
                            <h4>✅ <?php _e('Page Speed', 'flux-seo-enhanced'); ?></h4>
                            <p><?php _e('Good loading speed (2.3s)', 'flux-seo-enhanced'); ?></p>
                            <div class="flux-seo-audit-details">
                                <span><?php _e('First Contentful Paint: 1.2s', 'flux-seo-enhanced'); ?></span>
                                <span><?php _e('Largest Contentful Paint: 2.3s', 'flux-seo-enhanced'); ?></span>
                            </div>
                        </div>
                        <div class="flux-seo-audit-section">
                            <h4>✅ <?php _e('Mobile Optimization', 'flux-seo-enhanced'); ?></h4>
                            <p><?php _e('Mobile-friendly design detected', 'flux-seo-enhanced'); ?></p>
                            <div class="flux-seo-audit-details">
                                <span><?php _e('Responsive design: Yes', 'flux-seo-enhanced'); ?></span>
                                <span><?php _e('Mobile viewport: Configured', 'flux-seo-enhanced'); ?></span>
                            </div>
                        </div>
                        <div class="flux-seo-audit-section">
                            <h4>⚠️ <?php _e('Meta Tags', 'flux-seo-enhanced'); ?></h4>
                            <p><?php _e('Some meta descriptions missing', 'flux-seo-enhanced'); ?></p>
                            <div class="flux-seo-audit-details">
                                <span><?php _e('Title tags: 85% complete', 'flux-seo-enhanced'); ?></span>
                                <span><?php _e('Meta descriptions: 70% complete', 'flux-seo-enhanced'); ?></span>
                            </div>
                        </div>
                        <div class="flux-seo-audit-section">
                            <h4>✅ <?php _e('SSL Certificate', 'flux-seo-enhanced'); ?></h4>
                            <p><?php _e('Valid SSL certificate found', 'flux-seo-enhanced'); ?></p>
                            <div class="flux-seo-audit-details">
                                <span><?php _e('HTTPS: Enabled', 'flux-seo-enhanced'); ?></span>
                                <span><?php _e('Certificate: Valid until 2025', 'flux-seo-enhanced'); ?></span>
                            </div>
                        </div>
                        <div class="flux-seo-audit-section">
                            <h4>⚠️ <?php _e('Core Web Vitals', 'flux-seo-enhanced'); ?></h4>
                            <p><?php _e('Some improvements needed', 'flux-seo-enhanced'); ?></p>
                            <div class="flux-seo-audit-details">
                                <span><?php _e('LCP: 2.3s (Good)', 'flux-seo-enhanced'); ?></span>
                                <span><?php _e('FID: 120ms (Needs improvement)', 'flux-seo-enhanced'); ?></span>
                                <span><?php _e('CLS: 0.05 (Good)', 'flux-seo-enhanced'); ?></span>
                            </div>
                        </div>
                        <div class="flux-seo-audit-section">
                            <h4>✅ <?php _e('Structured Data', 'flux-seo-enhanced'); ?></h4>
                            <p><?php _e('Schema markup detected', 'flux-seo-enhanced'); ?></p>
                            <div class="flux-seo-audit-details">
                                <span><?php _e('JSON-LD: Present', 'flux-seo-enhanced'); ?></span>
                                <span><?php _e('Organization schema: Found', 'flux-seo-enhanced'); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recommendations -->
                    <div class="flux-seo-recommendations">
                        <h4><?php _e('AI Recommendations', 'flux-seo-enhanced'); ?></h4>
                        <div id="technical-recommendations" class="flux-seo-recommendations-list"></div>
                    </div>
                </div>
                
                <!-- Export Options -->
                <div class="flux-seo-export-options">
                    <button id="export-audit-pdf" class="flux-seo-btn flux-seo-btn-secondary">
                        <span class="flux-seo-btn-icon">📄</span>
                        <span class="flux-seo-btn-text"><?php _e('Export PDF Report', 'flux-seo-enhanced'); ?></span>
                    </button>
                    <button id="export-audit-csv" class="flux-seo-btn flux-seo-btn-outline">
                        <span class="flux-seo-btn-icon">📊</span>
                        <span class="flux-seo-btn-text"><?php _e('Export CSV Data', 'flux-seo-enhanced'); ?></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
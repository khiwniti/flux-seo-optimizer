<?php
// Analyzer Tab Content
?>
<div id="analyzer-tab" class="flux-seo-tab-content active">
    <div class="flux-seo-card">
        <div class="flux-seo-card-header">
            <h2 class="flux-seo-card-title">
                <span class="flux-seo-card-icon">🔍</span>
                <span data-key="contentAnalyzer"><?php _e('AI Content Analyzer', 'flux-seo-enhanced'); ?></span>
            </h2>
            <p class="flux-seo-card-description">
                <?php _e('Advanced content analysis powered by Google Gemini 2.5 Pro AI', 'flux-seo-enhanced'); ?>
            </p>
        </div>
        <div class="flux-seo-card-body">
            <div class="flux-seo-form">
                <div class="flux-seo-form-group">
                    <label class="flux-seo-label" data-key="contentToAnalyze"><?php _e('Content to Analyze', 'flux-seo-enhanced'); ?></label>
                    <textarea 
                        id="analyzer-content" 
                        class="flux-seo-textarea" 
                        rows="8" 
                        placeholder="<?php _e('Paste your content here for AI-powered SEO analysis...', 'flux-seo-enhanced'); ?>"></textarea>
                </div>
                <div class="flux-seo-form-row">
                    <div class="flux-seo-form-group">
                        <label class="flux-seo-label" data-key="keywords"><?php _e('Target Keywords', 'flux-seo-enhanced'); ?></label>
                        <input type="text" id="analyzer-keywords" class="flux-seo-input"
                               placeholder="<?php _e('Primary and secondary keywords', 'flux-seo-enhanced'); ?>">
                    </div>
                    <div class="flux-seo-form-group">
                        <label class="flux-seo-label" data-key="targetAudience"><?php _e('Target Audience', 'flux-seo-enhanced'); ?></label>
                        <select id="analyzer-audience" class="flux-seo-select">
                            <option value="general" data-key="general"><?php _e('General Audience', 'flux-seo-enhanced'); ?></option>
                            <option value="business" data-key="business"><?php _e('Business Professionals', 'flux-seo-enhanced'); ?></option>
                            <option value="students" data-key="students"><?php _e('Students', 'flux-seo-enhanced'); ?></option>
                            <option value="experts" data-key="experts"><?php _e('Industry Experts', 'flux-seo-enhanced'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="flux-seo-form-actions">
                    <button id="analyze-btn" class="flux-seo-btn flux-seo-btn-primary">
                        <span class="flux-seo-btn-icon">🤖</span>
                        <span class="flux-seo-btn-text" data-key="analyzeWithAI"><?php _e('Analyze with AI', 'flux-seo-enhanced'); ?></span>
                    </button>
                </div>
            </div>
            
            <div id="analyzer-results" class="flux-seo-results" style="display: none;">
                <div class="flux-seo-results-header">
                    <h3><?php _e('AI Analysis Results', 'flux-seo-enhanced'); ?></h3>
                    <span class="flux-seo-ai-badge" data-key="poweredByGemini"><?php _e('Powered by Google Gemini 2.5 Pro', 'flux-seo-enhanced'); ?></span>
                </div>
                <div id="analyzer-display" class="flux-seo-analyzer-display"></div>
            </div>
        </div>
    </div>
</div>
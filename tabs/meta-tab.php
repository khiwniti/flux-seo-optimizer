<?php
// Meta Tags Tab Content
?>
<div id="meta-tab" class="flux-seo-tab-content">
    <div class="flux-seo-card">
        <div class="flux-seo-card-header">
            <h2 class="flux-seo-card-title">
                <span class="flux-seo-card-icon">🌐</span>
                <span data-key="metaTags"><?php _e('Meta Tags Generator', 'flux-seo-enhanced'); ?></span>
            </h2>
            <p class="flux-seo-card-description">
                <?php _e('Generate and optimize meta tags for better search engine visibility', 'flux-seo-enhanced'); ?>
            </p>
        </div>
        <div class="flux-seo-card-body">
            <div class="flux-seo-form">
                <div class="flux-seo-form-group">
                    <label class="flux-seo-label"><?php _e('Page Title', 'flux-seo-enhanced'); ?></label>
                    <input type="text" id="meta-title" class="flux-seo-input" 
                           placeholder="<?php _e('Enter your page title (50-60 characters)', 'flux-seo-enhanced'); ?>">
                    <div class="flux-seo-char-count">
                        <span id="title-count">0</span>/60 <?php _e('characters', 'flux-seo-enhanced'); ?>
                    </div>
                </div>
                <div class="flux-seo-form-group">
                    <label class="flux-seo-label"><?php _e('Meta Description', 'flux-seo-enhanced'); ?></label>
                    <textarea id="meta-description" class="flux-seo-textarea" rows="3"
                              placeholder="<?php _e('Enter your meta description (150-160 characters)', 'flux-seo-enhanced'); ?>"></textarea>
                    <div class="flux-seo-input-meta">
                        <div class="flux-seo-char-count">
                            <span id="description-count">0</span>/160 <?php _e('characters', 'flux-seo-enhanced'); ?>
                        </div>
                        <button type="button" id="suggest-meta-description-btn" class="flux-seo-btn flux-seo-btn-xs flux-seo-btn-outline">
                            <span class="flux-seo-btn-icon">💡</span>
                            <?php _e('Suggest', 'flux-seo-enhanced'); ?>
                        </button>
                    </div>
                </div>
                <div id="meta-description-suggestions" class="flux-seo-suggestions-container" style="display: none;"></div>
                <div class="flux-seo-form-group">
                    <label class="flux-seo-label"><?php _e('Keywords', 'flux-seo-enhanced'); ?></label>
                    <input type="text" id="meta-keywords" class="flux-seo-input" 
                           placeholder="<?php _e('Enter keywords separated by commas', 'flux-seo-enhanced'); ?>">
                </div>
                <div class="flux-seo-form-group">
                    <label class="flux-seo-label"><?php _e('Page URL', 'flux-seo-enhanced'); ?></label>
                    <input type="url" id="meta-url" class="flux-seo-input" 
                           placeholder="https://example.com/page">
                </div>
                <div class="flux-seo-form-row">
                    <div class="flux-seo-form-group">
                        <label class="flux-seo-label"><?php _e('Open Graph Image', 'flux-seo-enhanced'); ?></label>
                        <input type="url" id="og-image" class="flux-seo-input" 
                               placeholder="https://example.com/image.jpg">
                    </div>
                    <div class="flux-seo-form-group">
                        <label class="flux-seo-label"><?php _e('Content Type', 'flux-seo-enhanced'); ?></label>
                        <select id="content-type" class="flux-seo-select">
                            <option value="website"><?php _e('Website', 'flux-seo-enhanced'); ?></option>
                            <option value="article"><?php _e('Article', 'flux-seo-enhanced'); ?></option>
                            <option value="product"><?php _e('Product', 'flux-seo-enhanced'); ?></option>
                            <option value="video"><?php _e('Video', 'flux-seo-enhanced'); ?></option>
                        </select>
                    </div>
                </div>
                <button id="generate-meta-btn" class="flux-seo-btn flux-seo-btn-primary">
                    <span class="flux-seo-btn-icon">🤖</span>
                    <span class="flux-seo-btn-text"><?php _e('Generate with AI', 'flux-seo-enhanced'); ?></span>
                </button>
            </div>
            
            <div id="meta-results" class="flux-seo-results" style="display: none;">
                <div class="flux-seo-results-header">
                    <h3><?php _e('Generated Meta Tags', 'flux-seo-enhanced'); ?></h3>
                    <span class="flux-seo-ai-badge"><?php _e('Powered by Google Gemini 2.5 Pro', 'flux-seo-enhanced'); ?></span>
                </div>
                <div id="meta-display" class="flux-seo-meta-display"></div>
                
                <!-- SERP Preview -->
                <div class="flux-seo-serp-preview">
                    <h4><?php _e('SERP Preview', 'flux-seo-enhanced'); ?></h4>
                    <div class="flux-seo-serp-result">
                        <div class="flux-seo-serp-title" id="serp-title"><?php _e('Your page title will appear here', 'flux-seo-enhanced'); ?></div>
                        <div class="flux-seo-serp-url" id="serp-url">https://example.com/page</div>
                        <div class="flux-seo-serp-description" id="serp-description"><?php _e('Your meta description will appear here', 'flux-seo-enhanced'); ?></div>
                    </div>
                </div>
                
                <!-- Social Media Preview -->
                <div class="flux-seo-social-preview">
                    <h4><?php _e('Social Media Preview', 'flux-seo-enhanced'); ?></h4>
                    <div class="flux-seo-social-card">
                        <div class="flux-seo-social-image" id="social-image">
                            <span><?php _e('Image Preview', 'flux-seo-enhanced'); ?></span>
                        </div>
                        <div class="flux-seo-social-content">
                            <div class="flux-seo-social-title" id="social-title"><?php _e('Page Title', 'flux-seo-enhanced'); ?></div>
                            <div class="flux-seo-social-description" id="social-description"><?php _e('Meta description', 'flux-seo-enhanced'); ?></div>
                            <div class="flux-seo-social-url" id="social-url">example.com</div>
                        </div>
                    </div>
                </div>
                
                <!-- Copy Meta Tags -->
                <div class="flux-seo-copy-section">
                    <button id="copy-meta-tags" class="flux-seo-btn flux-seo-btn-secondary">
                        <span class="flux-seo-btn-icon">📋</span>
                        <span class="flux-seo-btn-text"><?php _e('Copy Meta Tags', 'flux-seo-enhanced'); ?></span>
                    </button>
                    <button id="download-meta-tags" class="flux-seo-btn flux-seo-btn-outline">
                        <span class="flux-seo-btn-icon">💾</span>
                        <span class="flux-seo-btn-text"><?php _e('Download HTML', 'flux-seo-enhanced'); ?></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
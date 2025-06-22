<?php
// Generator Tab Content
?>
<div id="generator-tab" class="flux-seo-tab-content">
    <div class="flux-seo-card">
        <div class="flux-seo-card-header">
            <h2 class="flux-seo-card-title">
                <span class="flux-seo-card-icon">✨</span>
                <span data-key="blogGenerator"><?php _e('AI Content Generator', 'flux-seo-enhanced'); ?></span>
            </h2>
            <p class="flux-seo-card-description">
                <?php _e('Generate high-quality, SEO-optimized content with AI assistance', 'flux-seo-enhanced'); ?>
            </p>
        </div>
        <div class="flux-seo-card-body">
            <div class="flux-seo-form">
                <div class="flux-seo-form-group">
                    <label class="flux-seo-label" data-key="topic"><?php _e('Topic/Subject', 'flux-seo-enhanced'); ?></label>
                    <input type="text" id="generator-topic" class="flux-seo-input"
                           placeholder="<?php _e('Enter the main topic for content generation', 'flux-seo-enhanced'); ?>">
                </div>
                <div class="flux-seo-form-row">
                    <div class="flux-seo-form-group">
                        <label class="flux-seo-label" data-key="contentType"><?php _e('Content Type', 'flux-seo-enhanced'); ?></label>
                        <select id="generator-type" class="flux-seo-select">
                            <option value="blog" data-key="blogPost"><?php _e('Blog Post', 'flux-seo-enhanced'); ?></option>
                            <option value="article" data-key="article"><?php _e('Article', 'flux-seo-enhanced'); ?></option>
                            <option value="product" data-key="productDescription"><?php _e('Product Description', 'flux-seo-enhanced'); ?></option>
                            <option value="landing" data-key="landingPage"><?php _e('Landing Page', 'flux-seo-enhanced'); ?></option>
                            <option value="social" data-key="socialMedia"><?php _e('Social Media Post', 'flux-seo-enhanced'); ?></option>
                        </select>
                    </div>
                    <div class="flux-seo-form-group">
                        <label class="flux-seo-label" data-key="tone"><?php _e('Writing Tone', 'flux-seo-enhanced'); ?></label>
                        <select id="generator-tone" class="flux-seo-select">
                            <option value="professional" data-key="professional"><?php _e('Professional', 'flux-seo-enhanced'); ?></option>
                            <option value="casual" data-key="casual"><?php _e('Casual', 'flux-seo-enhanced'); ?></option>
                            <option value="friendly" data-key="friendly"><?php _e('Friendly', 'flux-seo-enhanced'); ?></option>
                            <option value="authoritative" data-key="authoritative"><?php _e('Authoritative', 'flux-seo-enhanced'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="flux-seo-form-group">
                    <label class="flux-seo-label" data-key="keywords"><?php _e('Target Keywords', 'flux-seo-enhanced'); ?></label>
                    <input type="text" id="generator-keywords" class="flux-seo-input"
                           placeholder="<?php _e('Keywords to include in the content', 'flux-seo-enhanced'); ?>">
                </div>
                <div class="flux-seo-form-group">
                    <label class="flux-seo-label" data-key="additionalInstructions"><?php _e('Additional Instructions', 'flux-seo-enhanced'); ?></label>
                    <textarea id="generator-instructions" class="flux-seo-textarea" rows="3"
                              placeholder="<?php _e('Any specific requirements or guidelines...', 'flux-seo-enhanced'); ?>"></textarea>
                </div>
                <div class="flux-seo-form-actions">
                    <button id="generate-btn" class="flux-seo-btn flux-seo-btn-primary">
                        <span class="flux-seo-btn-icon">🤖</span>
                        <span class="flux-seo-btn-text" data-key="generateContent"><?php _e('Generate Content', 'flux-seo-enhanced'); ?></span>
                    </button>
                </div>
            </div>
            
            <div id="generator-results" class="flux-seo-results" style="display: none;">
                <div class="flux-seo-results-header">
                    <h3><?php _e('Generated Content', 'flux-seo-enhanced'); ?></h3>
                    <span class="flux-seo-ai-badge" data-key="poweredByGemini"><?php _e('Powered by Google Gemini 2.5 Pro', 'flux-seo-enhanced'); ?></span>
                </div>
                <div id="generator-display" class="flux-seo-generator-display"></div>
                <div class="flux-seo-content-actions">
                    <button id="copy-content" class="flux-seo-btn flux-seo-btn-secondary">
                        <span class="flux-seo-btn-icon">📋</span>
                        <span class="flux-seo-btn-text"><?php _e('Copy Content', 'flux-seo-enhanced'); ?></span>
                    </button>
                    <button id="regenerate-content" class="flux-seo-btn flux-seo-btn-outline">
                        <span class="flux-seo-btn-icon">🔄</span>
                        <span class="flux-seo-btn-text"><?php _e('Regenerate', 'flux-seo-enhanced'); ?></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
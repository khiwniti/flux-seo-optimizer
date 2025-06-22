<?php
// Schema Tab Content
?>
<div id="schema-tab" class="flux-seo-tab-content">
    <div class="flux-seo-card">
        <div class="flux-seo-card-header">
            <h2 class="flux-seo-card-title">
                <span class="flux-seo-card-icon">📋</span>
                <span data-key="schemaMarkup"><?php _e('Schema Markup Generator', 'flux-seo-enhanced'); ?></span>
            </h2>
            <p class="flux-seo-card-description">
                <?php _e('Generate structured data markup for better search engine understanding', 'flux-seo-enhanced'); ?>
            </p>
        </div>
        <div class="flux-seo-card-body">
            <div class="flux-seo-form">
                <div class="flux-seo-form-group">
                    <label class="flux-seo-label"><?php _e('Schema Type', 'flux-seo-enhanced'); ?></label>
                    <select id="schema-type" class="flux-seo-select">
                        <option value="article"><?php _e('Article', 'flux-seo-enhanced'); ?></option>
                        <option value="product"><?php _e('Product', 'flux-seo-enhanced'); ?></option>
                        <option value="organization"><?php _e('Organization', 'flux-seo-enhanced'); ?></option>
                        <option value="person"><?php _e('Person', 'flux-seo-enhanced'); ?></option>
                        <option value="event"><?php _e('Event', 'flux-seo-enhanced'); ?></option>
                        <option value="recipe"><?php _e('Recipe', 'flux-seo-enhanced'); ?></option>
                        <option value="review"><?php _e('Review', 'flux-seo-enhanced'); ?></option>
                        <option value="faq"><?php _e('FAQ', 'flux-seo-enhanced'); ?></option>
                        <option value="breadcrumb"><?php _e('Breadcrumb', 'flux-seo-enhanced'); ?></option>
                        <option value="local-business"><?php _e('Local Business', 'flux-seo-enhanced'); ?></option>
                    </select>
                </div>
                <div class="flux-seo-form-group">
                    <label class="flux-seo-label"><?php _e('Content Title', 'flux-seo-enhanced'); ?></label>
                    <input type="text" id="schema-title" class="flux-seo-input" 
                           placeholder="<?php _e('Enter the title of your content', 'flux-seo-enhanced'); ?>">
                </div>
                <div class="flux-seo-form-group">
                    <label class="flux-seo-label"><?php _e('Description', 'flux-seo-enhanced'); ?></label>
                    <textarea id="schema-description" class="flux-seo-textarea" rows="3"
                              placeholder="<?php _e('Enter a description of your content', 'flux-seo-enhanced'); ?>"></textarea>
                </div>
                <div class="flux-seo-form-row">
                    <div class="flux-seo-form-group">
                        <label class="flux-seo-label"><?php _e('Author/Organization', 'flux-seo-enhanced'); ?></label>
                        <input type="text" id="schema-author" class="flux-seo-input" 
                               placeholder="<?php _e('Enter author or organization name', 'flux-seo-enhanced'); ?>">
                    </div>
                    <div class="flux-seo-form-group">
                        <label class="flux-seo-label"><?php _e('Date Published', 'flux-seo-enhanced'); ?></label>
                        <input type="date" id="schema-date" class="flux-seo-input">
                    </div>
                </div>
                <div class="flux-seo-form-group">
                    <label class="flux-seo-label"><?php _e('Image URL', 'flux-seo-enhanced'); ?></label>
                    <input type="url" id="schema-image" class="flux-seo-input" 
                           placeholder="https://example.com/image.jpg">
                </div>
                
                <!-- Dynamic fields based on schema type -->
                <div id="schema-dynamic-fields" class="flux-seo-dynamic-fields"></div>
                
                <button id="generate-schema-btn" class="flux-seo-btn flux-seo-btn-primary">
                    <span class="flux-seo-btn-icon">🤖</span>
                    <span class="flux-seo-btn-text"><?php _e('Generate Schema', 'flux-seo-enhanced'); ?></span>
                </button>
            </div>
            
            <div id="schema-results" class="flux-seo-results" style="display: none;">
                <div class="flux-seo-results-header">
                    <h3><?php _e('Generated Schema Markup', 'flux-seo-enhanced'); ?></h3>
                    <span class="flux-seo-ai-badge"><?php _e('Powered by Google Gemini 2.5 Pro', 'flux-seo-enhanced'); ?></span>
                </div>
                <div id="schema-display" class="flux-seo-schema-display"></div>
                
                <!-- Schema Validation -->
                <div class="flux-seo-schema-validation">
                    <h4><?php _e('Schema Validation', 'flux-seo-enhanced'); ?></h4>
                    <div id="schema-validation-results" class="flux-seo-validation-results">
                        <div class="flux-seo-validation-item">
                            <span class="flux-seo-validation-status">✅</span>
                            <span class="flux-seo-validation-text"><?php _e('Valid JSON-LD structure', 'flux-seo-enhanced'); ?></span>
                        </div>
                        <div class="flux-seo-validation-item">
                            <span class="flux-seo-validation-status">✅</span>
                            <span class="flux-seo-validation-text"><?php _e('Required properties included', 'flux-seo-enhanced'); ?></span>
                        </div>
                        <div class="flux-seo-validation-item">
                            <span class="flux-seo-validation-status">✅</span>
                            <span class="flux-seo-validation-text"><?php _e('Schema.org compliant', 'flux-seo-enhanced'); ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Schema Actions -->
                <div class="flux-seo-schema-actions">
                    <button id="copy-schema" class="flux-seo-btn flux-seo-btn-secondary">
                        <span class="flux-seo-btn-icon">📋</span>
                        <span class="flux-seo-btn-text"><?php _e('Copy Schema', 'flux-seo-enhanced'); ?></span>
                    </button>
                    <button id="test-schema" class="flux-seo-btn flux-seo-btn-outline">
                        <span class="flux-seo-btn-icon">🔍</span>
                        <span class="flux-seo-btn-text"><?php _e('Test in Google', 'flux-seo-enhanced'); ?></span>
                    </button>
                    <button id="download-schema" class="flux-seo-btn flux-seo-btn-outline">
                        <span class="flux-seo-btn-icon">💾</span>
                        <span class="flux-seo-btn-text"><?php _e('Download JSON', 'flux-seo-enhanced'); ?></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
// Keywords Tab Content
?>
<div id="keywords-tab" class="flux-seo-tab-content">
    <div class="flux-seo-card">
        <div class="flux-seo-card-header">
            <h2 class="flux-seo-card-title">
                <span class="flux-seo-card-icon">🎯</span>
                <span data-key="keywordResearch"><?php _e('AI Keyword Research', 'flux-seo-enhanced'); ?></span>
            </h2>
            <p class="flux-seo-card-description">
                <?php _e('Discover high-value keywords with AI-powered research and analysis', 'flux-seo-enhanced'); ?>
            </p>
        </div>
        <div class="flux-seo-card-body">
            <div class="flux-seo-form">
                <div class="flux-seo-form-group">
                    <label class="flux-seo-label" data-key="seedKeyword"><?php _e('Seed Keyword', 'flux-seo-enhanced'); ?></label>
                    <input type="text" id="keyword-seed" class="flux-seo-input"
                           placeholder="<?php _e('Enter your main keyword or topic', 'flux-seo-enhanced'); ?>">
                </div>
                <div class="flux-seo-form-row">
                    <div class="flux-seo-form-group">
                        <label class="flux-seo-label" data-key="industry"><?php _e('Industry/Niche', 'flux-seo-enhanced'); ?></label>
                        <select id="keyword-industry" class="flux-seo-select">
                            <option value="general" data-key="general"><?php _e('General', 'flux-seo-enhanced'); ?></option>
                            <option value="technology" data-key="technology"><?php _e('Technology', 'flux-seo-enhanced'); ?></option>
                            <option value="health" data-key="health"><?php _e('Health & Wellness', 'flux-seo-enhanced'); ?></option>
                            <option value="finance" data-key="finance"><?php _e('Finance', 'flux-seo-enhanced'); ?></option>
                            <option value="education" data-key="education"><?php _e('Education', 'flux-seo-enhanced'); ?></option>
                            <option value="ecommerce" data-key="ecommerce"><?php _e('E-commerce', 'flux-seo-enhanced'); ?></option>
                            <option value="travel" data-key="travel"><?php _e('Travel', 'flux-seo-enhanced'); ?></option>
                            <option value="food" data-key="food"><?php _e('Food & Beverage', 'flux-seo-enhanced'); ?></option>
                        </select>
                    </div>
                    <div class="flux-seo-form-group">
                        <label class="flux-seo-label" data-key="location"><?php _e('Target Location', 'flux-seo-enhanced'); ?></label>
                        <select id="keyword-location" class="flux-seo-select">
                            <option value="global" data-key="global"><?php _e('Global', 'flux-seo-enhanced'); ?></option>
                            <option value="us" data-key="unitedStates"><?php _e('United States', 'flux-seo-enhanced'); ?></option>
                            <option value="th" data-key="thailand"><?php _e('Thailand', 'flux-seo-enhanced'); ?></option>
                            <option value="uk" data-key="unitedKingdom"><?php _e('United Kingdom', 'flux-seo-enhanced'); ?></option>
                            <option value="ca" data-key="canada"><?php _e('Canada', 'flux-seo-enhanced'); ?></option>
                            <option value="au" data-key="australia"><?php _e('Australia', 'flux-seo-enhanced'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="flux-seo-form-row">
                    <div class="flux-seo-form-group">
                        <label class="flux-seo-label" data-key="difficulty"><?php _e('Keyword Difficulty', 'flux-seo-enhanced'); ?></label>
                        <select id="keyword-difficulty" class="flux-seo-select">
                            <option value="any" data-key="any"><?php _e('Any Difficulty', 'flux-seo-enhanced'); ?></option>
                            <option value="low" data-key="low"><?php _e('Low (Easy to rank)', 'flux-seo-enhanced'); ?></option>
                            <option value="medium" data-key="medium"><?php _e('Medium', 'flux-seo-enhanced'); ?></option>
                            <option value="high" data-key="high"><?php _e('High (Competitive)', 'flux-seo-enhanced'); ?></option>
                        </select>
                    </div>
                    <div class="flux-seo-form-group">
                        <label class="flux-seo-label" data-key="searchVolume"><?php _e('Search Volume', 'flux-seo-enhanced'); ?></label>
                        <select id="keyword-volume" class="flux-seo-select">
                            <option value="any" data-key="any"><?php _e('Any Volume', 'flux-seo-enhanced'); ?></option>
                            <option value="low" data-key="low"><?php _e('Low (0-1K)', 'flux-seo-enhanced'); ?></option>
                            <option value="medium" data-key="medium"><?php _e('Medium (1K-10K)', 'flux-seo-enhanced'); ?></option>
                            <option value="high" data-key="high"><?php _e('High (10K+)', 'flux-seo-enhanced'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="flux-seo-form-actions">
                    <button id="research-keywords-btn" class="flux-seo-btn flux-seo-btn-primary">
                        <span class="flux-seo-btn-icon">🤖</span>
                        <span class="flux-seo-btn-text" data-key="researchKeywords"><?php _e('Research Keywords', 'flux-seo-enhanced'); ?></span>
                    </button>
                </div>
            </div>
            
            <div id="keyword-results" class="flux-seo-results" style="display: none;">
                <div class="flux-seo-results-header">
                    <h3><?php _e('AI Keyword Research Results', 'flux-seo-enhanced'); ?></h3>
                    <span class="flux-seo-ai-badge" data-key="poweredByGemini"><?php _e('Powered by Google Gemini 2.5 Pro', 'flux-seo-enhanced'); ?></span>
                </div>
                <div id="keyword-display" class="flux-seo-keyword-display"></div>
                
                <!-- Keyword Analysis Tools -->
                <div class="flux-seo-keyword-tools">
                    <div class="flux-seo-tool-section">
                        <h4><?php _e('Keyword Analysis Tools', 'flux-seo-enhanced'); ?></h4>
                        <div class="flux-seo-tool-buttons">
                            <button onclick="analyzeKeywordTrends()" class="flux-seo-btn flux-seo-btn-secondary">
                                <span class="flux-seo-btn-icon">📈</span>
                                <span class="flux-seo-btn-text"><?php _e('Trend Analysis', 'flux-seo-enhanced'); ?></span>
                            </button>
                            <button onclick="findLongTailKeywords()" class="flux-seo-btn flux-seo-btn-secondary">
                                <span class="flux-seo-btn-icon">🎯</span>
                                <span class="flux-seo-btn-text"><?php _e('Long-tail Keywords', 'flux-seo-enhanced'); ?></span>
                            </button>
                            <button onclick="analyzeCompetitorKeywords()" class="flux-seo-btn flux-seo-btn-secondary">
                                <span class="flux-seo-btn-icon">🏆</span>
                                <span class="flux-seo-btn-text"><?php _e('Competitor Keywords', 'flux-seo-enhanced'); ?></span>
                            </button>
                            <button onclick="generateKeywordClusters()" class="flux-seo-btn flux-seo-btn-secondary">
                                <span class="flux-seo-btn-icon">🔗</span>
                                <span class="flux-seo-btn-text"><?php _e('Keyword Clusters', 'flux-seo-enhanced'); ?></span>
                            </button>
                        </div>
                    </div>
                    
                    <div id="keyword-tools-results" class="flux-seo-tools-results"></div>
                </div>
            </div>
        </div>
    </div>
</div>
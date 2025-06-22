<?php
// Analytics Tab Content
?>
<div id="analytics-tab" class="flux-seo-tab-content">
    <div class="flux-seo-card">
        <div class="flux-seo-card-header">
            <h2 class="flux-seo-card-title">
                <span class="flux-seo-card-icon">📊</span>
                <span data-key="advancedAnalytics"><?php _e('Advanced Analytics', 'flux-seo-enhanced'); ?></span>
            </h2>
            <p class="flux-seo-card-description">
                <?php _e('Comprehensive SEO analytics and performance insights', 'flux-seo-enhanced'); ?>
            </p>
        </div>
        <div class="flux-seo-card-body">
            <div class="flux-seo-analytics-grid">
                <!-- Content Performance Analysis -->
                <div class="flux-seo-analytics-section">
                    <div class="flux-seo-section-header" onclick="toggleAnalyticsSection('content-performance')">
                        <h3>📈 <?php _e('Content Performance Analysis', 'flux-seo-enhanced'); ?></h3>
                        <span class="flux-seo-toggle-icon">▼</span>
                    </div>
                    <div id="content-performance" class="flux-seo-section-content">
                        <div class="flux-seo-form-group">
                            <label class="flux-seo-label"><?php _e('Website URL', 'flux-seo-enhanced'); ?></label>
                            <input type="url" id="content-url" class="flux-seo-input" placeholder="https://example.com">
                        </div>
                        <button onclick="analyzeContentPerformance()" class="flux-seo-btn flux-seo-btn-primary">
                            <span class="flux-seo-btn-icon">🔍</span>
                            <span class="flux-seo-btn-text"><?php _e('Analyze Performance', 'flux-seo-enhanced'); ?></span>
                        </button>
                        <div id="content-performance-results" class="flux-seo-analytics-results"></div>
                    </div>
                </div>

                <!-- Keyword Ranking Analysis -->
                <div class="flux-seo-analytics-section">
                    <div class="flux-seo-section-header" onclick="toggleAnalyticsSection('keyword-ranking')">
                        <h3>🎯 <?php _e('Keyword Ranking Analysis', 'flux-seo-enhanced'); ?></h3>
                        <span class="flux-seo-toggle-icon">▼</span>
                    </div>
                    <div id="keyword-ranking" class="flux-seo-section-content">
                        <div class="flux-seo-form-group">
                            <label class="flux-seo-label"><?php _e('Target Keywords', 'flux-seo-enhanced'); ?></label>
                            <textarea id="ranking-keywords" class="flux-seo-textarea" rows="3" placeholder="<?php _e('Enter keywords to track (one per line)', 'flux-seo-enhanced'); ?>"></textarea>
                        </div>
                        <button onclick="analyzeKeywordRanking()" class="flux-seo-btn flux-seo-btn-primary">
                            <span class="flux-seo-btn-icon">📊</span>
                            <span class="flux-seo-btn-text"><?php _e('Check Rankings', 'flux-seo-enhanced'); ?></span>
                        </button>
                        <div id="keyword-ranking-results" class="flux-seo-analytics-results"></div>
                    </div>
                </div>

                <!-- Competitor Analysis -->
                <div class="flux-seo-analytics-section">
                    <div class="flux-seo-section-header" onclick="toggleAnalyticsSection('competitor-analysis')">
                        <h3>🏆 <?php _e('Competitor Analysis', 'flux-seo-enhanced'); ?></h3>
                        <span class="flux-seo-toggle-icon">▼</span>
                    </div>
                    <div id="competitor-analysis" class="flux-seo-section-content">
                        <div class="flux-seo-form-group">
                            <label class="flux-seo-label"><?php _e('Competitor URLs', 'flux-seo-enhanced'); ?></label>
                            <textarea id="competitor-urls" class="flux-seo-textarea" rows="3" placeholder="<?php _e('Enter competitor URLs (one per line)', 'flux-seo-enhanced'); ?>"></textarea>
                        </div>
                        <button onclick="analyzeCompetitors()" class="flux-seo-btn flux-seo-btn-primary">
                            <span class="flux-seo-btn-icon">🔍</span>
                            <span class="flux-seo-btn-text"><?php _e('Analyze Competitors', 'flux-seo-enhanced'); ?></span>
                        </button>
                        <div id="competitor-analysis-results" class="flux-seo-analytics-results"></div>
                    </div>
                </div>

                <!-- Backlink Analysis -->
                <div class="flux-seo-analytics-section">
                    <div class="flux-seo-section-header" onclick="toggleAnalyticsSection('backlink-analysis')">
                        <h3>🔗 <?php _e('Backlink Analysis', 'flux-seo-enhanced'); ?></h3>
                        <span class="flux-seo-toggle-icon">▼</span>
                    </div>
                    <div id="backlink-analysis" class="flux-seo-section-content">
                        <div class="flux-seo-form-group">
                            <label class="flux-seo-label"><?php _e('Domain to Analyze', 'flux-seo-enhanced'); ?></label>
                            <input type="url" id="backlink-domain" class="flux-seo-input" placeholder="https://example.com">
                        </div>
                        <button onclick="analyzeBacklinks()" class="flux-seo-btn flux-seo-btn-primary">
                            <span class="flux-seo-btn-icon">🔗</span>
                            <span class="flux-seo-btn-text"><?php _e('Analyze Backlinks', 'flux-seo-enhanced'); ?></span>
                        </button>
                        <div id="backlink-analysis-results" class="flux-seo-analytics-results"></div>
                    </div>
                </div>

                <!-- Technical SEO Audit -->
                <div class="flux-seo-analytics-section">
                    <div class="flux-seo-section-header" onclick="toggleAnalyticsSection('technical-audit')">
                        <h3>⚙️ <?php _e('Technical SEO Audit', 'flux-seo-enhanced'); ?></h3>
                        <span class="flux-seo-toggle-icon">▼</span>
                    </div>
                    <div id="technical-audit" class="flux-seo-section-content">
                        <div class="flux-seo-form-group">
                            <label class="flux-seo-label"><?php _e('Website URL', 'flux-seo-enhanced'); ?></label>
                            <input type="url" id="technical-url" class="flux-seo-input" placeholder="https://example.com">
                        </div>
                        <button onclick="runTechnicalAudit()" class="flux-seo-btn flux-seo-btn-primary">
                            <span class="flux-seo-btn-icon">🔧</span>
                            <span class="flux-seo-btn-text"><?php _e('Run Technical Audit', 'flux-seo-enhanced'); ?></span>
                        </button>
                        <div id="technical-audit-results" class="flux-seo-analytics-results"></div>
                    </div>
                </div>

                <!-- Content Gap Analysis -->
                <div class="flux-seo-analytics-section">
                    <div class="flux-seo-section-header" onclick="toggleAnalyticsSection('content-gap')">
                        <h3>📝 <?php _e('Content Gap Analysis', 'flux-seo-enhanced'); ?></h3>
                        <span class="flux-seo-toggle-icon">▼</span>
                    </div>
                    <div id="content-gap" class="flux-seo-section-content">
                        <div class="flux-seo-form-group">
                            <label class="flux-seo-label"><?php _e('Your Website', 'flux-seo-enhanced'); ?></label>
                            <input type="url" id="gap-your-site" class="flux-seo-input" placeholder="https://yoursite.com">
                        </div>
                        <div class="flux-seo-form-group">
                            <label class="flux-seo-label"><?php _e('Competitor Website', 'flux-seo-enhanced'); ?></label>
                            <input type="url" id="gap-competitor-site" class="flux-seo-input" placeholder="https://competitor.com">
                        </div>
                        <button onclick="analyzeContentGap()" class="flux-seo-btn flux-seo-btn-primary">
                            <span class="flux-seo-btn-icon">🔍</span>
                            <span class="flux-seo-btn-text"><?php _e('Find Content Gaps', 'flux-seo-enhanced'); ?></span>
                        </button>
                        <div id="content-gap-results" class="flux-seo-analytics-results"></div>
                    </div>
                </div>

                <!-- SERP Analysis -->
                <div class="flux-seo-analytics-section">
                    <div class="flux-seo-section-header" onclick="toggleAnalyticsSection('serp-analysis')">
                        <h3>🔍 <?php _e('SERP Analysis', 'flux-seo-enhanced'); ?></h3>
                        <span class="flux-seo-toggle-icon">▼</span>
                    </div>
                    <div id="serp-analysis" class="flux-seo-section-content">
                        <div class="flux-seo-form-group">
                            <label class="flux-seo-label"><?php _e('Search Query', 'flux-seo-enhanced'); ?></label>
                            <input type="text" id="serp-query" class="flux-seo-input" placeholder="<?php _e('Enter search query to analyze', 'flux-seo-enhanced'); ?>">
                        </div>
                        <div class="flux-seo-form-group">
                            <label class="flux-seo-label"><?php _e('Location', 'flux-seo-enhanced'); ?></label>
                            <select id="serp-location" class="flux-seo-select">
                                <option value="global"><?php _e('Global', 'flux-seo-enhanced'); ?></option>
                                <option value="us"><?php _e('United States', 'flux-seo-enhanced'); ?></option>
                                <option value="th"><?php _e('Thailand', 'flux-seo-enhanced'); ?></option>
                                <option value="uk"><?php _e('United Kingdom', 'flux-seo-enhanced'); ?></option>
                            </select>
                        </div>
                        <button onclick="analyzeSERP()" class="flux-seo-btn flux-seo-btn-primary">
                            <span class="flux-seo-btn-icon">🔍</span>
                            <span class="flux-seo-btn-text"><?php _e('Analyze SERP', 'flux-seo-enhanced'); ?></span>
                        </button>
                        <div id="serp-analysis-results" class="flux-seo-analytics-results"></div>
                    </div>
                </div>

                <!-- Performance Monitoring -->
                <div class="flux-seo-analytics-section">
                    <div class="flux-seo-section-header" onclick="toggleAnalyticsSection('performance-monitoring')">
                        <h3>📈 <?php _e('Performance Monitoring', 'flux-seo-enhanced'); ?></h3>
                        <span class="flux-seo-toggle-icon">▼</span>
                    </div>
                    <div id="performance-monitoring" class="flux-seo-section-content">
                        <div class="flux-seo-form-group">
                            <label class="flux-seo-label"><?php _e('Website URL', 'flux-seo-enhanced'); ?></label>
                            <input type="url" id="monitor-url" class="flux-seo-input" placeholder="https://example.com">
                        </div>
                        <div class="flux-seo-form-group">
                            <label class="flux-seo-label"><?php _e('Monitoring Period', 'flux-seo-enhanced'); ?></label>
                            <select id="monitor-period" class="flux-seo-select">
                                <option value="7"><?php _e('Last 7 days', 'flux-seo-enhanced'); ?></option>
                                <option value="30"><?php _e('Last 30 days', 'flux-seo-enhanced'); ?></option>
                                <option value="90"><?php _e('Last 90 days', 'flux-seo-enhanced'); ?></option>
                            </select>
                        </div>
                        <button onclick="monitorPerformance()" class="flux-seo-btn flux-seo-btn-primary">
                            <span class="flux-seo-btn-icon">📊</span>
                            <span class="flux-seo-btn-text"><?php _e('Monitor Performance', 'flux-seo-enhanced'); ?></span>
                        </button>
                        <div id="performance-monitoring-results" class="flux-seo-analytics-results"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
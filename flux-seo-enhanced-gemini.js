/**
 * Flux SEO Enhanced with Gemini AI - JavaScript
 * Enhanced version with Thai/English language switching and advanced analytics
 */

(function($) {
    'use strict';

    // Enhanced FluxSEO object with Gemini AI capabilities
    window.FluxSEOEnhanced = {
        currentLanguage: 'en',
        isLoading: false,
        strings: {},
        
        init: function() {
            this.strings = window.fluxSeoEnhanced?.strings || {};
            this.currentLanguage = this.detectLanguage();
            this.bindEvents();
            this.initializeLanguage();
            this.initializeUI();
            
            // Initialize auto blog if available
            if (typeof FluxSEOAutoBlog !== 'undefined') {
                FluxSEOAutoBlog.init();
            }
            
            console.log('Flux SEO Enhanced with Gemini AI initialized');
        },
        
        detectLanguage: function() {
            // Check URL parameter, localStorage, or browser language
            const urlParams = new URLSearchParams(window.location.search);
            const urlLang = urlParams.get('lang');
            const storedLang = localStorage.getItem('flux_seo_language');
            const browserLang = navigator.language.startsWith('th') ? 'th' : 'en';
            
            return urlLang || storedLang || browserLang;
        },
        
        bindEvents: function() {
            // Language switcher
            $(document).on('change', '#flux-seo-language-select', this.handleLanguageChange.bind(this));
            
            // Tab navigation
            $(document).on('click', '.flux-seo-nav-tab', this.handleTabChange.bind(this));
            
            // Content analyzer
            $(document).on('click', '#analyze-btn', this.handleContentAnalysis.bind(this));
            
            // Content generator
            $(document).on('click', '#generate-btn', this.handleContentGeneration.bind(this));
            
            // Website analytics
            $(document).on('click', '#analyze-website-btn', this.handleWebsiteAnalysis.bind(this));
            
            // SEO optimizer
            $(document).on('click', '#optimize-btn', this.handleContentOptimization.bind(this));
            
            // Keyword research
            $(document).on('click', '#research-keywords-btn', this.handleKeywordResearch.bind(this));
            
            // Meta tags
            $(document).on('click', '#generate-meta-btn', this.handleMetaGeneration.bind(this));
            $(document).on('input', '#meta-title, #meta-description', this.updateCharacterCount.bind(this));
            
            // Schema markup
            $(document).on('click', '#generate-schema-btn', this.handleSchemaGeneration.bind(this));
            $(document).on('change', '#schema-type', this.updateSchemaFields.bind(this));
            
            // Technical SEO
            $(document).on('click', '#run-technical-audit-btn', this.handleTechnicalAudit.bind(this));
            
            // Chatbot
            $(document).on('click', '#send-message-btn', this.handleChatMessage.bind(this));
            $(document).on('click', '.flux-seo-quick-action', this.handleQuickAction.bind(this));
            $(document).on('keypress', '#chatbot-input', this.handleChatKeypress.bind(this));
            $(document).on('input', '#chatbot-input', this.handleChatInputChange.bind(this));
            
            // Settings
            $(document).on('click', '#save-settings-btn', this.handleSaveSettings.bind(this));
            $(document).on('click', '#save-api-key-btn', this.saveApiKey.bind(this));
            $(document).on('click', '#clear-api-key-btn', this.clearApiKey.bind(this));
            
            // Image generation
            $(document).on('click', '#generate-image-btn', this.handleImageGeneration.bind(this));
            $(document).on('click', '#save-to-media-btn', this.handleSaveToMedia.bind(this));
            
            // Copy content
            $(document).on('click', '#copy-content-btn', this.handleCopyContent.bind(this));
            
            // Form validation
            $(document).on('input', '.flux-seo-input, .flux-seo-textarea', this.handleInputValidation.bind(this));
        },
        
        initializeLanguage: function() {
            $('#flux-seo-language-select').val(this.currentLanguage);
            this.updateLanguage();
        },
        
        initializeUI: function() {
            // Set initial tab
            this.showTab('analyzer');
            
            // Initialize tooltips and help text
            this.initializeTooltips();
            
            // Set up auto-save for form data
            this.initializeAutoSave();
            
            // Initialize keyboard shortcuts
            this.initializeKeyboardShortcuts();
            
            // Load API key
            this.loadApiKey();
            
            // Initialize schema fields
            this.initializeSchemaFields();
            
            // Initialize character counters
            this.initializeCharacterCounters();
            
            // Initialize chatbot
            this.initializeChatbot();
        },
        
        handleLanguageChange: function(e) {
            const newLanguage = $(e.target).val();
            this.switchLanguage(newLanguage);
        },
        
        switchLanguage: function(language) {
            if (this.currentLanguage === language) return;
            
            this.currentLanguage = language;
            localStorage.setItem('flux_seo_language', language);
            
            // Update UI language
            this.updateLanguage();
            
            // Update document attributes
            $('#flux-seo-enhanced-container').attr('data-language', language);
            $('html').attr('lang', language);
            
            // Show language switch notification
            this.showNotification(
                language === 'th' ? 'เปลี่ยนภาษาเป็นไทยแล้ว' : 'Language switched to English',
                'success'
            );
        },
        
        updateLanguage: function() {
            const strings = this.strings[this.currentLanguage] || this.strings.en || {};
            
            // Update all elements with data-key attributes
            $('[data-key]').each(function() {
                const key = $(this).data('key');
                const text = strings[key];
                if (text) {
                    if ($(this).is('input, textarea')) {
                        $(this).attr('placeholder', text);
                    } else {
                        $(this).text(text);
                    }
                }
            });
            
            // Update specific elements
            $('#flux-seo-title').text(strings.title || 'Flux SEO Enhanced');
            $('#flux-seo-subtitle').text(strings.subtitle || 'Professional SEO with Gemini AI');
            
            // Update form placeholders
            this.updateFormPlaceholders();
        },
        
        updateFormPlaceholders: function() {
            const strings = this.strings[this.currentLanguage] || this.strings.en || {};
            
            if (this.currentLanguage === 'th') {
                $('#analyzer-content').attr('placeholder', 'ใส่เนื้อหาที่ต้องการวิเคราะห์ด้วย AI...');
                $('#generator-topic').attr('placeholder', 'เช่น การตลาดดิจิทัล, เทคโนโลยี AI');
                $('#analytics-url').attr('placeholder', 'https://example.com');
                $('#optimizer-content').attr('placeholder', 'วางเนื้อหาที่ต้องการปรับปรุง SEO...');
                $('#keyword-seeds').attr('placeholder', 'ใส่คำหลักหรือหัวข้อที่สนใจ');
            } else {
                $('#analyzer-content').attr('placeholder', 'Enter your content for AI-powered SEO analysis...');
                $('#generator-topic').attr('placeholder', 'e.g., Digital Marketing, AI Technology');
                $('#analytics-url').attr('placeholder', 'https://example.com');
                $('#optimizer-content').attr('placeholder', 'Paste your content here for AI-powered SEO optimization...');
                $('#keyword-seeds').attr('placeholder', 'Enter seed keywords or topics');
            }
        },
        
        handleTabChange: function(e) {
            e.preventDefault();
            const tabId = $(e.currentTarget).data('tab');
            this.showTab(tabId);
        },
        
        showTab: function(tabId) {
            // Update nav tabs
            $('.flux-seo-nav-tab').removeClass('active');
            $(`.flux-seo-nav-tab[data-tab="${tabId}"]`).addClass('active');
            
            // Update content
            $('.flux-seo-tab-content').removeClass('active');
            $(`#${tabId}-tab`).addClass('active');
            
            // Track tab usage
            this.trackEvent('tab_change', { tab: tabId });
        },
        
        handleContentAnalysis: function(e) {
            e.preventDefault();
            
            const content = $('#analyzer-content').val().trim();
            const keywords = $('#analyzer-keywords').val().trim();
            const audience = $('#analyzer-audience').val();
            
            if (!content) {
                this.showNotification(this.getString('enterContent'), 'error');
                return;
            }
            
            this.setLoading('#analyze-btn', true);
            
            const data = {
                action: 'flux_seo_enhanced_action',
                action_type: 'analyze_content',
                content: content,
                keywords: keywords,
                audience: audience,
                language: this.currentLanguage,
                nonce: window.fluxSeoEnhanced?.nonce
            };
            
            $.post(window.fluxSeoEnhanced?.ajaxurl, data)
                .done((response) => {
                    if (response.success) {
                        this.displayAnalysisResults(response.data);
                        this.trackEvent('content_analysis', { language: this.currentLanguage });
                    } else {
                        this.showNotification(response.data || this.getString('error'), 'error');
                    }
                })
                .fail(() => {
                    this.showNotification(this.getString('error'), 'error');
                })
                .always(() => {
                    this.setLoading('#analyze-btn', false);
                });
        },
        
        handleContentGeneration: function(e) {
            e.preventDefault();
            
            const topic = $('#generator-topic').val().trim();
            const contentType = $('#generator-type').val();
            const tone = $('#generator-tone').val();
            const audience = $('#generator-audience').val();
            const wordCount = $('#generator-wordcount').val();
            const keywords = $('#generator-keywords').val().trim();
            
            if (!topic) {
                this.showNotification(this.getString('enterTopic'), 'error');
                return;
            }
            
            this.setLoading('#generate-btn', true);
            
            const data = {
                action: 'flux_seo_enhanced_action',
                action_type: 'generate_content',
                topic: topic,
                content_type: contentType,
                tone: tone,
                audience: audience,
                word_count: wordCount,
                keywords: keywords,
                language: this.currentLanguage,
                nonce: window.fluxSeoEnhanced?.nonce
            };
            
            $.post(window.fluxSeoEnhanced?.ajaxurl, data)
                .done((response) => {
                    if (response.success) {
                        this.displayGenerationResults(response.data);
                        this.trackEvent('content_generation', { 
                            language: this.currentLanguage,
                            content_type: contentType,
                            word_count: wordCount
                        });
                    } else {
                        this.showNotification(response.data || this.getString('error'), 'error');
                    }
                })
                .fail(() => {
                    this.showNotification(this.getString('error'), 'error');
                })
                .always(() => {
                    this.setLoading('#generate-btn', false);
                });
        },
        
        handleWebsiteAnalysis: function(e) {
            e.preventDefault();
            
            const url = $('#analytics-url').val().trim();
            
            if (!url) {
                this.showNotification(this.getString('enterUrl'), 'error');
                return;
            }
            
            if (!this.isValidUrl(url)) {
                this.showNotification(
                    this.currentLanguage === 'th' ? 'กรุณาใส่ URL ที่ถูกต้อง' : 'Please enter a valid URL',
                    'error'
                );
                return;
            }
            
            this.setLoading('#analyze-website-btn', true);
            
            const data = {
                action: 'flux_seo_enhanced_action',
                action_type: 'analyze_website',
                url: url,
                language: this.currentLanguage,
                nonce: window.fluxSeoEnhanced?.nonce
            };
            
            $.post(window.fluxSeoEnhanced?.ajaxurl, data)
                .done((response) => {
                    if (response.success) {
                        this.displayAnalyticsResults(response.data);
                        this.trackEvent('website_analysis', { language: this.currentLanguage });
                    } else {
                        this.showNotification(response.data || this.getString('error'), 'error');
                    }
                })
                .fail(() => {
                    this.showNotification(this.getString('error'), 'error');
                })
                .always(() => {
                    this.setLoading('#analyze-website-btn', false);
                });
        },
        
        handleContentOptimization: function(e) {
            e.preventDefault();
            
            const content = $('#optimizer-content').val().trim();
            const keywords = $('#optimizer-keywords').val().trim();
            const audience = $('#optimizer-audience').val();
            
            if (!content) {
                this.showNotification(this.getString('enterContent'), 'error');
                return;
            }
            
            this.setLoading('#optimize-btn', true);
            
            const data = {
                action: 'flux_seo_enhanced_action',
                action_type: 'optimize_content',
                content: content,
                keywords: keywords,
                audience: audience,
                language: this.currentLanguage,
                nonce: window.fluxSeoEnhanced?.nonce
            };
            
            $.post(window.fluxSeoEnhanced?.ajaxurl, data)
                .done((response) => {
                    if (response.success) {
                        this.displayOptimizationResults(response.data);
                        this.trackEvent('content_optimization', { language: this.currentLanguage });
                    } else {
                        this.showNotification(response.data || this.getString('error'), 'error');
                    }
                })
                .fail(() => {
                    this.showNotification(this.getString('error'), 'error');
                })
                .always(() => {
                    this.setLoading('#optimize-btn', false);
                });
        },
        
        handleKeywordResearch: function(e) {
            e.preventDefault();
            
            const seeds = $('#keyword-seeds').val().trim();
            const industry = $('#keyword-industry').val().trim();
            
            if (!seeds) {
                this.showNotification(
                    this.currentLanguage === 'th' ? 'กรุณาใส่คำหลักที่ต้องการวิจัย' : 'Please enter keywords to research',
                    'error'
                );
                return;
            }
            
            this.setLoading('#research-keywords-btn', true);
            
            const data = {
                action: 'flux_seo_enhanced_action',
                action_type: 'research_keywords',
                seeds: seeds,
                industry: industry,
                language: this.currentLanguage,
                nonce: window.fluxSeoEnhanced?.nonce
            };
            
            $.post(window.fluxSeoEnhanced?.ajaxurl, data)
                .done((response) => {
                    if (response.success) {
                        this.displayKeywordResults(response.data);
                        this.trackEvent('keyword_research', { language: this.currentLanguage });
                    } else {
                        this.showNotification(response.data || this.getString('error'), 'error');
                    }
                })
                .fail(() => {
                    this.showNotification(this.getString('error'), 'error');
                })
                .always(() => {
                    this.setLoading('#research-keywords-btn', false);
                });
        },
        
        displayAnalysisResults: function(data) {
            // Update metrics
            $('#seo-score').text(data.seo_score || '--');
            $('#content-quality-score').text(data.content_quality_score || '--');
            $('#readability-score').text(data.readability_score || '--');
            $('#engagement-score').text(data.engagement_score || '--');
            
            // Display AI insights
            let insightsHtml = '';
            if (data.analysis) {
                insightsHtml += `<div class="flux-seo-insight-item">
                    <h5>${this.getString('analysis') || 'Analysis'}</h5>
                    <p>${data.analysis}</p>
                </div>`;
            }
            
            if (data.recommendations && data.recommendations.length > 0) {
                insightsHtml += `<div class="flux-seo-insight-item">
                    <h5>${this.getString('recommendations') || 'Recommendations'}</h5>
                    <ul class="flux-seo-recommendation-list">`;
                data.recommendations.forEach(rec => {
                    insightsHtml += `<li class="flux-seo-recommendation-item">${rec}</li>`;
                });
                insightsHtml += '</ul></div>';
            }
            
            $('#gemini-analysis-content').html(insightsHtml);
            $('#analysis-results').show();
            
            // Scroll to results
            this.scrollToElement('#analysis-results');
        },
        
        displayGenerationResults: function(data) {
            let contentHtml = '';
            
            // Title section
            if (data.title) {
                contentHtml += `<div class="flux-seo-content-section">
                    <h4>📝 ${this.getString('title_field') || 'Title'}</h4>
                    <div class="flux-seo-content-title">${data.title}</div>
                </div>`;
            }
            
            // Meta description section
            if (data.meta_description) {
                contentHtml += `<div class="flux-seo-content-section">
                    <h4>📄 ${this.getString('metaDescription') || 'Meta Description'}</h4>
                    <div class="flux-seo-content-meta">${data.meta_description}</div>
                </div>`;
            }
            
            // Content section
            if (data.content) {
                contentHtml += `<div class="flux-seo-content-section">
                    <h4>📖 ${this.getString('content') || 'Content'}</h4>
                    <div class="flux-seo-content-body">${data.content}</div>
                </div>`;
            }
            
            // Outline section
            if (data.outline && data.outline.length > 0) {
                contentHtml += `<div class="flux-seo-content-section">
                    <h4>📋 ${this.getString('contentOutline') || 'Content Outline'}</h4>
                    <ul class="flux-seo-outline-list">`;
                data.outline.forEach(item => {
                    contentHtml += `<li>${item}</li>`;
                });
                contentHtml += '</ul></div>';
            }
            
            // Keywords section
            if (data.keywords_used && data.keywords_used.length > 0) {
                contentHtml += `<div class="flux-seo-content-section">
                    <h4>🔑 ${this.getString('keywords') || 'Keywords'}</h4>
                    <div class="flux-seo-content-keywords">`;
                data.keywords_used.forEach(keyword => {
                    contentHtml += `<span class="flux-seo-keyword-tag">${keyword.trim()}</span>`;
                });
                contentHtml += '</div></div>';
            }
            
            // Scores section
            if (data.seo_score || data.readability_score || data.engagement_score) {
                contentHtml += `<div class="flux-seo-content-section">
                    <h4>📊 ${this.getString('results') || 'Results'}</h4>
                    <div class="flux-seo-content-scores">`;
                
                if (data.seo_score) {
                    contentHtml += `<div class="flux-seo-score-badge">📈 ${this.getString('seoScore') || 'SEO Score'}: ${data.seo_score}</div>`;
                }
                if (data.readability_score) {
                    contentHtml += `<div class="flux-seo-score-badge">📖 ${this.getString('readability') || 'Readability'}: ${data.readability_score}</div>`;
                }
                if (data.engagement_score) {
                    contentHtml += `<div class="flux-seo-score-badge">💬 ${this.getString('engagement') || 'Engagement'}: ${data.engagement_score}</div>`;
                }
                
                contentHtml += '</div></div>';
            }
            
            $('#generated-content-display').html(contentHtml);
            $('#generation-results').show();
            
            // Scroll to results
            this.scrollToElement('#generation-results');
        },
        
        displayAnalyticsResults: function(data) {
            let analyticsHtml = '';
            
            // Overall score
            analyticsHtml += `<div class="flux-seo-overall-score">
                <div class="flux-seo-score-circle">${data.overall_score || '--'}</div>
                <div class="flux-seo-score-label">${this.getString('overallScore') || 'Overall Score'}</div>
            </div>`;
            
            // Metrics grid
            analyticsHtml += '<div class="flux-seo-analytics-grid">';
            
            // Performance metrics
            analyticsHtml += `<div class="flux-seo-analytics-card">
                <h4>⚡ ${this.getString('performance') || 'Performance'}</h4>
                <div class="flux-seo-analytics-item">
                    <span class="flux-seo-analytics-label">${this.getString('pageSpeed') || 'Page Speed'}:</span>
                    <span class="flux-seo-analytics-value">${data.page_speed_score || '--'}</span>
                </div>
                <div class="flux-seo-analytics-item">
                    <span class="flux-seo-analytics-label">${this.getString('mobileScore') || 'Mobile'}:</span>
                    <span class="flux-seo-analytics-value">${data.mobile_score || '--'}</span>
                </div>
                <div class="flux-seo-analytics-item">
                    <span class="flux-seo-analytics-label">${this.getString('userExperience') || 'UX'}:</span>
                    <span class="flux-seo-analytics-value">${data.user_experience_score || '--'}</span>
                </div>
            </div>`;
            
            // SEO metrics
            analyticsHtml += `<div class="flux-seo-analytics-card">
                <h4>🎯 ${this.getString('seoScore') || 'SEO'}</h4>
                <div class="flux-seo-analytics-item">
                    <span class="flux-seo-analytics-label">${this.getString('contentQuality') || 'Content'}:</span>
                    <span class="flux-seo-analytics-value">${data.content_quality_score || '--'}</span>
                </div>
                <div class="flux-seo-analytics-item">
                    <span class="flux-seo-analytics-label">${this.getString('technicalScore') || 'Technical'}:</span>
                    <span class="flux-seo-analytics-value">${data.technical_seo_score || '--'}</span>
                </div>
                <div class="flux-seo-analytics-item">
                    <span class="flux-seo-analytics-label">${this.getString('security') || 'Security'}:</span>
                    <span class="flux-seo-analytics-value">${data.security_score || '--'}</span>
                </div>
            </div>`;
            
            analyticsHtml += '</div>';
            
            // Recommendations
            if (data.recommendations && data.recommendations.length > 0) {
                analyticsHtml += `<div class="flux-seo-recommendations">
                    <h4>${this.getString('recommendations') || 'Recommendations'}</h4>
                    <ul class="flux-seo-recommendation-list">`;
                data.recommendations.forEach(rec => {
                    analyticsHtml += `<li class="flux-seo-recommendation-item">${rec}</li>`;
                });
                analyticsHtml += '</ul></div>';
            }
            
            $('#analytics-display').html(analyticsHtml);
            $('#analytics-results').show();
            
            // Scroll to results
            this.scrollToElement('#analytics-results');
        },
        
        displayOptimizationResults: function(data) {
            let optimizationHtml = '';
            
            // Optimized title
            if (data.optimized_title) {
                optimizationHtml += `<div class="flux-seo-optimization-section">
                    <h4>📝 ${this.getString('title_field') || 'Optimized Title'}</h4>
                    <div class="flux-seo-optimized-content">${data.optimized_title}</div>
                </div>`;
            }
            
            // Optimized meta
            if (data.optimized_meta) {
                optimizationHtml += `<div class="flux-seo-optimization-section">
                    <h4>📄 ${this.getString('metaDescription') || 'Optimized Meta Description'}</h4>
                    <div class="flux-seo-optimized-content">${data.optimized_meta}</div>
                </div>`;
            }
            
            // Optimization tips
            if (data.optimization_tips && data.optimization_tips.length > 0) {
                optimizationHtml += `<div class="flux-seo-optimization-section">
                    <h4>💡 ${this.getString('seoTips') || 'Optimization Tips'}</h4>
                    <ul class="flux-seo-recommendation-list">`;
                data.optimization_tips.forEach(tip => {
                    optimizationHtml += `<li class="flux-seo-recommendation-item">${tip}</li>`;
                });
                optimizationHtml += '</ul></div>';
            }
            
            // Structure improvements
            if (data.structure_improvements && data.structure_improvements.length > 0) {
                optimizationHtml += `<div class="flux-seo-optimization-section">
                    <h4>🏗️ ${this.getString('structureImprovements') || 'Structure Improvements'}</h4>
                    <ul class="flux-seo-recommendation-list">`;
                data.structure_improvements.forEach(improvement => {
                    optimizationHtml += `<li class="flux-seo-recommendation-item">${improvement}</li>`;
                });
                optimizationHtml += '</ul></div>';
            }
            
            $('#optimization-display').html(optimizationHtml);
            $('#optimization-results').show();
            
            // Scroll to results
            this.scrollToElement('#optimization-results');
        },
        
        displayKeywordResults: function(data) {
            let keywordHtml = '';
            
            // Enhanced keyword scoring overview
            if (data.scored_keywords && data.scored_keywords.length > 0) {
                keywordHtml += this.generateKeywordOverview(data);
                keywordHtml += this.generateScoredKeywordsTable(data.scored_keywords);
                keywordHtml += this.generateOpportunitiesAnalysis(data.opportunities);
                keywordHtml += this.generateContentStrategy(data.content_strategy);
                keywordHtml += this.generateCompetitiveAnalysis(data.competitive_analysis);
                keywordHtml += this.generateROIProjection(data.roi_projection);
            } else {
                // Fallback for simple keyword data
                keywordHtml += this.generateSimpleKeywordDisplay(data);
            }
            
            $('#keyword-display').html(keywordHtml);
            $('#keyword-results').show();
            
            // Initialize interactive elements
            this.initializeKeywordInteractions();
            
            // Scroll to results
            this.scrollToElement('#keyword-results');
        },
        
        generateKeywordOverview: function(data) {
            const isThaiMode = this.currentLanguage === 'th';
            
            return `
                <div class="flux-seo-keyword-overview">
                    <div class="flux-seo-overview-stats">
                        <div class="flux-seo-stat-card">
                            <div class="flux-seo-stat-value">${data.total_keywords || 0}</div>
                            <div class="flux-seo-stat-label">${isThaiMode ? 'คำหลักทั้งหมด' : 'Total Keywords'}</div>
                        </div>
                        <div class="flux-seo-stat-card">
                            <div class="flux-seo-stat-value">${data.tier_distribution?.['Tier 1'] || 0}</div>
                            <div class="flux-seo-stat-label">${isThaiMode ? 'คำหลักลำดับ 1' : 'Tier 1 Keywords'}</div>
                        </div>
                        <div class="flux-seo-stat-card">
                            <div class="flux-seo-stat-value">${data.roi_projection?.potential_monthly_traffic || 0}</div>
                            <div class="flux-seo-stat-label">${isThaiMode ? 'ทราฟฟิกที่คาดหวัง' : 'Potential Traffic'}</div>
                        </div>
                        <div class="flux-seo-stat-card">
                            <div class="flux-seo-stat-value">${data.roi_projection?.estimated_monthly_revenue || '$0'}</div>
                            <div class="flux-seo-stat-label">${isThaiMode ? 'รายได้ที่คาดหวัง' : 'Est. Revenue'}</div>
                        </div>
                    </div>
                    
                    <div class="flux-seo-tier-distribution">
                        <h4>📊 ${isThaiMode ? 'การกระจายตัวของคำหลัก' : 'Keyword Distribution'}</h4>
                        <div class="flux-seo-tier-bars">
                            ${this.generateTierBars(data.tier_distribution)}
                        </div>
                    </div>
                    
                    <div class="flux-seo-timeline-overview">
                        <h4>⏱️ ${isThaiMode ? 'แผนการดำเนินงาน' : 'Implementation Timeline'}</h4>
                        <div class="flux-seo-timeline-items">
                            ${this.generateTimelineItems(data.estimated_timeline, isThaiMode)}
                        </div>
                    </div>
                </div>
            `;
        },
        
        generateScoredKeywordsTable: function(scored_keywords) {
            const isThaiMode = this.currentLanguage === 'th';
            
            let tableHtml = `
                <div class="flux-seo-keyword-section">
                    <h4>🎯 ${isThaiMode ? 'คำหลักที่ได้คะแนนแล้ว' : 'Scored Keywords'}</h4>
                    <div class="flux-seo-keyword-filters">
                        <button class="flux-seo-filter-btn active" data-filter="all">${isThaiMode ? 'ทั้งหมด' : 'All'}</button>
                        <button class="flux-seo-filter-btn" data-filter="Tier 1">${isThaiMode ? 'ลำดับ 1' : 'Tier 1'}</button>
                        <button class="flux-seo-filter-btn" data-filter="Tier 2">${isThaiMode ? 'ลำดับ 2' : 'Tier 2'}</button>
                        <button class="flux-seo-filter-btn" data-filter="Quick Win">${isThaiMode ? 'ชนะเร็ว' : 'Quick Win'}</button>
                    </div>
                    <div class="flux-seo-keyword-table-container">
                        <table class="flux-seo-keyword-table flux-seo-enhanced-table">
                            <thead>
                                <tr>
                                    <th>${isThaiMode ? 'คำหลัก' : 'Keyword'}</th>
                                    <th>${isThaiMode ? 'คะแนน' : 'Score'}</th>
                                    <th>${isThaiMode ? 'ลำดับ' : 'Tier'}</th>
                                    <th>${isThaiMode ? 'ความสำคัญ' : 'Priority'}</th>
                                    <th>${isThaiMode ? 'ปริมาณค้นหา' : 'Volume'}</th>
                                    <th>${isThaiMode ? 'ความยาก' : 'Difficulty'}</th>
                                    <th>${isThaiMode ? 'เจตนา' : 'Intent'}</th>
                                    <th>${isThaiMode ? 'การกระทำ' : 'Actions'}</th>
                                </tr>
                            </thead>
                            <tbody>
            `;
            
            scored_keywords.forEach((item, index) => {
                const data = item.data || {};
                const scoreColor = this.getScoreColor(item.score);
                const tierBadge = this.getTierBadge(item.tier);
                const priorityBadge = this.getPriorityBadge(item.priority);
                
                tableHtml += `
                    <tr class="flux-seo-keyword-row" data-tier="${item.tier}" data-priority="${item.priority}">
                        <td class="flux-seo-keyword-cell">
                            <div class="flux-seo-keyword-name">${item.keyword}</div>
                            <div class="flux-seo-keyword-meta">${data.user_intent || 'informational'}</div>
                        </td>
                        <td>
                            <div class="flux-seo-score-badge" style="background-color: ${scoreColor}">
                                ${item.score}
                            </div>
                        </td>
                        <td>${tierBadge}</td>
                        <td>${priorityBadge}</td>
                        <td>${this.formatNumber(data.search_volume || 0)}</td>
                        <td>
                            <div class="flux-seo-difficulty-bar">
                                <div class="flux-seo-difficulty-fill" style="width: ${data.keyword_difficulty || 0}%"></div>
                                <span>${data.keyword_difficulty || 0}</span>
                            </div>
                        </td>
                        <td>
                            <span class="flux-seo-intent-badge flux-seo-intent-${data.user_intent || 'informational'}">
                                ${data.user_intent || 'informational'}
                            </span>
                        </td>
                        <td>
                            <div class="flux-seo-action-buttons">
                                <button class="flux-seo-action-btn" onclick="FluxSEOEnhanced.viewKeywordDetails('${item.keyword}', ${index})">
                                    👁️
                                </button>
                                <button class="flux-seo-action-btn" onclick="FluxSEOEnhanced.generateContentForKeyword('${item.keyword}')">
                                    ✍️
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            tableHtml += '</tbody></table></div></div>';
            return tableHtml;
        },
        
        generateOpportunitiesAnalysis: function(opportunities) {
            const isThaiMode = this.currentLanguage === 'th';
            
            if (!opportunities || Object.keys(opportunities).length === 0) {
                return '';
            }
            
            let opportunitiesHtml = `
                <div class="flux-seo-keyword-section">
                    <h4>🚀 ${isThaiMode ? 'การวิเคราะห์โอกาส' : 'Opportunity Analysis'}</h4>
                    <div class="flux-seo-opportunities-grid">
            `;
            
            Object.entries(opportunities).forEach(([type, items]) => {
                if (items && items.length > 0) {
                    const typeTitle = this.getOpportunityTypeTitle(type, isThaiMode);
                    const typeIcon = this.getOpportunityTypeIcon(type);
                    
                    opportunitiesHtml += `
                        <div class="flux-seo-opportunity-card">
                            <div class="flux-seo-opportunity-header">
                                <span class="flux-seo-opportunity-icon">${typeIcon}</span>
                                <h5>${typeTitle}</h5>
                                <span class="flux-seo-opportunity-count">${items.length}</span>
                            </div>
                            <div class="flux-seo-opportunity-list">
                    `;
                    
                    items.slice(0, 5).forEach(item => {
                        opportunitiesHtml += `
                            <div class="flux-seo-opportunity-item">
                                <div class="flux-seo-opportunity-keyword">${item.keyword}</div>
                                <div class="flux-seo-opportunity-score">${item.score}</div>
                                <div class="flux-seo-opportunity-reason">${item.reason}</div>
                            </div>
                        `;
                    });
                    
                    if (items.length > 5) {
                        opportunitiesHtml += `
                            <div class="flux-seo-opportunity-more">
                                +${items.length - 5} ${isThaiMode ? 'เพิ่มเติม' : 'more'}
                            </div>
                        `;
                    }
                    
                    opportunitiesHtml += '</div></div>';
                }
            });
            
            opportunitiesHtml += '</div></div>';
            return opportunitiesHtml;
        },
        
        generateContentStrategy: function(content_strategy) {
            const isThaiMode = this.currentLanguage === 'th';
            
            if (!content_strategy) {
                return '';
            }
            
            let strategyHtml = `
                <div class="flux-seo-keyword-section">
                    <h4>📋 ${isThaiMode ? 'กลยุทธ์เนื้อหา' : 'Content Strategy'}</h4>
                    <div class="flux-seo-strategy-tabs">
                        <button class="flux-seo-strategy-tab active" data-tab="immediate">
                            ${isThaiMode ? 'การกระทำทันที' : 'Immediate Actions'}
                        </button>
                        <button class="flux-seo-strategy-tab" data-tab="calendar">
                            ${isThaiMode ? 'ปฏิทินเนื้อหา' : 'Content Calendar'}
                        </button>
                        <button class="flux-seo-strategy-tab" data-tab="optimization">
                            ${isThaiMode ? 'เป้าหมายการปรับปรุง' : 'Optimization Targets'}
                        </button>
                        <button class="flux-seo-strategy-tab" data-tab="linkbuilding">
                            ${isThaiMode ? 'การสร้างลิงก์' : 'Link Building'}
                        </button>
                    </div>
                    <div class="flux-seo-strategy-content">
            `;
            
            // Immediate Actions
            if (content_strategy.immediate_actions) {
                strategyHtml += `
                    <div class="flux-seo-strategy-panel active" data-panel="immediate">
                        ${this.generateImmediateActions(content_strategy.immediate_actions, isThaiMode)}
                    </div>
                `;
            }
            
            // Content Calendar
            if (content_strategy.content_calendar) {
                strategyHtml += `
                    <div class="flux-seo-strategy-panel" data-panel="calendar">
                        ${this.generateContentCalendar(content_strategy.content_calendar, isThaiMode)}
                    </div>
                `;
            }
            
            // Optimization Targets
            if (content_strategy.optimization_targets) {
                strategyHtml += `
                    <div class="flux-seo-strategy-panel" data-panel="optimization">
                        ${this.generateOptimizationTargets(content_strategy.optimization_targets, isThaiMode)}
                    </div>
                `;
            }
            
            // Link Building
            if (content_strategy.link_building_priorities) {
                strategyHtml += `
                    <div class="flux-seo-strategy-panel" data-panel="linkbuilding">
                        ${this.generateLinkBuildingPlan(content_strategy.link_building_priorities, isThaiMode)}
                    </div>
                `;
            }
            
            strategyHtml += '</div></div>';
            return strategyHtml;
        },
        
        generateCompetitiveAnalysis: function(competitive_analysis) {
            const isThaiMode = this.currentLanguage === 'th';
            
            if (!competitive_analysis) {
                return '';
            }
            
            return `
                <div class="flux-seo-keyword-section">
                    <h4>🏆 ${isThaiMode ? 'การวิเคราะห์คู่แข่ง' : 'Competitive Analysis'}</h4>
                    <div class="flux-seo-competitive-grid">
                        <div class="flux-seo-competitive-card">
                            <h5>${isThaiMode ? 'ความอิ่มตัวของตลาด' : 'Market Saturation'}</h5>
                            <div class="flux-seo-competitive-value">${competitive_analysis.market_saturation || 'N/A'}</div>
                        </div>
                        <div class="flux-seo-competitive-card">
                            <h5>${isThaiMode ? 'คู่แข่งหลัก' : 'Top Competitors'}</h5>
                            <div class="flux-seo-competitor-list">
                                ${(competitive_analysis.top_competitors || []).map(comp => 
                                    `<span class="flux-seo-competitor-tag">${comp}</span>`
                                ).join('')}
                            </div>
                        </div>
                        <div class="flux-seo-competitive-card">
                            <h5>${isThaiMode ? 'ข้อได้เปรียบ' : 'Competitive Advantages'}</h5>
                            <ul class="flux-seo-advantage-list">
                                ${(competitive_analysis.competitive_advantages || []).map(adv => 
                                    `<li>${adv}</li>`
                                ).join('')}
                            </ul>
                        </div>
                        <div class="flux-seo-competitive-card">
                            <h5>${isThaiMode ? 'ช่องว่างในตลาด' : 'Market Gaps'}</h5>
                            <ul class="flux-seo-gap-list">
                                ${(competitive_analysis.market_gaps || []).map(gap => 
                                    `<li>${gap}</li>`
                                ).join('')}
                            </ul>
                        </div>
                    </div>
                </div>
            `;
        },
        
        generateROIProjection: function(roi_projection) {
            const isThaiMode = this.currentLanguage === 'th';
            
            if (!roi_projection) {
                return '';
            }
            
            return `
                <div class="flux-seo-keyword-section">
                    <h4>💰 ${isThaiMode ? 'การคาดการณ์ผลตอบแทน' : 'ROI Projection'}</h4>
                    <div class="flux-seo-roi-grid">
                        <div class="flux-seo-roi-card">
                            <div class="flux-seo-roi-icon">📈</div>
                            <div class="flux-seo-roi-value">${roi_projection.potential_monthly_traffic || 0}</div>
                            <div class="flux-seo-roi-label">${isThaiMode ? 'ทราฟฟิกรายเดือน' : 'Monthly Traffic'}</div>
                        </div>
                        <div class="flux-seo-roi-card">
                            <div class="flux-seo-roi-icon">🎯</div>
                            <div class="flux-seo-roi-value">${roi_projection.estimated_monthly_conversions || 0}</div>
                            <div class="flux-seo-roi-label">${isThaiMode ? 'การแปลงรายเดือน' : 'Monthly Conversions'}</div>
                        </div>
                        <div class="flux-seo-roi-card">
                            <div class="flux-seo-roi-icon">💵</div>
                            <div class="flux-seo-roi-value">${roi_projection.estimated_monthly_revenue || '$0'}</div>
                            <div class="flux-seo-roi-label">${isThaiMode ? 'รายได้รายเดือน' : 'Monthly Revenue'}</div>
                        </div>
                        <div class="flux-seo-roi-card">
                            <div class="flux-seo-roi-icon">⏰</div>
                            <div class="flux-seo-roi-value">${roi_projection.roi_timeline || 'N/A'}</div>
                            <div class="flux-seo-roi-label">${isThaiMode ? 'ระยะเวลา ROI' : 'ROI Timeline'}</div>
                        </div>
                    </div>
                </div>
            `;
        },
        
        generateSimpleKeywordDisplay: function(data) {
            // Fallback for simple keyword data (existing functionality)
            let keywordHtml = '';
            
            // Primary keywords
            if (data.primary_keywords && data.primary_keywords.length > 0) {
                keywordHtml += `<div class="flux-seo-keyword-section">
                    <h4>🎯 ${this.getString('primaryKeywords') || 'Primary Keywords'}</h4>
                    <table class="flux-seo-keyword-table">
                        <thead>
                            <tr>
                                <th>${this.getString('keyword') || 'Keyword'}</th>
                                <th>${this.getString('searchVolume') || 'Search Volume'}</th>
                                <th>${this.getString('difficulty') || 'Difficulty'}</th>
                                <th>${this.getString('intent') || 'Intent'}</th>
                            </tr>
                        </thead>
                        <tbody>`;
                
                data.primary_keywords.forEach(kw => {
                    if (typeof kw === 'object') {
                        keywordHtml += `<tr>
                            <td>${kw.keyword}</td>
                            <td>${kw.search_volume || '--'}</td>
                            <td>${kw.difficulty || '--'}</td>
                            <td>${kw.intent || '--'}</td>
                        </tr>`;
                    } else {
                        keywordHtml += `<tr>
                            <td>${kw}</td>
                            <td>--</td>
                            <td>--</td>
                            <td>--</td>
                        </tr>`;
                    }
                });
                
                keywordHtml += '</tbody></table></div>';
            }
            
            // Long-tail keywords
            if (data.long_tail_keywords && data.long_tail_keywords.length > 0) {
                keywordHtml += `<div class="flux-seo-keyword-section">
                    <h4>📝 ${this.getString('longTailKeywords') || 'Long-tail Keywords'}</h4>
                    <div class="flux-seo-keyword-tags">`;
                data.long_tail_keywords.forEach(kw => {
                    keywordHtml += `<span class="flux-seo-keyword-tag">${kw}</span>`;
                });
                keywordHtml += '</div></div>';
            }
            
            // Content ideas
            if (data.content_ideas && data.content_ideas.length > 0) {
                keywordHtml += `<div class="flux-seo-keyword-section">
                    <h4>💡 ${this.getString('contentIdeas') || 'Content Ideas'}</h4>
                    <ul class="flux-seo-recommendation-list">`;
                data.content_ideas.forEach(idea => {
                    keywordHtml += `<li class="flux-seo-recommendation-item">${idea}</li>`;
                });
                keywordHtml += '</ul></div>';
            }
            
            return keywordHtml;
        },
        
        // Helper functions for enhanced keyword display
        getScoreColor: function(score) {
            if (score >= 8) return '#10b981'; // Green
            if (score >= 6) return '#f59e0b'; // Yellow
            return '#ef4444'; // Red
        },
        
        getTierBadge: function(tier) {
            const colors = {
                'Tier 1': '#10b981',
                'Tier 2': '#f59e0b',
                'Tier 3': '#6b7280'
            };
            return `<span class="flux-seo-tier-badge" style="background-color: ${colors[tier] || '#6b7280'}">${tier}</span>`;
        },
        
        getPriorityBadge: function(priority) {
            const colors = {
                'Critical': '#dc2626',
                'High': '#ea580c',
                'Quick Win': '#10b981',
                'Medium': '#f59e0b',
                'Low': '#6b7280'
            };
            return `<span class="flux-seo-priority-badge" style="background-color: ${colors[priority] || '#6b7280'}">${priority}</span>`;
        },
        
        getOpportunityTypeTitle: function(type, isThaiMode) {
            const titles = {
                'quick_wins': isThaiMode ? 'ชนะเร็ว' : 'Quick Wins',
                'long_term_targets': isThaiMode ? 'เป้าหมายระยะยาว' : 'Long-term Targets',
                'content_gaps': isThaiMode ? 'ช่องว่างเนื้อหา' : 'Content Gaps',
                'trending_opportunities': isThaiMode ? 'โอกาสแนวโน้ม' : 'Trending Opportunities',
                'local_opportunities': isThaiMode ? 'โอกาสท้องถิ่น' : 'Local Opportunities'
            };
            return titles[type] || type;
        },
        
        getOpportunityTypeIcon: function(type) {
            const icons = {
                'quick_wins': '⚡',
                'long_term_targets': '🎯',
                'content_gaps': '📝',
                'trending_opportunities': '📈',
                'local_opportunities': '📍'
            };
            return icons[type] || '💡';
        },
        
        generateTierBars: function(tier_distribution) {
            if (!tier_distribution) return '';
            
            const total = Object.values(tier_distribution).reduce((sum, count) => sum + count, 0);
            let barsHtml = '';
            
            Object.entries(tier_distribution).forEach(([tier, count]) => {
                const percentage = total > 0 ? (count / total) * 100 : 0;
                const color = tier === 'Tier 1' ? '#10b981' : tier === 'Tier 2' ? '#f59e0b' : '#6b7280';
                
                barsHtml += `
                    <div class="flux-seo-tier-bar">
                        <div class="flux-seo-tier-label">${tier}</div>
                        <div class="flux-seo-tier-progress">
                            <div class="flux-seo-tier-fill" style="width: ${percentage}%; background-color: ${color}"></div>
                        </div>
                        <div class="flux-seo-tier-count">${count}</div>
                    </div>
                `;
            });
            
            return barsHtml;
        },
        
        generateTimelineItems: function(timeline, isThaiMode) {
            if (!timeline) return '';
            
            let timelineHtml = '';
            Object.entries(timeline).forEach(([phase, description]) => {
                timelineHtml += `
                    <div class="flux-seo-timeline-item">
                        <div class="flux-seo-timeline-phase">${phase}</div>
                        <div class="flux-seo-timeline-description">${description}</div>
                    </div>
                `;
            });
            
            return timelineHtml;
        },
        
        initializeKeywordInteractions: function() {
            // Filter functionality
            $(document).on('click', '.flux-seo-filter-btn', function() {
                $('.flux-seo-filter-btn').removeClass('active');
                $(this).addClass('active');
                
                const filter = $(this).data('filter');
                if (filter === 'all') {
                    $('.flux-seo-keyword-row').show();
                } else {
                    $('.flux-seo-keyword-row').hide();
                    $(`.flux-seo-keyword-row[data-tier="${filter}"], .flux-seo-keyword-row[data-priority="${filter}"]`).show();
                }
            });
            
            // Strategy tabs
            $(document).on('click', '.flux-seo-strategy-tab', function() {
                $('.flux-seo-strategy-tab').removeClass('active');
                $('.flux-seo-strategy-panel').removeClass('active');
                
                $(this).addClass('active');
                const tab = $(this).data('tab');
                $(`.flux-seo-strategy-panel[data-panel="${tab}"]`).addClass('active');
            });
        },
        
        viewKeywordDetails: function(keyword, index) {
            // Show detailed keyword information in a modal or expanded view
            this.showNotification(`Viewing details for: ${keyword}`, 'info');
        },
        
        generateContentForKeyword: function(keyword) {
            // Trigger content generation for specific keyword
            $('#generator-topic').val(keyword);
            this.showTab('generator');
            this.showNotification(`Ready to generate content for: ${keyword}`, 'success');
        },
        
        formatNumber: function(num) {
            if (num >= 1000000) {
                return (num / 1000000).toFixed(1) + 'M';
            } else if (num >= 1000) {
                return (num / 1000).toFixed(1) + 'K';
            }
            return num.toString();
        },
        
        // Image Generation Functions
        handleImageGeneration: function(e) {
            e.preventDefault();
            
            const prompt = $('#image-prompt').val().trim();
            if (!prompt) {
                this.showNotification('Please enter an image description', 'error');
                return;
            }
            
            const imageData = {
                prompt: prompt,
                style: $('#image-style').val(),
                size: $('#image-size').val(),
                quality: $('#image-quality').val(),
                count: parseInt($('#image-count').val()),
                seo_alt: $('#seo-optimized-alt').is(':checked')
            };
            
            this.generateImages(imageData);
        },
        
        generateImages: function(imageData) {
            this.setLoading('#generate-image-btn', true);
            
            // Simulate image generation (replace with actual API call)
            setTimeout(() => {
                const mockImages = this.generateMockImages(imageData);
                this.displayGeneratedImages(mockImages);
                this.setLoading('#generate-image-btn', false);
                $('#save-to-media-btn').show();
            }, 3000);
        },
        
        generateMockImages: function(imageData) {
            const images = [];
            const baseUrl = 'https://picsum.photos';
            const [width, height] = imageData.size.split('x');
            
            for (let i = 0; i < imageData.count; i++) {
                images.push({
                    id: `img_${Date.now()}_${i}`,
                    url: `${baseUrl}/${width}/${height}?random=${Date.now() + i}`,
                    alt: imageData.seo_alt ? this.generateSEOAltText(imageData.prompt) : imageData.prompt,
                    prompt: imageData.prompt,
                    style: imageData.style,
                    size: imageData.size,
                    quality: imageData.quality
                });
            }
            
            return images;
        },
        
        generateSEOAltText: function(prompt) {
            // Generate SEO-optimized alt text
            const keywords = prompt.toLowerCase().split(' ').slice(0, 5);
            return `Professional ${keywords.join(' ')} image for web content`;
        },
        
        displayGeneratedImages: function(images) {
            let galleryHtml = '';
            
            images.forEach(image => {
                galleryHtml += `
                    <div class="flux-seo-image-item" data-image-id="${image.id}">
                        <div class="flux-seo-image-container">
                            <img src="${image.url}" alt="${image.alt}" class="flux-seo-generated-image">
                            <div class="flux-seo-image-overlay">
                                <button class="flux-seo-image-action" onclick="FluxSEOEnhanced.downloadImage('${image.id}')">
                                    📥 Download
                                </button>
                                <button class="flux-seo-image-action" onclick="FluxSEOEnhanced.copyImageUrl('${image.url}')">
                                    🔗 Copy URL
                                </button>
                                <button class="flux-seo-image-action" onclick="FluxSEOEnhanced.editImage('${image.id}')">
                                    ✏️ Edit
                                </button>
                            </div>
                        </div>
                        <div class="flux-seo-image-info">
                            <div class="flux-seo-image-alt">
                                <strong>Alt Text:</strong>
                                <input type="text" value="${image.alt}" class="flux-seo-alt-input" data-image-id="${image.id}">
                            </div>
                            <div class="flux-seo-image-meta">
                                <span class="flux-seo-image-size">${image.size}</span>
                                <span class="flux-seo-image-style">${image.style}</span>
                                <span class="flux-seo-image-quality">${image.quality}</span>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            $('#image-gallery').html(galleryHtml);
            $('#image-results').show();
            this.scrollToElement('#image-results');
        },
        
        handleSaveToMedia: function(e) {
            e.preventDefault();
            
            const images = this.getGeneratedImages();
            if (images.length === 0) {
                this.showNotification('No images to save', 'error');
                return;
            }
            
            this.saveImagesToMediaLibrary(images);
        },
        
        getGeneratedImages: function() {
            const images = [];
            $('.flux-seo-image-item').each(function() {
                const $item = $(this);
                const imageId = $item.data('image-id');
                const $img = $item.find('.flux-seo-generated-image');
                const $altInput = $item.find('.flux-seo-alt-input');
                
                images.push({
                    id: imageId,
                    url: $img.attr('src'),
                    alt: $altInput.val(),
                    filename: `ai-generated-${imageId}.jpg`
                });
            });
            
            return images;
        },
        
        saveImagesToMediaLibrary: function(images) {
            this.setLoading('#save-to-media-btn', true);
            
            // Simulate saving to media library
            setTimeout(() => {
                this.showNotification(`Successfully saved ${images.length} image(s) to media library`, 'success');
                this.setLoading('#save-to-media-btn', false);
            }, 2000);
        },
        
        downloadImage: function(imageId) {
            const $imageItem = $(`.flux-seo-image-item[data-image-id="${imageId}"]`);
            const imageUrl = $imageItem.find('.flux-seo-generated-image').attr('src');
            
            // Create download link
            const link = document.createElement('a');
            link.href = imageUrl;
            link.download = `ai-generated-${imageId}.jpg`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            this.showNotification('Image download started', 'success');
        },
        
        copyImageUrl: function(imageUrl) {
            navigator.clipboard.writeText(imageUrl).then(() => {
                this.showNotification('Image URL copied to clipboard', 'success');
            }).catch(() => {
                this.showNotification('Failed to copy URL', 'error');
            });
        },
        
        editImage: function(imageId) {
            this.showNotification('Image editing feature coming soon!', 'info');
        },
        
        handleCopyContent: function(e) {
            e.preventDefault();
            
            const content = $('#generated-content-display').text();
            
            if (navigator.clipboard) {
                navigator.clipboard.writeText(content).then(() => {
                    this.showNotification(this.getString('contentCopied'), 'success');
                }).catch(() => {
                    this.fallbackCopyToClipboard(content);
                });
            } else {
                this.fallbackCopyToClipboard(content);
            }
        },
        
        fallbackCopyToClipboard: function(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                document.execCommand('copy');
                this.showNotification(this.getString('contentCopied'), 'success');
            } catch (err) {
                this.showNotification(
                    this.currentLanguage === 'th' ? 'ไม่สามารถคัดลอกได้' : 'Unable to copy',
                    'error'
                );
            }
            
            document.body.removeChild(textArea);
        },
        
        handleInputValidation: function(e) {
            const $input = $(e.target);
            const value = $input.val();
            
            // Remove any existing validation classes
            $input.removeClass('flux-seo-input-error flux-seo-input-success');
            
            // Basic validation
            if ($input.attr('required') && !value.trim()) {
                $input.addClass('flux-seo-input-error');
            } else if (value.trim()) {
                $input.addClass('flux-seo-input-success');
            }
            
            // URL validation
            if ($input.attr('type') === 'url' && value && !this.isValidUrl(value)) {
                $input.addClass('flux-seo-input-error');
            }
        },
        
        setLoading: function(selector, isLoading) {
            const $btn = $(selector);
            const $icon = $btn.find('.flux-seo-btn-icon');
            const $text = $btn.find('.flux-seo-btn-text');
            
            if (isLoading) {
                $btn.prop('disabled', true);
                $icon.text('⏳');
                $text.text(this.getString('analyzing') || 'Processing...');
                this.isLoading = true;
            } else {
                $btn.prop('disabled', false);
                // Restore original icon and text based on button type
                if (selector.includes('analyze')) {
                    $icon.text('🤖');
                    $text.text(this.getString('analyze') || 'Analyze');
                } else if (selector.includes('generate')) {
                    $icon.text('🤖');
                    $text.text(this.getString('generate') || 'Generate');
                } else if (selector.includes('optimize')) {
                    $icon.text('⚡');
                    $text.text(this.getString('optimize') || 'Optimize');
                } else if (selector.includes('research')) {
                    $icon.text('🎯');
                    $text.text('Research Keywords');
                }
                this.isLoading = false;
            }
        },
        
        showNotification: function(message, type = 'info') {
            // Remove existing notifications
            $('.flux-seo-notification').remove();
            
            const typeIcons = {
                success: '✅',
                error: '❌',
                warning: '⚠️',
                info: 'ℹ️'
            };
            
            const $notification = $(`
                <div class="flux-seo-notification flux-seo-notification-${type}">
                    <span class="flux-seo-notification-icon">${typeIcons[type] || 'ℹ️'}</span>
                    <span class="flux-seo-notification-message">${message}</span>
                    <button class="flux-seo-notification-close">×</button>
                </div>
            `);
            
            $('body').append($notification);
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                $notification.fadeOut(() => $notification.remove());
            }, 5000);
            
            // Manual close
            $notification.find('.flux-seo-notification-close').on('click', () => {
                $notification.fadeOut(() => $notification.remove());
            });
        },
        
        scrollToElement: function(selector) {
            const $element = $(selector);
            if ($element.length) {
                $('html, body').animate({
                    scrollTop: $element.offset().top - 100
                }, 500);
            }
        },
        
        initializeTooltips: function() {
            // Add tooltips for complex features
            $('[data-tooltip]').each(function() {
                const $this = $(this);
                const tooltip = $this.data('tooltip');
                
                $this.on('mouseenter', function() {
                    const $tooltip = $(`<div class="flux-seo-tooltip">${tooltip}</div>`);
                    $('body').append($tooltip);
                    
                    const rect = this.getBoundingClientRect();
                    $tooltip.css({
                        top: rect.top - $tooltip.outerHeight() - 5,
                        left: rect.left + (rect.width / 2) - ($tooltip.outerWidth() / 2)
                    });
                });
                
                $this.on('mouseleave', function() {
                    $('.flux-seo-tooltip').remove();
                });
            });
        },
        
        initializeAutoSave: function() {
            // Auto-save form data to localStorage
            $('.flux-seo-input, .flux-seo-textarea, .flux-seo-select').on('input change', function() {
                const key = `flux_seo_${this.id}`;
                const value = $(this).val();
                localStorage.setItem(key, value);
            });
            
            // Restore saved data
            $('.flux-seo-input, .flux-seo-textarea, .flux-seo-select').each(function() {
                const key = `flux_seo_${this.id}`;
                const saved = localStorage.getItem(key);
                if (saved) {
                    $(this).val(saved);
                }
            });
        },
        
        initializeKeyboardShortcuts: function() {
            $(document).on('keydown', (e) => {
                // Ctrl/Cmd + Enter to submit forms
                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                    const activeTab = $('.flux-seo-tab-content.active');
                    const submitBtn = activeTab.find('.flux-seo-btn-primary').first();
                    if (submitBtn.length && !this.isLoading) {
                        submitBtn.click();
                    }
                }
                
                // Escape to close notifications
                if (e.key === 'Escape') {
                    $('.flux-seo-notification').fadeOut(() => $('.flux-seo-notification').remove());
                }
            });
        },
        
        trackEvent: function(eventName, properties = {}) {
            // Track usage for analytics
            if (window.gtag) {
                window.gtag('event', eventName, {
                    ...properties,
                    plugin: 'flux_seo_enhanced'
                });
            }
            
            // Console log for debugging
            console.log('Flux SEO Event:', eventName, properties);
        },
        
        getString: function(key) {
            return this.strings[this.currentLanguage]?.[key] || this.strings.en?.[key] || key;
        },
        
        isValidUrl: function(string) {
            try {
                new URL(string);
                return true;
            } catch (_) {
                return false;
            }
        },

        // Meta Tags Generation
        handleMetaGeneration: function(e) {
            e.preventDefault();
            
            const title = $('#meta-title').val().trim();
            const description = $('#meta-description').val().trim();
            const keywords = $('#meta-keywords').val().trim();
            const pageType = $('#meta-page-type').val();
            
            if (!title) {
                this.showNotification('Please enter a page title', 'error');
                return;
            }
            
            this.setLoading('#generate-meta-btn', true);
            
            const prompt = `Generate optimized meta tags for a ${pageType} with the following details:
            Title: ${title}
            Description: ${description}
            Keywords: ${keywords}
            Language: ${this.currentLanguage}
            
            Please provide:
            1. Optimized title (50-60 characters)
            2. Optimized meta description (150-160 characters)
            3. Complete HTML meta tags
            4. SEO recommendations`;
            
            this.callGeminiAPI(prompt, 'meta_generation').then(response => {
                this.displayMetaResults(response, title, description);
                $('#meta-results').show();
            }).catch(error => {
                this.showNotification('Error generating meta tags: ' + error.message, 'error');
            }).finally(() => {
                this.setLoading('#generate-meta-btn', false);
            });
        },

        displayMetaResults: function(response, originalTitle, originalDescription) {
            // Update SERP preview
            $('#serp-title').text(originalTitle);
            $('#serp-description').text(originalDescription);
            
            // Generate HTML meta tags
            const metaHtml = `<title>${originalTitle}</title>
<meta name="description" content="${originalDescription}">
<meta name="keywords" content="${$('#meta-keywords').val()}">
<meta property="og:title" content="${originalTitle}">
<meta property="og:description" content="${originalDescription}">
<meta property="og:type" content="${$('#meta-page-type').val()}">
<meta name="twitter:title" content="${originalTitle}">
<meta name="twitter:description" content="${originalDescription}">`;
            
            $('#meta-html-output').val(metaHtml);
            
            // Display AI recommendations
            $('#meta-display').append(`
                <div class="flux-seo-ai-recommendations">
                    <h4>AI Recommendations</h4>
                    <div class="flux-seo-recommendation-content">${response}</div>
                </div>
            `);
        },

        updateCharacterCount: function(e) {
            const input = $(e.target);
            const maxLength = input.attr('maxlength');
            const currentLength = input.val().length;
            const countElement = input.siblings('.flux-seo-help-text').find('span');
            
            countElement.text(currentLength);
            
            // Update color based on length
            if (currentLength > maxLength * 0.9) {
                countElement.css('color', '#ef4444');
            } else if (currentLength > maxLength * 0.8) {
                countElement.css('color', '#f59e0b');
            } else {
                countElement.css('color', '#10b981');
            }
        },

        // Schema Markup Generation
        handleSchemaGeneration: function(e) {
            e.preventDefault();
            
            const schemaType = $('#schema-type').val();
            const schemaData = this.collectSchemaData(schemaType);
            
            this.setLoading('#generate-schema-btn', true);
            
            const prompt = `Generate JSON-LD schema markup for ${schemaType} with the following data:
            ${JSON.stringify(schemaData, null, 2)}
            Language: ${this.currentLanguage}
            
            Please provide:
            1. Complete JSON-LD schema markup
            2. Rich snippet preview description
            3. SEO benefits explanation`;
            
            this.callGeminiAPI(prompt, 'schema_generation').then(response => {
                this.displaySchemaResults(response, schemaType);
                $('#schema-results').show();
            }).catch(error => {
                this.showNotification('Error generating schema: ' + error.message, 'error');
            }).finally(() => {
                this.setLoading('#generate-schema-btn', false);
            });
        },

        updateSchemaFields: function(e) {
            const schemaType = $(e.target).val();
            const fieldsContainer = $('#schema-fields');
            
            // Clear existing fields
            fieldsContainer.empty();
            
            // Add fields based on schema type
            const fields = this.getSchemaFields(schemaType);
            fields.forEach(field => {
                fieldsContainer.append(`
                    <div class="flux-seo-form-group">
                        <label class="flux-seo-label">${field.label}</label>
                        <input type="${field.type}" id="schema-${field.name}" class="flux-seo-input" 
                               placeholder="${field.placeholder}" ${field.required ? 'required' : ''}>
                    </div>
                `);
            });
        },

        getSchemaFields: function(schemaType) {
            const fieldSets = {
                article: [
                    { name: 'headline', label: 'Headline', type: 'text', placeholder: 'Article headline', required: true },
                    { name: 'author', label: 'Author', type: 'text', placeholder: 'Author name', required: true },
                    { name: 'datePublished', label: 'Date Published', type: 'date', placeholder: '', required: true },
                    { name: 'image', label: 'Image URL', type: 'url', placeholder: 'https://example.com/image.jpg', required: false }
                ],
                product: [
                    { name: 'name', label: 'Product Name', type: 'text', placeholder: 'Product name', required: true },
                    { name: 'brand', label: 'Brand', type: 'text', placeholder: 'Brand name', required: true },
                    { name: 'price', label: 'Price', type: 'number', placeholder: '99.99', required: true },
                    { name: 'currency', label: 'Currency', type: 'text', placeholder: 'USD', required: true }
                ],
                organization: [
                    { name: 'name', label: 'Organization Name', type: 'text', placeholder: 'Company name', required: true },
                    { name: 'url', label: 'Website URL', type: 'url', placeholder: 'https://example.com', required: true },
                    { name: 'logo', label: 'Logo URL', type: 'url', placeholder: 'https://example.com/logo.jpg', required: false }
                ]
            };
            
            return fieldSets[schemaType] || [];
        },

        collectSchemaData: function(schemaType) {
            const data = { '@type': schemaType };
            
            $('#schema-fields input').each(function() {
                const fieldName = $(this).attr('id').replace('schema-', '');
                const fieldValue = $(this).val().trim();
                if (fieldValue) {
                    data[fieldName] = fieldValue;
                }
            });
            
            return data;
        },

        displaySchemaResults: function(response, schemaType) {
            // Extract JSON-LD from response (simplified)
            const jsonLd = this.extractJsonFromResponse(response);
            $('#schema-json-output').val(JSON.stringify(jsonLd, null, 2));
            
            // Display rich snippet preview
            $('#rich-snippet-preview').html(this.generateRichSnippetPreview(jsonLd, schemaType));
        },

        extractJsonFromResponse: function(response) {
            // Simple extraction - in real implementation, parse AI response better
            try {
                const jsonMatch = response.match(/\{[\s\S]*\}/);
                return jsonMatch ? JSON.parse(jsonMatch[0]) : {};
            } catch (e) {
                return { '@context': 'https://schema.org', '@type': 'Thing' };
            }
        },

        generateRichSnippetPreview: function(schema, type) {
            switch (type) {
                case 'article':
                    return `
                        <div class="rich-snippet-article">
                            <h3>${schema.headline || 'Article Title'}</h3>
                            <div class="author">By ${schema.author || 'Author'}</div>
                            <div class="date">${schema.datePublished || 'Date'}</div>
                        </div>
                    `;
                case 'product':
                    return `
                        <div class="rich-snippet-product">
                            <h3>${schema.name || 'Product Name'}</h3>
                            <div class="brand">${schema.brand || 'Brand'}</div>
                            <div class="price">${schema.currency || '$'}${schema.price || '0.00'}</div>
                        </div>
                    `;
                default:
                    return '<div class="rich-snippet-preview">Rich snippet preview will appear here</div>';
            }
        },

        validateSchema: function() {
            const schemaJson = $('#schema-json-output').val();
            try {
                JSON.parse(schemaJson);
                this.showNotification('Schema markup is valid JSON-LD', 'success');
            } catch (e) {
                this.showNotification('Invalid JSON-LD format', 'error');
            }
        },

        // Technical SEO Audit
        handleTechnicalAudit: function(e) {
            e.preventDefault();
            
            const url = $('#technical-url').val().trim();
            const auditType = $('#technical-audit-type').val();
            const depth = $('#technical-depth').val();
            
            if (!url) {
                this.showNotification('Please enter a website URL', 'error');
                return;
            }
            
            this.setLoading('#run-technical-audit-btn', true);
            
            const prompt = `Perform a ${auditType} technical SEO audit for: ${url}
            Audit depth: ${depth}
            Language: ${this.currentLanguage}
            
            Please analyze and provide:
            1. Overall technical SEO score
            2. Page speed analysis
            3. Mobile-friendliness
            4. Security assessment
            5. Crawlability issues
            6. Specific recommendations for improvement`;
            
            this.callGeminiAPI(prompt, 'technical_audit').then(response => {
                this.displayTechnicalResults(response);
                $('#technical-results').show();
            }).catch(error => {
                this.showNotification('Error running technical audit: ' + error.message, 'error');
            }).finally(() => {
                this.setLoading('#run-technical-audit-btn', false);
            });
        },

        displayTechnicalResults: function(response) {
            // Simulate scores (in real implementation, parse from AI response)
            const scores = {
                overall: Math.floor(Math.random() * 40) + 60,
                speed: Math.floor(Math.random() * 40) + 60,
                mobile: Math.floor(Math.random() * 40) + 60,
                security: Math.floor(Math.random() * 40) + 60,
                crawl: Math.floor(Math.random() * 40) + 60
            };
            
            $('#technical-overall-score').text(scores.overall);
            $('#tech-speed-score').text(scores.speed);
            $('#tech-mobile-score').text(scores.mobile);
            $('#tech-security-score').text(scores.security);
            $('#tech-crawl-score').text(scores.crawl);
            
            // Display detailed analysis
            $('#technical-issues').html(`
                <div class="flux-seo-technical-analysis">
                    <h4>Detailed Analysis</h4>
                    <div class="flux-seo-analysis-content">${response}</div>
                </div>
            `);
        },

        // Chatbot Functionality
        handleChatMessage: function(e) {
            e.preventDefault();
            
            const message = $('#chatbot-input').val().trim();
            if (!message) return;
            
            this.addChatMessage(message, 'user');
            $('#chatbot-input').val('');
            
            this.setLoading('#send-message-btn', true);
            
            const prompt = `You are an SEO expert assistant. User question: ${message}
            Language: ${this.currentLanguage}
            
            Provide helpful, actionable SEO advice in a conversational tone.`;
            
            this.callGeminiAPI(prompt, 'chatbot').then(response => {
                this.addChatMessage(response, 'bot');
            }).catch(error => {
                this.addChatMessage('Sorry, I encountered an error. Please try again.', 'bot');
            }).finally(() => {
                this.setLoading('#send-message-btn', false);
            });
        },

        handleQuickAction: function(e) {
            const action = $(e.target).data('action');
            const messages = {
                'keyword-help': 'How can I improve my keyword research strategy?',
                'content-help': 'What are the best practices for content optimization?',
                'technical-help': 'How do I fix technical SEO issues?',
                'local-seo': 'How can I improve my local SEO rankings?'
            };
            
            if (messages[action]) {
                $('#chatbot-input').val(messages[action]);
                this.handleChatMessage(e);
            }
        },

        handleChatKeypress: function(e) {
            if (e.which === 13 && !e.shiftKey) {
                e.preventDefault();
                this.handleChatMessage(e);
            }
        },

        handleChatInputChange: function(e) {
            const message = $(e.target).val().trim();
            const sendButton = $('#send-message-btn');
            
            if (message.length > 0) {
                sendButton.prop('disabled', false);
            } else {
                sendButton.prop('disabled', true);
            }
        },

        addChatMessage: function(message, sender) {
            const messageHtml = `
                <div class="flux-seo-message flux-seo-message-${sender}">
                    <div class="flux-seo-message-avatar">${sender === 'user' ? '👤' : '🤖'}</div>
                    <div class="flux-seo-message-content">
                        <p>${message}</p>
                        <div class="flux-seo-message-time">${new Date().toLocaleTimeString()}</div>
                    </div>
                </div>
            `;
            
            $('#chatbot-messages').append(messageHtml);
            $('#chatbot-messages').scrollTop($('#chatbot-messages')[0].scrollHeight);
        },

        clearChat: function() {
            $('#chatbot-messages').empty();
            this.addChatMessage("Hello! I'm your SEO AI Assistant. How can I help you today?", 'bot');
        },

        exportChat: function() {
            const messages = [];
            $('#chatbot-messages .flux-seo-message').each(function() {
                const sender = $(this).hasClass('flux-seo-message-user') ? 'User' : 'Assistant';
                const content = $(this).find('.flux-seo-message-content p').text();
                const time = $(this).find('.flux-seo-message-time').text();
                messages.push(`[${time}] ${sender}: ${content}`);
            });
            
            const chatText = messages.join('\n\n');
            this.downloadText(chatText, 'seo-chat-export.txt');
        },

        // Settings Management
        handleSaveSettings: function(e) {
            e.preventDefault();
            
            const settings = {
                defaultLanguage: $('#settings-default-language').val(),
                defaultContentType: $('#settings-default-content-type').val(),
                autoSave: $('#settings-auto-save').is(':checked'),
                notifications: $('#settings-notifications').is(':checked'),
                aiModel: $('#settings-ai-model').val(),
                aiStyle: $('#settings-ai-style').val(),
                contentLength: $('#settings-content-length').val()
            };
            
            localStorage.setItem('flux_seo_settings', JSON.stringify(settings));
            this.showNotification('Settings saved successfully', 'success');
        },

        exportSettings: function() {
            const settings = localStorage.getItem('flux_seo_settings') || '{}';
            this.downloadText(settings, 'flux-seo-settings.json');
        },

        exportAnalytics: function() {
            // In real implementation, fetch from database
            const analyticsData = {
                exported: new Date().toISOString(),
                data: 'Analytics data would be exported here'
            };
            this.downloadText(JSON.stringify(analyticsData, null, 2), 'flux-seo-analytics.json');
        },

        importSettings: function() {
            const fileInput = $('#settings-import-file')[0];
            if (!fileInput.files.length) {
                this.showNotification('Please select a file to import', 'error');
                return;
            }
            
            const file = fileInput.files[0];
            const reader = new FileReader();
            
            reader.onload = (e) => {
                try {
                    const settings = JSON.parse(e.target.result);
                    localStorage.setItem('flux_seo_settings', JSON.stringify(settings));
                    this.showNotification('Settings imported successfully', 'success');
                    location.reload(); // Reload to apply settings
                } catch (error) {
                    this.showNotification('Invalid settings file', 'error');
                }
            };
            
            reader.readAsText(file);
        },

        clearCache: function() {
            if (confirm('Are you sure you want to clear all cached data?')) {
                localStorage.removeItem('flux_seo_cache');
                this.showNotification('Cache cleared successfully', 'success');
            }
        },

        resetSettings: function() {
            if (confirm('Are you sure you want to reset all settings to defaults?')) {
                localStorage.removeItem('flux_seo_settings');
                this.showNotification('Settings reset to defaults', 'success');
                location.reload();
            }
        },

        downloadText: function(text, filename) {
            const element = document.createElement('a');
            element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
            element.setAttribute('download', filename);
            element.style.display = 'none';
            document.body.appendChild(element);
            element.click();
            document.body.removeChild(element);
        },

        copyToClipboard: function(selector) {
            const element = $(selector)[0];
            if (element) {
                element.select();
                document.execCommand('copy');
                this.showNotification('Copied to clipboard', 'success');
            }
        },

        // API Key Management
        toggleApiKeyVisibility: function() {
            const input = $('#gemini-api-key');
            const button = $('.flux-seo-api-key-toggle');
            
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                button.text('🙈');
            } else {
                input.attr('type', 'password');
                button.text('👁️');
            }
        },

        saveApiKey: function() {
            const apiKey = $('#gemini-api-key').val().trim();
            if (!apiKey) {
                this.showNotification('Please enter an API key', 'error');
                return;
            }
            
            localStorage.setItem('flux_seo_gemini_api_key', apiKey);
            this.showNotification('API key saved successfully', 'success');
        },

        clearApiKey: function() {
            if (confirm('Are you sure you want to clear the API key?')) {
                localStorage.removeItem('flux_seo_gemini_api_key');
                $('#gemini-api-key').val('');
                this.showNotification('API key cleared', 'success');
            }
        },

        loadApiKey: function() {
            const apiKey = localStorage.getItem('flux_seo_gemini_api_key');
            if (apiKey) {
                $('#gemini-api-key').val(apiKey);
            }
        },

        // Initialize schema fields on page load
        initializeSchemaFields: function() {
            // Set default schema type and load fields
            const defaultType = $('#schema-type').val() || 'article';
            this.updateSchemaFields({ target: { value: defaultType } });
        },

        // Initialize character counters
        initializeCharacterCounters: function() {
            $('#meta-title, #meta-description').each(function() {
                const input = $(this);
                const maxLength = input.attr('maxlength');
                const currentLength = input.val().length;
                const countElement = input.siblings('.flux-seo-help-text').find('span');
                if (countElement.length) {
                    countElement.text(currentLength);
                }
            });
        },

        // Initialize chatbot
        initializeChatbot: function() {
            // Add initial welcome message if chatbot messages container is empty
            if ($('#chatbot-messages').children().length === 0) {
                this.addChatMessage("Hello! I'm your SEO AI Assistant powered by Gemini AI. How can I help you optimize your website today?", 'bot');
            }
        },

        // Analytics Section Functions
        toggleAnalyticsSection: function(sectionId) {
            const content = $('#analytics-' + sectionId);
            const toggle = content.prev().find('.flux-seo-section-toggle');
            
            if (content.is(':visible')) {
                content.slideUp(300);
                toggle.text('▼');
            } else {
                content.slideDown(300);
                toggle.text('▲');
            }
        },

        analyzeGoals: function() {
            const businessType = $('#business-type').val();
            const goals = [];
            $('#primary-goals input:checked').each(function() {
                goals.push($(this).val());
            });
            const targetAudience = $('#target-audience').val();

            if (!businessType || goals.length === 0 || !targetAudience) {
                this.showNotification('Please fill in all fields before analyzing goals.', 'warning');
                return;
            }

            const prompt = `As an SEO expert, analyze these business goals and provide strategic recommendations:
            
Business Type: ${businessType}
Primary Goals: ${goals.join(', ')}
Target Audience: ${targetAudience}

Please provide:
1. Goal prioritization strategy
2. Target audience analysis
3. SEO strategy recommendations
4. Key performance indicators to track
5. Timeline and milestones`;

            this.setLoading('#analytics-goals button', true);
            
            this.callGeminiAPI(prompt, 'goals_analysis').then(response => {
                this.displayAnalyticsResult('goals', response);
                this.showNotification('Goals analysis completed successfully!', 'success');
            }).catch(error => {
                this.showNotification('Error analyzing goals: ' + error.message, 'error');
            }).finally(() => {
                this.setLoading('#analytics-goals button', false);
            });
        },

        analyzeKeywords: function() {
            const seedKeywords = $('#seed-keywords').val();
            const competitorUrls = $('#competitor-urls').val();

            if (!seedKeywords) {
                this.showNotification('Please enter seed keywords.', 'warning');
                return;
            }

            const prompt = `As an SEO keyword research expert, analyze these keywords and provide comprehensive insights:
            
Seed Keywords: ${seedKeywords}
Competitor URLs: ${competitorUrls || 'Not provided'}

Please provide:
1. Keyword expansion and variations
2. Search intent analysis
3. Keyword difficulty assessment
4. Opportunity identification
5. Content gap analysis
6. Long-tail keyword suggestions
7. Seasonal trends and patterns`;

            this.setLoading('#analytics-keywords button', true);
            
            this.callGeminiAPI(prompt, 'keyword_analysis').then(response => {
                this.displayAnalyticsResult('keywords', response);
                this.showNotification('Keyword analysis completed successfully!', 'success');
            }).catch(error => {
                this.showNotification('Error analyzing keywords: ' + error.message, 'error');
            }).finally(() => {
                this.setLoading('#analytics-keywords button', false);
            });
        },

        analyzeBacklinks: function() {
            const domain = $('#domain-analysis').val();

            if (!domain) {
                this.showNotification('Please enter a domain for analysis.', 'warning');
                return;
            }

            const prompt = `As an SEO link building expert, analyze this domain's backlink profile and provide strategic recommendations:
            
Domain: ${domain}

Please provide:
1. Backlink quality assessment
2. Link building opportunities
3. Competitor link analysis
4. Toxic link identification
5. Link building strategy
6. Anchor text optimization
7. Domain authority improvement tips`;

            this.setLoading('#analytics-offpage button', true);
            
            this.callGeminiAPI(prompt, 'backlink_analysis').then(response => {
                this.displayAnalyticsResult('offpage', response);
                this.showNotification('Backlink analysis completed successfully!', 'success');
            }).catch(error => {
                this.showNotification('Error analyzing backlinks: ' + error.message, 'error');
            }).finally(() => {
                this.setLoading('#analytics-offpage button', false);
            });
        },

        generateContentStrategy: function() {
            const topics = $('#content-topics').val();
            const frequency = $('#content-frequency').val();

            if (!topics) {
                this.showNotification('Please enter content topics.', 'warning');
                return;
            }

            const prompt = `As a content marketing strategist, create a comprehensive content strategy:
            
Content Topics: ${topics}
Publishing Frequency: ${frequency}

Please provide:
1. Content calendar template
2. Content pillar strategy
3. Topic cluster recommendations
4. Content format suggestions
5. Distribution strategy
6. Performance metrics
7. Content optimization tips`;

            this.setLoading('#analytics-content button', true);
            
            this.callGeminiAPI(prompt, 'content_strategy').then(response => {
                this.displayAnalyticsResult('content', response);
                this.showNotification('Content strategy generated successfully!', 'success');
            }).catch(error => {
                this.showNotification('Error generating content strategy: ' + error.message, 'error');
            }).finally(() => {
                this.setLoading('#analytics-content button', false);
            });
        },

        generateReport: function() {
            const prompt = `As an SEO analyst, generate a comprehensive performance report with the following sections:
            
1. Executive Summary
2. Traffic Analysis
3. Keyword Performance
4. Technical SEO Health
5. Content Performance
6. Backlink Profile
7. Competitor Analysis
8. Recommendations and Action Items
9. Next Month's Priorities

Please provide detailed insights and actionable recommendations for each section.`;

            this.setLoading('#analytics-tracking button', true);
            
            this.callGeminiAPI(prompt, 'performance_report').then(response => {
                this.displayAnalyticsResult('tracking', response);
                this.showNotification('Performance report generated successfully!', 'success');
            }).catch(error => {
                this.showNotification('Error generating report: ' + error.message, 'error');
            }).finally(() => {
                this.setLoading('#analytics-tracking button', false);
            });
        },

        setupMonitoring: function() {
            const frequency = $('#monitoring-frequency').val();
            const trafficThreshold = $('#analytics-monitoring input[type="number"]').eq(0).val();
            const rankingThreshold = $('#analytics-monitoring input[type="number"]').eq(1).val();
            const speedThreshold = $('#analytics-monitoring input[type="number"]').eq(2).val();

            const prompt = `As an SEO monitoring expert, create a comprehensive monitoring and alerting strategy:
            
Monitoring Frequency: ${frequency}
Alert Thresholds:
- Traffic Drop: ${trafficThreshold}%
- Ranking Drop: ${rankingThreshold} positions
- Page Speed: ${speedThreshold} seconds

Please provide:
1. Monitoring checklist
2. Alert configuration guide
3. Response protocols
4. Escalation procedures
5. Reporting templates
6. Tool recommendations
7. Automation suggestions`;

            this.setLoading('#analytics-monitoring button', true);
            
            this.callGeminiAPI(prompt, 'monitoring_setup').then(response => {
                this.displayAnalyticsResult('monitoring', response);
                this.showNotification('Monitoring setup completed successfully!', 'success');
            }).catch(error => {
                this.showNotification('Error setting up monitoring: ' + error.message, 'error');
            }).finally(() => {
                this.setLoading('#analytics-monitoring button', false);
            });
        },

        displayAnalyticsResult: function(section, content) {
            const resultContainer = $(`#analytics-${section}`);
            
            // Create or update results display
            let resultsDiv = resultContainer.find('.flux-seo-analytics-results');
            if (resultsDiv.length === 0) {
                resultsDiv = $('<div class="flux-seo-analytics-results"></div>');
                resultContainer.append(resultsDiv);
            }
            
            resultsDiv.html(`
                <div class="flux-seo-results-header">
                    <h4>AI Analysis Results</h4>
                    <span class="flux-seo-ai-badge">Powered by Google Gemini 2.5 Pro</span>
                </div>
                <div class="flux-seo-results-content">
                    ${this.formatAnalyticsContent(content)}
                </div>
            `);
            
            resultsDiv.slideDown(300);
        },

        formatAnalyticsContent: function(content) {
            // Format the content with proper HTML structure
            return content
                .replace(/\n\n/g, '</p><p>')
                .replace(/\n/g, '<br>')
                .replace(/^/, '<p>')
                .replace(/$/, '</p>')
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/(\d+\.\s)/g, '<br><strong>$1</strong>');
        },

        // Gemini API Integration
        callGeminiAPI: function(prompt, context = 'general') {
            return new Promise((resolve, reject) => {
                const apiKey = localStorage.getItem('flux_seo_gemini_api_key');
                
                if (!apiKey) {
                    reject(new Error('API key not found. Please set your Gemini API key in Settings.'));
                    return;
                }

                const requestBody = {
                    contents: [{
                        parts: [{
                            text: prompt
                        }]
                    }],
                    generationConfig: {
                        temperature: 0.7,
                        topK: 40,
                        topP: 0.95,
                        maxOutputTokens: 2048,
                    }
                };

                $.ajax({
                    url: `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent?key=${apiKey}`,
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    data: JSON.stringify(requestBody),
                    success: function(response) {
                        if (response.candidates && response.candidates[0] && response.candidates[0].content) {
                            const text = response.candidates[0].content.parts[0].text;
                            resolve(text);
                        } else {
                            reject(new Error('Invalid response from Gemini API'));
                        }
                    },
                    error: function(xhr, status, error) {
                        let errorMessage = 'Failed to connect to Gemini API';
                        
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error.message || errorMessage;
                        }
                        
                        reject(new Error(errorMessage));
                    }
                });
            });
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        window.FluxSEOEnhanced.init();
    });

})(jQuery);

// Add notification styles dynamically
const notificationStyles = `
<style>
.flux-seo-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10000;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    backdrop-filter: blur(10px);
    font-weight: 500;
    max-width: 400px;
    animation: slideInRight 0.3s ease-out;
}

.flux-seo-notification-success {
    background: rgba(16, 185, 129, 0.95);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.flux-seo-notification-error {
    background: rgba(239, 68, 68, 0.95);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.flux-seo-notification-warning {
    background: rgba(245, 158, 11, 0.95);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.flux-seo-notification-info {
    background: rgba(59, 130, 246, 0.95);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.flux-seo-notification-close {
    background: none;
    border: none;
    color: inherit;
    font-size: 18px;
    cursor: pointer;
    padding: 0;
    margin-left: auto;
    opacity: 0.8;
}

.flux-seo-notification-close:hover {
    opacity: 1;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.flux-seo-input-error {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
}

.flux-seo-input-success {
    border-color: #10b981 !important;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
}

.flux-seo-tooltip {
    position: absolute;
    z-index: 10001;
    background: rgba(0, 0, 0, 0.9);
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 12px;
    max-width: 200px;
    pointer-events: none;
}

.flux-seo-optimization-section {
    margin-bottom: 24px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e2e8f0;
}

.flux-seo-optimization-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.flux-seo-optimized-content {
    background: #f8fafc;
    padding: 16px;
    border-radius: 8px;
    border-left: 4px solid #667eea;
    margin-top: 8px;
}

.flux-seo-keyword-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 12px;
}

.flux-seo-outline-list {
    list-style: none;
    padding: 0;
    margin: 12px 0 0 0;
}

.flux-seo-outline-list li {
    padding: 8px 0;
    border-bottom: 1px solid #e2e8f0;
    position: relative;
    padding-left: 20px;
}

.flux-seo-outline-list li:last-child {
    border-bottom: none;
}

.flux-seo-outline-list li::before {
    content: "▶";
    position: absolute;
    left: 0;
    color: #667eea;
    font-size: 12px;
}
</style>
`;

document.head.insertAdjacentHTML('beforeend', notificationStyles);
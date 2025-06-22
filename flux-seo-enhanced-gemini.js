/**
 * Flux SEO Enhanced with Gemini AI - JavaScript
 * Enhanced version with Thai/English language switching and advanced analytics
 */

(function($) {
    'use strict';

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
            
            if (typeof FluxSEOAutoBlog !== 'undefined' && typeof FluxSEOAutoBlog.init === 'function') {
                FluxSEOAutoBlog.init(
                    this.showNotification.bind(this),
                    this.getString.bind(this),
                    this.escapeHtml.bind(this),
                    this.setLoading.bind(this)
                );
            }
            
            console.log('Flux SEO Enhanced with Gemini AI initialized');
        },
        
        detectLanguage: function() {
            const urlParams = new URLSearchParams(window.location.search);
            const urlLang = urlParams.get('lang');
            const storedLang = localStorage.getItem('flux_seo_language');
            const browserLang = navigator.language.startsWith('th') ? 'th' : 'en';
            return urlLang || storedLang || browserLang;
        },
        
        bindEvents: function() {
            $(document).on('change', '#flux-seo-language-select', this.handleLanguageChange.bind(this));
            $(document).on('click', '.flux-seo-nav-tab', this.handleTabChange.bind(this));
            $(document).on('click', '#analyze-btn', this.handleContentAnalysis.bind(this));
            $(document).on('click', '#generate-btn', this.handleContentGeneration.bind(this));
            $(document).on('click', '#analyze-website-btn', this.handleWebsiteAnalysis.bind(this));
            $(document).on('click', '#optimize-btn', this.handleContentOptimization.bind(this));
            $(document).on('click', '#research-keywords-btn', this.handleKeywordResearch.bind(this));
            $(document).on('click', '#generate-meta-btn', this.handleMetaGeneration.bind(this));
            $(document).on('input', '#meta-title, #meta-description, #meta-url, #og-image', this.updateMetaPreviews.bind(this));
            $(document).on('input', '#meta-title, #meta-description', this.updateCharacterCount.bind(this));
            $(document).on('click', '#copy-meta-tags', this.copyMetaTags.bind(this));
            $(document).on('click', '#download-meta-tags', this.downloadMetaTags.bind(this));
            $(document).on('click', '#suggest-meta-description-btn', this.handleSuggestMetaDescription.bind(this));
            $(document).on('click', '#generate-schema-btn', this.handleSchemaGeneration.bind(this));
            $(document).on('change', '#schema-type', this.updateSchemaFields.bind(this));
            $(document).on('click', '#copy-schema', this.copySchemaMarkup.bind(this));
            $(document).on('click', '#test-schema', this.testSchemaMarkup.bind(this));
            $(document).on('click', '#download-schema', this.downloadSchemaMarkup.bind(this));
            $(document).on('click', '#run-audit-btn', this.handleTechnicalAudit.bind(this));
            $(document).on('click', '#chatbot-send', this.handleChatMessage.bind(this));
            $(document).on('click', '.flux-seo-suggestion-btn', this.handleQuickAction.bind(this));
            $(document).on('keypress', '#chatbot-input', this.handleChatKeypress.bind(this));
            $(document).on('input', '#chatbot-input', this.handleChatInputChange.bind(this));
            $(document).on('click', '#clear-chat', this.clearChat.bind(this));
            $(document).on('click', '#export-chat', this.exportChat.bind(this));
            $(document).on('click', '#save-settings-btn', this.handleSaveSettings.bind(this));
            $(document).on('click', '#save-api-key', this.saveApiKey.bind(this));
            $(document).on('click', '#generate-image-btn', this.handleImageGeneration.bind(this));
            $(document).on('click', '#save-to-media-btn', this.handleSaveToMedia.bind(this));
            $(document).on('click', '#copy-content-btn', this.handleCopyContent.bind(this));
            $(document).on('input', '.flux-seo-input, .flux-seo-textarea', this.handleInputValidation.bind(this));
            // Event delegation for collapsible headers
            $(document).on('click', '.flux-seo-collapsible-header', function() {
                const $header = $(this);
                const $content = $header.next('.flux-seo-collapsible-content');
                const $arrow = $header.find('.flux-seo-collapsible-arrow');
                const isExpanded = $header.attr('aria-expanded') === 'true';
                $content.slideToggle(200);
                $header.attr('aria-expanded', !isExpanded);
                $arrow.text(!isExpanded ? '▲' : '▼');
            });
        },
        
        getScoreClass: function(score) {
            if (score === undefined || score === null || score === '--' || isNaN(parseFloat(score))) return 'score-unknown';
            const numericScore = parseFloat(score);
            if (numericScore >= 80) return 'score-good';
            if (numericScore >= 50) return 'score-ok';
            return 'score-bad';
        },

        initializeLanguage: function() {
            $('#flux-seo-language-select').val(this.currentLanguage);
            this.updateLanguage();
        },
        
        initializeUI: function() {
            this.showTab('analyzer');
            this.initializeTooltips();
            this.initializeAutoSave();
            this.initializeKeyboardShortcuts();
            this.initializeSchemaFields();
            this.initializeCharacterCounters();
            this.initializeChatbot();
            if ($('#meta-tab').hasClass('active') || $('.flux-seo-nav-tab[data-tab="meta"]').hasClass('active')) {
                this.updateMetaPreviews();
            }
             // Trigger initial character count update for any visible meta fields
            $('#meta-title, #meta-description').trigger('input');
        },
        
        handleLanguageChange: function(e) {
            this.switchLanguage($(e.target).val());
        },
        
        switchLanguage: function(language) {
            if (this.currentLanguage === language) return;
            this.currentLanguage = language;
            localStorage.setItem('flux_seo_language', language);
            this.updateLanguage();
            $('#flux-seo-enhanced-container').attr('data-language', language);
            $('html').attr('lang', language);
            this.showNotification(language === 'th' ? 'เปลี่ยนภาษาเป็นไทยแล้ว' : 'Language switched to English', 'success');
        },
        
        updateLanguage: function() {
            const strings = this.strings[this.currentLanguage] || this.strings.en || {};
            $('[data-key]').each(function() {
                const key = $(this).data('key'); const text = strings[key];
                if (text) { $(this).is('input, textarea') ? $(this).attr('placeholder', text) : $(this).text(text); }
            });
            $('#flux-seo-title').text(strings.title || 'Flux SEO Enhanced');
            $('#flux-seo-subtitle').text(strings.subtitle || 'Professional SEO with Gemini AI');
            this.updateFormPlaceholders();
        },
        
        updateFormPlaceholders: function() {
            const strings = this.strings[this.currentLanguage] || this.strings.en || {};
            const placeholders = {
                'analyzer-content': 'analyzerPlaceholder', 'generator-topic': 'generatorTopicPlaceholder',
                'analytics-url': 'analyticsUrlPlaceholder', 'optimizer-content': 'optimizerPlaceholder',
                'keyword-seeds': 'keywordSeedsPlaceholder',
            };
            for (const id in placeholders) {
                const key = placeholders[id];
                const placeholderText = strings[key] || (this.strings.en ? this.strings.en[key] : '');
                if(placeholderText) $(`#${id}`).attr('placeholder', placeholderText);
            }
        },
        
        handleTabChange: function(e) {
            e.preventDefault(); const tabId = $(e.currentTarget).data('tab');
            this.showTab(tabId);
            if (tabId === 'meta') {
                this.updateMetaPreviews();
                 $('#meta-title, #meta-description').trigger('input'); // Update char counts
            }
        },
        
        showTab: function(tabId) {
            $('.flux-seo-nav-tab').removeClass('active');
            $(`.flux-seo-nav-tab[data-tab="${tabId}"]`).addClass('active');
            $('.flux-seo-tab-content').removeClass('active');
            $(`#${tabId}-tab`).addClass('active');
            this.trackEvent('tab_change', { tab: tabId });
        },
        
        handleContentAnalysis: function(e) {
            e.preventDefault();
            const content = $('#analyzer-content').val().trim();
            const keywords = $('#analyzer-keywords').val().trim();
            const audience = $('#analyzer-audience').val();
            if (!content) { this.showNotification(this.getString('enterContent', 'Please enter content to analyze.'), 'error'); return; }
            this.setLoading('#analyze-btn', true, this.getString('analyzing', 'Analyzing...'));
            const data = {
                action: 'flux_seo_enhanced_action', action_type: 'analyze_content',
                content, keywords, audience, language: this.currentLanguage, nonce: window.fluxSeoEnhanced?.nonce
            };
            $.post(window.fluxSeoEnhanced?.ajaxurl, data)
                .done(response => {
                    if (response.success && response.data && !response.data.error) {
                        this.displayAnalysisResults(response.data);
                        this.trackEvent('content_analysis', { language: this.currentLanguage });
                    } else {
                        let msg = (response.data && response.data.message) || this.getString('errorAnalyzingContent', 'Error analyzing content.');
                        if(response.data && response.data.raw_response) msg += ` Raw: ${this.escapeHtml(response.data.raw_response.substring(0,100))}...`;
                        this.showNotification(msg, 'error');
                    }
                })
                .fail((xhr, status, err) => this.showNotification(`${this.getString('ajaxError', 'AJAX Error')}: ${err}`, 'error'))
                .always(() => this.setLoading('#analyze-btn', false, this.getString('analyze', 'Analyze with Gemini AI')));
        },

        displayAnalysisResults: function(data) {
            const resultsContainer = $('#analysis-results');
            const geminiInsightsContainer = resultsContainer.find('#gemini-insights');
            const geminiAnalysisContent = geminiInsightsContainer.find('#gemini-analysis-content');
            geminiAnalysisContent.empty();

            resultsContainer.find('#seo-score').text(data.overall_seo_score || '--').removeClass('score-good score-ok score-bad score-unknown').addClass(this.getScoreClass(data.overall_seo_score));
            resultsContainer.find('#content-quality-score').text(data.content_quality_score || '--').removeClass('score-good score-ok score-bad score-unknown').addClass(this.getScoreClass(data.content_quality_score));

            let readabilityText = data.readability_score || '--';
            let readabilityNumericScore = parseFloat(data.readability_score);

            if (typeof data.readability_score === 'object' && data.readability_score.score !== undefined) {
                readabilityNumericScore = parseFloat(data.readability_score.score);
                readabilityText = data.readability_score.score;
                if(data.readability_score.grade_level) {
                    readabilityText += ` (${this.escapeHtml(data.readability_score.grade_level)})`;
                }
            } else if (typeof data.readability_score === 'string') {
                 const match = String(data.readability_score).match(/(\d+)(\s*\(.*\))?/);
                if (match && match[1]) {
                    readabilityNumericScore = parseFloat(match[1]);
                } else {
                    readabilityNumericScore = NaN;
                }
                readabilityText = this.escapeHtml(data.readability_score);
            } else if (typeof data.readability_score === 'number') {
                readabilityText = data.readability_score.toString();
            }
            resultsContainer.find('#readability-score').html(readabilityText).removeClass('score-good score-ok score-bad score-unknown').addClass(this.getScoreClass(readabilityNumericScore));
            resultsContainer.find('#engagement-score').text(data.engagement_potential_score || '--').removeClass('score-good score-ok score-bad score-unknown').addClass(this.getScoreClass(data.engagement_potential_score));

            let detailsHtml = '<div class="flux-seo-analysis-details">';

            const createCollapsibleSection = (id, titleKey, defaultTitle, content, icon = 'ℹ️', isOpen = false) => {
                let hasMeaningfulContent = false;
                let sectionContentHtml = '';

                if (typeof content === 'string' && content.trim() !== '') {
                    sectionContentHtml = `<p>${this.escapeHtml(content)}</p>`;
                    hasMeaningfulContent = true;
                } else if (Array.isArray(content) && content.length > 0) {
                    let listItems = '';
                    content.forEach(item => {
                         if (typeof item === 'object' && item.action && item.priority) {
                            listItems += `<li><strong>[${this.escapeHtml(item.priority)}]</strong> ${this.escapeHtml(item.action)}</li>`;
                        } else if (item && item.toString) {
                            listItems += `<li>${this.escapeHtml(item.toString())}</li>`;
                        }
                    });
                    if (listItems) {
                        sectionContentHtml = `<ul>${listItems}</ul>`;
                        hasMeaningfulContent = true;
                    }
                } else if (typeof content === 'object' && content !== null && Object.keys(content).length > 0) {
                    let objectListItems = '';
                    for (const key in content) {
                        if (Object.prototype.hasOwnProperty.call(content, key) && content[key] !== undefined && content[key] !== null) {
                            let subValue = content[key];
                            if(Array.isArray(subValue)) {
                                if (subValue.length > 0) {
                                    subValue = `<ul>${subValue.map(sv => sv && sv.toString ? `<li>${this.escapeHtml(sv.toString())}</li>` : '').join('')}</ul>`;
                                } else {
                                    subValue = 'N/A';
                                }
                            } else if (subValue && subValue.toString) {
                                subValue = this.escapeHtml(subValue.toString());
                            } else {
                                subValue = 'N/A';
                            }
                            objectListItems += `<li><strong>${this.escapeHtml(key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()))}:</strong> ${subValue}</li>`;
                        }
                    }
                    if (objectListItems) {
                        sectionContentHtml = `<ul>${objectListItems}</ul>`;
                        hasMeaningfulContent = true;
                    }
                }

                if (!hasMeaningfulContent && !(titleKey === 'actionableRecommendations' && data.actionable_recommendations && data.actionable_recommendations.length > 0)) return '';

                const uniqueId = `collapsible-${id}-${Math.random().toString(36).substring(2, 9)}`;
                return `
                    <div class="flux-seo-collapsible-section">
                        <button class="flux-seo-collapsible-header" aria-expanded="${isOpen}" aria-controls="${uniqueId}">
                            <span class="flux-seo-collapsible-icon">${icon}</span>
                            ${this.getString(titleKey, defaultTitle)}
                            <span class="flux-seo-collapsible-arrow">${isOpen ? '▲' : '▼'}</span>
                        </button>
                        <div id="${uniqueId}" class="flux-seo-collapsible-content" ${isOpen ? '' : 'style="display: none;"'}>
                            ${sectionContentHtml || `<p>${this.getString('noDetailsAvailable', 'No details available for this section.')}</p>`}
                        </div>
                    </div>`;
            };

            detailsHtml += createCollapsibleSection('summary', 'summaryOfFindings', 'Summary of Findings', data.summary_of_findings, '📝', true);
            detailsHtml += createCollapsibleSection('positive', 'positiveAspects', 'Positive Aspects', data.positive_aspects, '✅');
            detailsHtml += createCollapsibleSection('keywords', 'keywordAnalysis', 'Keyword Analysis', data.keyword_analysis, '🎯');
            detailsHtml += createCollapsibleSection('clarity', 'clarityConciseness', 'Clarity & Conciseness', data.clarity_and_conciseness, '✨');
            detailsHtml += createCollapsibleSection('tone', 'toneAnalysis', 'Tone Analysis', data.tone_analysis, '🎭');
            detailsHtml += createCollapsibleSection('structure', 'structuralAnalysis', 'Structural Analysis', data.structural_analysis, '🏗️');

            if (data.actionable_recommendations && data.actionable_recommendations.length > 0) {
                let recommendationsHtml = `<div class="flux-seo-analysis-section"><h4>${this.getString('actionableRecommendations', 'Actionable Recommendations')}</h4>`;
                const groupedRecommendations = data.actionable_recommendations.reduce((acc, rec) => {
                    const category = rec.category || 'General';
                    if (!acc[category]) acc[category] = [];
                    acc[category].push(rec.recommendation);
                    return acc;
                }, {});

                for (const category in groupedRecommendations) {
                     if (groupedRecommendations[category] && groupedRecommendations[category].length > 0) {
                        recommendationsHtml += createCollapsibleSection(
                            `recs-${category.toLowerCase().replace(/\s+/g, '-')}`,
                            '',
                            category,
                            groupedRecommendations[category],
                            '💡',
                            true
                        );
                    }
                }
                recommendationsHtml += '</div>';
                detailsHtml += recommendationsHtml;
            }

            detailsHtml += '</div>';
            geminiAnalysisContent.html(detailsHtml);
            resultsContainer.show();
            geminiInsightsContainer.show();
            this.scrollToElement('#analysis-results');
        },
        
        handleContentGeneration: function(e) {
            e.preventDefault();
            const topic = $('#generator-topic').val().trim();
            const contentType = $('#generator-type').val();
            const tone = $('#generator-tone').val();
            const keywords = $('#generator-keywords').val().trim();
            const audience = $('#generator-audience').length ? $('#generator-audience').val() : 'general';
            const wordCount = $('#generator-wordcount').length ? $('#generator-wordcount').val() : 1000;
            const additionalInstructions = $('#generator-instructions').val() ? $('#generator-instructions').val().trim() : '';


            if (!topic) {
                this.showNotification(this.getString('enterTopic', 'Please enter a topic.'), 'error');
                return;
            }
            this.setLoading('#generate-btn', true, this.getString('generating', 'Generating...'));
            const data = {
                action: 'flux_seo_enhanced_action', action_type: 'generate_content',
                topic, content_type: contentType, tone, audience, word_count: wordCount, keywords,
                additional_instructions: additionalInstructions,
                language: this.currentLanguage, nonce: window.fluxSeoEnhanced?.nonce
            };
            $.post(window.fluxSeoEnhanced?.ajaxurl, data)
                .done(response => {
                     if (response.success && response.data && !response.data.error) {
                        this.displayGenerationResults(response.data);
                        this.trackEvent('content_generation', { language: this.currentLanguage, content_type: contentType });
                    } else {
                        let msg = (response.data && response.data.message) || this.getString('errorGeneratingContent', 'Error generating content.');
                         if(response.data && response.data.raw_response) msg += ` Raw: ${this.escapeHtml(response.data.raw_response.substring(0,100))}...`;
                        this.showNotification(msg, 'error');
                    }
                })
                .fail((xhr, status, err) => this.showNotification(`${this.getString('ajaxError', 'AJAX Error')}: ${err}`, 'error'))
                .always(() => this.setLoading('#generate-btn', false, this.getString('generate', 'Generate with AI')));
        },

        displayGenerationResults: function(data) {
            const displayContainer = $('#generated-content-display');
            displayContainer.empty();

            let contentHtml = '<div class="flux-seo-generated-article">';

            if (data.title) {
                contentHtml += `<h1 class="flux-seo-generated-title">${this.escapeHtml(data.title)}</h1>`;
            }
            if (data.meta_description) {
                contentHtml += `<div class="flux-seo-generated-meta"><strong>${this.getString('metaDescription') || 'Meta Description'}:</strong> ${this.escapeHtml(data.meta_description)}</div>`;
            }
             if (data.suggested_slug) {
                contentHtml += `<div class="flux-seo-generated-slug"><strong>${this.getString('suggestedSlug') || 'Suggested Slug'}:</strong> <code>${this.escapeHtml(data.suggested_slug)}</code></div>`;
            }

            let scoresHtml = '<div class="flux-seo-generated-scores">';
            let hasScores = false;
            if (data.seo_score_estimation) {
                scoresHtml += `<span class="flux-seo-score-badge">📈 ${this.getString('seoScore') || 'SEO'}: ${this.escapeHtml(data.seo_score_estimation.toString())}</span>`;
                hasScores = true;
            }
            if (data.readability_score_estimation) {
                scoresHtml += `<span class="flux-seo-score-badge">📖 ${this.getString('readability') || 'Readability'}: ${this.escapeHtml(data.readability_score_estimation.toString())}</span>`;
                hasScores = true;
            }
             if (data.engagement_score) {
                scoresHtml += `<span class="flux-seo-score-badge">💬 ${this.getString('engagement') || 'Engagement'}: ${this.escapeHtml(data.engagement_score.toString())}</span>`;
                hasScores = true;
            }
            scoresHtml += '</div>';
            if(hasScores) contentHtml += scoresHtml;

            if (data.outline && Array.isArray(data.outline) && data.outline.length > 0) {
                contentHtml += `<div class="flux-seo-generated-outline"><h4>${this.getString('contentOutline') || 'Outline'}</h4><ul>`;
                data.outline.forEach(item => {
                    contentHtml += `<li>${this.escapeHtml(item)}</li>`;
                });
                contentHtml += '</ul></div>';
            }

            if (data.content_html) {
                contentHtml += `<div class="flux-seo-generated-main-content"><h4>${this.getString('mainContent') || 'Main Content'}</h4>${data.content_html}</div>`;
            } else if (data.content) {
                 contentHtml += `<div class="flux-seo-generated-main-content"><h4>${this.getString('mainContent') || 'Main Content'}</h4><div>${data.content}</div></div>`; // Assuming data.content is HTML or pre-formatted
            }

            if (data.keywords_identified && Array.isArray(data.keywords_identified) && data.keywords_identified.length > 0) {
                contentHtml += `<div class="flux-seo-generated-keywords"><h4>${this.getString('keywordsUsed') || 'Keywords Used'}</h4><p>`;
                data.keywords_identified.forEach(keyword => {
                    contentHtml += `<span class="flux-seo-keyword-tag">${this.escapeHtml(keyword)}</span> `;
                });
                contentHtml += '</p></div>';
            }
            
            if (data.warnings && Array.isArray(data.warnings) && data.warnings.length > 0) {
                contentHtml += `<div class="flux-seo-generated-warnings"><h4>⚠️ ${this.getString('warnings') || 'Warnings/Suggestions'}</h4><ul>`;
                data.warnings.forEach(warning => {
                    contentHtml += `<li class="flux-seo-warning-item">${this.escapeHtml(warning)}</li>`;
                });
                contentHtml += '</ul></div>';
            }

            if (data.seo_recommendations && Array.isArray(data.seo_recommendations) && data.seo_recommendations.length > 0) {
                contentHtml += `<div class="flux-seo-generated-recommendations"><h4>💡 ${this.getString('seoRecommendations') || 'SEO Recommendations'}</h4><ul>`;
                data.seo_recommendations.forEach(rec => {
                    contentHtml += `<li class="flux-seo-recommendation-item">${this.escapeHtml(rec)}</li>`;
                });
                contentHtml += '</ul></div>';
            }

            contentHtml += '</div>';
            
            displayContainer.html(contentHtml);
            $('#generation-results').show();
            this.scrollToElement('#generation-results');
        },
        
        displayAnalyticsResults: function(data) {
            const displayContainer = $('#analytics-display');
            displayContainer.empty();
            $('#analytics-results').show();

            let analyticsHtml = '<div class="flux-seo-website-analysis-report">';

            if (data.executive_summary) {
                analyticsHtml += `<div class="flux-seo-analysis-section">
                                    <h3>${this.getString('executiveSummary') || 'Executive Summary'} 📝</h3>
                                    <p>${this.escapeHtml(data.executive_summary)}</p>
                                 </div>`;
            }

            if (data.overall_site_health && data.overall_site_health.score !== undefined) {
                 analyticsHtml += `<div class="flux-seo-analysis-section">
                                    <h3>${this.getString('overallSiteHealth') || 'Overall Site Health'} ❤️</h3>
                                    <div class="flux-seo-score-display">
                                        <span class="flux-seo-score-value">${data.overall_site_health.score}</span>/100
                                    </div>
                                    ${data.overall_site_health.recommendations ? this.formatRecommendationsList(data.overall_site_health.recommendations) : ''}
                                 </div>`;
            }

            const createSectionHtml = (titleKey, defaultTitle, sectionData, icon = '📊') => {
                if (!sectionData) return '';
                let sectionHtml = `<div class="flux-seo-analysis-section">
                                     <h4>${icon} ${this.getString(titleKey) || defaultTitle}
                                         ${sectionData.score !== undefined ? `(<span class="flux-seo-score-value">${sectionData.score}</span>/100)` : ''}
                                     </h4>`;
                if (sectionData.analysis) {
                    sectionHtml += `<p>${this.escapeHtml(sectionData.analysis)}</p>`;
                }
                sectionHtml += this.formatRecommendationsList(sectionData.recommendations);
                if (titleKey === 'pageSpeedInsights' && sectionData.core_web_vitals) {
                    sectionHtml += '<p><strong>Core Web Vitals:</strong> ';
                    sectionHtml += Object.entries(sectionData.core_web_vitals).map(([key, value]) => `${key.toUpperCase()}: ${this.escapeHtml(value)}`).join(', ');
                    sectionHtml += '</p>';
                }
                sectionHtml += `</div>`;
                return sectionHtml;
            };
            
            analyticsHtml += createSectionHtml('onPageSeo', 'On-Page SEO', data.on_page_seo, '📄');
            analyticsHtml += createSectionHtml('technicalSeo', 'Technical SEO', data.technical_seo, '⚙️');
            analyticsHtml += createSectionHtml('userExperience', 'User Experience (UX)', data.user_experience_ux, '😊');
            analyticsHtml += createSectionHtml('contentStrategyAnalysis', 'Content Strategy', data.content_strategy_analysis, '📝');
            analyticsHtml += createSectionHtml('mobileFriendliness', 'Mobile Friendliness', data.mobile_friendliness, '📱');
            analyticsHtml += createSectionHtml('pageSpeedInsights', 'Page Speed Insights', data.page_speed_insights, '⚡');
            analyticsHtml += createSectionHtml('securityAnalysis', 'Security', data.security_analysis, '🔒');
            analyticsHtml += createSectionHtml('accessibilityWcag', 'Accessibility (WCAG)', data.accessibility_wcag, '♿');

            if (data.top_positive_points && data.top_positive_points.length > 0) {
                analyticsHtml += `<div class="flux-seo-analysis-section">
                                    <h4>${this.getString('topPositivePoints') || 'Key Strengths'} 👍</h4>
                                    ${this.formatRecommendationsList(data.top_positive_points)}
                                 </div>`;
            }

            if (data.top_areas_for_improvement && data.top_areas_for_improvement.length > 0) {
                analyticsHtml += `<div class="flux-seo-analysis-section">
                                    <h4>${this.getString('topAreasForImprovement') || 'Top Areas for Improvement'} 🛠️</h4>
                                    ${this.formatRecommendationsList(data.top_areas_for_improvement)}
                                 </div>`;
            }

            if (data.prioritized_action_plan && data.prioritized_action_plan.length > 0) {
                analyticsHtml += `<div class="flux-seo-analysis-section">
                                    <h4>${this.getString('prioritizedActionPlan') || 'Prioritized Action Plan'} 🚀</h4><ul>`;
                data.prioritized_action_plan.forEach(item => {
                    analyticsHtml += `<li><strong>[${this.escapeHtml(item.priority)}]</strong> ${this.escapeHtml(item.action)}</li>`;
                });
                analyticsHtml += '</ul></div>';
            }

            if (data.conceptual_competitor_comparison) {
                analyticsHtml += `<div class="flux-seo-analysis-section">
                                    <h4>${this.getString('competitorComparison') || 'Competitor Comparison'} 🆚</h4>`;
                if (data.conceptual_competitor_comparison.strength_comparison) {
                    analyticsHtml += `<p><strong>Strengths/Weaknesses:</strong> ${this.escapeHtml(data.conceptual_competitor_comparison.strength_comparison)}</p>`;
                }
                if (data.conceptual_competitor_comparison.opportunity_areas && data.conceptual_competitor_comparison.opportunity_areas.length > 0) {
                    analyticsHtml += `<p><strong>Opportunities:</strong></p>${this.formatRecommendationsList(data.conceptual_competitor_comparison.opportunity_areas)}`;
                }
                analyticsHtml += `</div>`;
            }

            analyticsHtml += '</div>';
            displayContainer.html(analyticsHtml);
            this.scrollToElement('#analytics-results');
        },

        formatRecommendationsList: function(recommendations) {
            if (!recommendations || !Array.isArray(recommendations) || recommendations.length === 0) {
                 return `<p>${this.getString('noSpecificPoints') || 'No specific points provided for this section.'}</p>`;
            }
            let listHtml = '<ul>';
            recommendations.forEach(rec => {
                if(rec && typeof rec === 'string') listHtml += `<li>${this.escapeHtml(rec)}</li>`;
                else if (rec && rec.text) listHtml += `<li>${this.escapeHtml(rec.text)}${rec.details ? `<em>(${this.escapeHtml(rec.details)})</em>` : ''}</li>`;
            });
            listHtml += '</ul>';
            return listHtml;
        },
        
        displayOptimizationResults: function(data) {
            let optimizationHtml = '';
            
            if (data.optimized_title) {
                optimizationHtml += `<div class="flux-seo-optimization-section">
                    <h4>📝 ${this.getString('title_field') || 'Optimized Title'}</h4>
                    <div class="flux-seo-optimized-content">${this.escapeHtml(data.optimized_title)}</div>
                </div>`;
            }
            
            if (data.optimized_meta) {
                optimizationHtml += `<div class="flux-seo-optimization-section">
                    <h4>📄 ${this.getString('metaDescription') || 'Optimized Meta Description'}</h4>
                    <div class="flux-seo-optimized-content">${this.escapeHtml(data.optimized_meta)}</div>
                </div>`;
            }
            
            if (data.optimization_tips && data.optimization_tips.length > 0) {
                optimizationHtml += `<div class="flux-seo-optimization-section">
                    <h4>💡 ${this.getString('seoTips') || 'Optimization Tips'}</h4>
                    <ul class="flux-seo-recommendation-list">`;
                data.optimization_tips.forEach(tip => {
                    optimizationHtml += `<li class="flux-seo-recommendation-item">${this.escapeHtml(tip)}</li>`;
                });
                optimizationHtml += '</ul></div>';
            }
            
            if (data.structure_improvements && data.structure_improvements.length > 0) {
                optimizationHtml += `<div class="flux-seo-optimization-section">
                    <h4>🏗️ ${this.getString('structureImprovements') || 'Structure Improvements'}</h4>
                    <ul class="flux-seo-recommendation-list">`;
                data.structure_improvements.forEach(improvement => {
                    optimizationHtml += `<li class="flux-seo-recommendation-item">${this.escapeHtml(improvement)}</li>`;
                });
                optimizationHtml += '</ul></div>';
            }
             if (data.seo_score_improvement) {
                optimizationHtml += `<div class="flux-seo-optimization-section"><h4>📈 ${this.getString('expectedImprovement') || 'Expected Score Improvement'}</h4><p>${this.escapeHtml(data.seo_score_improvement)}</p></div>`;
            }
            
            $('#optimization-display').html(optimizationHtml);
            $('#optimization-results').show();
            
            this.scrollToElement('#optimization-results');
        },
        
        displayKeywordResults: function(data) {
            let keywordHtml = '';
            
            if (data.scored_keywords && data.scored_keywords.length > 0) {
                keywordHtml += this.generateKeywordOverview(data);
                keywordHtml += this.generateScoredKeywordsTable(data.scored_keywords);
                keywordHtml += this.generateOpportunitiesAnalysis(data.opportunities);
                keywordHtml += this.generateContentStrategy(data.content_strategy);
                keywordHtml += this.generateCompetitiveAnalysis(data.competitive_analysis);
                keywordHtml += this.generateROIProjection(data.roi_projection);
            } else {
                keywordHtml += `<p>${this.getString('noKeywordsFound') || 'No keywords found or data is not in the expected format.'}</p>`;
            }
            
            $('#keyword-display').html(keywordHtml);
            $('#keyword-results').show();
            
            this.initializeKeywordInteractions();
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
                            <div class="flux-seo-keyword-name">${this.escapeHtml(item.keyword)}</div>
                            <div class="flux-seo-keyword-meta">${this.escapeHtml(data.user_intent || 'informational')}</div>
                        </td>
                        <td>
                            <div class="flux-seo-score-badge" style="background-color: ${scoreColor}">
                                ${this.escapeHtml(item.score)}
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
                            <span class="flux-seo-intent-badge flux-seo-intent-${this.escapeHtml(data.user_intent || 'informational')}">
                                ${this.escapeHtml(data.user_intent || 'informational')}
                            </span>
                        </td>
                        <td>
                            <div class="flux-seo-action-buttons">
                                <button class="flux-seo-action-btn" onclick="FluxSEOEnhanced.viewKeywordDetails('${this.escapeHtml(item.keyword)}', ${index})">
                                    👁️
                                </button>
                                <button class="flux-seo-action-btn" onclick="FluxSEOEnhanced.generateContentForKeyword('${this.escapeHtml(item.keyword)}')">
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
            if (!opportunities || Object.keys(opportunities).length === 0) return '';
            let opportunitiesHtml = `<div class="flux-seo-keyword-section"><h4>🚀 ${isThaiMode ? 'การวิเคราะห์โอกาส' : 'Opportunity Analysis'}</h4><div class="flux-seo-opportunities-grid">`;
            Object.entries(opportunities).forEach(([type, items]) => {
                if (items && items.length > 0) {
                    const typeTitle = this.getOpportunityTypeTitle(type, isThaiMode);
                    const typeIcon = this.getOpportunityTypeIcon(type);
                    opportunitiesHtml += `<div class="flux-seo-opportunity-card"><div class="flux-seo-opportunity-header"><span class="flux-seo-opportunity-icon">${typeIcon}</span><h5>${typeTitle}</h5><span class="flux-seo-opportunity-count">${items.length}</span></div><div class="flux-seo-opportunity-list">`;
                    items.slice(0, 5).forEach(item => {
                        opportunitiesHtml += `<div class="flux-seo-opportunity-item"><div class="flux-seo-opportunity-keyword">${this.escapeHtml(item.keyword)}</div><div class="flux-seo-opportunity-score">${this.escapeHtml(item.score)}</div><div class="flux-seo-opportunity-reason">${this.escapeHtml(item.reason)}</div></div>`;
                    });
                    if (items.length > 5) opportunitiesHtml += `<div class="flux-seo-opportunity-more">+${items.length - 5} ${isThaiMode ? 'เพิ่มเติม' : 'more'}</div>`;
                    opportunitiesHtml += '</div></div>';
                }
            });
            opportunitiesHtml += '</div></div>';
            return opportunitiesHtml;
        },
        
        generateContentStrategy: function(content_strategy) {
            const isThaiMode = this.currentLanguage === 'th';
            if (!content_strategy) return '';
            let strategyHtml = `<div class="flux-seo-keyword-section"><h4>📋 ${isThaiMode ? 'กลยุทธ์เนื้อหา' : 'Content Strategy'}</h4><div class="flux-seo-strategy-tabs"><button class="flux-seo-strategy-tab active" data-tab="immediate">${isThaiMode ? 'การกระทำทันที' : 'Immediate Actions'}</button><button class="flux-seo-strategy-tab" data-tab="calendar">${isThaiMode ? 'ปฏิทินเนื้อหา' : 'Content Calendar'}</button><button class="flux-seo-strategy-tab" data-tab="optimization">${isThaiMode ? 'เป้าหมายการปรับปรุง' : 'Optimization Targets'}</button><button class="flux-seo-strategy-tab" data-tab="linkbuilding">${isThaiMode ? 'การสร้างลิงก์' : 'Link Building'}</button></div><div class="flux-seo-strategy-content">`;
            if (content_strategy.immediate_actions) strategyHtml += `<div class="flux-seo-strategy-panel active" data-panel="immediate">${this.generateImmediateActions(content_strategy.immediate_actions, isThaiMode)}</div>`;
            if (content_strategy.content_calendar) strategyHtml += `<div class="flux-seo-strategy-panel" data-panel="calendar">${this.generateContentCalendar(content_strategy.content_calendar, isThaiMode)}</div>`;
            if (content_strategy.optimization_targets) strategyHtml += `<div class="flux-seo-strategy-panel" data-panel="optimization">${this.generateOptimizationTargets(content_strategy.optimization_targets, isThaiMode)}</div>`;
            if (content_strategy.link_building_priorities) strategyHtml += `<div class="flux-seo-strategy-panel" data-panel="linkbuilding">${this.generateLinkBuildingPlan(content_strategy.link_building_priorities, isThaiMode)}</div>`;
            strategyHtml += '</div></div>';
            return strategyHtml;
        },
        
        generateCompetitiveAnalysis: function(competitive_analysis) {
            const isThaiMode = this.currentLanguage === 'th';
            if (!competitive_analysis) return '';
            return `<div class="flux-seo-keyword-section"><h4>🏆 ${isThaiMode ? 'การวิเคราะห์คู่แข่ง' : 'Competitive Analysis'}</h4><div class="flux-seo-competitive-grid"><div class="flux-seo-competitive-card"><h5>${isThaiMode ? 'ความอิ่มตัวของตลาด' : 'Market Saturation'}</h5><div class="flux-seo-competitive-value">${competitive_analysis.market_saturation || 'N/A'}</div></div><div class="flux-seo-competitive-card"><h5>${isThaiMode ? 'คู่แข่งหลัก' : 'Top Competitors'}</h5><div class="flux-seo-competitor-list">${(competitive_analysis.top_competitors || []).map(comp => `<span class="flux-seo-competitor-tag">${this.escapeHtml(comp)}</span>`).join('')}</div></div><div class="flux-seo-competitive-card"><h5>${isThaiMode ? 'ข้อได้เปรียบ' : 'Competitive Advantages'}</h5><ul class="flux-seo-advantage-list">${(competitive_analysis.competitive_advantages || []).map(adv => `<li>${this.escapeHtml(adv)}</li>`).join('')}</ul></div><div class="flux-seo-competitive-card"><h5>${isThaiMode ? 'ช่องว่างในตลาด' : 'Market Gaps'}</h5><ul class="flux-seo-gap-list">${(competitive_analysis.market_gaps || []).map(gap => `<li>${this.escapeHtml(gap)}</li>`).join('')}</ul></div></div></div>`;
        },
        
        generateROIProjection: function(roi_projection) {
            const isThaiMode = this.currentLanguage === 'th';
            if (!roi_projection) return '';
            return `<div class="flux-seo-keyword-section"><h4>💰 ${isThaiMode ? 'การคาดการณ์ผลตอบแทน' : 'ROI Projection'}</h4><div class="flux-seo-roi-grid"><div class="flux-seo-roi-card"><div class="flux-seo-roi-icon">📈</div><div class="flux-seo-roi-value">${roi_projection.potential_monthly_traffic || 0}</div><div class="flux-seo-roi-label">${isThaiMode ? 'ทราฟฟิกรายเดือน' : 'Monthly Traffic'}</div></div><div class="flux-seo-roi-card"><div class="flux-seo-roi-icon">🎯</div><div class="flux-seo-roi-value">${roi_projection.estimated_monthly_conversions || 0}</div><div class="flux-seo-roi-label">${isThaiMode ? 'การแปลงรายเดือน' : 'Monthly Conversions'}</div></div><div class="flux-seo-roi-card"><div class="flux-seo-roi-icon">💵</div><div class="flux-seo-roi-value">${roi_projection.estimated_monthly_revenue || '$0'}</div><div class="flux-seo-roi-label">${isThaiMode ? 'รายได้รายเดือน' : 'Monthly Revenue'}</div></div><div class="flux-seo-roi-card"><div class="flux-seo-roi-icon">⏰</div><div class="flux-seo-roi-value">${roi_projection.roi_timeline || 'N/A'}</div><div class="flux-seo-roi-label">${isThaiMode ? 'ระยะเวลา ROI' : 'ROI Timeline'}</div></div></div></div>`;
        },
        
        generateSimpleKeywordDisplay: function(data) {
            let keywordHtml = '';
            if (data.primary_keywords && data.primary_keywords.length > 0) {
                keywordHtml += `<div class="flux-seo-keyword-section"><h4>🎯 ${this.getString('primaryKeywords') || 'Primary Keywords'}</h4><table class="flux-seo-keyword-table"><thead><tr><th>${this.getString('keyword') || 'Keyword'}</th><th>${this.getString('searchVolume') || 'Search Volume'}</th><th>${this.getString('difficulty') || 'Difficulty'}</th><th>${this.getString('intent') || 'Intent'}</th></tr></thead><tbody>`;
                data.primary_keywords.forEach(kw => {
                    if (typeof kw === 'object') keywordHtml += `<tr><td>${this.escapeHtml(kw.keyword)}</td><td>${kw.search_volume || '--'}</td><td>${kw.difficulty || '--'}</td><td>${this.escapeHtml(kw.intent || '--')}</td></tr>`;
                    else keywordHtml += `<tr><td>${this.escapeHtml(kw)}</td><td>--</td><td>--</td><td>--</td></tr>`;
                });
                keywordHtml += '</tbody></table></div>';
            }
            if (data.long_tail_keywords && data.long_tail_keywords.length > 0) {
                keywordHtml += `<div class="flux-seo-keyword-section"><h4>📝 ${this.getString('longTailKeywords') || 'Long-tail Keywords'}</h4><div class="flux-seo-keyword-tags">`;
                data.long_tail_keywords.forEach(kw => { keywordHtml += `<span class="flux-seo-keyword-tag">${this.escapeHtml(kw)}</span>`; });
                keywordHtml += '</div></div>';
            }
            if (data.content_ideas && data.content_ideas.length > 0) {
                keywordHtml += `<div class="flux-seo-keyword-section"><h4>💡 ${this.getString('contentIdeas') || 'Content Ideas'}</h4><ul class="flux-seo-recommendation-list">`;
                data.content_ideas.forEach(idea => { keywordHtml += `<li class="flux-seo-recommendation-item">${this.escapeHtml(idea)}</li>`; });
                keywordHtml += '</ul></div>';
            }
            return keywordHtml;
        },
        
        getTierBadge: function(tier) {
            const colors = { 'Tier 1': '#10b981', 'Tier 2': '#f59e0b', 'Tier 3': '#6b7280' };
            return `<span class="flux-seo-tier-badge" style="background-color: ${colors[tier] || '#6b7280'}">${this.escapeHtml(tier)}</span>`;
        },
        
        getPriorityBadge: function(priority) {
            const colors = { 'Critical': '#dc2626', 'High': '#ea580c', 'Quick Win': '#10b981', 'Medium': '#f59e0b', 'Low': '#6b7280' };
            return `<span class="flux-seo-priority-badge" style="background-color: ${colors[priority] || '#6b7280'}">${this.escapeHtml(priority)}</span>`;
        },
        
        getOpportunityTypeTitle: function(type, isThaiMode) {
            const titles = {
                'quick_wins': isThaiMode ? 'ชนะเร็ว' : 'Quick Wins', 'long_term_targets': isThaiMode ? 'เป้าหมายระยะยาว' : 'Long-term Targets',
                'content_gaps': isThaiMode ? 'ช่องว่างเนื้อหา' : 'Content Gaps', 'trending_opportunities': isThaiMode ? 'โอกาสแนวโน้ม' : 'Trending Opportunities',
                'local_opportunities': isThaiMode ? 'โอกาสท้องถิ่น' : 'Local Opportunities'
            };
            return titles[type] || type;
        },
        
        getOpportunityTypeIcon: function(type) {
            const icons = { 'quick_wins': '⚡', 'long_term_targets': '🎯', 'content_gaps': '📝', 'trending_opportunities': '📈', 'local_opportunities': '📍' };
            return icons[type] || '💡';
        },
        
        generateTierBars: function(tier_distribution) {
            if (!tier_distribution) return '';
            const total = Object.values(tier_distribution).reduce((sum, count) => sum + count, 0);
            let barsHtml = '';
            Object.entries(tier_distribution).forEach(([tier, count]) => {
                const percentage = total > 0 ? (count / total) * 100 : 0;
                const color = tier === 'Tier 1' ? '#10b981' : tier === 'Tier 2' ? '#f59e0b' : '#6b7280';
                barsHtml += `<div class="flux-seo-tier-bar"><div class="flux-seo-tier-label">${this.escapeHtml(tier)}</div><div class="flux-seo-tier-progress"><div class="flux-seo-tier-fill" style="width: ${percentage}%; background-color: ${color}"></div></div><div class="flux-seo-tier-count">${count}</div></div>`;
            });
            return barsHtml;
        },
        
        generateTimelineItems: function(timeline, isThaiMode) {
            if (!timeline) return '';
            let timelineHtml = '';
            Object.entries(timeline).forEach(([phase, description]) => {
                timelineHtml += `<div class="flux-seo-timeline-item"><div class="flux-seo-timeline-phase">${this.escapeHtml(phase)}</div><div class="flux-seo-timeline-description">${this.escapeHtml(description)}</div></div>`;
            });
            return timelineHtml;
        },
        
        initializeKeywordInteractions: function() {
            // Event delegation for dynamically added elements
            $('#keyword-display').on('click', '.flux-seo-filter-btn', function() {
                const $this = $(this);
                $this.addClass('active').siblings().removeClass('active');
                const filter = $this.data('filter');
                if (filter === 'all') {
                    $('#keyword-display .flux-seo-keyword-row').show();
                } else {
                    $('#keyword-display .flux-seo-keyword-row').hide();
                    $(`#keyword-display .flux-seo-keyword-row[data-tier="${filter}"], #keyword-display .flux-seo-keyword-row[data-priority="${filter}"]`).show();
                }
            });
            $('#keyword-display').on('click', '.flux-seo-strategy-tab', function() {
                const $this = $(this);
                $this.addClass('active').siblings().removeClass('active');
                const tab = $this.data('tab');
                $('#keyword-display .flux-seo-strategy-panel').removeClass('active');
                $(`#keyword-display .flux-seo-strategy-panel[data-panel="${tab}"]`).addClass('active');
            });
        },
        
        viewKeywordDetails: function(keyword, index) {
            this.showNotification(`Viewing details for: ${this.escapeHtml(keyword)}`, 'info');
        },
        
        generateContentForKeyword: function(keyword) {
            $('#generator-topic').val(keyword);
            this.showTab('generator');
            this.showNotification(`Ready to generate content for: ${this.escapeHtml(keyword)}`, 'success');
        },
        
        formatNumber: function(num) {
            if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
            if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
            return num.toString();
        },
        
        handleImageGeneration: function(e) {
            e.preventDefault();
            const prompt = $('#image-prompt').val().trim();
            if (!prompt) {
                this.showNotification('Please enter an image description', 'error');
                return;
            }
            const imageData = {
                prompt, style: $('#image-style').val(), size: $('#image-size').val(),
                quality: $('#image-quality').val(), count: parseInt($('#image-count').val()),
                seo_alt: $('#seo-optimized-alt').is(':checked')
            };
            this.generateImages(imageData);
        },
        
        generateImages: function(imageData) {
            this.setLoading('#generate-image-btn', true);
            setTimeout(() => { // Simulate API call
                const mockImages = this.generateMockImages(imageData);
                this.displayGeneratedImages(mockImages);
                this.setLoading('#generate-image-btn', false);
                $('#save-to-media-btn').show();
            }, 2000);
        },
        
        generateMockImages: function(imageData) {
            const images = []; const baseUrl = 'https://picsum.photos';
            const [width, height] = imageData.size.split('x');
            for (let i = 0; i < imageData.count; i++) {
                images.push({
                    id: `img_${Date.now()}_${i}`, url: `${baseUrl}/${width}/${height}?random=${Date.now() + i}`,
                    alt: imageData.seo_alt ? this.generateSEOAltText(imageData.prompt) : imageData.prompt,
                    ...imageData
                });
            }
            return images;
        },
        
        generateSEOAltText: function(prompt) {
            return `Professional ${prompt.toLowerCase().split(' ').slice(0, 5).join(' ')} image`;
        },
        
        displayGeneratedImages: function(images) {
            let galleryHtml = '';
            images.forEach(image => {
                galleryHtml += `<div class="flux-seo-image-item" data-image-id="${image.id}"><div class="flux-seo-image-container"><img src="${image.url}" alt="${this.escapeHtml(image.alt)}" class="flux-seo-generated-image"><div class="flux-seo-image-overlay"><button class="flux-seo-image-action" onclick="FluxSEOEnhanced.downloadImage('${image.id}')">📥 Download</button><button class="flux-seo-image-action" onclick="FluxSEOEnhanced.copyImageUrl('${image.url}')">🔗 Copy URL</button><button class="flux-seo-image-action" onclick="FluxSEOEnhanced.editImage('${image.id}')">✏️ Edit</button></div></div><div class="flux-seo-image-info"><div class="flux-seo-image-alt"><strong>Alt Text:</strong><input type="text" value="${this.escapeHtml(image.alt)}" class="flux-seo-alt-input" data-image-id="${image.id}"></div><div class="flux-seo-image-meta"><span class="flux-seo-image-size">${image.size}</span><span class="flux-seo-image-style">${image.style}</span><span class="flux-seo-image-quality">${image.quality}</span></div></div></div>`;
            });
            $('#image-gallery').html(galleryHtml);
            $('#image-results').show();
            this.scrollToElement('#image-results');
        },
        
        handleSaveToMedia: function(e) {
            e.preventDefault();
            const images = this.getGeneratedImages();
            if (images.length === 0) {
                this.showNotification('No images to save', 'error'); return;
            }
            this.saveImagesToMediaLibrary(images);
        },
        
        getGeneratedImages: function() {
            const images = [];
            $('.flux-seo-image-item').each(function() {
                const $item = $(this); const imageId = $item.data('image-id');
                const $img = $item.find('.flux-seo-generated-image'); const $altInput = $item.find('.flux-seo-alt-input');
                images.push({ id: imageId, url: $img.attr('src'), alt: $altInput.val(), filename: `ai-generated-${imageId}.jpg` });
            });
            return images;
        },
        
        saveImagesToMediaLibrary: function(images) {
            this.setLoading('#save-to-media-btn', true);
            setTimeout(() => { // Simulate
                this.showNotification(`Successfully saved ${images.length} image(s) to media library`, 'success');
                this.setLoading('#save-to-media-btn', false);
            }, 1500);
        },
        
        downloadImage: function(imageId) {
            const imageUrl = $(`.flux-seo-image-item[data-image-id="${imageId}"] .flux-seo-generated-image`).attr('src');
            const link = document.createElement('a'); link.href = imageUrl;
            link.download = `ai-generated-${imageId}.jpg`; document.body.appendChild(link);
            link.click(); document.body.removeChild(link);
            this.showNotification('Image download started', 'success');
        },
        
        copyImageUrl: function(imageUrl) {
            navigator.clipboard.writeText(imageUrl)
                .then(() => this.showNotification('Image URL copied', 'success'))
                .catch(() => this.showNotification('Failed to copy URL', 'error'));
        },
        
        editImage: function(imageId) {
            this.showNotification('Image editing feature coming soon!', 'info');
        },
        
        handleCopyContent: function(e) {
            e.preventDefault();
            // Attempt to get HTML content if available, otherwise fall back to text
            let contentToCopy = '';
            const $generatedContentDisplay = $('#generated-content-display'); // Target the main display div
            const $articleContent = $generatedContentDisplay.find('.flux-seo-generated-main-content');

            if ($articleContent.length && $articleContent.html().trim() !== '') {
                contentToCopy = $articleContent.html();
                this.fallbackCopyToClipboard(contentToCopy, true);
            } else if ($generatedContentDisplay.text().trim() !== '') {
                contentToCopy = $generatedContentDisplay.text();
                this.fallbackCopyToClipboard(contentToCopy, false);
            } else {
                 this.showNotification(this.getString('noContentToCopy') || 'No content to copy.', 'warning');
            }
        },
        
        fallbackCopyToClipboard: function(text, isHtml = false) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.focus(); textArea.select();
            try {
                document.execCommand('copy');
                this.showNotification(this.getString(isHtml ? 'htmlCopied' : 'contentCopied') || 'Content copied!', 'success');
            } catch (err) {
                this.showNotification(this.getString('errorCopying') || 'Unable to copy', 'error');
            }
            document.body.removeChild(textArea);
        },
        
        handleInputValidation: function(e) {
            const $input = $(e.target); const value = $input.val();
            $input.removeClass('flux-seo-input-error flux-seo-input-success');
            if ($input.attr('required') && !value.trim()) $input.addClass('flux-seo-input-error');
            else if (value.trim()) $input.addClass('flux-seo-input-success');
            if ($input.attr('type') === 'url' && value && !this.isValidUrl(value)) $input.addClass('flux-seo-input-error');
        },
        
        setLoading: function(selector, isLoading, buttonTextKey = 'processing') {
            const $btn = $(selector);
            if (!$btn.length) return;
            const $icon = $btn.find('.flux-seo-btn-icon');
            const $text = $btn.find('.flux-seo-btn-text');
            
            if (!$btn.data('original-text') && $text.length) {
                 $btn.data('original-text', $text.text());
            }
            if (!$btn.data('original-icon') && $icon.length) {
                 $btn.data('original-icon', $icon.html());
            }

            if (isLoading) {
                $btn.prop('disabled', true);
                if($icon.length) $icon.html('⏳');
                if($text.length) $text.text(this.getString(buttonTextKey) || this.getString('processing') || 'Processing...');
            } else {
                $btn.prop('disabled', false);
                if($icon.length && $btn.data('original-icon')) $icon.html($btn.data('original-icon'));
                if($text.length && $btn.data('original-text')) $text.text($btn.data('original-text'));
            }
            this.isLoading = isLoading;
        },
        
        showNotification: function(message, type = 'info') {
            $('.flux-seo-notification').remove();
            const typeIcons = { success: '✅', error: '❌', warning: '⚠️', info: 'ℹ️' };
            const $notification = $(`<div class="flux-seo-notification flux-seo-notification-${type}"><span class="flux-seo-notification-icon">${typeIcons[type] || 'ℹ️'}</span><span class="flux-seo-notification-message">${message}</span><button class="flux-seo-notification-close">×</button></div>`);
            $('body').append($notification);
            setTimeout(() => $notification.fadeOut(() => $notification.remove()), 5000);
            $notification.find('.flux-seo-notification-close').on('click', () => $notification.fadeOut(() => $notification.remove()));
        },
        
        scrollToElement: function(selector) {
            const $element = $(selector);
            if ($element.length) $('html, body').animate({ scrollTop: $element.offset().top - 100 }, 500);
        },
        
        initializeTooltips: function() {
            $(document).on('mouseenter', '[data-tooltip]', function() {
                const $this = $(this); const tooltipText = $this.data('tooltip');
                const $tooltip = $(`<div class="flux-seo-tooltip">${tooltipText}</div>`).appendTo('body');
                const rect = this.getBoundingClientRect();
                const top = rect.top - $tooltip.outerHeight() - 5 + $(window).scrollTop();
                const left = rect.left + (rect.width / 2) - ($tooltip.outerWidth() / 2) + $(window).scrollLeft();
                $tooltip.css({ top: top, left: left });
            }).on('mouseleave', '[data-tooltip]', function() {
                $('.flux-seo-tooltip').remove();
            });
        },
        
        initializeAutoSave: function() {
            $('.flux-seo-input, .flux-seo-textarea, .flux-seo-select').on('input change', function() {
                localStorage.setItem(`flux_seo_${this.id}`, $(this).val());
            });
            $('.flux-seo-input, .flux-seo-textarea, .flux-seo-select').each(function() {
                const saved = localStorage.getItem(`flux_seo_${this.id}`);
                if (saved) $(this).val(saved);
            });
        },
        
        initializeKeyboardShortcuts: function() {
            $(document).on('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                    const $submitBtn = $('.flux-seo-tab-content.active .flux-seo-btn-primary').first();
                    if ($submitBtn.length && !$submitBtn.prop('disabled')) $submitBtn.click();
                }
                if (e.key === 'Escape') $('.flux-seo-notification').fadeOut(() => $('.flux-seo-notification').remove());
            });
        },
        
        trackEvent: function(eventName, properties = {}) {
            if (window.gtag) window.gtag('event', eventName, { ...properties, plugin: 'flux_seo_enhanced' });
            console.log('Flux SEO Event:', eventName, properties);
        },
        
        getString: function(key, fallback = '') {
             const langStrings = this.strings[this.currentLanguage] || this.strings.en || {};
             return langStrings[key] || fallback || key;
        },
        
        isValidUrl: function(string) {
            try { new URL(string); return true; } catch (_) { return false; }
        },

    }; // End of window.FluxSEOEnhanced

    $(document).ready(function() {
        window.FluxSEOEnhanced.init();
    });

})(jQuery);

const notificationStyles = `
<style>
/* ... (styles remain the same) ... */
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
.flux-seo-notification-success { background: rgba(16, 185, 129, 0.95); color: white; border: 1px solid rgba(255, 255, 255, 0.2); }
.flux-seo-notification-error { background: rgba(239, 68, 68, 0.95); color: white; border: 1px solid rgba(255, 255, 255, 0.2); }
.flux-seo-notification-warning { background: rgba(245, 158, 11, 0.95); color: white; border: 1px solid rgba(255, 255, 255, 0.2); }
.flux-seo-notification-info { background: rgba(59, 130, 246, 0.95); color: white; border: 1px solid rgba(255, 255, 255, 0.2); }
.flux-seo-notification-close { background: none; border: none; color: inherit; font-size: 18px; cursor: pointer; padding: 0; margin-left: auto; opacity: 0.8; }
.flux-seo-notification-close:hover { opacity: 1; }
@keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
.flux-seo-input-error { border-color: #ef4444 !important; box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important; }
.flux-seo-input-success { border-color: #10b981 !important; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important; }
.flux-seo-tooltip { position: absolute; z-index: 10001; background: rgba(0, 0, 0, 0.9); color: white; padding: 8px 12px; border-radius: 6px; font-size: 12px; max-width: 200px; pointer-events: none; }
.flux-seo-optimization-section { margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #e2e8f0; }
.flux-seo-optimization-section:last-child { border-bottom: none; margin-bottom: 0; }
.flux-seo-optimized-content { background: #f8fafc; padding: 16px; border-radius: 8px; border-left: 4px solid #667eea; margin-top: 8px; }
.flux-seo-keyword-tags { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
.flux-seo-outline-list { list-style: none; padding: 0; margin: 12px 0 0 0; }
.flux-seo-outline-list li { padding: 8px 0; border-bottom: 1px solid #e2e8f0; position: relative; padding-left: 20px; }
.flux-seo-outline-list li:last-child { border-bottom: none; }
.flux-seo-outline-list li::before { content: "▶"; position: absolute; left: 0; color: #667eea; font-size: 12px; }
.score-good { color: #10b981 !important; } /* Green for good scores */
.score-ok { color: #f59e0b !important; }   /* Orange for okay scores */
.score-bad { color: #ef4444 !important; }  /* Red for bad scores */
.score-unknown { color: #6b7280 !important; } /* Gray for unknown scores */
.flux-seo-collapsible-section { margin-bottom: 1rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; }
.flux-seo-collapsible-header { background-color: #f9fafb; padding: 0.75rem 1rem; width: 100%; text-align: left; border: none; font-size: 1rem; font-weight: 600; cursor: pointer; display: flex; justify-content: space-between; align-items: center; }
.flux-seo-collapsible-header:hover { background-color: #f3f4f6; }
.flux-seo-collapsible-icon { margin-right: 0.5rem; }
.flux-seo-collapsible-arrow { font-size: 0.8rem; transition: transform 0.2s; }
.flux-seo-collapsible-header[aria-expanded="true"] .flux-seo-collapsible-arrow { transform: rotate(180deg); }
.flux-seo-collapsible-content { padding: 1rem; border-top: 1px solid #e2e8f0; display: none; /* Initially hidden */ }
.flux-seo-collapsible-content ul { margin-top: 0.5rem; padding-left: 1.5rem; }
.flux-seo-collapsible-content li { margin-bottom: 0.25rem; }
.flux-seo-analysis-details .flux-seo-analysis-section:not(:last-child) { margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px dashed #cbd5e1; }
.flux-seo-analysis-section h4 { margin-bottom: 0.75rem; font-size: 1.125rem; color: #4a5568; }
.flux-seo-analysis-section h5 { margin-top: 1rem; margin-bottom: 0.5rem; font-size: 1rem; color: #4a5568; font-weight:600; }
.flux-seo-score-display { font-size: 1.5rem; font-weight: bold; margin-bottom: 0.5rem; }
.flux-seo-score-display .flux-seo-score-value { font-size: 2rem; }
.flux-seo-suggestion-pill { background-color: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; margin: 0.25rem; cursor: pointer; transition: background-color 0.2s; }
.flux-seo-suggestion-pill:hover { background-color: #e0e7ff; }
.flux-seo-suggestions-container { margin-top: 0.5rem; margin-bottom: 1rem; }
</style>
`;

document.head.insertAdjacentHTML('beforeend', notificationStyles);
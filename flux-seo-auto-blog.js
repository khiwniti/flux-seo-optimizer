/**
 * Flux SEO Auto Blog JavaScript
 * Handles auto blog scheduling interface and functionality
 */

(function($) {
    'use strict';

    window.FluxSEOAutoBlog = {
        schedules: [],
        currentLanguage: 'en',
        
        init: function() {
            this.bindEvents();
            this.loadSchedules();
            this.loadAnalytics();
            this.initializeTimezone();
            console.log('Flux SEO Auto Blog initialized');
        },
        
        bindEvents: function() {
            // Form submission
            $('#flux-seo-auto-blog-form').on('submit', this.handleCreateSchedule.bind(this));
            
            // Frequency change
            $('#schedule-frequency').on('change', this.handleFrequencyChange.bind(this));
            
            // Post status change
            $('#post-status').on('change', this.handlePostStatusChange.bind(this));
            
            // Test generation
            $('#test-generation-btn').on('click', this.handleTestGeneration.bind(this));
            
            // Schedule actions
            $(document).on('click', '.schedule-toggle-btn', this.handleToggleSchedule.bind(this));
            $(document).on('click', '.schedule-edit-btn', this.handleEditSchedule.bind(this));
            $(document).on('click', '.schedule-delete-btn', this.handleDeleteSchedule.bind(this));
            $(document).on('click', '.schedule-test-btn', this.handleTestSchedule.bind(this));
            
            // Auto-refresh every 30 seconds
            setInterval(() => {
                this.loadSchedules();
                this.loadAnalytics();
            }, 30000);
        },
        
        handleFrequencyChange: function(e) {
            const frequency = $(e.target).val();
            const customGroup = $('#custom-frequency-group');
            
            if (frequency === 'custom') {
                customGroup.show();
            } else {
                customGroup.hide();
            }
        },
        
        handlePostStatusChange: function(e) {
            const status = $(e.target).val();
            const delayGroup = $('#publish-delay-group');
            
            if (status === 'scheduled') {
                delayGroup.show();
            } else {
                delayGroup.hide();
            }
        },
        
        handleCreateSchedule: function(e) {
            e.preventDefault();
            
            const formData = this.getFormData();
            
            if (!this.validateFormData(formData)) {
                return;
            }
            
            this.setLoading('#flux-seo-auto-blog-form button[type="submit"]', true);
            
            $.post(ajaxurl, {
                action: 'flux_seo_create_auto_blog_schedule',
                nonce: fluxSeoEnhanced.nonce,
                ...formData
            })
            .done((response) => {
                if (response.success) {
                    this.showNotification(response.data.message, 'success');
                    this.resetForm();
                    this.loadSchedules();
                } else {
                    this.showNotification(response.data || 'Failed to create schedule', 'error');
                }
            })
            .fail(() => {
                this.showNotification('Network error occurred', 'error');
            })
            .always(() => {
                this.setLoading('#flux-seo-auto-blog-form button[type="submit"]', false);
            });
        },
        
        getFormData: function() {
            return {
                schedule_name: $('#schedule-name').val(),
                frequency: $('#schedule-frequency').val(),
                custom_frequency_value: $('#custom-frequency-value').val(),
                custom_frequency_unit: $('#custom-frequency-unit').val(),
                post_status: $('#post-status').val(),
                publish_delay_hours: $('#publish-delay').val(),
                language: $('#schedule-language').val(),
                timezone: $('#schedule-timezone').val(),
                generation_time: $('#generation-time').val(),
                content_topics: $('#content-topics').val(),
                content_type: $('#content-type').val(),
                word_count_range: $('#word-count-range').val(),
                writing_tone: $('#writing-tone').val(),
                target_audience: $('#target-audience').val(),
                auto_seo_optimization: $('#auto-seo-optimization').is(':checked'),
                auto_keyword_research: $('#auto-keyword-research').is(':checked')
            };
        },
        
        validateFormData: function(data) {
            if (!data.schedule_name.trim()) {
                this.showNotification('Please enter a schedule name', 'error');
                $('#schedule-name').focus();
                return false;
            }
            
            if (!data.content_topics.trim()) {
                this.showNotification('Please enter at least one content topic', 'error');
                $('#content-topics').focus();
                return false;
            }
            
            if (data.frequency === 'custom') {
                if (!data.custom_frequency_value || data.custom_frequency_value < 1) {
                    this.showNotification('Please enter a valid custom frequency', 'error');
                    $('#custom-frequency-value').focus();
                    return false;
                }
            }
            
            return true;
        },
        
        resetForm: function() {
            $('#flux-seo-auto-blog-form')[0].reset();
            $('#custom-frequency-group').hide();
            $('#publish-delay-group').hide();
        },
        
        handleTestGeneration: function(e) {
            e.preventDefault();
            
            const formData = this.getFormData();
            
            if (!formData.content_topics.trim()) {
                this.showNotification('Please enter content topics to test', 'error');
                return;
            }
            
            this.setLoading('#test-generation-btn', true);
            
            // Simulate test generation
            setTimeout(() => {
                const topics = formData.content_topics.split('\n');
                const randomTopic = topics[Math.floor(Math.random() * topics.length)].trim();
                
                const testResult = {
                    topic: randomTopic,
                    title: `Test: ${randomTopic} - Complete Guide`,
                    estimated_words: this.getWordCountFromRange(formData.word_count_range),
                    estimated_time: '2-3 minutes',
                    seo_score: Math.floor(Math.random() * 20) + 80,
                    readability_score: Math.floor(Math.random() * 20) + 75
                };
                
                this.showTestResult(testResult);
                this.setLoading('#test-generation-btn', false);
            }, 2000);
        },
        
        showTestResult: function(result) {
            const modal = $(`
                <div class="flux-seo-modal-overlay">
                    <div class="flux-seo-modal">
                        <div class="flux-seo-modal-header">
                            <h3>🧪 Test Generation Result</h3>
                            <button class="flux-seo-modal-close">&times;</button>
                        </div>
                        <div class="flux-seo-modal-body">
                            <div class="flux-seo-test-result">
                                <div class="flux-seo-test-item">
                                    <strong>Topic:</strong> ${result.topic}
                                </div>
                                <div class="flux-seo-test-item">
                                    <strong>Generated Title:</strong> ${result.title}
                                </div>
                                <div class="flux-seo-test-item">
                                    <strong>Estimated Words:</strong> ${result.estimated_words}
                                </div>
                                <div class="flux-seo-test-item">
                                    <strong>Generation Time:</strong> ${result.estimated_time}
                                </div>
                                <div class="flux-seo-test-scores">
                                    <div class="flux-seo-test-score">
                                        <span class="flux-seo-score-label">SEO Score:</span>
                                        <span class="flux-seo-score-value">${result.seo_score}/100</span>
                                    </div>
                                    <div class="flux-seo-test-score">
                                        <span class="flux-seo-score-label">Readability:</span>
                                        <span class="flux-seo-score-value">${result.readability_score}/100</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flux-seo-modal-footer">
                            <button class="flux-seo-btn flux-seo-btn-primary flux-seo-modal-close">
                                ✅ Looks Good
                            </button>
                        </div>
                    </div>
                </div>
            `);
            
            $('body').append(modal);
            
            modal.find('.flux-seo-modal-close').on('click', function() {
                modal.remove();
            });
            
            modal.on('click', function(e) {
                if (e.target === this) {
                    modal.remove();
                }
            });
        },
        
        getWordCountFromRange: function(range) {
            const parts = range.split('-');
            const min = parseInt(parts[0]);
            const max = parseInt(parts[1]);
            return Math.floor(Math.random() * (max - min + 1)) + min;
        },
        
        loadSchedules: function() {
            $.post(ajaxurl, {
                action: 'flux_seo_get_auto_blog_schedules',
                nonce: fluxSeoEnhanced.nonce
            })
            .done((response) => {
                if (response.success) {
                    this.schedules = response.data;
                    this.renderSchedules();
                }
            });
        },
        
        renderSchedules: function() {
            const container = $('#auto-blog-schedules-list');
            
            if (this.schedules.length === 0) {
                container.html(`
                    <div class="flux-seo-empty-state">
                        <div class="flux-seo-empty-icon">📅</div>
                        <h3>No Auto Blog Schedules</h3>
                        <p>Create your first auto blog schedule to start generating content automatically.</p>
                    </div>
                `);
                return;
            }
            
            let schedulesHtml = '';
            
            this.schedules.forEach(schedule => {
                const settings = JSON.parse(schedule.content_settings || '{}');
                const nextGen = schedule.next_generation ? new Date(schedule.next_generation + ' UTC') : null;
                const lastGen = schedule.last_generation ? new Date(schedule.last_generation + ' UTC') : null;
                
                schedulesHtml += `
                    <div class="flux-seo-schedule-card ${schedule.status}" data-schedule-id="${schedule.id}">
                        <div class="flux-seo-schedule-header">
                            <div class="flux-seo-schedule-info">
                                <h3 class="flux-seo-schedule-name">${schedule.schedule_name}</h3>
                                <div class="flux-seo-schedule-meta">
                                    <span class="flux-seo-schedule-frequency">${this.formatFrequency(schedule)}</span>
                                    <span class="flux-seo-schedule-language">${schedule.language === 'th' ? '🇹🇭 Thai' : '🇺🇸 English'}</span>
                                    <span class="flux-seo-schedule-status flux-seo-status-${schedule.status}">${schedule.status}</span>
                                </div>
                            </div>
                            <div class="flux-seo-schedule-actions">
                                <button class="flux-seo-action-btn schedule-toggle-btn" 
                                        data-schedule-id="${schedule.id}" 
                                        data-status="${schedule.status}"
                                        title="${schedule.status === 'active' ? 'Pause' : 'Activate'}">
                                    ${schedule.status === 'active' ? '⏸️' : '▶️'}
                                </button>
                                <button class="flux-seo-action-btn schedule-test-btn" 
                                        data-schedule-id="${schedule.id}"
                                        title="Test Generation">
                                    🧪
                                </button>
                                <button class="flux-seo-action-btn schedule-edit-btn" 
                                        data-schedule-id="${schedule.id}"
                                        title="Edit">
                                    ✏️
                                </button>
                                <button class="flux-seo-action-btn schedule-delete-btn" 
                                        data-schedule-id="${schedule.id}"
                                        title="Delete">
                                    🗑️
                                </button>
                            </div>
                        </div>
                        
                        <div class="flux-seo-schedule-body">
                            <div class="flux-seo-schedule-stats">
                                <div class="flux-seo-stat">
                                    <span class="flux-seo-stat-value">${schedule.total_generated || 0}</span>
                                    <span class="flux-seo-stat-label">Generated</span>
                                </div>
                                <div class="flux-seo-stat">
                                    <span class="flux-seo-stat-value">${schedule.success_rate || 0}%</span>
                                    <span class="flux-seo-stat-label">Success Rate</span>
                                </div>
                                <div class="flux-seo-stat">
                                    <span class="flux-seo-stat-value">${settings.content_type || 'blog_post'}</span>
                                    <span class="flux-seo-stat-label">Type</span>
                                </div>
                            </div>
                            
                            <div class="flux-seo-schedule-timing">
                                <div class="flux-seo-timing-item">
                                    <strong>Next Generation:</strong>
                                    <span class="flux-seo-next-generation">
                                        ${nextGen ? this.formatDateTime(nextGen) : 'Not scheduled'}
                                    </span>
                                </div>
                                ${lastGen ? `
                                <div class="flux-seo-timing-item">
                                    <strong>Last Generation:</strong>
                                    <span class="flux-seo-last-generation">
                                        ${this.formatDateTime(lastGen)}
                                    </span>
                                </div>
                                ` : ''}
                            </div>
                            
                            <div class="flux-seo-schedule-topics">
                                <strong>Topics:</strong>
                                <div class="flux-seo-topics-preview">
                                    ${this.formatTopics(settings.topics)}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            container.html(schedulesHtml);
        },
        
        formatFrequency: function(schedule) {
            if (schedule.frequency === 'custom') {
                return `Every ${schedule.custom_frequency_value} ${schedule.custom_frequency_unit}`;
            }
            return schedule.frequency.charAt(0).toUpperCase() + schedule.frequency.slice(1);
        },
        
        formatDateTime: function(date) {
            return date.toLocaleString(undefined, {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        
        formatTopics: function(topics) {
            if (!topics) return 'No topics specified';
            
            const topicList = topics.split('\n').filter(t => t.trim());
            const preview = topicList.slice(0, 3).map(t => t.trim()).join(', ');
            
            if (topicList.length > 3) {
                return preview + ` (+${topicList.length - 3} more)`;
            }
            
            return preview;
        },
        
        handleToggleSchedule: function(e) {
            const scheduleId = $(e.target).data('schedule-id');
            const currentStatus = $(e.target).data('status');
            const newStatus = currentStatus === 'active' ? 'paused' : 'active';
            
            $.post(ajaxurl, {
                action: 'flux_seo_update_auto_blog_schedule',
                nonce: fluxSeoEnhanced.nonce,
                schedule_id: scheduleId,
                status: newStatus
            })
            .done((response) => {
                if (response.success) {
                    this.showNotification(response.data, 'success');
                    this.loadSchedules();
                } else {
                    this.showNotification(response.data || 'Failed to update schedule', 'error');
                }
            });
        },
        
        handleEditSchedule: function(e) {
            const scheduleId = $(e.target).data('schedule-id');
            const schedule = this.schedules.find(s => s.id == scheduleId);
            
            if (schedule) {
                this.populateFormWithSchedule(schedule);
                $('html, body').animate({
                    scrollTop: $('#flux-seo-auto-blog-form').offset().top - 100
                }, 500);
            }
        },
        
        populateFormWithSchedule: function(schedule) {
            const settings = JSON.parse(schedule.content_settings || '{}');
            
            $('#schedule-name').val(schedule.schedule_name);
            $('#schedule-frequency').val(schedule.frequency).trigger('change');
            $('#custom-frequency-value').val(schedule.custom_frequency_value);
            $('#custom-frequency-unit').val(schedule.custom_frequency_unit);
            $('#post-status').val(schedule.post_status).trigger('change');
            $('#publish-delay').val(schedule.publish_delay_hours);
            $('#schedule-language').val(schedule.language);
            $('#schedule-timezone').val(schedule.timezone);
            $('#content-topics').val(settings.topics);
            $('#content-type').val(settings.content_type);
            $('#word-count-range').val(settings.word_count_range);
            $('#writing-tone').val(settings.writing_tone);
            $('#target-audience').val(settings.target_audience);
            $('#auto-seo-optimization').prop('checked', settings.auto_seo_optimization);
            $('#auto-keyword-research').prop('checked', settings.auto_keyword_research);
        },
        
        handleDeleteSchedule: function(e) {
            const scheduleId = $(e.target).data('schedule-id');
            const schedule = this.schedules.find(s => s.id == scheduleId);
            
            if (schedule && confirm(`Are you sure you want to delete the schedule "${schedule.schedule_name}"?`)) {
                $.post(ajaxurl, {
                    action: 'flux_seo_delete_auto_blog_schedule',
                    nonce: fluxSeoEnhanced.nonce,
                    schedule_id: scheduleId
                })
                .done((response) => {
                    if (response.success) {
                        this.showNotification(response.data, 'success');
                        this.loadSchedules();
                    } else {
                        this.showNotification(response.data || 'Failed to delete schedule', 'error');
                    }
                });
            }
        },
        
        handleTestSchedule: function(e) {
            const scheduleId = $(e.target).data('schedule-id');
            const schedule = this.schedules.find(s => s.id == scheduleId);
            
            if (schedule) {
                this.showNotification('Test generation started...', 'info');
                
                // Simulate test generation
                setTimeout(() => {
                    const settings = JSON.parse(schedule.content_settings || '{}');
                    const topics = settings.topics ? settings.topics.split('\n') : ['Test Topic'];
                    const randomTopic = topics[Math.floor(Math.random() * topics.length)].trim();
                    
                    const testResult = {
                        topic: randomTopic,
                        title: `${randomTopic} - AI Generated Article`,
                        estimated_words: this.getWordCountFromRange(settings.word_count_range || '800-1200'),
                        estimated_time: '2-3 minutes',
                        seo_score: Math.floor(Math.random() * 20) + 80,
                        readability_score: Math.floor(Math.random() * 20) + 75
                    };
                    
                    this.showTestResult(testResult);
                }, 1500);
            }
        },
        
        loadAnalytics: function() {
            // Calculate analytics from schedules
            let totalGenerated = 0;
            let totalSuccess = 0;
            let totalSeoScore = 0;
            let activeSchedules = 0;
            let nextGeneration = null;
            
            this.schedules.forEach(schedule => {
                totalGenerated += parseInt(schedule.total_generated || 0);
                totalSuccess += (parseInt(schedule.total_generated || 0) * (parseFloat(schedule.success_rate || 0) / 100));
                
                if (schedule.status === 'active') {
                    activeSchedules++;
                    
                    if (schedule.next_generation) {
                        const nextGen = new Date(schedule.next_generation + ' UTC');
                        if (!nextGeneration || nextGen < nextGeneration) {
                            nextGeneration = nextGen;
                        }
                    }
                }
            });
            
            const successRate = totalGenerated > 0 ? (totalSuccess / totalGenerated) * 100 : 0;
            const avgSeoScore = totalGenerated > 0 ? 85 : 0; // Placeholder
            
            $('#total-generated').text(totalGenerated);
            $('#success-rate').text(Math.round(successRate) + '%');
            $('#avg-seo-score').text(avgSeoScore);
            $('#next-generation').text(nextGeneration ? this.formatDateTime(nextGeneration) : '--');
        },
        
        initializeTimezone: function() {
            // Set default timezone to user's timezone
            const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            const timezoneSelect = $('#schedule-timezone');
            
            if (timezoneSelect.find(`option[value="${userTimezone}"]`).length > 0) {
                timezoneSelect.val(userTimezone);
            }
        },
        
        setLoading: function(selector, isLoading) {
            const $btn = $(selector);
            const $icon = $btn.find('.flux-seo-btn-icon');
            const $text = $btn.find('.flux-seo-btn-text');
            
            if (isLoading) {
                $btn.prop('disabled', true);
                if ($icon.length) $icon.text('⏳');
                if ($text.length) $text.text('Processing...');
            } else {
                $btn.prop('disabled', false);
                // Restore original content
                if (selector.includes('test-generation')) {
                    if ($icon.length) $icon.text('🧪');
                    if ($text.length) $text.text('Test Generation');
                } else {
                    if ($icon.length) $icon.text('⏰');
                    if ($text.length) $text.text('Create Schedule');
                }
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
        }
    };

})(jQuery);
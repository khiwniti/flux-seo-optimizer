<?php
// Chatbot Tab Content
?>
<div id="chatbot-tab" class="flux-seo-tab-content">
    <div class="flux-seo-card">
        <div class="flux-seo-card-header">
            <h2 class="flux-seo-card-title">
                <span class="flux-seo-card-icon">💬</span>
                <span data-key="chatbot"><?php _e('SEO Assistant Chatbot', 'flux-seo-enhanced'); ?></span>
            </h2>
            <p class="flux-seo-card-description">
                <?php _e('Get instant SEO advice and answers from our AI assistant', 'flux-seo-enhanced'); ?>
            </p>
        </div>
        <div class="flux-seo-card-body">
            <div class="flux-seo-chatbot">
                <div id="chatbot-messages" class="flux-seo-chatbot-messages">
                    <div class="flux-seo-message flux-seo-message-bot">
                        <div class="flux-seo-message-avatar">🤖</div>
                        <div class="flux-seo-message-content">
                            <div class="flux-seo-message-text">
                                👋 <?php _e('Hello! I\'m your SEO assistant. Ask me anything about SEO optimization, keyword research, content strategy, or technical SEO issues.', 'flux-seo-enhanced'); ?>
                            </div>
                            <div class="flux-seo-message-time"><?php echo date('H:i'); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Suggestions -->
                <div class="flux-seo-chatbot-suggestions">
                    <h4><?php _e('Quick Questions', 'flux-seo-enhanced'); ?></h4>
                    <div class="flux-seo-suggestions-grid">
                        <button class="flux-seo-suggestion-btn" data-suggestion="How to improve my website's SEO score?">
                            <span class="flux-seo-suggestion-icon">📈</span>
                            <span class="flux-seo-suggestion-text"><?php _e('How to improve my website\'s SEO score?', 'flux-seo-enhanced'); ?></span>
                        </button>
                        <button class="flux-seo-suggestion-btn" data-suggestion="What are the best keyword research strategies?">
                            <span class="flux-seo-suggestion-icon">🎯</span>
                            <span class="flux-seo-suggestion-text"><?php _e('What are the best keyword research strategies?', 'flux-seo-enhanced'); ?></span>
                        </button>
                        <button class="flux-seo-suggestion-btn" data-suggestion="How to optimize for mobile SEO?">
                            <span class="flux-seo-suggestion-icon">📱</span>
                            <span class="flux-seo-suggestion-text"><?php _e('How to optimize for mobile SEO?', 'flux-seo-enhanced'); ?></span>
                        </button>
                        <button class="flux-seo-suggestion-btn" data-suggestion="What is technical SEO and why is it important?">
                            <span class="flux-seo-suggestion-icon">⚙️</span>
                            <span class="flux-seo-suggestion-text"><?php _e('What is technical SEO and why is it important?', 'flux-seo-enhanced'); ?></span>
                        </button>
                        <button class="flux-seo-suggestion-btn" data-suggestion="How to create effective meta descriptions?">
                            <span class="flux-seo-suggestion-icon">📝</span>
                            <span class="flux-seo-suggestion-text"><?php _e('How to create effective meta descriptions?', 'flux-seo-enhanced'); ?></span>
                        </button>
                        <button class="flux-seo-suggestion-btn" data-suggestion="What is schema markup and how to implement it?">
                            <span class="flux-seo-suggestion-icon">📋</span>
                            <span class="flux-seo-suggestion-text"><?php _e('What is schema markup and how to implement it?', 'flux-seo-enhanced'); ?></span>
                        </button>
                    </div>
                </div>
                
                <!-- Chat Input -->
                <div class="flux-seo-chatbot-input">
                    <div class="flux-seo-input-container">
                        <input type="text" id="chatbot-input" class="flux-seo-input" 
                               placeholder="<?php _e('Ask me about SEO...', 'flux-seo-enhanced'); ?>" maxlength="500">
                        <div class="flux-seo-input-actions">
                            <button id="chatbot-attach" class="flux-seo-btn-icon" title="<?php _e('Attach file', 'flux-seo-enhanced'); ?>">
                                📎
                            </button>
                            <button id="chatbot-send" class="flux-seo-btn flux-seo-btn-primary" disabled>
                                <span class="flux-seo-btn-icon">📤</span>
                                <span class="flux-seo-btn-text"><?php _e('Send', 'flux-seo-enhanced'); ?></span>
                            </button>
                        </div>
                    </div>
                    <div class="flux-seo-input-info">
                        <span id="char-count">0</span>/500 <?php _e('characters', 'flux-seo-enhanced'); ?>
                        <span class="flux-seo-ai-badge"><?php _e('Powered by Google Gemini 2.5 Pro', 'flux-seo-enhanced'); ?></span>
                    </div>
                </div>
                
                <!-- Chat Controls -->
                <div class="flux-seo-chatbot-controls">
                    <button id="clear-chat" class="flux-seo-btn flux-seo-btn-outline">
                        <span class="flux-seo-btn-icon">🗑️</span>
                        <span class="flux-seo-btn-text"><?php _e('Clear Chat', 'flux-seo-enhanced'); ?></span>
                    </button>
                    <button id="export-chat" class="flux-seo-btn flux-seo-btn-secondary">
                        <span class="flux-seo-btn-icon">💾</span>
                        <span class="flux-seo-btn-text"><?php _e('Export Chat', 'flux-seo-enhanced'); ?></span>
                    </button>
                    <button id="share-chat" class="flux-seo-btn flux-seo-btn-secondary">
                        <span class="flux-seo-btn-icon">🔗</span>
                        <span class="flux-seo-btn-text"><?php _e('Share Chat', 'flux-seo-enhanced'); ?></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
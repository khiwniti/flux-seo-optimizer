/**
 * Component Verification Script
 * Verifies that all React app components are properly implemented in WordPress plugin
 */

// Component verification checklist
const componentChecklist = {
    // Core Navigation
    navigation: {
        tabs: ['analyzer', 'generator', 'analytics', 'keywords', 'meta', 'schema', 'technical', 'chatbot', 'settings'],
        languageSwitcher: true,
        headerBadges: true
    },
    
    // Tab Components
    analyzer: {
        contentInput: true,
        targetKeywords: true,
        aiAnalysis: true,
        seoScore: true,
        recommendations: true
    },
    
    generator: {
        topicInput: true,
        contentTypeSelection: true,
        toneSelection: true,
        wordCountOptions: true,
        aiGeneration: true
    },
    
    analytics: {
        websiteMetrics: true,
        performanceScores: true,
        trafficAnalysis: true,
        seoInsights: true
    },
    
    keywords: {
        keywordInput: true,
        competitorAnalysis: true,
        difficultyScoring: true,
        opportunityIdentification: true
    },
    
    metaTags: {
        titleInput: true,
        descriptionInput: true,
        keywordsInput: true,
        serpPreview: true,
        htmlGeneration: true,
        characterCounting: true
    },
    
    schema: {
        schemaTypeSelection: true,
        dynamicFields: true,
        jsonLdGeneration: true,
        richSnippetPreview: true,
        validation: true
    },
    
    technical: {
        urlInput: true,
        auditTypeSelection: true,
        performanceScoring: true,
        issueIdentification: true,
        recommendations: true
    },
    
    chatbot: {
        messageInput: true,
        sendButton: true,
        messageHistory: true,
        aiResponses: true,
        inputValidation: true
    },
    
    settings: {
        apiKeyManagement: true,
        visibilityToggle: true,
        securityWarnings: true,
        saveFunction: true,
        clearFunction: true
    },
    
    // Core Functionality
    api: {
        geminiIntegration: true,
        errorHandling: true,
        loadingStates: true,
        responseProcessing: true
    },
    
    ui: {
        responsiveDesign: true,
        animations: true,
        notifications: true,
        formValidation: true,
        dataBinding: true
    },
    
    localization: {
        englishSupport: true,
        thaiSupport: true,
        dynamicSwitching: true,
        fontLoading: true
    }
};

// Verification functions
function verifyComponent(componentName, requirements) {
    console.group(`🔍 Verifying ${componentName.toUpperCase()} Component`);
    
    let passed = 0;
    let total = 0;
    
    for (const [requirement, expected] of Object.entries(requirements)) {
        total++;
        
        let exists = false;
        
        // Check based on requirement type
        switch (requirement) {
            case 'tabs':
                exists = expected.every(tab => document.querySelector(`[data-tab="${tab}"]`) !== null);
                break;
            case 'languageSwitcher':
                exists = document.querySelector('#flux-seo-language-select') !== null;
                break;
            case 'headerBadges':
                exists = document.querySelectorAll('.flux-seo-badge').length > 0;
                break;
            default:
                // Check for element existence based on naming convention
                const selector = `#${componentName}-${requirement.replace(/([A-Z])/g, '-$1').toLowerCase()}`;
                exists = document.querySelector(selector) !== null;
                
                // Alternative checks for common patterns
                if (!exists) {
                    const altSelectors = [
                        `[id*="${requirement}"]`,
                        `[class*="${requirement}"]`,
                        `[data-${requirement}]`
                    ];
                    
                    exists = altSelectors.some(sel => document.querySelector(sel) !== null);
                }
        }
        
        if (exists === expected) {
            console.log(`✅ ${requirement}: PASS`);
            passed++;
        } else {
            console.log(`❌ ${requirement}: FAIL`);
        }
    }
    
    const percentage = Math.round((passed / total) * 100);
    console.log(`📊 Score: ${passed}/${total} (${percentage}%)`);
    console.groupEnd();
    
    return { passed, total, percentage };
}

// Main verification function
function verifyAllComponents() {
    console.log('🚀 Starting Component Verification...\n');
    
    let totalPassed = 0;
    let totalRequirements = 0;
    const results = {};
    
    for (const [componentName, requirements] of Object.entries(componentChecklist)) {
        const result = verifyComponent(componentName, requirements);
        results[componentName] = result;
        totalPassed += result.passed;
        totalRequirements += result.total;
    }
    
    const overallPercentage = Math.round((totalPassed / totalRequirements) * 100);
    
    console.log('\n📋 VERIFICATION SUMMARY');
    console.log('='.repeat(50));
    
    for (const [componentName, result] of Object.entries(results)) {
        const status = result.percentage >= 80 ? '✅' : result.percentage >= 60 ? '⚠️' : '❌';
        console.log(`${status} ${componentName}: ${result.percentage}%`);
    }
    
    console.log('='.repeat(50));
    console.log(`🎯 OVERALL SCORE: ${totalPassed}/${totalRequirements} (${overallPercentage}%)`);
    
    if (overallPercentage >= 90) {
        console.log('🎉 EXCELLENT! All components are properly implemented.');
    } else if (overallPercentage >= 80) {
        console.log('👍 GOOD! Most components are working correctly.');
    } else if (overallPercentage >= 60) {
        console.log('⚠️ NEEDS WORK! Some components need attention.');
    } else {
        console.log('❌ CRITICAL! Major components are missing or broken.');
    }
    
    return results;
}

// Function verification
function verifyJavaScriptFunctions() {
    console.log('\n🔧 Verifying JavaScript Functions...');
    
    const requiredFunctions = [
        'init',
        'showTab',
        'handleLanguageChange',
        'handleMetaGeneration',
        'handleSchemaGeneration',
        'handleTechnicalAudit',
        'handleChatMessage',
        'saveApiKey',
        'clearApiKey',
        'callGeminiAPI',
        'showNotification'
    ];
    
    let functionsFound = 0;
    
    if (window.FluxSEOEnhanced) {
        requiredFunctions.forEach(funcName => {
            if (typeof window.FluxSEOEnhanced[funcName] === 'function') {
                console.log(`✅ ${funcName}: Available`);
                functionsFound++;
            } else {
                console.log(`❌ ${funcName}: Missing`);
            }
        });
    } else {
        console.log('❌ FluxSEOEnhanced object not found!');
    }
    
    const funcPercentage = Math.round((functionsFound / requiredFunctions.length) * 100);
    console.log(`📊 Functions: ${functionsFound}/${requiredFunctions.length} (${funcPercentage}%)`);
    
    return funcPercentage;
}

// CSS verification
function verifyCSSStyles() {
    console.log('\n🎨 Verifying CSS Styles...');
    
    const requiredClasses = [
        'flux-seo-enhanced-app',
        'flux-seo-nav-tab',
        'flux-seo-card',
        'flux-seo-btn',
        'flux-seo-input',
        'flux-seo-chatbot-container',
        'flux-seo-meta-display',
        'flux-seo-schema-fields',
        'flux-seo-technical-overview',
        'flux-seo-notification'
    ];
    
    let stylesFound = 0;
    
    requiredClasses.forEach(className => {
        const elements = document.querySelectorAll(`.${className}`);
        if (elements.length > 0) {
            console.log(`✅ .${className}: Found (${elements.length} elements)`);
            stylesFound++;
        } else {
            console.log(`❌ .${className}: Not found`);
        }
    });
    
    const stylePercentage = Math.round((stylesFound / requiredClasses.length) * 100);
    console.log(`📊 Styles: ${stylesFound}/${requiredClasses.length} (${stylePercentage}%)`);
    
    return stylePercentage;
}

// Run verification when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const componentResults = verifyAllComponents();
            const functionScore = verifyJavaScriptFunctions();
            const styleScore = verifyCSSStyles();
            
            const overallScore = Math.round((
                Object.values(componentResults).reduce((sum, r) => sum + r.percentage, 0) / Object.keys(componentResults).length +
                functionScore +
                styleScore
            ) / 3);
            
            console.log(`\n🏆 FINAL SCORE: ${overallScore}%`);
            
            // Store results for external access
            window.verificationResults = {
                components: componentResults,
                functions: functionScore,
                styles: styleScore,
                overall: overallScore
            };
        }, 1000);
    });
} else {
    // DOM already loaded
    setTimeout(() => {
        verifyAllComponents();
        verifyJavaScriptFunctions();
        verifyCSSStyles();
    }, 1000);
}
<?php
/**
 * Flux SEO Keyword Scoring Engine
 * High-accuracy keyword scoring model for professional SEO analytics
 */

class FluxSEOKeywordScoringEngine {
    
    private $scoring_weights;
    private $normalization_ranges;
    private $intent_multipliers;
    
    public function __construct() {
        $this->init_scoring_parameters();
    }
    
    private function init_scoring_parameters() {
        // Default scoring weights (can be customized per campaign)
        $this->scoring_weights = array(
            'search_volume' => 0.35,      // 35% - Traffic potential
            'keyword_difficulty' => 0.25, // 25% - Competition level
            'relevance' => 0.20,          // 20% - Business alignment
            'user_intent' => 0.10,        // 10% - Intent value
            'current_rank' => 0.05,       // 5% - Existing performance
            'ctr_potential' => 0.05       // 5% - Click-through potential
        );
        
        // Normalization ranges for different metrics
        $this->normalization_ranges = array(
            'search_volume' => array('min' => 0, 'max' => 50000),
            'keyword_difficulty' => array('min' => 0, 'max' => 100),
            'relevance' => array('min' => 1, 'max' => 10),
            'current_rank' => array('min' => 1, 'max' => 100),
            'ctr_potential' => array('min' => 1, 'max' => 10)
        );
        
        // Intent value multipliers
        $this->intent_multipliers = array(
            'transactional' => 1.0,       // Highest value
            'commercial' => 0.9,          // High commercial intent
            'informational' => 0.7,       // Medium value
            'navigational' => 0.5         // Lowest priority
        );
    }
    
    /**
     * Calculate comprehensive keyword score
     */
    public function calculate_keyword_score($keyword_data, $custom_weights = null) {
        $weights = $custom_weights ?: $this->scoring_weights;
        
        // Normalize all metrics to 0-10 scale
        $normalized = $this->normalize_metrics($keyword_data);
        
        // Calculate weighted score
        $score = 0;
        foreach ($weights as $metric => $weight) {
            if (isset($normalized[$metric])) {
                $score += $normalized[$metric] * $weight;
            }
        }
        
        // Apply intent multiplier
        if (isset($keyword_data['user_intent'])) {
            $intent_multiplier = $this->intent_multipliers[$keyword_data['user_intent']] ?? 0.7;
            $score *= $intent_multiplier;
        }
        
        // Apply trend boost (if trending upward)
        if (isset($keyword_data['trend_direction']) && $keyword_data['trend_direction'] === 'up') {
            $score *= 1.1; // 10% boost for trending keywords
        }
        
        // Apply seasonality factor
        if (isset($keyword_data['seasonality_score'])) {
            $score *= (1 + ($keyword_data['seasonality_score'] / 100));
        }
        
        return min(10, max(0, $score)); // Ensure score stays within 0-10 range
    }
    
    /**
     * Normalize metrics to 0-10 scale
     */
    private function normalize_metrics($data) {
        $normalized = array();
        
        // Search Volume (higher is better)
        if (isset($data['search_volume'])) {
            $normalized['search_volume'] = $this->normalize_value(
                $data['search_volume'],
                $this->normalization_ranges['search_volume']['min'],
                $this->normalization_ranges['search_volume']['max']
            );
        }
        
        // Keyword Difficulty (lower is better, so invert)
        if (isset($data['keyword_difficulty'])) {
            $inverted_kd = 100 - $data['keyword_difficulty'];
            $normalized['keyword_difficulty'] = $this->normalize_value(
                $inverted_kd,
                0,
                100
            );
        }
        
        // Relevance (already 1-10 scale)
        if (isset($data['relevance'])) {
            $normalized['relevance'] = min(10, max(0, $data['relevance']));
        }
        
        // Current Rank (lower rank number is better, so invert)
        if (isset($data['current_rank'])) {
            $inverted_rank = 101 - $data['current_rank']; // Invert ranking
            $normalized['current_rank'] = $this->normalize_value(
                $inverted_rank,
                1,
                100
            );
        }
        
        // CTR Potential (already 1-10 scale)
        if (isset($data['ctr_potential'])) {
            $normalized['ctr_potential'] = min(10, max(0, $data['ctr_potential']));
        }
        
        // User Intent (convert to numeric)
        if (isset($data['user_intent'])) {
            $intent_scores = array(
                'transactional' => 10,
                'commercial' => 8,
                'informational' => 6,
                'navigational' => 4
            );
            $normalized['user_intent'] = $intent_scores[$data['user_intent']] ?? 6;
        }
        
        return $normalized;
    }
    
    /**
     * Normalize a value to 0-10 scale
     */
    private function normalize_value($value, $min, $max) {
        if ($max <= $min) return 5; // Default middle value
        
        $normalized = (($value - $min) / ($max - $min)) * 10;
        return min(10, max(0, $normalized));
    }
    
    /**
     * Batch score multiple keywords
     */
    public function score_keyword_batch($keywords_data, $custom_weights = null) {
        $scored_keywords = array();
        
        foreach ($keywords_data as $keyword => $data) {
            $score = $this->calculate_keyword_score($data, $custom_weights);
            $scored_keywords[] = array(
                'keyword' => $keyword,
                'score' => round($score, 2),
                'tier' => $this->get_keyword_tier($score),
                'priority' => $this->get_priority_level($score, $data),
                'data' => $data
            );
        }
        
        // Sort by score (highest first)
        usort($scored_keywords, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        return $scored_keywords;
    }
    
    /**
     * Determine keyword tier based on score
     */
    private function get_keyword_tier($score) {
        if ($score >= 7.5) return 'Tier 1'; // High priority
        if ($score >= 5.5) return 'Tier 2'; // Medium priority
        return 'Tier 3'; // Low priority
    }
    
    /**
     * Get priority level with additional context
     */
    private function get_priority_level($score, $data) {
        $priority = 'Medium';
        
        if ($score >= 8.0) {
            $priority = 'Critical';
        } elseif ($score >= 7.0) {
            $priority = 'High';
        } elseif ($score >= 5.0) {
            $priority = 'Medium';
        } else {
            $priority = 'Low';
        }
        
        // Boost priority for low competition + high volume
        if (isset($data['keyword_difficulty']) && isset($data['search_volume'])) {
            if ($data['keyword_difficulty'] < 30 && $data['search_volume'] > 1000) {
                $priority = 'Quick Win';
            }
        }
        
        return $priority;
    }
    
    /**
     * Generate keyword opportunities analysis
     */
    public function analyze_keyword_opportunities($keywords_data) {
        $analysis = array(
            'quick_wins' => array(),
            'long_term_targets' => array(),
            'content_gaps' => array(),
            'trending_opportunities' => array(),
            'local_opportunities' => array()
        );
        
        foreach ($keywords_data as $keyword => $data) {
            $score = $this->calculate_keyword_score($data);
            
            // Quick wins: Low competition + decent volume
            if (isset($data['keyword_difficulty']) && $data['keyword_difficulty'] < 30 && 
                isset($data['search_volume']) && $data['search_volume'] > 500) {
                $analysis['quick_wins'][] = array(
                    'keyword' => $keyword,
                    'score' => $score,
                    'reason' => 'Low competition with good search volume'
                );
            }
            
            // Long-term targets: High volume + high competition
            if (isset($data['search_volume']) && $data['search_volume'] > 5000 && 
                isset($data['keyword_difficulty']) && $data['keyword_difficulty'] > 60) {
                $analysis['long_term_targets'][] = array(
                    'keyword' => $keyword,
                    'score' => $score,
                    'reason' => 'High volume opportunity requiring sustained effort'
                );
            }
            
            // Content gaps: High relevance + no current ranking
            if (isset($data['relevance']) && $data['relevance'] >= 8 && 
                (!isset($data['current_rank']) || $data['current_rank'] > 50)) {
                $analysis['content_gaps'][] = array(
                    'keyword' => $keyword,
                    'score' => $score,
                    'reason' => 'High relevance but missing content opportunity'
                );
            }
            
            // Trending opportunities
            if (isset($data['trend_direction']) && $data['trend_direction'] === 'up') {
                $analysis['trending_opportunities'][] = array(
                    'keyword' => $keyword,
                    'score' => $score,
                    'reason' => 'Rising search trend detected'
                );
            }
            
            // Local opportunities (if location data available)
            if (isset($data['local_volume']) && $data['local_volume'] > 100) {
                $analysis['local_opportunities'][] = array(
                    'keyword' => $keyword,
                    'score' => $score,
                    'reason' => 'Strong local search potential'
                );
            }
        }
        
        return $analysis;
    }
    
    /**
     * Generate content strategy recommendations
     */
    public function generate_content_strategy($scored_keywords) {
        $strategy = array(
            'immediate_actions' => array(),
            'content_calendar' => array(),
            'optimization_targets' => array(),
            'link_building_priorities' => array()
        );
        
        foreach ($scored_keywords as $item) {
            $keyword = $item['keyword'];
            $score = $item['score'];
            $data = $item['data'];
            
            // Immediate actions for high-scoring keywords
            if ($score >= 7.5) {
                $action = 'Create optimized content';
                if (isset($data['current_rank']) && $data['current_rank'] <= 20) {
                    $action = 'Optimize existing content';
                }
                
                $strategy['immediate_actions'][] = array(
                    'keyword' => $keyword,
                    'action' => $action,
                    'priority' => $item['priority'],
                    'estimated_timeline' => '1-2 weeks'
                );
            }
            
            // Content calendar suggestions
            if (isset($data['user_intent'])) {
                $content_type = $this->suggest_content_type($data['user_intent']);
                $strategy['content_calendar'][] = array(
                    'keyword' => $keyword,
                    'content_type' => $content_type,
                    'priority' => $item['tier'],
                    'estimated_effort' => $this->estimate_content_effort($score, $data)
                );
            }
            
            // Optimization targets for existing content
            if (isset($data['current_rank']) && $data['current_rank'] > 0 && $data['current_rank'] <= 50) {
                $strategy['optimization_targets'][] = array(
                    'keyword' => $keyword,
                    'current_rank' => $data['current_rank'],
                    'target_rank' => max(1, $data['current_rank'] - 10),
                    'optimization_focus' => $this->suggest_optimization_focus($data)
                );
            }
            
            // Link building priorities
            if ($score >= 6.0 && isset($data['keyword_difficulty']) && $data['keyword_difficulty'] > 40) {
                $strategy['link_building_priorities'][] = array(
                    'keyword' => $keyword,
                    'priority' => $item['priority'],
                    'target_da' => $this->calculate_target_domain_authority($data['keyword_difficulty']),
                    'link_strategy' => $this->suggest_link_strategy($data)
                );
            }
        }
        
        return $strategy;
    }
    
    /**
     * Suggest content type based on user intent
     */
    private function suggest_content_type($intent) {
        $content_types = array(
            'transactional' => 'Product/Service Page',
            'commercial' => 'Comparison/Review Article',
            'informational' => 'Blog Post/Guide',
            'navigational' => 'Landing Page'
        );
        
        return $content_types[$intent] ?? 'Blog Post';
    }
    
    /**
     * Estimate content creation effort
     */
    private function estimate_content_effort($score, $data) {
        $base_effort = 'Medium';
        
        if (isset($data['keyword_difficulty'])) {
            if ($data['keyword_difficulty'] > 70) {
                $base_effort = 'High';
            } elseif ($data['keyword_difficulty'] < 30) {
                $base_effort = 'Low';
            }
        }
        
        // Adjust based on content type
        if (isset($data['user_intent'])) {
            if ($data['user_intent'] === 'transactional') {
                $base_effort = 'High'; // Product pages need more work
            }
        }
        
        return $base_effort;
    }
    
    /**
     * Suggest optimization focus areas
     */
    private function suggest_optimization_focus($data) {
        $focus_areas = array();
        
        if (isset($data['keyword_difficulty']) && $data['keyword_difficulty'] > 50) {
            $focus_areas[] = 'Link Building';
        }
        
        if (isset($data['ctr_potential']) && $data['ctr_potential'] < 6) {
            $focus_areas[] = 'Title & Meta Optimization';
        }
        
        if (isset($data['relevance']) && $data['relevance'] < 8) {
            $focus_areas[] = 'Content Relevance';
        }
        
        if (empty($focus_areas)) {
            $focus_areas[] = 'Technical SEO';
        }
        
        return implode(', ', $focus_areas);
    }
    
    /**
     * Calculate target domain authority for link building
     */
    private function calculate_target_domain_authority($keyword_difficulty) {
        // Higher KD requires higher DA links
        if ($keyword_difficulty > 80) return '70+';
        if ($keyword_difficulty > 60) return '50+';
        if ($keyword_difficulty > 40) return '30+';
        return '20+';
    }
    
    /**
     * Suggest link building strategy
     */
    private function suggest_link_strategy($data) {
        $strategies = array();
        
        if (isset($data['user_intent'])) {
            switch ($data['user_intent']) {
                case 'informational':
                    $strategies[] = 'Guest Posting';
                    $strategies[] = 'Resource Page Links';
                    break;
                case 'commercial':
                    $strategies[] = 'Product Reviews';
                    $strategies[] = 'Comparison Mentions';
                    break;
                case 'transactional':
                    $strategies[] = 'Directory Listings';
                    $strategies[] = 'Partner Links';
                    break;
            }
        }
        
        if (empty($strategies)) {
            $strategies[] = 'General Link Building';
        }
        
        return implode(', ', $strategies);
    }
    
    /**
     * Validate scoring model accuracy
     */
    public function validate_model($historical_data, $actual_results) {
        $validation_results = array(
            'accuracy' => 0,
            'precision' => 0,
            'recall' => 0,
            'recommendations' => array()
        );
        
        $correct_predictions = 0;
        $total_predictions = count($historical_data);
        
        foreach ($historical_data as $keyword => $predicted_data) {
            if (isset($actual_results[$keyword])) {
                $predicted_score = $this->calculate_keyword_score($predicted_data);
                $actual_performance = $actual_results[$keyword]['performance_score'];
                
                // Consider prediction correct if within 20% margin
                $margin = abs($predicted_score - $actual_performance) / 10;
                if ($margin <= 0.2) {
                    $correct_predictions++;
                }
            }
        }
        
        $validation_results['accuracy'] = ($correct_predictions / $total_predictions) * 100;
        
        // Generate recommendations for model improvement
        if ($validation_results['accuracy'] < 80) {
            $validation_results['recommendations'][] = 'Consider adjusting keyword difficulty weight';
            $validation_results['recommendations'][] = 'Review relevance scoring methodology';
            $validation_results['recommendations'][] = 'Update search volume normalization ranges';
        }
        
        return $validation_results;
    }
    
    /**
     * Export scoring results for analysis
     */
    public function export_scoring_results($scored_keywords, $format = 'csv') {
        if ($format === 'csv') {
            return $this->export_to_csv($scored_keywords);
        } elseif ($format === 'json') {
            return json_encode($scored_keywords, JSON_PRETTY_PRINT);
        }
        
        return false;
    }
    
    /**
     * Export to CSV format
     */
    private function export_to_csv($scored_keywords) {
        $csv_data = "Keyword,Score,Tier,Priority,Search Volume,Keyword Difficulty,Relevance,User Intent,Current Rank,CTR Potential\n";
        
        foreach ($scored_keywords as $item) {
            $data = $item['data'];
            $csv_data .= sprintf(
                '"%s",%.2f,"%s","%s",%d,%d,%d,"%s",%d,%d' . "\n",
                $item['keyword'],
                $item['score'],
                $item['tier'],
                $item['priority'],
                $data['search_volume'] ?? 0,
                $data['keyword_difficulty'] ?? 0,
                $data['relevance'] ?? 0,
                $data['user_intent'] ?? 'unknown',
                $data['current_rank'] ?? 0,
                $data['ctr_potential'] ?? 0
            );
        }
        
        return $csv_data;
    }
    
    /**
     * Get scoring model configuration
     */
    public function get_model_config() {
        return array(
            'weights' => $this->scoring_weights,
            'normalization_ranges' => $this->normalization_ranges,
            'intent_multipliers' => $this->intent_multipliers,
            'version' => '1.0',
            'last_updated' => current_time('mysql')
        );
    }
    
    /**
     * Update scoring model configuration
     */
    public function update_model_config($new_weights = null, $new_ranges = null) {
        if ($new_weights) {
            $this->scoring_weights = $new_weights;
        }
        
        if ($new_ranges) {
            $this->normalization_ranges = $new_ranges;
        }
        
        // Save to database for persistence
        update_option('flux_seo_scoring_config', array(
            'weights' => $this->scoring_weights,
            'ranges' => $this->normalization_ranges,
            'updated' => current_time('mysql')
        ));
        
        return true;
    }
}
?>
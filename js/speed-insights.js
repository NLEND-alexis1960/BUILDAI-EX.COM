/**
 * Vercel Speed Insights Integration
 * 
 * This script initializes Vercel Speed Insights for tracking web vitals
 * and performance metrics on your website.
 * 
 * Usage:
 * Include this script in your HTML pages before the closing </body> tag:
 * <script src="/js/speed-insights.js"></script>
 */

import { injectSpeedInsights } from '@vercel/speed-insights';

// Initialize Speed Insights
injectSpeedInsights();

console.log('Vercel Speed Insights initialized');

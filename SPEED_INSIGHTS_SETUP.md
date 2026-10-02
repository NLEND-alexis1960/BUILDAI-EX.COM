# Vercel Speed Insights Setup Guide

This document explains how Vercel Speed Insights has been configured for this project.

## What is Vercel Speed Insights?

Vercel Speed Insights is a performance monitoring tool that tracks Web Vitals and other performance metrics for your website. It helps you understand how your site performs for real users.

## Installation

The `@vercel/speed-insights` package has been installed in this project:

```bash
npm install @vercel/speed-insights
```

## Integration Options

Since this project uses PHP, we've created multiple integration options:

### Option 1: HTML Snippet (Recommended for PHP Projects)

Add this snippet before the closing `</body>` tag in your HTML/PHP templates:

```html
<script>
  window.si = window.si || function () { (window.siq = window.siq || []).push(arguments); };
</script>
<script defer src="/_vercel/speed-insights/script.js"></script>
```

### Option 2: PHP Include File

Include the provided PHP file in your templates:

```php
<?php include __DIR__ . '/includes/speed-insights.php'; ?>
```

Place this before the closing `</body>` tag in your layout/footer template.

### Option 3: JavaScript Module (For Modern Frameworks)

If you're using a build system or bundler, you can import the module:

```javascript
import { injectSpeedInsights } from '@vercel/speed-insights';
injectSpeedInsights();
```

## Files Created

1. **`public/index.html`** - Example HTML page with Speed Insights integrated
2. **`public/speed-insights-snippet.html`** - Standalone HTML snippet for easy copying
3. **`includes/speed-insights.php`** - PHP include file for integration
4. **`js/speed-insights.js`** - JavaScript module for modern frameworks

## Usage in Existing PHP Views

To add Speed Insights to your existing PHP views (e.g., in the `views/` directory mentioned in your router):

1. **Create a footer template** (`views/footer.php`):
```php
    <?php include __DIR__ . '/../includes/speed-insights.php'; ?>
  </body>
</html>
```

2. **Include the footer in your views**:
```php
// In views/home.php, views/contact.php, etc.
<!DOCTYPE html>
<html>
<head>
    <title>Your Page</title>
</head>
<body>
    <!-- Your page content -->
    
    <?php include __DIR__ . '/footer.php'; ?>
```

## Viewing Analytics

Once deployed to Vercel and users visit your site:

1. Go to your [Vercel Dashboard](https://vercel.com/dashboard)
2. Select your project
3. Navigate to the "Speed Insights" tab
4. View real-time performance metrics including:
   - First Contentful Paint (FCP)
   - Largest Contentful Paint (LCP)
   - Cumulative Layout Shift (CLS)
   - First Input Delay (FID)
   - Time to First Byte (TTFB)

## Deployment

Deploy your project to Vercel:

```bash
vercel deploy
```

Or connect your Git repository for automatic deployments on every push.

## Testing Locally

Speed Insights only works on deployed Vercel projects. To test locally:

1. Deploy your project: `vercel deploy`
2. Visit the deployed URL
3. Check the Network tab in browser DevTools for the Speed Insights script
4. Wait for data to appear in your Vercel dashboard (may take a few minutes)

## Additional Configuration

### Custom Routes

If you need to track custom routes or events:

```javascript
// For SPAs or custom route tracking
window.si('route', '/custom-route');
```

### Framework-Specific Instructions

If you migrate to a JavaScript framework in the future:

- **Next.js**: Import from `@vercel/speed-insights/next`
- **React**: Import from `@vercel/speed-insights/react`
- **Vue**: Import from `@vercel/speed-insights/vue`
- **Svelte**: Import from `@vercel/speed-insights/sveltekit`

See the [official documentation](https://vercel.com/docs/speed-insights/quickstart) for more details.

## Troubleshooting

### Speed Insights not showing data

1. Ensure your site is deployed on Vercel
2. Wait at least 5-10 minutes after deployment for data to appear
3. Check that the script is loading in your browser's Network tab
4. Verify the script URL is correct: `/_vercel/speed-insights/script.js`

### Script not loading

1. Make sure the snippet is placed before the closing `</body>` tag
2. Check for any JavaScript errors in the console
3. Verify your Vercel deployment completed successfully

## Support

For more information, visit:
- [Vercel Speed Insights Documentation](https://vercel.com/docs/speed-insights)
- [Vercel Speed Insights Quickstart](https://vercel.com/docs/speed-insights/quickstart)

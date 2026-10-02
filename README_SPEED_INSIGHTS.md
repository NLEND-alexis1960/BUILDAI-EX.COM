# Vercel Speed Insights - Quick Start

This project has been configured with **Vercel Speed Insights** for real-time performance monitoring.

## ✅ What Has Been Installed

- ✅ `@vercel/speed-insights` package installed (v2.0.0)
- ✅ Speed Insights snippet configured
- ✅ Example HTML page with integration
- ✅ PHP include file for easy integration
- ✅ Documentation and setup guide

## 🚀 Quick Integration

### For HTML Pages

Add this snippet before the closing `</body>` tag:

```html
<script>
  window.si = window.si || function () { (window.siq = window.siq || []).push(arguments); };
</script>
<script defer src="/_vercel/speed-insights/script.js"></script>
```

### For PHP Templates

Include the provided PHP file:

```php
<?php include __DIR__ . '/includes/speed-insights.php'; ?>
```

## 📁 Files Created

1. **`public/index.html`** - Example page with Speed Insights
2. **`public/speed-insights-snippet.html`** - HTML snippet
3. **`includes/speed-insights.php`** - PHP include file
4. **`js/speed-insights.js`** - JavaScript module
5. **`vercel.json`** - Vercel deployment configuration
6. **`SPEED_INSIGHTS_SETUP.md`** - Detailed setup guide

## 📖 Next Steps

1. **Deploy to Vercel**: `vercel deploy` or connect your Git repository
2. **Wait for data**: Allow 5-10 minutes after deployment for metrics to appear
3. **View analytics**: Visit your Vercel Dashboard → Select project → Speed Insights tab

## 📚 Documentation

For detailed instructions, see:
- [`SPEED_INSIGHTS_SETUP.md`](./SPEED_INSIGHTS_SETUP.md) - Complete setup guide
- [Official Vercel Docs](https://vercel.com/docs/speed-insights/quickstart)

## 🔍 What Gets Tracked

Speed Insights automatically tracks these Web Vitals:
- **FCP** - First Contentful Paint
- **LCP** - Largest Contentful Paint
- **CLS** - Cumulative Layout Shift
- **FID** - First Input Delay
- **TTFB** - Time to First Byte

## ⚡ Features

- 🎯 Real user monitoring (RUM)
- 📊 Performance score tracking
- 🌍 Geographic performance breakdown
- 📱 Device-specific metrics
- 🔔 Performance alerts

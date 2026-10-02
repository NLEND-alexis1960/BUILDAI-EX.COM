<?php
/**
 * Vercel Speed Insights Integration for PHP
 * 
 * Include this file in your PHP templates to add Vercel Speed Insights tracking.
 * 
 * Usage in your PHP views:
 * <?php include __DIR__ . '/../includes/speed-insights.php'; ?>
 * 
 * Or include it in your layout/footer template before the closing </body> tag.
 */
?>
<!-- Vercel Speed Insights -->
<script>
  window.si = window.si || function () { (window.siq = window.siq || []).push(arguments); };
</script>
<script defer src="/_vercel/speed-insights/script.js"></script>
<!-- End Vercel Speed Insights -->

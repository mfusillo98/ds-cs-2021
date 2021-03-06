<?php
/**
 * Custom page for HTTP error
 */
?>
<!DOCTYPE>
<html lang="it">
<head>
    <title>404 Page not found - <?= PROJECT_NAME ?></title>
</head>

<body class="bg-light">
<h1>404 page not found</h1>
<p>
    This is a custom HTTP error page
</p>
<div>
    <a href="<?= routeFullUrl("/") ?>">Back to the homepage</a>
</div>
</body>
</html>

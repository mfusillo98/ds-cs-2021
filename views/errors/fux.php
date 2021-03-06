<?php
/**
 * Custom page for pretty-printed FuxResponse with "ERROR" status
 *
 * @var string $errorMessage Include the message of the FuxResponse
 */
?>
<!DOCTYPE>
<html lang="it">
<head>
    <title>General Error - <?= PROJECT_NAME ?></title>
</head>

<body class="bg-light">
<h1>This is custom page for application errors</h1>
<p>
    <?= $errorMessage ?>
</p>
<div>
    <a href="<?= routeFullUrl("/") ?>">Back to the homepage</a>
</div>
</body>
</html>

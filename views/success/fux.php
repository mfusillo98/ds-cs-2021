<?php
/**
 * Custom page for pretty-printed FuxResponse with "OK" status
 *
 * @var string $successMessage Include the message of the FuxResponse
 * @var string $forwardLink Link which will be displayed to the user to redirect him/her to a custom page
 * @var string $forwardLinkText The text which is associated with the $forwardLink
 */
?>
<!DOCTYPE>
<html lang="it">
<head>
    <title>General Success page - <?= PROJECT_NAME ?></title>
</head>

<body class="bg-light">
<h1>This is custom page for application success state</h1>
<p>
    <?= $successMessage ?>
</p>
<?php if (isset($forwardLink)) { ?>
    <a href="<?= $forwardLink ?>" class="btn btn-primary btn-lg"><?= $forwardLinkText ?></a>
<?php } ?>
</body>
</html>

<?php
/**
 * Show all saved queries
 *
 * @var array $queries
 */
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <title>Saved queries</title>
    <?= view('head') ?>
</head>
<body>

<?= view('navbar') ?>

<div class="container" style="margin-top: 100px">

    <h1 class="font-weight-bold">Saved queries list</h1>

    <?php if (!count($queries)){ ?>
        <p class="lead text-muted">
            You don't have any query stored yet!
        </p>
    <?php } ?>

    <?php foreach($queries as $q){ ?>
        <div class="py-3">
            <a href="<?= routeFullUrl("/view-query?query_id=$q[QUERY_ID]") ?>"><h4><?= $q['KEYWORDS'] ?></h4></a>
            <div class="text-muted">
                Contains <?= $q['RESULTSNUM'] ?> results
            </div>
        </div>
    <?php } ?>

</div>
</body>
</html>

<?php
/**
 * Show all web pages stored within a saved query
 *
 * @var array $query
 * @var array $results
 */
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <title>Explore query</title>
    <?= view('head') ?>
</head>
<body>

<?= view('navbar') ?>

<div class="container" style="margin-top: 100px">

    <h1 class="font-weight-bold">Exploring query #<?= $query['QUERY_ID'] ?></h1>

    <p class="lead text-muted">Keywords: <?= $query['KEYWORDS'] ?></p>

    <?php if (!count($results)){ ?>
        <p class="lead text-muted">
            You don't have any query stored yet!
        </p>
    <?php }else{ ?>
        <span class="text-muted"><?= count($results) ?> results found for this query</span>
    <?php }?>


    <?php foreach($results as $r){ ?>

        <div class="py-3">
            <h4><a href="<?= routeFullUrl("/view-page/$r[URL]") ?>"><?= $r['TITLE'] ?></a></h4>
            <div class="small text-success"><?= $r['URL'] ?></div>
            <div class="small text-muted">
                <?= substr($r['PAGE_CONTENT'],0, 128).(strlen($r['PAGE_CONTENT']) > 128 ? '...' : '') ?>
            </div>
        </div>

    <?php } ?>

</div>
</body>
</html>

<?php
/**
 * My View description here
 *
 * @var string $query This variable includes the page heading
 * @var array $results
 */
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <title>Example</title>
    <?= view('head') ?>
</head>
<body>

<?= view('navbar') ?>

<div class="container" style="margin-top: 100px">

    <form method="GET" action="<?= routeFullUrl('/search') ?>">
        <div class="input-group input-group-lg" style="max-width: 500px; width: 100%;">
            <input type="text" name="q" class="form-control" placeholder="Enter search terms" value="<?= htmlspecialchars($query) ?>"/>
            <div class="input-group-append">
                <button class="btn btn-light border">
                    Search
                </button>
            </div>
        </div>
    </form>

    <span class="text-muted"><?= count($results) ?> results found for this query</span>

    <form method="GET" action="<?= routeFullUrl('/save-search') ?>">
        <div class="input-group input-group-lg" style="max-width: 500px; width: 100%;">
            <input type="hidden" name="q" value="<?= htmlspecialchars($query) ?>"/>
            <div class="input-group-append">
                <button class="btn btn-link">
                    Save query and results
                </button>
            </div>
        </div>
    </form>

    <hr/>

    <?php foreach($results as $r){ ?>

        <div class="py-3">
            <h4><a href="<?= routeFullUrl("/view-page/".base64_encode($r['URL'])) ?>"><?= $r['TITLE'] ?></a></h4>
            <div class="small text-success"><?= $r['URL'] ?></div>
            <div class="small text-muted">
                <?= substr($r['PAGE_CONTENT'],0, 128).(strlen($r['PAGE_CONTENT']) > 128 ? '...' : '') ?>
            </div>
        </div>

    <?php } ?>

</div>
</body>
</html>

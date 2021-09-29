<?php
/**
 * Show web page info
 *
 * @var array $webpage
 * @var array $terms
 * @var array $media
 */
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <title>View web page</title>
    <?= view('head') ?>
</head>
<body>

<?= view('navbar') ?>

<div class="container" style="margin-top: 100px">

    <h1 class="font-weight-bold">View page <?= $webpage['URL'] ?></h1>
    <div>
        <?= $webpage['PAGE_CONTENT'] ?>
    </div>

    <div class="mt-5">
        <h2>Page terms</h2>
        <p>
            <?php foreach ($terms as $i => $t) { ?>
                <span><sup><?= $i ?></sup> <?= $t['TERM'] ?></span>
            <?php } ?>
        </p>
        <?php if (!count($terms)) { ?>
            <p class="lead text-muted">
                This page has no associated terms!
            </p>
        <?php } ?>
    </div>

    <div class="mt-5">
        <h2>Page media</h2>
        <ul>
            <?php foreach ($media as $m) { ?>
                <li><?= $media['URL'] ?> (<?= $media['MIME_TYPE'] ?>) <a href="<?= $media['URL'] ?>">Open</a></li>
            <?php } ?>
        </ul>
        <?php if (!count($media)) { ?>
            <p class="lead text-muted">
                This page has no associated media!
            </p>
        <?php } ?>
    </div>

    <div class="mt-5">
        <h2>Explore linked web pages (Op. 4)</h2>
        <form method="get" action="<?= routeFullUrl('/view-linked-pages') ?>">
            <input type="hidden" name="page_url" value="<?= base64_encode($webpage['URL']) ?>"/>
            <label>
                <input type="checkbox" name="backlink" value="1"/>
                Read also "incoming" hyperlinks (backlinks)
            </label><br/>
            <button type="submit" class="btn btn-link">
                Show linked web pages terms
            </button>
        </form>
    </div>
</div>
</body>
</html>

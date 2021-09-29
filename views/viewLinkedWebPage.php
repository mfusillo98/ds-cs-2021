<?php
/**
 * Show web page info
 *
 * @var array $webpage
 * @var array $terms
 * @var array{term: string, url: string, type: integer} $linked_terms type=0:outcoming link, type=1:incoming link
 */
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <title>Explore linked pages</title>
    <?= view('head') ?>
</head>
<body>

<?= view('navbar') ?>

<div class="container" style="margin-top: 100px">

    <h1 class="font-weight-bold">Master page: <?= $webpage['URL'] ?></h1>
    <div>
        <?= $webpage['PAGE_CONTENT'] ?>
    </div>

    <div class="mt-5">
        <h2>Page terms</h2>
        <p>
            <?php foreach ($terms as $i => $t) { ?>
                <span><sup><?= $i+1 ?></sup> <?= $t['TERM'] ?></span>
            <?php } ?>
        </p>
        <?php if (!count($terms)) { ?>
            <p class="lead text-muted">
                This page has no associated terms!
            </p>
        <?php } ?>
    </div>

    <div class="mt-5">
        <h1 class="font-weight-bold">Linked pages terms (<?= count(array_unique(array_column($linked_terms,'URL'))) ?> web pages)</h1>
        <?php $lastUrl = ""; $counter = 0;
        foreach ($linked_terms as $t) { $counter++; ?>
            <?php if ($lastUrl != $t['URL']) {
                $lastUrl = $t['URL'];
                $counter = 1;
                ?>
                <h3 class="mt-3">Page: <?= $t['URL'] ?></h3>
                <div class="text-muted">
                <a href="<?= routeFullUrl('/view-page/'.base64_encode($t['URL'])) ?>">View page</a> | Link type: <?= $t['LINK_TYPE'] == 0 ? 'Forward link ->' : 'Backlink <-'  ?>

                </div>
            <?php } ?>
            <span><sup><?= $counter ?></sup> <?= $t['TERM'] ?></span>
        <?php } ?>
    </div>
</div>
</body>
</html>

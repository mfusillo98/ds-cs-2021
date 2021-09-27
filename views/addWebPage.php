<?php
/**
 *
 * @var string | null $success
 */
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <title>Add web page</title>
    <?= view('head') ?>
</head>
<body>

<?= view('navbar') ?>

<div class="container" style="margin-top: 100px">

    <?php if(isset($success)){ ?>
        <div class="alert alert-success">
            <?= $success ?>
        </div>
    <?php } ?>
    <h1 class="font-weight-bold">Add new web page</h1>
    <form method="POST" action="<?= routeFullUrl('/add-web-page') ?>">
        <div class="form-group">
            <label>Page URL</label>
            <input type="text" name="url" class="form-control"/>
            <div class="small text-muted">
                If the URL already exists, the web page will be updated
            </div>
        </div>
        <div class="form-group">
            <label>Page title</label>
            <input type="text" name="title" class="form-control"/>
        </div>
        <div class="form-group">
            <label>Page content (text only)</label>
            <textarea name="page_content" class="form-control"></textarea>
        </div>
        <button class="btn btn-primary">
            Save web page
        </button>
    </form>

</div>
</body>
</html>

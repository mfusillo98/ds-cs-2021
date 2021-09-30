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
<body class="bg-light">

<?= view('navbar') ?>

<div class="container" style="margin-top: 100px">

    <?php if (isset($success)) { ?>
        <div class="alert alert-success">
            <?= $success ?>
        </div>
    <?php } ?>
    <div class="card border-0 shadow">
        <div class="card-body">
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
                <div class="form-group">
                    <h3>Media references</h3>
                    <div id="media-container"></div>
                    <button class="btn btn-link" type="button" onclick="addMedia()">+ Add new media</button>
                </div>
                <button class="btn btn-primary">
                    Save web page
                </button>
            </form>

        </div>
    </div>

    <div id="media-template" class="d-none">
        <div class="form-group">
            <div class="row">
                <div class="col-6">
                    <input type="text" name="media_url[]" class="form-control" placeholder="URL"/>
                </div>
                <div class="col-3">
                    <input type="text" name="media_mime[]" class="form-control" placeholder="Mime type"/>
                </div>
                <div class="col-3">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeMedia(this)">Remove</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function addMedia(){
            const html = document.getElementById('media-template').innerHTML;
            const el = document.createElement('div');
            el.innerHTML = html;
            document.getElementById('media-container').appendChild(el);
        }

        function removeMedia(btnEl){
            const formGroupEl = btnEl.parentElement.parentElement.parentElement;
            formGroupEl.parentElement.removeChild(formGroupEl);
        }
    </script>
</div>
</body>
</html>

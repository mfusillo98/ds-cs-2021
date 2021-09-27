<?php
/**
 * My View description here
 *
 * @var string $myViewParameter This variable includes the page heading
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

<div class="container">
    <div class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="w-100">
            <h1 class="text-center font-weight-bold">Google Police</h1>
            <div class="d-flex align-items-center justify-content-center w-100">
                <form method="GET" action="<?= routeFullUrl('/search') ?>">
                    <div class="input-group input-group-lg" style="max-width: 500px; width: 100%;">
                        <input type="text" name="q" class="form-control" placeholder="Inserisci parole chiave"/>
                        <div class="input-group-append">
                            <button class="btn btn-light border">
                                Cerca
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="small text-muted text-center">Operation 2</div>
        </div>
    </div>
</div>
</body>
</html>

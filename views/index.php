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
    <div class="d-flex align-items-center justify-content-center" style="min-height: 90vh;">
        <div class="w-100">
            <img src="<?= asset('img/logo.png') ?>" alt="Google Police logo" class="img-fluid d-block mb-3 mx-auto" style="height: 150px;"/>
            <div class="d-flex align-items-center justify-content-center w-100">
                <form method="GET" action="<?= routeFullUrl('/search') ?>">
                    <div class="input-group input-group-lg" style="max-width: 700px; width: 100%;">
                        <input type="text" name="q" class="form-control" placeholder="Enter search terms"/>
                        <div class="input-group-append">
                            <button class="btn btn-light border">
                                Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="small text-muted text-center">Operation 2</div>
        </div>
    </div>
</div>

<div class="fixed-bottom text-center bg-light border-top py-3">
    Matteo Fusillo - Matr. 747822 - Database System Lab. - A.Y. 2020/2021
</div>
</body>
</html>

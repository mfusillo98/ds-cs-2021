<nav class="navbar navbar-expand-lg navbar-light border-bottom bg-white">
    <a class="navbar-brand" href="<?= routeFullUrl('') ?>">
        <img src="<?= asset('img/logo.png') ?>" alt="Google Police logo" style="height: 35px;"/>
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02"
            aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
        <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
            <li class="nav-item mx-md-2">
                <a class="nav-link" href="<?= routeFullUrl('/') ?>">
                    Search
                </a>
            </li>
            <li class="nav-item mx-md-2">
                <a class="nav-link" href="<?= routeFullUrl('/add-web-page') ?>">
                    Add web page
                </a>
            </li>
            <li class="nav-item mx-md-2">
                <a class="nav-link" href="<?= routeFullUrl('/saved-queries') ?>">
                    Saved queries
                </a>
            </li>
        </ul>
    </div>

</nav>
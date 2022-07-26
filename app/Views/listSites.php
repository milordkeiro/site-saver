<div class="row" data-masonry='{"percentPosition": true }'>
    <?php
    foreach($sites as $site) {
    ?>
    <div class="col-sm-6 col-lg-4 mb-4">
        <a href="/site/<?= esc($site->idsite)?>" class="card text-decoration-none">
            <div class="card-body">
            <h5 class="card-title"><?= esc($site->domain)?></h5>
            <p class="card-text"><?= esc($site->nick)?></p>
            </div>
      </a>
    </div>
    <?php } ?>
</div>
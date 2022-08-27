<div class="row mb-4">
        <div class="col">
        <button type="button" class="btn btn-success" title="Create site" data-bs-toggle="modal" data-bs-target="#createSiteModal">
            <span class="material-symbols-outlined">add</span> New site
        </button>
        </div>
    </div>
<div class="row" data-masonry='{"percentPosition": true }'>
    <?php
    foreach($sites as $site) {
    ?>
    <div class="col-sm-6 col-lg-4 mb-4">
        <a href="/site/<?= esc($site->idsite)?>" class="card text-decoration-none">
            <div class="card-body">
            <h3 class="card-title"><?= esc($site->titleadmin)?></h3>    
            <h5 class="card-title"><?= esc($site->domain)?></h5>
            <p class="card-text"><?= esc($site->nick)?></p>
            </div>
      </a>
    </div>
    <?php } ?>
</div>

<!-- Modal to create site -->
<div class="modal fade" id="createSiteModal" tabindex="-1" aria-labelledby="createSiteModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createSiteModalLabel">create info site</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="createSiteForm" action="/site/save" method="post">
        <input type="hidden" name="idsite" value="" />    
        <div class="mb-3">
            <label for="titleadminInput1" class="form-label">Title admin:</label>
            <input type="text" class="form-control" id="titleadminInput1" name="titleadmin" value="" placeholder="" required>
        </div>
        <div class="mb-3">
            <label for="domainInput1" class="form-label">Domain:</label>
            <input type="text" class="form-control" id="domainInput1" name="domain" value="" placeholder="https://...." required>
        </div>
        <div class="mb-3">
            <label for="titleInput1" class="form-label">Title of the site:</label>
            <input type="text" class="form-control" id="titleInput1" name="title" value="" placeholder="" require>
        </div>
        <div class="mb-3">
            <label for="usernameInput1" class="form-label">Username of N.nu:</label>
            <input type="text" class="form-control" id="usernameInput1" name="nick" value="" placeholder="">
        </div>
        <h5>Searching by:</h5>
        <div class="mb-3">
            <label for="tagInput1" class="form-label">Tag:</label>
            <input type="text" class="form-control" id="tagInput1" name="tagcontent" value="" placeholder="">
        </div>
        <div class="mb-3">
            <label for="classInput1" class="form-label">Class:</label>
            <input type="text" class="form-control" id="classInput1" name="classcontent" value="" placeholder="">
        </div>
        <div class="mb-3">
            <label for="idInput1" class="form-label">Id:</label>
            <input type="text" class="form-control" id="idInput1" name="idcontent" value="" placeholder="" >
        </div> 
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" form="createSiteForm">Save changes</button>
      </div>
    </div>
  </div>
</div>
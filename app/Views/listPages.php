<div class="row">
    <div class="col">
        <a href="/site/<?= esc($site->idsite)?>/upload-xml" class="btn btn-primary" title="Subir XML de las subpaginas del sitio">
            <span class="material-symbols-outlined float-start me-2">file_upload</span> Subir XML
        </a>
        |
        <button type="button" class="btn btn-primary" title="Editar info del sitio" data-bs-toggle="modal" data-bs-target="#editSiteModal">
        <span class="material-symbols-outlined float-start me-2">edit_document</span> Editar Sitio
        </button>
        |
        <a href="/download-xml/<?= esc($site->idsite)?>" class="btn btn-success" title="Generar XML de las subpaginas">
            <span class="material-symbols-outlined float-start me-2">file_download</span> Download XML
        </a>
        
    </div>
</div>

<div class="row mt-4">
    <div class="col">
    <table class="table table-hover">
        <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col">Path</th>
            <th scope="col">Title</th>
            <th scope="col">Description</th>
            <th scope="col">Last Update</th>
            <th scope="col">Priority</th>
            <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $index = 0;
            foreach($pages as $page){
            ?>
            <tr>
                <th scope="row"><?= esc ($index + 1)?></th>
                <td><?= esc($page->path)?></td>
                <td><?= esc($page->title)?></td>
                <td><?= esc($page->descriptionpage)?></td>
                <td><?= esc($page->lastmod)?></td>
                <td><?= esc($page->priority)?></td>
                <td><a href="/page/<?=esc($page->idpage)?>" class="btn"><span class="material-symbols-outlined float-start">open_in_new</span></a></td>
            </tr>
            <?php $index++; } ?>
        </tbody>

  </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editSiteModal" tabindex="-1" aria-labelledby="editSiteModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editSiteModalLabel">Edit info site</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editSiteForm" action="/site/save" method="post">
        <input type="hidden" name="idsite" value="<?=esc($site->idsite)?>" />    
        <div class="mb-3">
            <label for="domainInput1" class="form-label">Domain:</label>
            <input type="text" class="form-control" id="domainInput1" name="domain" value="<?=esc($site->domain)?>" placeholder="https://...." required>
        </div>
        <div class="mb-3">
            <label for="titleInput1" class="form-label">Title of the site:</label>
            <input type="text" class="form-control" id="titleInput1" name="title" value="<?=esc($site->title)?>" placeholder="" require>
        </div>
        <div class="mb-3">
            <label for="usernameInput1" class="form-label">Username of N.nu:</label>
            <input type="text" class="form-control" id="usernameInput1" name="nick" value="<?=esc($site->nick)?>" placeholder="">
        </div>
        <h5>Searching by:</h5>
        <div class="mb-3">
            <label for="tagInput1" class="form-label">Tag:</label>
            <input type="text" class="form-control" id="tagInput1" name="tagcontent" value="<?=esc($site->tagcontent)?>" placeholder="">
        </div>
        <div class="mb-3">
            <label for="classInput1" class="form-label">Class:</label>
            <input type="text" class="form-control" id="classInput1" name="classcontent" value="<?=esc($site->classcontent)?>" placeholder="">
        </div>
        <div class="mb-3">
            <label for="idInput1" class="form-label">Id:</label>
            <input type="text" class="form-control" id="idInput1" name="idcontent" value="<?=esc($site->idcontent)?>" placeholder="" >
        </div> 
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" form="editSiteForm">Save changes</button>
      </div>
    </div>
  </div>
</div>
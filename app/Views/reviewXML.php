<div class="row">
    <div class="col">
        <?php
        if(!isset($error)){

        ?>
        
        <div class="mb-3">
        <a href="/site/<?= esc($site->idsite)?>/upload-xml" class="btn btn-primary" title="Subir XML de las subpaginas del sitio">
            <span class="material-symbols-outlined float-start me-2">file_upload</span> Subir XML
        </a>
        </div>

        <table class="table">
        <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col">loc</th>
            <th scope="col">LastMond</th>
            <th scope="col">Priority</th>
            <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
                //var_dump($sitemapXml);
                // echo sizeof($sitemapXml->url);
                // echo $sitemapXml->url[0]->loc;
                $index=0;
                foreach($sitemapXml as $url){
            ?>

            <tr>
            <th scope="row"><?=esc($index+1)?></th>
            <td><?=esc($sitemapXml[$index]->loc)?></td>
            <td><?=esc($sitemapXml[$index]->lastmod)?></td>
            <td><?=esc($sitemapXml[$index]->priority)?></td>
            <td>
                <span id="action<?=esc($index+1)?>">
                <?php if($sitemapXml[$index]->isSaved) {?>
                    <span class="material-symbols-outlined text-success font-bold">task_alt</span>
                <?php } else {?>
                    <a href="javascript: savePage('<?=esc($index+1)?>','<?=esc($sitemapXml[$index]->loc)?>','<?=esc($sitemapXml[$index]->lastmod)?>','
                                                   <?=esc($sitemapXml[$index]->priority)?>')" >
                        <span class="material-symbols-outlined">file_upload</span></a>
                <?php } ?>
                </span>
                <span id="pending<?=esc($index+1)?>" style="display:none"><span class="material-symbols-outlined text-muted">pending</span></span>
            </td>
            </tr>

            <?php $index++; } ?>
           
        </tbody>
        </table>
        
        <?php } else{?>
            <div class="alert alert-danger" role="alert">
            <?=esc($error)?>
            </div> 
        <?php  } ?>
    </div>
</div>

<script>

    var idsite = <?=esc($site->idsite)?>;

    

function savePage(index,loc,lastmod, priority){
        var url = '/site/'+idsite+'/save-page';
        var dataPage = {'loc': loc, 'lastmod': lastmod, 'priority':priority};
        $("#action"+index).hide();
        $("#pending"+index).show();
        $.post( url, dataPage)
                .done(function( data ) {
                    //alert( "Data Loaded: " + data.message );
                    if(data.success)
                    {
                        $("#action"+index).html('<span class="material-symbols-outlined text-success font-bold">task_alt</span>');
                        $("#pending"+index).hide();
                        $("#action"+index).show();
                    }
                })
                .fail(function() {
                    alert( "Error: No se pudo guardar la pagina" );
                    $("#action"+index).show();
                    $("#pending"+index).hide();
                });
    }
</script>
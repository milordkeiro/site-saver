<div class="row mb-2">
    <div class="col">
    
        <textarea class="content-page" disabled>
            
                <?php echo $page->content; ?>
            
        </textarea>
    
    </div>
</div>
<div class="row">
    <div class="col">
        <h2>List of Imges</h2>
        <table class="table table-hover">
            <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">URL</th>
            <th scope="col">URL N.nu</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $index = 0;
            foreach($listImages as $image){
            ?>
            <tr>
                <th scope="row"><?=esc($index + 1)?></th>
                <td><?=esc($image->name)?></td>
                <td><?=esc($image->url)?></td>
                <td><?=esc($image->urlnnu)?></td>
            </tr>
            <?php $index++; }?>
        </tbody>

        </table>
    </div>
</div>
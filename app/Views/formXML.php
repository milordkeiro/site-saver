<div class="row">
    <div class="col">
        <form action="/site/<?=esc($site->idsite)?>/scan-xml" method="post">
        <div class="mb-3">
            <label for="exampleFormControlTextarea1" class="form-label">Sitemap XML</label>
            <textarea name="sitemapXml" class="form-control editorXML" id="sitemapXml"></textarea>
        </div>
        <div class="mb-3">
            <input type="hidden" name="idsite" value="<?=esc($site->idsite)?>">
            <button type="submit" class="btn btn-primary">Scan</button>
        </div>
        </form>
    </div>
</div>
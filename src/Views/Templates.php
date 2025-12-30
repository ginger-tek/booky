<?php
/** 
 * @var \App\Models\TemplateListItem[] $items 
 * @var object[] $clients
 */
?>
<dialog id="newTemplate">
  <article>
    <header><b>New Template</b></header>
    <form method="POST" action="/templates">
      <label>Name
        <input type="text" name="name" autocomplete="off" autofocus required>
      </label>
      <div class="flex fill">
        <button type="button" class="secondary" onclick="newTemplate.close()">Cancel</button>
        <button type="submit">Create</button>
      </div>
    </form>
  </article>
</dialog>
<div class="flex spread bottom-spacing">
  <h2>Templates</h2>
  <button onclick="this.blur();newTemplate.showModal()"><i class="bi bi-file-earmark-plus-fill"></i> New
    Template</button>
</div>
<?php if (empty($items)): ?>
  <div align="center">
    <br>
    <div style="font-size:2em"><i class="bi bi-slash-square"></i></div>
    <p>No templates yet</p>
  </div>
<?php else: ?>
  <input type="search" id="search" placeholder="Search templates...">
  <div id="templates">
    <?php foreach ($items as $item): ?>
      <article>
        <div class="flex spread">
          <h3 class="bottom-clear"><?= $item->isDefault == 1 ? '<i class="bi bi-star-fill"></i> ' : '' ?><?= $item->name ?></h3>
          <div>Created <?= \App\Utils::slashDate($item->created) ?></div>
        </div>
        <a href="/templates/<?= $item->id ?>" class="stretch"></a>
      </article>
    <?php endforeach ?>
  </div>
  <script type="module">
    import { initFilterChildElements } from '/assets/utils.js'
    initFilterChildElements(templates, search, 'No templates found');
  </script>
<?php endif ?>
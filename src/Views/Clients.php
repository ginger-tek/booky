<?php
/**
 * @var object[] $clients
 */
?>
<dialog id="newClient">
  <article>
    <header><b>New Client</b></header>
    <form method="POST" action="/clients">
      <label>Name
        <input type="text" autocomplete="off" name="name" id="name" required>
      </label>
      <div class="flex fill">
        <button type="button" class="secondary" onclick="newClient.close()">Cancel</button>
        <button type="submit">Create</button>
      </div>
    </form>
  </article>
</dialog>
<div class="flex spread bottom-spacing">
  <h2>Clients</h2>
  <button onclick="this.blur();newClient.showModal()"><i class="bi bi-plus"></i> New Client</button>
</div>
<?php if (empty($items)): ?>
  <div align="center">
    <br>
    <div style="font-size:2em"><i class="bi bi-slash-square"></i></div>
    <p>No clients yet</p>
  </div>
<?php else: ?>
  <input type="search" id="search" placeholder="Search clients...">
  <div id="clients">
    <?php foreach ($items as $item): ?>
      <article>
        <header>
          <h3 class="bottom-clear"><?= $item->name ?></h3>
          <a href="/clients/<?= $item->id ?>" class="stretch"></a>
        </header>
        <div class="flex fill">
          <a role="button" href="mailto:<?= $item->email ?>"><i class="bi bi-envelope"></i> Email</a>
          <?php if (!empty($item->phone)): ?>
            <a role="button" href="tel:<?= $item->phone ?>"><i class="bi bi-telephone"></i> Call</a>
          <?php endif ?>
          <?php if (!empty($item->address)): ?>
            <a role="button" href="https://maps.google.com/?q=<?= urlencode($item->address) ?>"><i class="bi bi-geo-alt"></i>
              Map</a>
          <?php endif ?>
        </div>
      </article>
    <?php endforeach ?>
  </div>
  <script type="module">
    import { initFilterChildElements } from '/assets/utils.js'
    initFilterChildElements(clients, search, 'No clients found');
  </script>
<?php endif ?>
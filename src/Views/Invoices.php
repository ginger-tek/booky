<h2>Invoices</h2>
<?php foreach($items as $item): ?>
<article>
<h4><?= $item->summary ?></h4>
<?= $item->clientName ?>
<a href="/invoices/<?= $item->id ?>" class="stretch"></a>
</article>
<?php endforeach ?>

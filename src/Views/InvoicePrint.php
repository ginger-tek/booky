<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice #<?= $invoice->id ?></title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0 auto;
      aspect-ratio: 8.5 / 11;
    }

    h1 {
      text-align: center;
    }

    table {
      width: 500px;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th,
    td {
      border: 1px solid #000;
      padding: 8px;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
    }
  </style>
</head>

<body>
  <table>
    <tr>
      <th>Invoice #</th>
      <td><?= $invoice->id ?></td>
    </tr>
    <tr>
      <th>Due Date</th>
      <td><?= $invoice->dueDate ? date('n/j/Y', strtotime($invoice->dueDate)) : 'N/A' ?></td>
    </tr>
    <tr>
      <th>Client</th>
      <td><?= $invoice->clientName ?></td>
    </tr>
  </table>
  <table>
    <tr>
      <th>Summary</th>
      <td><?= htmlspecialchars($invoice->summary) ?></td>
    </tr>
    <tr>
      <th>Details</th>
      <td><?= nl2br(htmlspecialchars($invoice->details)) ?></td>
    </tr>
  </table>
  <table>
    <tr>
      <th colspan="2">Itemization</th>
    </tr>
    <?php foreach ($items as $item): ?>
      <tr>
        <td><?= htmlspecialchars($item->summary) ?></td>
        <td style="text-align:right"><?= \App\Utils::currency($item->amount) ?></td>
      </tr>
    <?php endforeach ?>
    <tr>
      <th style="text-align:right">Amount Due</th>
      <td style="text-align:right"><b><?= \App\Utils::currency($subtotal) ?></b></td>
    </tr>
  </table>
</body>

</html>
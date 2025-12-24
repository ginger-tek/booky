<?php

namespace App;

class Utils
{
  public static function createId(): string
  {
    return ucwords(uniqid());
  }

  public static function slashDate(string|null $date = ''): string
  {
    if (!$date)
      return '';
    return (new \DateTime($date))->format('n/j/Y');
  }

  public static function currency(int|float|null $amount = 0): string
  {
    $formatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
    return $formatter->format($amount);
  }

  public static function renderStrategy(string $view, array $ctx, \GingerTek\Routy $app): void
  {
    ob_start();
    $user = $app->getCtx('user');
    $ctx['title'] ??= join(' ', preg_split('/(?=[A-Z])/', $view));
    extract([
      ...$ctx,
      'isAuthed' => !!$user,
      'user' => $user
    ], EXTR_OVERWRITE);
    $view = "../src/Views/$view.php";
    include "../src/Views/_Layout.php";
    exit(ob_get_clean());
  }

  public static function reqSet(string $key, mixed $value): void
  {
    $_REQUEST[$key] = $value;
  }

  public static function reqGet(string $key): mixed
  {
    return $_REQUEST[$key] ?? null;
  }

  public static function parseTemplate(string $template, array $ctx): string
  {
    ['invoice' => $invoice, 'client' => $client, 'items' => $items, 'settings' => $settings] = $ctx;
    return preg_replace_callback('/{{\s*(?<key>[\w.]+)\s*(?:\|\s*(?<mod>\w+)\s*(?::\s*(?<arg>.*?))?)?\s*}}/sm', function ($matches) use ($invoice, $client, $items, $settings) {
      $key = $matches['key'];
      $mod = $matches['mod'] ?? null;
      $arg = $matches['arg'] ?? null;
      $val = match ($key) {
        'invoice.id' => $invoice->id,
        'invoice.summary' => $invoice->summary ?? '',
        'invoice.details' => $invoice->details ?? '',
        'invoice.created' => $invoice->created,
        'invoice.dueDate' => $invoice->dueDate,
        'invoice.paidDate' => $invoice->paidDate,
        'invoice.amountDue' => $invoice->amountDue,
        'invoice.amountPaid' => $invoice->amountPaid,
        'invoice.subtotal' => $invoice->subtotal ?? '',
        'client.name' => $client->name ?? '',
        'client.email' => $client->email ?? '',
        'client.phone' => $client->phone ?? '',
        'client.address' => $client->address ?? '',
        'itemization' => (function ($items) use ($invoice) {
          $rows = '';
          foreach ($items as $item)
            $rows .= "<tr><td>" . htmlspecialchars($item->summary) . "</td>
                <td style=\"text-align:right\">" . \App\Utils::currency($item->amount) . "</td></tr>";
          return "<table><tbody>
              <tr><th colspan=\"2\">Itemization</th></tr>
              {$rows}
              <tr>
                <th style=\"text-align:right\">Amount Total</th>
                <td style=\"text-align:right;font-weight:bold\">" . \App\Utils::currency($invoice->amountDue) . "</td>
              </tr>
            </tbody></table>";
        })($items),
        'company' => $settings['company'] ?? '',
        'website' => $settings['website'] ?? '',
        'email' => $settings['email'] ?? '',
        'phone' => $settings['phone'] ?? '',
        'address' => $settings['address'] ?? '',
        default => ''
      };
      return match ($mod) {
        'ucase' => strtoupper($val),
        'lcase' => strtolower($val),
        'date' => date($arg ?? 'F j, Y', strtotime($val)),
        'currency' => \App\Utils::currency((float) $val),
        'raw' => htmlspecialchars($val),
        null => $val,
        default => $val
      };
    }, $template);
  }
}

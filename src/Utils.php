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
    return preg_replace_callback(
      '/{{\s*(?<key>[\w\.\/:&?]+)\s*(?:\|\s*(?<mod>\w+)\s*(?::\s*(?<arg>.*?))?)?\s*}}/sm',
      function ($matches) use ($invoice, $client, $items, $settings) {
        $key = $matches['key'];
        $mod = $matches['mod'] ?? null;
        $arg = $matches['arg'] ?? null;
        $val = match ($key) {
          'invoice.id' => $invoice->id,
          'invoice.summary' => $invoice->summary,
          'invoice.details' => $invoice->details,
          'invoice.created' => $invoice->created,
          'invoice.dueDate' => $invoice->dueDate,
          'invoice.paidDate' => $invoice->paidDate,
          'invoice.amountDue' => $invoice->amountDue,
          'invoice.amountPaid' => $invoice->amountPaid,
          'invoice.subtotal' => $invoice->subtotal,
          'itemizationsTable' => (function ($items) use ($invoice) {
              $rows = '';
              foreach ($items as $item)
                $rows .= "<tr><td>" . htmlspecialchars($item->summary) . "</td>
                <td style=\"text-align:right\">" . \App\Utils::currency($item->amount) . "</td></tr>";
              return "<table><tbody>
              <tr><th colspan=\"2\" style=\"text-align:left\">Itemizations</th></tr>
              {$rows}
              <tr>
                <th style=\"text-align:right\">Amount Total</th>
                <td style=\"text-align:right;font-weight:bold\">" . \App\Utils::currency($invoice->amountDue) . "</td>
              </tr>
            </tbody></table>";
            })($items),
          'client.name' => $client->name ?? '',
          'client.email' => $client->email ?? '',
          'client.phone' => $client->phone ?? '',
          'client.address' => $client->address ?? '',
          'company' => $settings['company'] ?? '',
          'website' => $settings['website'] ?? '',
          'email' => $settings['email'] ?? '',
          'phone' => $settings['phone'] ?? '',
          'address' => $settings['address'] ?? '',
          default => $key ?? ''
        };
        return match ($mod) {
          'ucase' => strtoupper($val),
          'lcase' => strtolower($val),
          'pre' => "<div style=\"white-space:pre-wrap\">{$val}</div>",
          'date' => date($arg ?? 'M j, Y', strtotime($val ?? 'now')),
          'currency' => \App\Utils::currency((float) $val),
          'link' => "<a href=\"https://{$val}\" target=\"_blank\">" . ($arg ?? $val) . "</a>",
          'mailto' => "<a href=\"mailto:{$val}\" target=\"_blank\">" . ($arg ?? $val) . "</a>",
          'tel' => "<a href=\"tel:{$val}\" target=\"_blank\">" . ($arg ?? (!preg_match('/^\(\d{3}\) \d{3}-\d{4}$/', $val)
            ? preg_replace('/(\d{3})-?(\d{3})-?(\d{4})/', '($1) $2-$3', $val)
            : $val)) . "</a>",
          null => $val,
          default => $val
        };
      },
      $template
    );
  }
}

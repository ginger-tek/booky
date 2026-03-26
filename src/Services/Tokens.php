<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Tokens
{
  /**
   * @return array{token: string, exp: int}
   */
  public static function encode(array $data, ?int $exp = null): array
  {
    $exp ??= getenv('TOKEN_EXP_SECONDS') ?: 3600;
    $exp = time() + $exp;
    $token = JWT::encode([
      'iat' => time(),
      'exp' => $exp,
      ...$data
    ], getenv('TOKEN_SECRET'), 'HS256');
    return [$token, $exp];
  }

  public static function decode(string $token): ?object
  {
    try {
      return JWT::decode($token, new Key(getenv('TOKEN_SECRET'), 'HS256'));
    } catch (\Exception $ex) {
      return null;
    }
  }
}

<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Tokens
{
  private static string $secret = 'potatodemon';

  /**
  * @return array{token: string, exp: int}
  */
  public static function encode(array $data, ?int $exp): array
  {
    $exp = time() + $exp;
    $token = JWT::encode([
      'iat' => time(),
      'exp' => $exp,
      ...$data
    ], self::$secret, 'HS256');
    return [$token, $exp];
  }

  public static function decode(string $token): ?object
  {
    try {
      return JWT::decode($token, new Key(self::$secret, 'HS256'));
    } catch(\Exception $ex) {
      return null;
    }
  }
}

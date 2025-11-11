<?php

function show($stuff)
{
    echo "<pre>";
    print_r($stuff);
    echo "</pre>";
}

function esc($str)
{
    return htmlspecialchars($str);
}

function redirect($path)
{
    $root = rtrim(ROOT, '/');
    $path = ltrim($path, '/');
    header("Location: {$root}/{$path}");
    exit;
}

function encryptId(int $id): string
{
    if (!defined('LOCK_KEY') || LOCK_KEY === '') {
        throw new RuntimeException('LOCK_KEY must be defined.');
    }

    if ($id < 0 || $id > 0xFFFFFFFF) {
        throw new InvalidArgumentException('ID must fit in 32 bits (0 .. 4294967295).');
    }

    // 16-bit signature (2 bytes) -> compact, gives ~1/65536 forgery chance.
    $secret = LOCK_KEY;
    $sigHex = substr(hash_hmac('sha256', (string)$id, $secret), 0, 4); // 4 hex chars = 2 bytes
    $sig = hexdec($sigHex) & 0xFFFF;

    // pack into single integer: (id << 16) | sig  => a 48-bit number
    $num = ($id << 16) | $sig; // works on 64-bit PHP

    return base62_encode($num);
}

function decryptId(string $code)
{
    if (!defined('LOCK_KEY') || LOCK_KEY === '') {
        throw new RuntimeException('LOCK_KEY must be defined.');
    }

    // decode base62 -> integer
    $num = base62_decode($code);
    if ($num === false) return false;

    // split
    $sig = $num & 0xFFFF;
    $id  = $num >> 16;

    // verify signature
    $expectedHex = substr(hash_hmac('sha256', (string)$id, LOCK_KEY), 0, 4);
    $expectedSig = hexdec($expectedHex) & 0xFFFF;

    if ($expectedSig === $sig) {
        return (int)$id;
    }

    return false; // tampered / invalid
}

function base62_encode($num): string
{
    if (!is_int($num) && !is_float($num)) $num = (int)$num;
    if ($num === 0) return '0';

    $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $base = strlen($chars);
    $out = '';
    while ($num > 0) {
        $rem = $num % $base;
        $out = $chars[$rem] . $out;
        $num = intdiv($num, $base);
    }
    return $out;
}

function base62_decode(string $str)
{
    $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $base = strlen($chars);
    $len = strlen($str);
    if ($len === 0) return false;

    $num = 0;
    for ($i = 0; $i < $len; $i++) {
        $pos = strpos($chars, $str[$i]);
        if ($pos === false) return false; // invalid char
        $num = $num * $base + $pos;
    }
    return $num;
}

function detectChanges($old, $new, $fieldsToCheck = [])
{
    $changes = [];

    // If no specific fields are passed, check all keys in $new
    if (empty($fieldsToCheck)) {
        $fieldsToCheck = array_keys($new);
    }

    foreach ($fieldsToCheck as $field) {
        // Normalize values
        $oldVal = isset($old[$field]) ? trim((string)$old[$field]) : '';
        $newVal = isset($new[$field]) ? trim((string)$new[$field]) : '';

        // Optional: decode HTML entities before comparing
        $oldVal = html_entity_decode($oldVal, ENT_QUOTES, 'UTF-8');
        $newVal = html_entity_decode($newVal, ENT_QUOTES, 'UTF-8');

        // Compare strictly
        if ($oldVal !== $newVal) {
            $changes[$field] = $new[$field];
        }
    }

    return $changes;
}

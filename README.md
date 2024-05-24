# SHA-256 Implementation in PHP

This repository contains a PHP implementation of the SHA-256 hashing algorithm. SHA-256 is a widely used cryptographic hash function that produces a 256-bit (32-byte) hash value. It is commonly used in various security applications and protocols, including Bitcoin.

## Usage

To compute the SHA-256 hash of a message, simply call the `generate_hash` function with the message as input. For example:

```php
echo bin2hex(generate_hash("Hello")); // Output: 185f8db32271fe25f561a6fc938b2e264306ec304eda518007d1764826381969

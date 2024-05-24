<?php

function generate_hash($message) {
    $K = array(
        0x428a2f98, 0x71374491, 0xb5c0fbcf, 0xe9b5dba5, 0x3956c25b, 0x59f111f1, 0x923f82a4, 0xab1c5ed5,
        0xd807aa98, 0x12835b01, 0x243185be, 0x550c7dc3, 0x72be5d74, 0x80deb1fe, 0x9bdc06a7, 0xc19bf174,
        0xe49b69c1, 0xefbe4786, 0x0fc19dc6, 0x240ca1cc, 0x2de92c6f, 0x4a7484aa, 0x5cb0a9dc, 0x76f988da,
        0x983e5152, 0xa831c66d, 0xb00327c8, 0xbf597fc7, 0xc6e00bf3, 0xd5a79147, 0x06ca6351, 0x14292967,
        0x27b70a85, 0x2e1b2138, 0x4d2c6dfc, 0x53380d13, 0x650a7354, 0x766a0abb, 0x81c2c92e, 0x92722c85,
        0xa2bfe8a1, 0xa81a664b, 0xc24b8b70, 0xc76c51a3, 0xd192e819, 0xd6990624, 0xf40e3585, 0x106aa070,
        0x19a4c116, 0x1e376c08, 0x2748774c, 0x34b0bcb5, 0x391c0cb3, 0x4ed8aa4a, 0x5b9cca4f, 0x682e6ff3,
        0x748f82ee, 0x78a5636f, 0x84c87814, 0x8cc70208, 0x90befffa, 0xa4506ceb, 0xbef9a3f7, 0xc67178f2
    );

    if (is_string($message)) {
        $message = unpack("C*", $message);
    } elseif (!is_array($message)) {
        throw new \InvalidArgumentException("Input message should be a string or an array of bytes.");
    }

    $length = count($message) * 8;
    $message[] = 0x80;
    while ((count($message) * 8 + 64) % 512 != 0) {
        $message[] = 0x00;
    }

    $length_bytes = pack("NN", 0, $length);
    for ($i = 0; $i < 8; $i++) {
        array_push($message, ord($length_bytes[$i]));
    }

    $h_final = array(
        0x6a09e667, 0xbb67ae85, 0x3c6ef372, 0xa54ff53a,
        0x510e527f, 0x9b05688c, 0x1f83d9ab, 0x5be0cd19
    );

    $blocks = array_chunk($message, 64);

    foreach ($blocks as $block) {
        $w = array();
        for ($t = 0; $t < 64; $t++) {
            if ($t <= 15) {
                $w[] = pack("N", $block[$t * 4] << 24 | $block[$t * 4 + 1] << 16 | $block[$t * 4 + 2] << 8 | $block[$t * 4 + 3]);
            } else {
                $w[] = pack("N", ((_sigma1(unpack("N", $w[$t - 2])[1]) + unpack("N", $w[$t - 7])[1] + _sigma0(unpack("N", $w[$t - 15])[1]) + unpack("N", $w[$t - 16])[1]) % (2 ** 32)));
            }
        }

        $h = $h_final;

        for ($t = 0; $t < 64; $t++) {
            $t1 = (($h[7] + _capsigma1($h[4]) + _ch($h[4], $h[5], $h[6]) + $K[$t] + unpack("N", $w[$t])[1]) % (2 ** 32));
            $t2 = (_capsigma0($h[0]) + _maj($h[0], $h[1], $h[2])) % (2 ** 32);

            $h_temp = $h;
            $h[7] = $h[6];
            $h[6] = $h[5];
            $h[5] = $h[4];
            $h[4] = ($h[3] + $t1) % (2 ** 32);
            $h[3] = $h[2];
            $h[2] = $h[1];
            $h[1] = $h[0];
            $h[0] = ($t1 + $t2) % (2 ** 32);
        }

        for ($i = 0; $i < 8; $i++) {
            $h_final[$i] = ($h_final[$i] + $h[$i]) % (2 ** 32);
        }
    }

    $hash = "";
    for ($i = 0; $i < 8; $i++) {
        $hash .= pack("N", $h_final[$i]);
    }

    return $hash;
}

function _sigma0($num) {
    return ((_rotate_right($num, 7) ^ _rotate_right($num, 18) ^ ($num >> 3)) % (2 ** 32));
}

function _sigma1($num) {
    return ((_rotate_right($num, 17) ^ _rotate_right($num, 19) ^ ($num >> 10)) % (2 ** 32));
}

function _capsigma0($num) {
    return ((_rotate_right($num, 2) ^ _rotate_right($num, 13) ^ _rotate_right($num, 22)) % (2 ** 32));
}

function _capsigma1($num) {
    return ((_rotate_right($num, 6) ^ _rotate_right($num, 11) ^ _rotate_right($num, 25)) % (2 ** 32));
}

function _ch($x, $y, $z) {
    return (($x & $y) ^ (~$x & $z)) % (2 ** 32);
}

function _maj($x, $y, $z) {
    return (($x & $y) ^ ($x & $z) ^ ($y & $z)) % (2 ** 32);
}

function _rotate_right($num, $shift) {
    return (($num >> $shift) | ($num << (32 - $shift))) % (2 ** 32);
}




//TEST
echo bin2hex(generate_hash("Hello"));
?>

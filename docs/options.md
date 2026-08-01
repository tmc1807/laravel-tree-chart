---
layout: default
title: Opsi
parent: Beranda
nav_order: 4
---

# Opsi

Kirim opsi per komponen, atau atur default global di `config/tree-chart.php`
(publish dengan `php artisan vendor:publish --tag=tree-chart-config`).
Opsi per-komponen selalu menang.

```blade
<x-tree-chart :nodes="$nodes" :options="[
    'title'    => 'Cascading Bagan Kinerja RPJMD 2025 - 2030',
    'subtitle' => 'Periode akhir RPJMD',
    'colors'   => ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#6f42c1'],
]" />
```

## Referensi

| Opsi | Tipe | Default | Deskripsi |
| --- | --- | --- | --- |
| `title` | string \| null | `null` | Judul yang dirender di atas pohon. |
| `subtitle` | string \| null | `null` | Subjudul redup di bawah judul. |
| `colors` | array | `['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#6f42c1']` | Palet yang diterapkan per level kedalaman (index 0 = root). `color` milik node sendiri mengesampingkannya. |
| `card_width` | int | `260` | Lebar kartu default dalam px. |
| `photo` | bool | `true` | Saklar utama untuk avatar. `true` selalu merendernya (photo node atau placeholder); `false` tidak pernah merendernya, bahkan jika node menyediakan photo. |
| `photo_placeholder` | string \| null | *inline SVG data-URI* | Gambar cadangan yang ditampilkan ketika node tidak punya `photo`. Set ke `null` untuk menyembunyikan area avatar sepenuhnya bagi node tanpa foto. |
| `side_width` | int | `500` | Lebar panel samping default dalam px. |
| `animate` | bool | `true` | Animasi masuk/keluar bertahap (staggered) untuk kartu dan garis penghubung. |
| `connector` | string | `dashed` | Gaya garis penghubung: `dashed` atau `solid`. |
| `collapsible` | bool | `true` | Render chevron untuk membuka/melipat anak. |
| `expand_level` | `'all'` \| `'click'` \| int | `'all'` | Perluasan awal. `'all'` menampilkan semua node terbuka; `'click'` memulai semuanya terlipat (buka lewat chevron); integer N otomatis membuka level `0..N` dan melipat level yang lebih dalam. Flag `collapsed` eksplisit pada node selalu menang. |
| `side_toggle` | bool | `true` | Render switch untuk menampilkan/menyembunyikan panel samping. |
| `scrollable` | bool | `true` | Membungkus pohon dalam kontainer yang bisa di-scroll horizontal. |
| `demo` | bool | `false` | Mendaftarkan route `/tree-chart/demo` (hanya untuk pengembangan). |

## Warna

Setiap level kedalaman mengambil warnanya dari `colors`. Node yang mendefinisikan
`color` memakai nilainya sendiri:

```php
$options = ['colors' => ['#4e73df', '#1cc88a', '#36b9cc']];
// depth 0 (root)  -> #4e73df
// depth 1         -> #1cc88a
// depth 2         -> #36b9cc
```

## File config

Config yang di-publish:

```php
<?php

return [
    'colors'      => ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#6f42c1'],
    'card_width'  => 260,
    'photo'       => true,
    'photo_placeholder' => 'data:image/svg+xml,...',
    'side_width'  => 500,
    'animate'     => true,
    'connector'   => 'dashed',
    'collapsible' => true,
    'expand_level' => 'all',
    'side_toggle' => true,
    'scrollable'  => true,
    'title'       => null,
    'subtitle'    => null,
    'demo'        => false,
];
```

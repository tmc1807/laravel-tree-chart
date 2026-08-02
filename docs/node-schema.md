---
layout: default
title: Skema Node
parent: Beranda
nav_order: 3
---

# Skema Node

Node adalah array bersarang apa pun (atau object / `Collection` — semuanya
dinormalkan secara otomatis). Hanya `id` dan `label` yang wajib.

| Key | Tipe | Deskripsi |
| --- | --- | --- |
| `id` | string | Id unik. Dipakai untuk DOM id, target collapse, sembunyikan/pulihkan. |
| `header` | string | Teks pada baris header berwarna (mis. `Misi 1`). Hapus untuk menyembunyikan baris header. |
| `label` | string | Teks utama pada badan kartu. |
| `sub_label` | string | Teks sekunder redup di bawah label. |
| `photo` | string \| false | URL gambar opsional yang ditampilkan sebagai avatar bulat. Jika tidak ada (atau kosong), kartu menampilkan gambar `photo_placeholder` sebagai gantinya. Set `false` untuk menyembunyikan avatar pada node ini saja, walau opsi `photo` global aktif. |
| `position` | string | Tempat node dirender relatif terhadap parent: `down` (ke bawah, default) atau `side` (ke samping kartu parent, dihubungkan garis horizontal). |
| `color` | string | Warna hex untuk border kartu, latar header, dan garis penghubung. Jatuh ke palet per level jika tidak ada. |
| `width` | int | Lebar kartu dalam px. Default mengikuti opsi `card_width`. |
| `children` | array | Node anak bersarang (rekursif). |
| `side` | string \| Htmlable | Konten panel samping yang dirender apa adanya (`{!! !!}`). Bisa `view('name', [...])` atau HTML mentah. |
| `side_visible` | bool | Apakah panel samping mulai terlihat. Default `true`. |
| `collapsed` | bool | Mulai dengan anak terlipat. Default `false`. |
| `hideable` | bool | Tampilkan tombol `×` untuk menyembunyikan cabang menjadi badge. Default `false`. |

Key tambahan apa pun disimpan di `extra` dan diabaikan oleh renderer.

## Contoh

```php
$nodes = [
    [
        'id'          => 'root',
        'header'      => 'Root',
        'label'       => 'Tujuan utama',
        'sub_label'   => 'Periode 2025 - 2030',
        'photo'       => 'https://example.test/foto/kepala-daerah.jpg',
        'color'       => '#4e73df',
        'width'       => 320,
        'collapsed'   => false,
        'hideable'    => true,
        'side'        => view('partials.detail', ['data' => $detail]),
        'children'    => [/* ... */],
    ],
];
```

## Panel samping

Field `side` dirender dengan `{!! !!}`, jadi Anda bisa mengoper apa pun yang
bisa dirender:

- `view('partials.indicator', [...])` — partial Blade (mis. tabel indikator).
- String HTML mentah.
- Apa pun yang mengimplementasikan `Htmlable` / `Stringable`.

```php
'side' => view('partials.indicator', [
    'title' => 'Indikator Tujuan',
    'rows'  => [
        ['IPM', '%', '70.1', '71.2', '72.5'],
        ['RLS', 'tahun', '8.5', '8.8', '9.1'],
    ],
]),
```

Panel muncul di sebelah kanan kartu, dihubungkan garis putus-putus. Jika opsi
`side_toggle` diaktifkan, sebuah switch di header kartu akan menampilkan/menyembunyikannya.

## Foto

`photo` bersifat opsional. Kartu merender avatar bulat menggunakan URL; jika
field tidak ada/kosong, `photo_placeholder` yang dikonfigurasi (lihat [Opsi](options.md))
yang ditampilkan. Set `photo_placeholder` ke `null` untuk menyembunyikan area
avatar sepenuhnya bagi node tanpa foto. Seluruh fitur bisa dimatikan dengan
opsi `photo` (`false` menyembunyikan avatar bahkan jika node menyediakan photo).

Untuk menyembunyikan avatar pada **satu node saja** (walau opsi `photo` global
aktif), set `photo` node ke `false`:

```php
[
    'id'    => 'instansi-kosong',
    'label' => 'Dinas Tanpa Pegawai',
    'photo' => false, // tidak menampilkan avatar untuk node ini
]
```

```php
[
    'id'    => 'kepala-daerah',
    'label' => 'Kepala Daerah',
    'photo' => 'https://example.test/foto/kepala-daerah.jpg', // tanpa foto → placeholder
]
```

## Builder helper

Lebih suka API fluent? Gunakan builder `Node` alih-alih array:

```php
use Tmc\LaravelTreeChart\Data\Node;

$nodes = [
    Node::make('visi', 'Terwujudnya Masyarakat Sejahtera')
        ->header('Visi')
        ->color('#4e73df')
        ->child(
            Node::make('m-1', 'Meningkatkan kualitas SDM')
                ->header('Misi 1')
                ->hideable()
                ->child(Node::make('t-1', 'Meningkatkan kualitas pendidikan')),
        ),
];
```

Metode yang tersedia: `id()`, `header()`, `label()`, `subLabel()`,
`photo()` (lewat `false` untuk menyembunyikan avatar node), `position()`,
`color()`, `width()`, `collapsed()`, `sideVisible()`, `hideable()`,
`children()`, `child()`, `side()`, `extra()`.

## Penempatan node

Sebuah anak bisa dirender **ke samping** parent-nya (kanan, dihubungkan garis
horizontal) atau **ke bawah** parent-nya (seperti biasa). Nilai `position`:
`side` untuk ke samping, `down` untuk ke bawah (default):

```php
$nodes = [
    [
        'id'    => 'kepala',
        'label' => 'Kepala Daerah',
        'children' => [
            ['id' => 'sekda', 'label' => 'Sekretariat Daerah', 'position' => 'side'],
            ['id' => 'dinas', 'label' => 'Dinas Pendidikan', 'position' => 'down'],
        ],
    ],
];
```

Anak `side` digambar ke samping kanan kartu parent, dihubungkan garis horizontal
pendek, dan bisa berisi anak lagi. Anak `down` digambar ke bawah parent seperti
biasa.

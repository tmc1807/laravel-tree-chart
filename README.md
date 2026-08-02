# laravel-tree-chart

[![PHP](https://img.shields.io/badge/PHP-8.2+-8892BF?logo=php&logoColor=fff)](https://www.php.net)
[![Laravel](https://img.shields.io/badge/Laravel-11%20%7C%2012-red?logo=laravel&logoColor=fff)](https://laravel.com)
[![Tests](https://github.com/tmc1807/laravel-tree-chart/actions/workflows/tests.yml/badge.svg)](https://github.com/tmc1807/laravel-tree-chart/actions)
[![Docs](https://img.shields.io/badge/docs-github%20pages-4B7BEC?logo=github)](https://tmc1807.github.io/laravel-tree-chart)
[![License: MIT](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Diagram struktur pohon untuk Laravel Blade yang framework-agnostic.

Komponen Blade tunggal dan mandiri yang merender pohon bersarang apa pun
sebagai diagram interaktif — dengan kartu berwarna per level, garis penghubung
beranimasi, node yang bisa dilipat (collapse), panel samping, dan kemampuan
menyembunyikan node. Tanpa Bootstrap, tanpa Livewire, tanpa JS/CSS eksternal:
semua style dan script disuntikkan inline, hanya sekali per halaman.

> Awalnya diekstrak dari modul RPJMD *pohon kinerja* — cocok untuk bagan
> organisasi, kaskade tujuan, pohon keluarga, peta situs, dan lainnya.

## Dokumentasi

Dokumentasi lengkap tersedia di **<https://tmc1807.github.io/laravel-tree-chart>**:

- [Memulai](https://tmc1807.github.io/laravel-tree-chart/getting-started.html)
- [Skema node](https://tmc1807.github.io/laravel-tree-chart/node-schema.html)
- [Opsi](https://tmc1807.github.io/laravel-tree-chart/options.html)
- [Lanjutan](https://tmc1807.github.io/laravel-tree-chart/advanced.html)
- [Kontribusi](https://tmc1807.github.io/laravel-tree-chart/contributing.html)

## Fitur

- **Data-driven** — terima array/collection/object bersarang apa pun; tidak ada syarat skema selain `id` + `label`.
- **Tanpa dependensi UI** — CSS minimal + vanilla JS sendiri, dengan prefiks `tc-` agar tidak bertabrakan.
- **Aset inline** — style/script hanya dikeluarkan sekali per halaman (`@once`), tidak ada yang perlu di-publish atau di-build.
- **Node dapat dilipat** — tampil/sembunyi anak dengan animasi.
- **Panel samping** — panel opsional (HTML/Blade apa pun) di sebelah kanan kartu, bisa di-toggle lewat switch.
- **Node dapat disembunyikan** — tombol `×` menyembunyikan cabang dan menyisakan badge yang bisa diklik di dekat root.
- **Ramah Livewire** — menginisialisasi pohon yang ditambahkan setelah halaman dimuat secara otomatis (mutation observer), tanpa dependensi Livewire.
- **Banyak instance** — beberapa pohon dalam satu halaman berbagi satu blok CSS/JS.
- **Dapat dikonfigurasi** — warna per level, lebar kartu/panel samping, animasi, gaya konektor.

## Instalasi

```bash
composer require tmc1807/laravel-tree-chart
```

Untuk pengembangan lokal lewat path repository:

```json
{
    "repositories": [
        { "type": "path", "url": "../laravel-tree-chart" }
    ]
}
```

Laravel meng-auto-discovery service provider. Jika aplikasi Anda menonaktifkan
package discovery, daftarkan secara manual di `bootstrap/providers.php`:

```php
Tmc\LaravelTreeChart\TreeChartServiceProvider::class,
```

## Mulai cepat

```blade
<x-tree-chart :nodes="$nodes" :options="$options" />
```

Bangun node sebagai array bersarang:

```php
$nodes = [
    [
        'id'        => 'visi',
        'header'    => 'Visi',
        'label'     => 'Terwujudnya Masyarakat Sejahtera',
        'sub_label' => 'Periode 2025 - 2030',
        'photo'     => 'https://example.test/foto/kepala-daerah.jpg',
        'color'     => '#4e73df',
        'children'  => [
            [
                'id'        => 'm-1',
                'header'    => 'Misi 1',
                'label'     => 'Meningkatkan kualitas SDM',
                'sub_label' => 'Sasaran pembangunan manusia',
                'hideable'  => true,
                'children'  => [
                    [
                        'id'       => 't-1',
                        'header'   => 'Tujuan 1',
                        'label'    => 'Meningkatkan kualitas pendidikan',
                        'position' => 'side',
                        'side'     => view('partials.indicator', ['rows' => $indikatorRows]),
                        'children' => [
                            [
                                'id'    => 's-1',
                                'label' => 'Meningkatnya mutu layanan pendidikan',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
```

## Skema node

| Key | Tipe | Deskripsi |
| --- | --- | --- |
| `id` | string | Id unik (dipakai untuk DOM id, collapse, sembunyikan/pulihkan). |
| `header` | string | Teks pada baris header berwarna (mis. `Misi 1`). Hapus untuk menyembunyikan baris header. |
| `label` | string | Teks utama pada badan kartu. |
| `sub_label` | string | Teks sekunder redup pada badan kartu. |
| `photo` | string \| false | URL gambar opsional yang dirender sebagai avatar bulat. Jika kosong, opsi `photo_placeholder` yang ditampilkan. Set `false` untuk menyembunyikan avatar pada node ini saja (walau opsi `photo` global aktif). |
| `position` | string | Penempatan relatif terhadap parent: `down` (ke bawah, default) atau `side` (ke samping kartu parent). |
| `color` | string | Warna hex untuk border kartu, header, dan garis. Jatuh ke palet per level jika tidak ada. |
| `width` | int | Lebar kartu dalam px; default mengikuti opsi `card_width`. |
| `children` | array | Node anak (bersarang). |
| `side` | string \| Htmlable | Konten panel samping, dirender apa adanya (`{!! !!}`). Bisa `view('name', [...])` atau HTML. |
| `side_visible` | bool | Apakah panel samping mulai terlihat (default `true`). |
| `collapsed` | bool | Mulai dengan anak terlipat (default `false`). |
| `hideable` | bool | Tampilkan tombol `×` untuk menyembunyikan cabang menjadi badge (default `false`). |

Key tambahan apa pun disimpan di `extra` dan diabaikan oleh renderer.

### Builder helper

Alih-alih array, Anda bisa memakai builder fluent `Node`:

```php
use Tmc\LaravelTreeChart\Data\Node;

$nodes = [
    Node::make('visi', 'Terwujudnya Masyarakat Sejahtera')
        ->header('Visi')
        ->color('#4e73df')
        ->photo('https://example.test/foto/visi.jpg')
        ->child(
            Node::make('m-1', 'Meningkatkan kualitas SDM')
                ->header('Misi 1')
                ->hideable()
        ),
];
```

## Opsi

Semua opsi bersifat opsional; default ada di `config/tree-chart.php`
(publish dengan `php artisan vendor:publish --tag=tree-chart-config`).

```php
$options = [
    'title'       => 'Cascading Bagan Kinerja RPJMD 2025 - 2030',
    'subtitle'    => 'Akhir periode',
    'colors'      => ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#6f42c1'], // per level (depth 0 = root)
    'card_width'  => 260,
    'card_gap'    => 14, // jarak horizontal antar kartu (px)
    'card_height' => null, // tinggi minimum kartu (px); null = mengikuti isi
    'font_size'   => 11, // ukuran font kartu (px); sub-label mengikuti secara proporsional
    'photo'       => true, // saklar utama: false menyembunyikan avatar bahkan jika node punya photo; node bisa override sendiri dengan 'photo' => false
    'photo_placeholder' => 'data:image/svg+xml,...', // fallback jika node tanpa photo; null menyembunyikan avatar
    'side_width'  => 500,
    'animate'     => true,
    'connector'   => 'dashed', // 'dashed' | 'solid'
    'collapsible' => true,
    'expand_level' => 'all', // 'all' (semua tampil) | 'click' (klik baru muncul) | 3 (auto sampai level 3)
    'side_toggle' => true,
    'scrollable'  => true,
];
```

## Demo

Halaman contoh disertakan. Aktifkan hanya saat pengembangan:

```bash
# .env
TREECHART_DEMO=true
```

```php
// config/tree-chart.php (yang sudah dipublish) atau secara dinamis
'tree-chart.demo' => env('TREECHART_DEMO', false),
```

Lalu kunjungi `/tree-chart/demo`. Saat dinonaktifkan (default), route tidak didaftarkan.

## Pengujian & gaya

```bash
composer install
composer test   # Pest (testbench)
composer pint   # Laravel Pint
```

## Lisensi

MIT

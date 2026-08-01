---
layout: default
title: Memulai
parent: Beranda
nav_order: 2
---

# Memulai

## Persyaratan

- PHP 8.2+
- Laravel 11 / 12

## Instalasi

```bash
composer require tmc1807/laravel-tree-chart
```

Laravel meng-auto-discovery service provider. Jika aplikasi Anda menonaktifkan
package discovery, daftarkan secara manual di `bootstrap/providers.php`:

```php
Tmc\LaravelTreeChart\TreeChartServiceProvider::class,
```

### Pengembangan lokal (path repository)

Untuk mengembangkan atau menguji dengan checkout lokal dari package:

```json
{
    "repositories": [
        { "type": "path", "url": "../laravel-tree-chart" }
    ]
}
```

```bash
composer require tmc1807/laravel-tree-chart:@dev
```

## Pohon pertama Anda

Buat data node (misalnya di controller):

```php
$nodes = [
    [
        'id'        => 'visi',
        'header'    => 'Visi',
        'label'     => 'Terwujudnya Masyarakat yang Adil dan Sejahtera',
        'sub_label' => 'Periode 2025 - 2030',
        'photo'     => 'https://example.test/foto/kepala-daerah.jpg',
        'color'     => '#4e73df',
        'children'  => [
            [
                'id'        => 'm-1',
                'header'    => 'Misi 1',
                'label'     => 'Meningkatkan kualitas sumber daya manusia',
                'sub_label' => 'Sasaran pembangunan manusia',
                'hideable'  => true,
                'children'  => [
                    [
                        'id'       => 't-1',
                        'header'   => 'Tujuan 1',
                        'label'    => 'Meningkatkan kualitas pendidikan',
                        'position' => 'side',
                        'side'     => view('partials.indicator', ['rows' => $indikatorRows]),
                    ],
                ],
            ],
        ],
    ],
];
```

Render di view Blade mana pun:

```blade
<x-tree-chart :nodes="$nodes" :options="$options ?? []" />
```

Selesai. Komponen menyuntikkan CSS dan JavaScript-nya sendiri — tidak ada yang
perlu di-publish, tidak ada yang perlu ditambahkan ke layout Anda.

## Mempublish config (opsional)

Untuk mengubah default global:

```bash
php artisan vendor:publish --tag=tree-chart-config
```

File hasil publish berada di `config/tree-chart.php`. `:options` per-komponen
selalu mengalahkan config.

## Langkah berikutnya

- Pelajari setiap [field node](node-schema.html).
- Baca [referensi opsi](options.html).
- Lihat [halaman demo](advanced.html) dan [kustomisasi](advanced.html).

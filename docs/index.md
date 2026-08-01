---
layout: default
title: Beranda
nav_order: 1
---

# laravel-tree-chart

**Diagram struktur pohon untuk Laravel Blade yang framework-agnostic.**

Komponen Blade tunggal dan mandiri yang merender pohon bersarang apa pun
sebagai diagram interaktif — dengan kartu berwarna per level, garis penghubung
beranimasi, node yang bisa dilipat, panel samping, dan kemampuan menyembunyikan
cabang. Tanpa Bootstrap, tanpa Livewire, tanpa JS/CSS eksternal: semua style
dan script disuntikkan inline, hanya sekali per halaman.

> Awalnya diekstrak dari modul RPJMD *pohon kinerja*.
> Cocok untuk bagan organisasi, kaskade tujuan, pohon keluarga, peta situs,
> dan lainnya.

## Fitur

- **Data-driven** — terima array/collection/object bersarang apa pun; hanya `id` dan `label` yang wajib.
- **Tanpa dependensi UI** — CSS minimal + vanilla JS sendiri, dengan prefiks `tc-` agar tidak bertabrakan.
- **Aset inline** — style/script hanya dikeluarkan sekali per halaman (`@once`); tidak ada yang perlu di-publish atau di-build.
- **Node dapat dilipat** — tampil/sembunyi anak dengan animasi.
- **Foto opsional** — avatar bulat per node, dengan placeholder jika node tidak punya foto.
- **Panel samping** — panel opsional (HTML/Blade apa pun) di sebelah kanan kartu, bisa di-toggle lewat switch.
- **Node dapat disembunyikan** — tombol `×` menyembunyikan cabang dan menyisakan badge yang bisa diklik di dekat root.
- **Ramah Livewire** — menginisialisasi pohon yang ditambahkan setelah halaman dimuat, tanpa dependensi Livewire.
- **Banyak instance** — beberapa pohon dalam satu halaman berbagi satu blok CSS/JS.
- **Dapat dikonfigurasi** — warna per level, lebar kartu/panel samping, animasi, gaya konektor.

## Pratinjau cepat

```php
$nodes = [
    [
        'id'        => 'visi',
        'header'    => 'Visi',
        'label'     => 'Terwujudnya Masyarakat Sejahtera',
        'sub_label' => 'Periode 2025 - 2030',
        'badge'     => '1 Tujuan',
        'color'     => '#4e73df',
        'photo'     => 'https://example.test/foto/kepala-daerah.jpg', // opsional
        'children'  => [
            ['id' => 'm-1', 'header' => 'Misi 1', 'label' => 'Meningkatkan kualitas SDM'],
        ],
    ],
];
```

Foto bersifat **opsional**: jika `photo` ada maka gambar tersebut yang ditampilkan
sebagai avatar bulat; jika tidak ada, placeholder dari opsi `photo_placeholder`
yang muncul (lihat [Skema node](node-schema.html) dan [Opsi](options.html)).

```blade
<x-tree-chart :nodes="$nodes" :options="['title' => 'Pohon Kinerja RPJMD 2025 - 2030']" />
```

## Dokumentasi

- [Memulai](getting-started.html) — instalasi & pohon pertama.
- [Skema node](node-schema.html) — penjelasan setiap field node.
- [Opsi](options.html) — referensi konfigurasi.
- [Lanjutan](advanced.html) — halaman demo, kustomisasi, Livewire, FAQ.

## Lisensi

MIT — lihat [LICENSE](https://github.com/tmc1807/laravel-tree-chart/blob/main/LICENSE).

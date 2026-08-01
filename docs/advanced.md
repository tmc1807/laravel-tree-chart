---
layout: default
title: Lanjutan
parent: Beranda
nav_order: 5
---

# Lanjutan

## Halaman demo

Halaman contoh disertakan dalam package. Aktifkan hanya saat pengembangan:

```bash
# .env
TREECHART_DEMO=true
```

Lalu arahkan config ke nilai env (jika Anda sudah mem-publish-nya):

```php
'demo' => env('TREECHART_DEMO', false),
```

Kunjungi `/tree-chart/demo`. Ketika `demo` bernilai `false` (default), route
tidak didaftarkan.

## Styling

Semua CSS dikelompokkan di bawah `.tc-tree-chart` dengan prefiks `tc-`, sehingga
tidak akan bertabrakan dengan style aplikasi Anda. Timpa dengan CSS biasa setelah
komponen dirender — contoh:

```css
.tc-tree-chart .tc-card { border-radius: 6px; }
.tc-tree-chart .tc-head { text-transform: none; }
```

Nama class utama:

| Class | Elemen |
| --- | --- |
| `.tc-tree-chart` | Pembungkus root (juga membawa `tc-animate`, `tc-connector-dashed`). |
| `.tc-tree`, `.tc-tree-scroll` | Layout pohon + scroll horizontal. |
| `.tc-node`, `.tc-up` | Kolom node dan garis konektor vertikal. |
| `.tc-tree-children`, `.tc-hline` | Baris sibling dan konektor horizontal. |
| `.tc-anchor`, `.tc-card` | Pembungkus kartu dan kartu. |
| `.tc-head`, `.tc-body`, `.tc-title`, `.tc-sub` | Bagian-bagian kartu. |
| `.tc-side`, `.tc-side-card`, `.tc-side-connector` | Panel samping. |
| `.tc-collapse` (+ `.tc-open`) | Kontainer anak yang bisa dilipat. |
| `.tc-switch` | Saklar toggle murni CSS. |
| `.tc-hidden-badges`, `.tc-hidden-badge` | Badge sembunyikan/pulihkan di dekat root. |

## JavaScript

Satu object `window.TreeChart` disuntikkan sekali per halaman:

- `TreeChart.init(root?)` — menginisialisasi chart apa pun yang belum diinisialisasi di `root`.
- `TreeChart.updateHlines(root?)` — menghitung ulang posisi garis penghubung.
- `TreeChart.stagger(root?)` — memutar ulang animasi masuk bertahap.
- `TreeChart.toggleCollapse(node)` — membuka/melipat anak sebuah node.
- `TreeChart.toggleSide(input)` — mengaktifkan/menonaktifkan panel samping.
- `TreeChart.hideNode(domId)` — menyembunyikan cabang menjadi badge.

Script menginisialisasi otomatis saat `DOMContentLoaded` dan mengamati DOM dengan
`MutationObserver`, sehingga pohon yang ditambahkan kemudian (termasuk lewat
morph Livewire) akan terambil secara otomatis.

## Livewire

Tidak diperlukan kode integrasi. Komponen merender HTML biasa; observer
menginisialisasi ulang chart setiap kali node baru muncul di DOM. Jika Anda
merender ulang sebuah pohon dan ingin garis penghubung sejajar kembali,
panggil:

```js
TreeChart.updateHlines();
```

## Menyembunyikan cabang secara terprogram

Setiap node yang bisa disembunyikan merender tombol `×`. Untuk menyembunyikan
cabang dari kode Anda sendiri, panggil helper yang sama yang dipakai tombol:

```js
TreeChart.hideNode('tc-<uid>-<node-id>');
```

`<uid>` dan `<node-id>` yang sudah di-sanitasi membentuk `data-tc-dom` node.

## FAQ

**Bisakah saya merender beberapa pohon dalam satu halaman?**
Ya. Beberapa komponen `<x-tree-chart>` berbagi satu blok CSS/JS (`@once`)
dan setiap pohon menginisialisasi secara independen.

**Apakah saya butuh Bootstrap atau Livewire?**
Tidak. Package ini mandiri; keduanya opsional.

**Bisakah saya memakai model Eloquent secara langsung?**
Ya — object apa pun dengan metode `toArray()` (model, object `Data`) akan
dinormalkan secara otomatis. Hanya `id`/`label` yang perlu ada.

**Mengapa garis penghubung hilang pada beberapa screenshot?**
Garis horizontal diukur saat runtime dan hanya digambar untuk baris yang benar-benar
dirender. Jika sebuah baris terlipat penuh, garisnya sengaja disembunyikan.

## Kontribusi

Lihat [Kontribusi](contributing.html).

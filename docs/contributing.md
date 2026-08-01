---
layout: default
title: Kontribusi
parent: Beranda
nav_order: 6
---

# Kontribusi

## Pengaturan pengembangan

```bash
git clone https://github.com/tmc1807/laravel-tree-chart.git
cd laravel-tree-chart
composer install
```

## Menjalankan test

```bash
composer test
```

Menjalankan Pest dengan Orchestra Testbench (28 test yang mencakup rendering,
normalisasi node, deduplikasi `@once`, dan route demo).

## Gaya kode

```bash
composer pint
```

## Situs dokumentasi

Dokumentasi GitHub Pages berada di [`docs/`](https://github.com/tmc1807/laravel-tree-chart/tree/main/docs)
dan dibangun otomatis oleh GitHub Pages (branch `main`, folder `/docs`)
menggunakan theme jarak jauh `just-the-docs`. Untuk pratinjau lokal:

```bash
gem install bundler
cd docs
bundle install
bundle exec jekyll serve
```

Lalu buka <http://localhost:4000>.

> Pembangun GitHub Pages menyelesaikan `remote_theme: just-the-docs` sendiri;
> `Gemfile` (gem github-pages) hanya untuk pratinjau lokal.

## Checklist fitur untuk sebuah PR

- [ ] Test ditambahkan/diperbarui dan lolos (`composer test`).
- [ ] Pint bersih (`composer pint`).
- [ ] Docs diperbarui (README dan/atau `docs/`) saat perilaku berubah.

## Lisensi

MIT. Dengan berkontribusi, Anda menyetujui perubahan Anda dirilis di bawah
lisensi yang sama.

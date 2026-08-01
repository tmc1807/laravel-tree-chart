---
layout: default
title: Contributing
parent: Home
nav_order: 6
---

# Contributing

## Development setup

```bash
git clone https://github.com/tmc1807/laravel-tree-chart.git
cd laravel-tree-chart
composer install
```

## Running tests

```bash
composer test
```

Runs Pest with Orchestra Testbench (15 tests covering rendering, node
normalization, `@once` deduplication and the demo route).

## Code style

```bash
composer pint
```

## Documentation site

The GitHub Pages documentation lives in [`docs/`](https://github.com/tmc1807/laravel-tree-chart/tree/main/docs)
and is built automatically by GitHub Pages (branch `main`, folder `/docs`)
using the `just-the-docs` remote theme. To preview locally:

```bash
gem install bundler
cd docs
bundle install
bundle exec jekyll serve
```

Then open <http://localhost:4000>.

> The GitHub Pages builder resolves `remote_theme: just-the-docs` on its own;
> the `Gemfile` (github-pages gem) is only for local preview.

## Feature checklist for a PR

- [ ] Tests added/updated and passing (`composer test`).
- [ ] Pint clean (`composer pint`).
- [ ] Docs updated (README and/or `docs/`) when behavior changes.

## License

MIT. By contributing you agree your changes are released under the same license.

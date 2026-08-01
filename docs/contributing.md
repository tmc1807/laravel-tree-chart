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
and is built with Jekyll using the `just-the-docs` theme. To preview locally:

```bash
gem install bundler jekyll
cd docs
bundle init
bundle add jekyll just-the-docs
bundle exec jekyll serve
```

Then open <http://localhost:4000>.

## Feature checklist for a PR

- [ ] Tests added/updated and passing (`composer test`).
- [ ] Pint clean (`composer pint`).
- [ ] Docs updated (README and/or `docs/`) when behavior changes.

## License

MIT. By contributing you agree your changes are released under the same license.

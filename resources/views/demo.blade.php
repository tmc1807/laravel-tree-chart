<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>laravel-tree-chart — demo</title>
    <style>
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0; padding: 32px 16px; background: #f5f6fa; color: #212529;
        }
        .wrap { max-width: 1400px; margin: 0 auto; }
        .page-head { text-align: center; margin-bottom: 28px; }
        .page-head h1 { margin: 0; font-size: 1.5rem; font-weight: 800; }
        .page-head p { margin: 4px 0 0; color: #6c757d; font-size: .85rem; }
        .side-table { width: 100%; border-collapse: collapse; font-size: .58rem; }
        .side-table th, .side-table td {
            border: 1px solid #e9ecef; padding: 3px 5px; text-align: left; white-space: nowrap;
        }
        .side-table thead th { background: #f1f3f5; }
        .side-table .num { text-align: center; font-weight: 700; }
        .side-box { padding: 6px 8px; }
        .side-title { font-size: .62rem; font-weight: 700; color: #fff; background: #495057; padding: 4px 8px; border-radius: 6px 6px 0 0; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="page-head">
        <h1>laravel-tree-chart &middot; demo</h1>
        <p>Blade component generik — data-driven, tanpa Bootstrap/Livewire.</p>
    </div>

    @php
        $nodes = [
            [
                'id' => 'visi',
                'header' => 'Visi',
                'label' => 'Terwujudnya Kesejahteraan Masyarakat yang Adil dan Sejahtera',
                'color' => '#4e73df',
                'photo' => 'https://i.pravatar.cc/80?img=12',
                'children' => [
                    [
                        'id' => 'm-1',
                        'header' => 'Misi 1',
                        'label' => 'Meningkatkan kualitas sumber daya manusia',
                        'badge' => '2 Tujuan',
                        'badge_color' => '#1cc88a',
                        'hideable' => true,
                        'photo' => 'https://i.pravatar.cc/80?img=5',
                        'children' => [
                            [
                                'id' => 't-1-1',
                                'header' => 'Tujuan 1',
                                'label' => 'Meningkatkan kualitas pendidikan',
                                'badge' => '2 Sasaran',
                                'color' => '#36b9cc',
                                'side' => view('tree-chart::demo-partials.indicator', [
                                    'title' => 'Indikator Tujuan',
                                    'rows' => [
                                        ['Indeks Pembangunan Manusia (IPM)', '%', '70.1', '71.2', '72.5', '73.0', '74.0', '75.0'],
                                        ['Rata-rata Lama Sekolah', 'tahun', '8.5', '8.8', '9.1', '9.4', '9.7', '10.0'],
                                    ],
                                ]),
                                'children' => [
                                    [
                                        'id' => 's-1-1-1',
                                        'header' => 'Sasaran 1.1',
                                        'label' => 'Meningkatnya mutu dan pemerataan layanan pendidikan',
                                        'badge' => '2 Program',
                                        'badge_color' => '#f6c23e',
                                        'side' => view('tree-chart::demo-partials.indicator', [
                                            'title' => 'Indikator Sasaran',
                                            'rows' => [
                                                ['Angka Partisipasi Kasar (APK) SD', '%', '99', '100', '100', '100', '100', '100'],
                                                ['Angka Partisipasi Murni (APM) SMP', '%', '80', '82', '84', '86', '88', '90'],
                                            ],
                                        ]),
                                        'children' => [
                                            [
                                                'id' => 'p-1-1-1-1',
                                                'header' => 'Program',
                                                'label' => 'Program Wajib Belajar Pendidikan Dasar',
                                                'badge' => '3 Outcome',
                                                'color' => '#6f42c1',
                                                'side' => view('tree-chart::demo-partials.outcome', [
                                                    'outcomes' => [
                                                        'Meningkatnya angka partisipasi sekolah' => [
                                                            ['Proporsi anak usia 7-12 tahun bersekolah', '%', '98', '99', '99', '100', '100', '100'],
                                                        ],
                                                        'Meningkatnya kualitas sarana belajar' => [
                                                            ['Sekolah memenuhi standar nasional', 'unit', '120', '135', '150', '165', '180', '195'],
                                                        ],
                                                    ],
                                                ]),
                                            ],
                                        ],
                                    ],
                                    [
                                        'id' => 's-1-1-2',
                                        'header' => 'Sasaran 1.2',
                                        'label' => 'Meningkatnya kualitas kesehatan masyarakat',
                                        'side' => view('tree-chart::demo-partials.indicator', [
                                            'title' => 'Indikator Sasaran',
                                            'rows' => [
                                                ['Angka Harapan Hidup', 'tahun', '70', '70.5', '71', '71.5', '72', '72.5'],
                                            ],
                                        ]),
                                        'children' => [
                                            [
                                                'id' => 'p-1-1-2-1',
                                                'header' => 'Program',
                                                'label' => 'Program Pelayanan Kesehatan',
                                                'color' => '#6f42c1',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'id' => 't-1-2',
                                'header' => 'Tujuan 2',
                                'label' => 'Meningkatkan kualitas kesehatan masyarakat',
                                'badge' => '1 Sasaran',
                                'collapsed' => true,
                                'children' => [
                                    [
                                        'id' => 's-1-2-1',
                                        'header' => 'Sasaran 2.1',
                                        'label' => 'Menurunnya angka stunting',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'id' => 'm-2',
                        'header' => 'Misi 2',
                        'label' => 'Mewujudkan pertumbuhan ekonomi yang inklusif',
                        'badge' => '1 Tujuan',
                        'badge_color' => '#1cc88a',
                        'hideable' => true,
                        'photo' => 'https://i.pravatar.cc/80?img=32',
                        'children' => [
                            [
                                'id' => 't-2-1',
                                'header' => 'Tujuan 3',
                                'label' => 'Meningkatkan pertumbuhan ekonomi daerah',
                                'side' => view('tree-chart::demo-partials.indicator', [
                                    'title' => 'Indikator Tujuan',
                                    'rows' => [
                                        ['Laju Pertumbuhan Ekonomi', '%', '4.5', '5.0', '5.5', '6.0', '6.5', '7.0'],
                                    ],
                                ]),
                            ],
                        ],
                    ],
                ],
            ],
        ];
    @endphp

    <x-tree-chart
        :nodes="$nodes"
        :options="[
            'title' => 'Cascading Bagan Kinerja RPJMD 2025 - 2030',
            'subtitle' => 'Contoh: package generik laravel-tree-chart',
            'colors' => ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#6f42c1'],
        ]"
    />
</div>
</body>
</html>

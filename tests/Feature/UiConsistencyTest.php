<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Guard konsistensi UI.
 *
 * Ada 20 view yang masing-masing punya <style> sendiri. sebelum
 * dirapikan, 204 selector terduplikasi dan ~171 properti punya
 * nilai berbeda antar halaman (mis. .btn-primary berjumlah 6 warna,
 * .badge-point merah di satu halaman dan hijau di halaman lain).
 *
 * Test ini menjaga supaya token & partial baru tetap jadi satu
 * sumber kebenaran, dan nilai liar tidak diam-diam masuk lagi.
 */
class UiConsistencyTest extends TestCase
{
    /** @return array<string, array{string}> */
    public static function viewProvider(): array
    {
        $out = [];
        foreach (self::viewFiles() as $file) {
            $out[$file] = [$file];
        }

        return $out;
    }

    /**
     * Path relatif terhadap resources/views. Sengaja tidak memakai
     * resource_path() karena data provider dijalankan SEBELUM
     * aplikasi boot.
     *
     * @return array<int, string>
     */
    private static function viewFiles(): array
    {
        $root = dirname(__DIR__, 2).'/resources/views';
        $prefix = realpath($root).DIRECTORY_SEPARATOR;

        $files = [];
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root));

        foreach ($rii as $f) {
            if (! $f->isFile() || $f->getExtension() !== 'php') {
                continue;
            }

            $real = realpath($f->getPathname());

            if ($real === false || ! str_starts_with($real, $prefix)) {
                continue;
            }

            $rel = str_replace('\\', '/', substr($real, strlen($prefix)));

            // Di luar scope: file mati & halaman login mandiri
            // (tidak me-extend layout app).
            if (str_starts_with($rel, 'welcome')
                || str_starts_with($rel, 'partials/')
                || str_starts_with($rel, 'layouts/')
                || str_starts_with($rel, 'auth/')) {
                continue;
            }

            $files[] = $rel;
        }

        sort($files);

        return $files;
    }

    private static function styleOf(string $file): string
    {
        $src = file_get_contents(self::abs($file));

        return preg_match("/@section\('styles'\)(.*?)@endsection/s", $src, $m) ? $m[1] : '';
    }

    /** Path relatif view -> path absolut. */
    private static function abs(string $file): string
    {
        return str_starts_with($file, '/')
            ? $file
            : dirname(__DIR__, 2).'/resources/views/'.$file;
    }

    /**
     * Selector level-atas (di luar @media), sudah dirapikan spasi.
     *
     * @return array<int, string>
     */
    private static function topLevelSelectors(string $css): array
    {
        $out = [];
        $depth = 0;
        $len = strlen($css);
        $start = 0;

        for ($i = 0; $i < $len; $i++) {
            $ch = $css[$i];

            if ($ch === '{') {
                if ($depth === 0) {
                    $chunk = substr($css, $start, $i - $start);

                    // Buang sisa rule sebelumnya & komentar yang tertinggal.
                    $chunk = preg_replace('#/\*.*?\*/#s', '', $chunk);
                    $chunk = trim((string) $chunk);
                    $chunk = (string) preg_replace('/\s+/', ' ', $chunk);

                    if ($chunk !== '') {
                        $out[] = $chunk;
                    }
                }

                $depth++;

                continue;
            }

            if ($ch === '}') {
                $depth--;
                $start = $i + 1;
            }
        }

        return $out;
    }

    /**
     * Class yang hanya berfungsi sebagai hook untuk <script> di
     * halaman yang sama (mis. `.js-approve`, `.pilihSiswa`).
     * Kelas seperti ini memang tidak perlu punya aturan CSS.
     *
     * @return array<int, string>
     */
    private static function jsHookClasses(string $file): array
    {
        $src = file_get_contents(self::abs($file));

        // ambil isi <script>
        if (! preg_match_all('#<script[^>]*>(.*?)</script>#s', $src, $m)) {
            return [];
        }

        $js = implode("\n", $m[1]);

        preg_match_all('/[\'"]([\w-]+)[\'"]/', $js, $mm);
        preg_match_all('/\.([a-z][\w-]*)/', $js, $mm2);

        return array_values(array_unique(array_merge($mm[1], $mm2[1])));
    }

    /**
     * Buang isi @media dari CSS supaya hanya menyisakan deklarasi
     * tingkat atas (blok yang tidak berada di dalam kurung kurawal
     * media query).
     */
    private static function topLevelCss(string $css): string
    {
        $out = '';
        $depth = 0;
        $len = strlen($css);

        for ($i = 0; $i < $len; $i++) {
            $ch = $css[$i];

            if ($ch === '{') {
                $depth++;

                continue;
            }

            if ($ch === '}') {
                $depth--;

                continue;
            }

            if ($depth === 0) {
                $out .= $ch;
            }
        }

        return $out;
    }

    /** @param  array<int, string>  $a  @param  array<int, string>  $b */
    private static function intersect(array $a, array $b): array
    {
        return array_values(array_intersect($a, $b));
    }

    /**
     * Setiap view harus memakai design system, bukan CSS sendiri
     * dari nol.
     */
    #[DataProvider('viewProvider')]
    public function test_setiap_view_menggunakan_design_system(string $file): void
    {
        $this->assertStringContainsString(
            "@include('partials.ui')",
            file_get_contents(self::abs($file)),
            $file.' belum meng-include partials.ui'
        );
    }

    /**
     * View TIDAK boleh mendefinisikan ULANG persis primitive yang
     * sudah ada di design system. Itu persis sumber duplikasi yang
     * dibersihkan: 204 selector sama dengan ~171 nilai berbeda.
     *
     * Yang TIDAK dilarang, karena memang hak halaman:
     *  - deklarasi di dalam @media (tuning responsif per halaman)
     *  - sub-selector turunan: `.btn-primary:hover`,
     *    `.empty-state p`, `.page-heading > div:first-child`,
     *    `.upload-box input[type=file]`, dan sejenisnya
     *  - selector yang TIDAK ada di design system sama sekali
     *    (`.walas-banner`, `.pending-card`, `.lb-pill`, dll)
     */
    #[DataProvider('viewProvider')]
    public function test_view_tidak_mendefinisikan_ulang_primitive(string $file): void
    {
        $partial = file_get_contents(resource_path('views/partials/ui.blade.php'));

        preg_match_all('/\.(-?[_a-zA-Z][\w-]*)/', $partial, $m);
        $primitives = array_values(array_unique(array_filter(
            $m[1],
            fn ($c) => ! str_starts_with($c, 'fa')
        )));

        // Kumpulkan selector PERSIS yang ditulis ulang, di luar @media.
        $bad = [];
        foreach (self::topLevelSelectors(self::styleOf($file)) as $sel) {
            foreach (explode(',', $sel) as $part) {
                $part = trim($part);
                if (in_array($part, $primitives, true)) {
                    $bad[$part] = true;
                }
            }
        }

        $bad = array_keys($bad);

        $this->assertSame(
            [],
            $bad,
            $file.' mendefinisikan ulang primitive design system: '.implode(', ', array_map(fn ($c) => '.'.$c, $bad))
        );
    }

    /**
     * Warna brand tidak boleh ditulis ulang sebagai hex liar di view.
     * Semua harus lewat token, supaya ganti tema cukup 1 tempat.
     */
    #[DataProvider('viewProvider')]
    public function test_warna_brand_tidak_ditulis_ulang(string $file): void
    {
        $css = self::styleOf($file);

        // Maroon brand & turunannya. Nilai uppercase_hex adalah gaya lama.
        $forbidden = [
            '#6D1408', '#6d1408',
            '#4E0D06', '#4e0d06',
            '#8A1C0D', '#8a1c0d',
        ];

        $found = [];
        foreach ($forbidden as $hex) {
            if (preg_match('/(?<![\w-])'.preg_quote($hex, '/').'(?![\w-])/', $css)) {
                $found[] = $hex;
            }
        }

        $this->assertSame(
            [],
            $found,
            $file.' masih menulis warna brand langsung: '.implode(', ', $found)
            .' — pakai var(--primary) / var(--primary-dark)'
        );
    }

    /**
     * Tombol utama harus satu warna. Sebelumnya .btn-primary
     * muncul dalam 6 warna termasuk biru (#2563eb) yang sama
     * sekali tidak on-brand.
     */
    #[DataProvider('viewProvider')]
    public function test_tidak_ada_warna_liar_pada_tombol_utama(string $file): void
    {
        $css = self::styleOf($file);

        preg_match_all('/\.pv-btn-primary[^{]*\{([^}]*)\}/', $css, $m);

        $bad = [];
        foreach ($m[1] as $body) {
            if (preg_match('/background:\s*(#[0-9a-fA-F]{3,8})/', $body, $bg)) {
                $bad[] = $bg[1];
            }
        }

        $this->assertSame(
            [],
            $bad,
            $file.' memberi warna hex pada tombol utama: '.implode(', ', $bad)
            .' — pakai var(--primary)'
        );
    }

    /**
     * Poin harus satu warna di seluruh aplikasi. Semuanya
     * diturunkan dari --primary lewat --c-point-bg.
     */
    #[DataProvider('viewProvider')]
    public function test_warna_poin_konsisten(string $file): void
    {
        $css = self::styleOf($file);

        preg_match_all('/\.(badge-point|mobile-point|points-badge)[^{]*\{([^}]*)\}/', $css, $m);

        $bad = [];
        foreach ($m[2] as $body) {
            if (preg_match('/background:\s*(#[0-9a-fA-F]{3,8})/', $body, $bg)) {
                $bad[] = $bg[1];
            }
        }

        $this->assertSame(
            [],
            $bad,
            $file.' memberi warna hex pada badge poin: '.implode(', ', $bad)
            .' — pakai var(--c-point-bg)'
        );
    }

    /**
     * Tidak boleh ada <style> di dalam @section('styles').
     * Layout sudah membungkus @yield('styles') dengan <style>,
     * jadi hasilnya <style><style>…</style></style> yang tidak
     * valid HTML.
     */
    #[DataProvider('viewProvider')]
    public function test_tidak_ada_style_nested(string $file): void
    {
        $css = self::styleOf($file);

        $this->assertStringNotContainsString(
            '<style>',
            $css,
            $file.' punya <style> di dalam @section(\'styles\')'
        );
        $this->assertStringNotContainsString(
            '</style>',
            $css,
            $file.' punya </style> di dalam @section(\'styles\')'
        );
    }

    /**
     * Guard terakhir: setiap class yang dipakai di markup harus
     * punya definisi CSS di view, partial, atau layout. Class
     * tanpa CSS = styling hilang tanpa error.
     */
    #[DataProvider('viewProvider')]
    public function test_tidak_ada_class_yatim(string $file): void
    {
        $src = file_get_contents(self::abs($file));

        $content = preg_match("/@section\('content'\)(.*?)\n@endsection/s", $src, $m) ? $m[1] : '';

        preg_match_all('/class="([^"]*)"/', $content, $m3);

        $used = [];
        foreach ($m3[1] as $attr) {
            if (str_contains($attr, '{{') || str_contains($attr, '@') || str_contains($attr, '{')) {
                continue;
            }
            foreach (preg_split('/\s+/', $attr) as $c) {
                if ($c === '' || str_starts_with($c, 'fa')) {
                    continue;
                }
                $used[$c] = true;
            }
        }

        $base = file_get_contents(resource_path('views/partials/ui.blade.php'))
            .file_get_contents(resource_path('views/layouts/app.blade.php'))
            .self::styleOf($file);

        // Hook JS & class pembantu tidak wajib punya styling.
        // Hook: dipakai sebagai selektor di <script> halaman.
        $jsHooks = self::jsHookClasses($file);
        $helpers = ['is-hidden', 'show', 'container', 'searchable-select'];

        $orphan = [];
        foreach (array_keys($used) as $c) {
            if (in_array($c, $helpers, true) || in_array($c, $jsHooks, true)) {
                continue;
            }

            if (! preg_match('/\.'.preg_quote($c, '/').'(?![\w-])/', $base)) {
                $orphan[] = $c;
            }
        }

        $this->assertSame(
            [],
            $orphan,
            $file.' memakai class tanpa definisi CSS: '.implode(', ', $orphan)
        );
    }
}

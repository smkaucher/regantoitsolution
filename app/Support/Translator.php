<?php

namespace App\Support;

/**
 * English to Bangla for the public site.
 *
 * The pages are written in English. For a Bangla visitor the finished HTML is passed through this class,
 * which swaps every visible text, placeholder, title and alt for its Bangla version from lang/bn.json
 * (key = the exact English text, value = Bangla). Anything not in the file stays English, so new content
 * added in the admin panel simply shows in English until a line is added to lang/bn.json.
 * Scripts and styles are never touched.
 */
class Translator
{
    private static ?array $map = null;

    public static function map(): array
    {
        if (self::$map === null) {
            $file = base_path('lang/bn.json');
            $raw = is_file($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
            self::$map = [];
            foreach ($raw as $en => $bn) {
                self::$map[self::key($en)] = $bn;
            }
        }

        return self::$map;
    }

    /** Whitespace-insensitive lookup key. */
    private static function key(string $text): string
    {
        return trim(preg_replace('/\s+/u', ' ', $text));
    }

    /** Bangla for one plain text, or null when there is none. */
    public static function lookup(string $text): ?string
    {
        $key = self::key($text);
        if ($key === '') {
            return null;
        }
        $map = self::map();
        if (isset($map[$key])) {
            return $map[$key];
        }
        // A title that went through escaping twice ("Design &amp; Branding").
        if (str_contains($key, '&amp;')) {
            return self::lookup(str_replace('&amp;', '&', $key));
        }

        return self::pattern($key);
    }

    /** Texts that carry a number, a date or another translated text. */
    private static function pattern(string $key): ?string
    {
        $digits = fn (string $n) => strtr($n, '0123456789', '০১২৩৪৫৬৭৮৯');

        // Breadcrumb after the "Home" link: "/ Services / Development".
        if (str_starts_with($key, '/ ')) {
            return '/ ' . implode(' / ', array_map([self::class, 'text'], explode(' / ', substr($key, 2))));
        }
        if (preg_match('/^(\d+) services$/', $key, $m)) {
            return $digits($m[1]) . 'টি সার্ভিস';
        }
        if (preg_match('/^(\d+) things included$/', $key, $m)) {
            return $digits($m[1]) . 'টি বিষয় অন্তর্ভুক্ত';
        }
        if (preg_match('/^(\d+) included$/', $key, $m)) {
            return $digits($m[1]) . 'টি অন্তর্ভুক্ত';
        }
        if (preg_match('/^Show review (\d+)$/', $key, $m)) {
            return 'রিভিউ ' . $digits($m[1]) . ' দেখুন';
        }
        if (preg_match('/^(.+) from the Reganto IT Solution team\. Everything that is included is listed below\.$/', $key, $m)) {
            return self::text($m[1]) . ' — Reganto IT Solution টিমের পক্ষ থেকে। যা যা অন্তর্ভুক্ত, নিচে দেওয়া আছে।';
        }
        if (preg_match('/^Talk to us about (.+)$/', $key, $m)) {
            return self::text($m[1]) . ' নিয়ে আমাদের সাথে কথা বলুন';
        }
        if (preg_match('/^Recent Work in (.+)$/', $key, $m)) {
            return self::text($m[1]) . '-এর সাম্প্রতিক কাজ';
        }
        if (preg_match('/^More in (.+)$/', $key, $m)) {
            return self::text($m[1]) . '-এর আরও সার্ভিস';
        }
        if (preg_match('/^(.+) – Reganto IT Solution$/', $key, $m)) {
            $title = self::lookup($m[1]) ?? implode(' · ', array_map([self::class, 'text'], explode(' · ', $m[1])));

            return $title . ' – Reganto IT Solution';
        }
        if (preg_match('/^([A-Z][a-z]{2}) (\d{1,2}), (\d{4})(?: · (\d+) min read)?$/', $key, $m)) {
            $months = ['Jan' => 'জানুয়ারি', 'Feb' => 'ফেব্রুয়ারি', 'Mar' => 'মার্চ', 'Apr' => 'এপ্রিল', 'May' => 'মে', 'Jun' => 'জুন',
                'Jul' => 'জুলাই', 'Aug' => 'আগস্ট', 'Sep' => 'সেপ্টেম্বর', 'Oct' => 'অক্টোবর', 'Nov' => 'নভেম্বর', 'Dec' => 'ডিসেম্বর'];
            if (isset($months[$m[1]])) {
                $out = $digits($m[2]) . ' ' . $months[$m[1]] . ', ' . $digits($m[3]);

                return isset($m[4]) ? $out . ' · ' . $digits($m[4]) . ' মিনিটে পড়া' : $out;
            }
        }

        return null;
    }

    public static function text(string $text): string
    {
        return self::lookup($text) ?? $text;
    }

    /** Translate a whole HTML page. */
    public static function html(string $html): string
    {
        // Odd indexes are the script and style blocks, which are left alone.
        $parts = preg_split('~(<script\b.*?</script>|<style\b.*?</style>)~is', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        foreach ($parts as $i => $part) {
            if ($i % 2 === 1) {
                continue;
            }
            $part = preg_replace_callback('~>([^<>]+)(?=<)~', function ($m) {
                preg_match('~^(\s*)(.*?)(\s*)$~s', $m[1], $w);
                $bn = self::lookup(html_entity_decode($w[2], ENT_QUOTES | ENT_HTML5, 'UTF-8'));

                return '>' . $w[1] . ($bn !== null ? htmlspecialchars($bn, ENT_NOQUOTES, 'UTF-8') : $w[2]) . $w[3];
            }, $part);
            $part = preg_replace_callback('~\b(placeholder|aria-label|title|alt|content)="([^"]*)"~', function ($m) {
                $bn = self::lookup(html_entity_decode($m[2], ENT_QUOTES | ENT_HTML5, 'UTF-8'));

                return $bn !== null ? $m[1] . '="' . htmlspecialchars($bn, ENT_QUOTES, 'UTF-8') . '"' : $m[0];
            }, $part);
            // The typing animation in the home page hero keeps its lines as a JSON list in an attribute.
            $part = preg_replace_callback('~data-typing=\'(\[.*?\])\'~s', function ($m) {
                $list = json_decode($m[1], true);
                if (!is_array($list)) {
                    return $m[0];
                }

                return "data-typing='" . json_encode(array_map(fn ($t) => self::text((string) $t), $list), JSON_UNESCAPED_UNICODE | JSON_HEX_APOS) . "'";
            }, $part);
            $parts[$i] = $part;
        }

        return implode('', $parts);
    }
}

<?php

namespace App\Support;

class PointIcon
{
    /** First match wins. Keys are lower-case words found in a point's text, values are names from the icon component. */
    private const MAP = [
        ' ai ' => 'bot', ' ai-' => 'bot', 'chatbot' => 'bot', 'assistant' => 'bot', 'automat' => 'bot', 'voice' => 'bot',
        'whatsapp' => 'whatsapp',
        'email' => 'mail', 'newsletter' => 'mail',
        'sms' => 'phone', 'mobile' => 'phone', 'android' => 'phone', ' ios' => 'phone', ' app' => 'phone', 'push notification' => 'phone',
        'security' => 'shield', 'secure' => 'shield', 'backup' => 'shield', 'protection' => 'shield', 'firewall' => 'shield',
        'malware' => 'shield', 'penetration' => 'shield', 'vulnerab' => 'shield', 'encrypt' => 'shield', 'audit' => 'shield',
        'dashboard' => 'chart', 'analytics' => 'chart', 'report' => 'chart', 'revenue' => 'chart', 'seo' => 'chart', 'tracking' => 'chart',
        'cloud' => 'cloud', 'hosting' => 'cloud', 'deployment' => 'cloud', 'server' => 'cloud',
        'integration' => 'layers', ' api' => 'layers', 'system' => 'layers', 'architecture' => 'layers',
        'customer' => 'users', 'user' => 'users', 'vendor' => 'users', 'seller' => 'users', 'student' => 'users', 'patient' => 'users',
        'employee' => 'users', 'staff' => 'users', 'team' => 'users', 'community' => 'users', 'audience' => 'users', ' role' => 'users',
        'design' => 'sparkle', 'creative' => 'sparkle', 'brand' => 'sparkle', 'video' => 'sparkle', 'content' => 'sparkle', 'logo' => 'sparkle',
        'payment' => 'gear', 'billing' => 'gear', 'management' => 'gear', 'configuration' => 'gear', 'optimization' => 'gear',
    ];

    public static function for(string $text): string
    {
        $t = ' ' . strtolower($text) . ' ';
        foreach (self::MAP as $needle => $icon) {
            if (str_contains($t, $needle)) {
                return $icon;
            }
        }

        return 'check';
    }
}

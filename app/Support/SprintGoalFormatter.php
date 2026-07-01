<?php

namespace App\Support;

class SprintGoalFormatter
{
    protected const ALLOWED_TAGS = '<p><br><br/><strong><b><em><i><u><ul><ol><li><h1><h2><h3><h4><h5><h6><a><blockquote><div><span>';

    public static function sanitize(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return null;
        }

        $clean = strip_tags($html, self::ALLOWED_TAGS);
        $clean = preg_replace('/<a\s+([^>]*?)href\s*=\s*"(javascript:[^"]*)"/i', '<a $1href="#"', $clean);
        $clean = preg_replace('/<a\s+([^>]*?)href\s*=\s*\'(javascript:[^\']*)\'/i', '<a $1href="#"', $clean);

        return trim($clean) ?: null;
    }

    public static function plainText(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $text = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $text = preg_replace('/<\/p>/i', "\n\n", $text);
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace("/\n{3,}/", "\n\n", $text));
    }

    public static function isEmpty(?string $html): bool
    {
        return self::plainText($html) === '';
    }
}

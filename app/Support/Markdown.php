<?php

namespace App\Support;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use Tempest\Highlight\CommonMark\HighlightExtension;

/**
 * Renders post bodies: GitHub-flavoured Markdown with server-side code
 * highlighting (fenced blocks with a language, e.g. ```php). Raw HTML is
 * allowed because only the owner writes posts.
 */
class Markdown
{
    private static ?MarkdownConverter $converter = null;

    public static function render(string $markdown): string
    {
        return self::converter()->convert($markdown)->getContent();
    }

    private static function converter(): MarkdownConverter
    {
        if (self::$converter === null) {
            $environment = new Environment([
                'html_input' => 'allow',
                'allow_unsafe_links' => false,
            ]);
            $environment->addExtension(new CommonMarkCoreExtension);
            $environment->addExtension(new GithubFlavoredMarkdownExtension);
            $environment->addExtension(new HighlightExtension);

            self::$converter = new MarkdownConverter($environment);
        }

        return self::$converter;
    }
}

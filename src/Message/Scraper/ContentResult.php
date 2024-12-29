<?php declare(strict_types=1);

namespace Sofyco\Bundle\SpiderBundle\Message\Scraper;

final class ContentResult
{
    public string $url;

    public ?string $image;

    public ?string $title;

    public ?string $description;

    public ?string $content;

    public array $tags;

    public array $categories;

    public ?\DateTime $publishedAt;

    public function __construct(string $url, ?string $image = null, ?string $title = null, ?string $description = null, ?string $content = null, array $tags = [], array $categories = [], ?\DateTime $publishedAt = null)
    {
        $this->url = $url;
        $this->image = $image;
        $this->title = $title;
        $this->description = $description;
        $this->content = $content;
        $this->tags = $tags;
        $this->categories = $categories;
        $this->publishedAt = $publishedAt;
    }
}

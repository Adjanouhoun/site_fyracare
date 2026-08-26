<?php

namespace App\Twig;

use App\Entity\SiteContent;
use App\Repository\SiteContentRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class SiteContentExtension extends AbstractExtension
{
    public function __construct(private SiteContentRepository $contents, private RequestStack $requestStack) {}
    public function getFilters(): array { return [new TwigFilter('cms', [$this, 'text'])]; }
    public function getFunctions(): array { return [new TwigFunction('cms_image', [$this, 'image'])]; }
    public function text(string $fallback, string $code, array $parameters = []): string
    {
        $item = $this->contents->findActiveByCode($code);
        if (!$item || $item->getType() !== SiteContent::TYPE_TEXT) return $fallback;
        $value = trim((string) $item->getContent($this->requestStack->getCurrentRequest()?->getLocale() ?? 'fr')) ?: $fallback;
        return $parameters ? strtr($value, $parameters) : $value;
    }
    public function image(string $code, string $fallback): string
    {
        $item = $this->contents->findActiveByCode($code);
        return $item && $item->getType() === SiteContent::TYPE_IMAGE && $item->getImage() ? '/uploads/content/'.$item->getImage() : $fallback;
    }
}

<?php

namespace App\Tests\Seo;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SeoMarkupTest extends WebTestCase
{
    public function testLocalizedPageExposesCanonicalAlternatesAndStructuredData(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/fr/a-propos');

        self::assertResponseIsSuccessful();
        self::assertSame('http://localhost/fr/a-propos', $crawler->filter('link[rel="canonical"]')->attr('href'));
        self::assertCount(4, $crawler->filter('link[rel="alternate"][hreflang]'));
        self::assertSame(
            ['fr', 'en', 'ar', 'x-default'],
            $crawler->filter('link[rel="alternate"][hreflang]')->each(static fn ($node): string => $node->attr('hreflang')),
        );

        $schemas = $crawler->filter('script[type="application/ld+json"]')->each(
            static fn ($node): array => json_decode($node->text(), true, 512, JSON_THROW_ON_ERROR),
        );
        self::assertSame('MedicalClinic', $schemas[0]['@type']);
        self::assertSame('BreadcrumbList', $schemas[1]['@type']);
        self::assertSame('FyraCare', $schemas[0]['name']);
    }

    public function testSitemapContainsEditorialPagesForEveryLocale(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/sitemap.xml');

        self::assertResponseIsSuccessful();
        $xml = $crawler->text();
        foreach (['fr', 'en', 'ar'] as $locale) {
            self::assertStringContainsString("/$locale/a-propos", $xml);
            self::assertStringContainsString("/$locale/notre-expertise", $xml);
            self::assertStringContainsString("/$locale/galerie", $xml);
        }
    }
}

<?php

namespace App\Service;

use App\Entity\SiteContent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Yaml\Yaml;

final class SiteContentSynchronizer
{
    public function __construct(private EntityManagerInterface $em, private string $projectDir) {}
    public function synchronize(): void
    {
        $repo = $this->em->getRepository(SiteContent::class);
        $existing = [];
        foreach ($repo->findAll() as $item) $existing[$item->getCode()] = $item;
        $translations = [];
        foreach (['fr','en','ar'] as $locale) {
            $data = Yaml::parseFile($this->projectDir.'/translations/messages.'.$locale.'.yaml');
            $translations[$locale] = $this->flatten($data);
        }
        $changed = false;
        foreach ($translations['fr'] as $code => $value) {
            if (!is_string($value) || isset($existing[$code])) continue;
            [$sitePage, $section] = $this->classification($code);
            $item = (new SiteContent())->setCode($code)->setLabel($this->label($code))->setSitePage($sitePage)->setPage($section)->setContentFr($value)->setContentEn($translations['en'][$code] ?? $value)->setContentAr($translations['ar'][$code] ?? $value);
            $this->em->persist($item); $changed = true;
        }
        foreach ($this->imageDefinitions() as $code => [$label, $page]) {
            if (isset($existing[$code])) continue;
            [$sitePage, $section] = $this->classification($code);
            $this->em->persist((new SiteContent())->setCode($code)->setLabel($label)->setSitePage($sitePage)->setPage($section)->setType(SiteContent::TYPE_IMAGE));
            $changed = true;
        }
        foreach ($existing as $item) {
            [$sitePage, $section] = $this->classification($item->getCode());
            if ($item->getSitePage() !== $sitePage || $item->getPage() !== $section) {
                $item->setSitePage($sitePage)->setPage($section); $changed = true;
            }
        }
        if ($changed) $this->em->flush();
    }
    private function flatten(array $data, string $prefix = ''): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            $code = $prefix === '' ? (string) $key : $prefix.'.'.$key;
            if (is_array($value)) $result += $this->flatten($value, $code); else $result[$code] = $value;
        }
        return $result;
    }
    private function label(string $code): string { return ucfirst(str_replace(['.','_'], ' ', $code)); }
    private function classification(string $code): array
    {
        $prefix = explode('.', $code, 2)[0];
        $sitePage = match ($prefix) {
            'home','hero','manifesto','founder','experience','testimonials','booking','appointment','welcome' => 'home',
            'about','about_page' => 'about', 'expertise','expertise_page' => 'expertise',
            'services','services_page','service_detail','trust' => 'services',
            'advice_page' => 'advice', 'contact' => 'contact', 'legal' => 'legal',
            'gallery','gallery_page' => 'gallery', 'nav','footer','actions','meta','seo','global' => 'global',
            default => 'general',
        };
        $section = str_contains($code, 'image') || $code === 'global.logo' ? 'images' : match ($prefix) {
            'about_page' => 'contenu', 'expertise_page' => 'contenu', 'services_page' => 'introduction',
            'advice_page' => 'journal', 'gallery_page' => 'introduction',
            'service_detail' => 'details', 'trust' => 'trust', 'legal' => 'legal', 'global' => 'general',
            default => $prefix,
        };
        return [$sitePage, $section];
    }
    private function imageDefinitions(): array
    {
        return [
            'global.logo' => ['Logo du site', 'general'],
            'global.social_image' => ['Image de partage du site', 'general'],
            'home.hero_image' => ['Accueil — image principale', 'home'],
            'home.founder_image' => ['Accueil — portrait de la fondatrice', 'home'],
            'home.experience_image' => ['Accueil — espace de soin', 'home'],
            'about.hero_image' => ['À propos — image principale', 'about_page'],
            'expertise.hero_image' => ['Expertise — portrait principal', 'expertise_page'],
            'services.hero_image' => ['Prestations — image principale', 'services_page'],
        ];
    }
}

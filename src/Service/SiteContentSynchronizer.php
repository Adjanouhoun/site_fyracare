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
            $item = (new SiteContent())->setCode($code)->setLabel($this->label($code))->setPage(strtok($code, '.') ?: 'general')->setContentFr($value)->setContentEn($translations['en'][$code] ?? $value)->setContentAr($translations['ar'][$code] ?? $value);
            $this->em->persist($item); $changed = true;
        }
        foreach ($this->imageDefinitions() as $code => [$label, $page]) {
            if (isset($existing[$code])) continue;
            $this->em->persist((new SiteContent())->setCode($code)->setLabel($label)->setPage($page)->setType(SiteContent::TYPE_IMAGE));
            $changed = true;
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

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260827003000 extends AbstractMigration
{
    public function getDescription(): string { return 'Ajoute les exemples de galerie FyraCare classes par thematique'; }

    public function up(Schema $schema): void
    {
        $categories = [
            ['naissance-premiers-soins', 'Naissance & premiers soins', 'Birth & newborn care', 'الولادة والعناية الأولى', 'Des gestes professionnels et humains autour de la naissance et des premiers instants.', 'Professional, compassionate care around birth and the first moments.', 'رعاية مهنية وإنسانية حول الولادة واللحظات الأولى.', 10],
            ['preparation-naissance-perinee', 'Préparation à la naissance & périnée', 'Birth preparation & pelvic health', 'التحضير للولادة وصحة الحوض', 'Préparer le corps, comprendre le périnée et avancer vers la naissance avec confiance.', 'Prepare the body, understand pelvic health and approach birth with confidence.', 'تهيئة الجسم وفهم صحة الحوض والاستعداد للولادة بثقة.', 20],
            ['bien-etre-traditions', 'Bien-être & traditions', 'Wellness & traditions', 'العافية والتقاليد', 'Des soins de bien-être qui associent écoute, détente et savoir-faire traditionnels.', 'Wellness care combining attentive support, relaxation and traditional knowledge.', 'رعاية تجمع بين الإصغاء والاسترخاء والخبرات التقليدية.', 30],
            ['centre-fyracare', 'Le centre FyraCare', 'The FyraCare centre', 'مركز فيراكير', 'Découvrez les espaces, l’identité et l’atmosphère du centre.', 'Discover the spaces, identity and atmosphere of the centre.', 'اكتشفوا فضاءات المركز وهويته وأجواءه.', 40],
            ['parcours-rayonnement', 'Parcours & rayonnement', 'Journey & outreach', 'المسيرة والتأثير', 'Les actions, reconnaissances et contenus qui prolongent l’engagement de FyraCare.', 'Initiatives, recognition and content extending FyraCare’s commitment.', 'المبادرات والتقدير والمحتوى الذي يعزز رسالة فيراكير.', 50],
        ];
        foreach ($categories as $category) {
            $values = array_map([$this->connection, 'quote'], array_slice($category, 0, 7));
            $this->addSql(sprintf('INSERT OR IGNORE INTO gallery_category (slug, title_fr, title_en, title_ar, description_fr, description_en, description_ar, display_order, active) VALUES (%s, %d, 1)', implode(', ', $values), $category[7]));
        }

        $items = [
            ['naissance-premiers-soins', 'gallery-naissance-accueil.jpg', 'Accueil du nouveau-né', 'Welcoming the newborn', 'استقبال المولود', 'Un accompagnement attentif lors des premiers instants après la naissance.', 10, 1],
            ['naissance-premiers-soins', 'gallery-naissance-soins.jpeg', 'Premiers soins après la naissance', 'First care after birth', 'العناية الأولى بعد الولادة', 'Des gestes précis réalisés avec douceur pour la mère et son bébé.', 20, 0],
            ['preparation-naissance-perinee', 'gallery-perinee-anatomie.jpg', 'Comprendre le périnée', 'Understanding pelvic health', 'فهم صحة الحوض', 'Des supports anatomiques pour mieux comprendre le corps et préparer la naissance.', 10, 1],
            ['preparation-naissance-perinee', 'gallery-preparation-outils.jpg', 'Outils de préparation à la naissance', 'Birth preparation tools', 'أدوات التحضير للولادة', 'Des exercices et outils concrets pour avancer avec davantage de confiance.', 20, 0],
            ['preparation-naissance-perinee', 'gallery-preparation-bassin.jpg', 'Transmission autour du bassin', 'Learning about the pelvis', 'التوعية حول الحوض', 'Une pédagogie claire autour du bassin, du périnée et de la naissance.', 30, 0],
            ['preparation-naissance-perinee', 'gallery-atelier-ballon.jpeg', 'Atelier collectif de préparation', 'Group birth preparation workshop', 'ورشة جماعية للتحضير', 'Un moment collectif d’apprentissage, de mouvement et d’échange.', 40, 0],
            ['preparation-naissance-perinee', 'gallery-massage-prenatal.jpeg', 'Massage prénatal', 'Prenatal massage', 'تدليك ما قبل الولادة', 'Un soin adapté pour favoriser la détente et le confort pendant la grossesse.', 50, 0],
            ['bien-etre-traditions', 'gallery-damp-traditionnel.jpg', 'Damp traditionnel', 'Traditional Damp care', 'العناية التقليدية دامب', 'Un savoir-faire traditionnel proposé dans une approche attentive et apaisante.', 10, 1],
            ['bien-etre-traditions', 'gallery-damp-simple.jpeg', 'Le Damp au centre', 'Damp care at the centre', 'رعاية دامب في المركز', 'Un soin de bien-être dans un espace calme pensé pour la détente.', 20, 0],
            ['centre-fyracare', 'gallery-centre-identite.png', 'L’univers La Sage Fyra', 'La Sage Fyra identity', 'هوية الحكيمة فيرا', 'Un univers de sensibilisation qui prolonge les engagements du centre.', 10, 1],
            ['centre-fyracare', 'gallery-centre-salle.JPG', 'La salle de soin', 'The treatment room', 'غرفة العناية', 'Un espace intime et calme dédié aux soins et au bien-être.', 20, 0],
            ['parcours-rayonnement', 'gallery-parcours-owla.jpg', 'Aminata Diarra — parcours OWLA', 'Aminata Diarra — OWLA journey', 'أميناتا ديارا — مسيرة أولا', 'Une reconnaissance de son engagement en santé maternelle et reproductive.', 10, 1],
            ['parcours-rayonnement', 'gallery-brochure-cover.jpg', 'Brochure Protect & Care', 'Protect & Care brochure', 'كتيب الحماية والرعاية', 'La couverture de la brochure de présentation FyraCare.', 20, 0],
        ];
        foreach ($items as $item) {
            [$slug, $file, $fr, $en, $ar, $caption, $order, $featured] = $item;
            $this->addSql(sprintf(
                "INSERT INTO gallery_item (type, category_id, title_fr, title_en, title_ar, caption_fr, caption_en, caption_ar, media_file, video_url, thumbnail, display_order, active, featured, created_at) SELECT 'image', id, %s, %s, %s, %s, %s, %s, %s, NULL, NULL, %d, 1, %d, CURRENT_TIMESTAMP FROM gallery_category WHERE slug = %s",
                $this->connection->quote($fr), $this->connection->quote($en), $this->connection->quote($ar), $this->connection->quote($caption), $this->connection->quote($caption), $this->connection->quote($caption), $this->connection->quote($file), $order, $featured, $this->connection->quote($slug)
            ));
        }
    }

    public function down(Schema $schema): void
    {
        $files = ['gallery-naissance-accueil.jpg','gallery-naissance-soins.jpeg','gallery-perinee-anatomie.jpg','gallery-preparation-outils.jpg','gallery-preparation-bassin.jpg','gallery-atelier-ballon.jpeg','gallery-massage-prenatal.jpeg','gallery-damp-traditionnel.jpg','gallery-damp-simple.jpeg','gallery-centre-salle.JPG','gallery-centre-identite.png','gallery-parcours-owla.jpg','gallery-brochure-cover.jpg'];
        $this->addSql('DELETE FROM gallery_item WHERE media_file IN ('.implode(', ', array_map([$this->connection, 'quote'], $files)).')');
        $this->addSql("DELETE FROM gallery_category WHERE slug IN ('naissance-premiers-soins','preparation-naissance-perinee','bien-etre-traditions','centre-fyracare','parcours-rayonnement')");
    }
}

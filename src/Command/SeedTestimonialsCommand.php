<?php

namespace App\Command;

use App\Entity\Testimonial;
use App\Repository\TestimonialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:seed-testimonials', description: 'Ajoute des avis de démonstration')]
final class SeedTestimonialsCommand extends Command
{
    public function __construct(private EntityManagerInterface $em, private TestimonialRepository $repository) { parent::__construct(); }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->repository->count([]) > 0) { $output->writeln('<comment>Des avis existent déjà.</comment>'); return Command::SUCCESS; }
        $items = [
            ['Mariam A.', 'Accompagnement à la naissance', 'Je me suis sentie écoutée, rassurée et accompagnée à chaque étape. FyraCare est devenu un véritable repère dans mon parcours de maternité.', 5],
            ['Aïcha M.', 'Massage Prénatal', 'Un moment de calme et de douceur qui m’a vraiment aidée à soulager mes tensions pendant la grossesse. L’accueil était très attentionné.', 5],
            ['Hawa S.', 'Préparation à la naissance', 'Les explications étaient claires et les exercices très utiles. Je suis arrivée à l’accouchement plus confiante et mieux préparée.', 5],
        ];
        foreach ($items as [$author, $care, $content, $rating]) $this->em->persist((new Testimonial())->setAuthor($author)->setCare($care)->setContent($content)->setRating($rating)->setStatus(Testimonial::STATUS_APPROVED));
        $this->em->flush();
        $output->writeln('<info>Avis de démonstration ajoutés.</info>');
        return Command::SUCCESS;
    }
}

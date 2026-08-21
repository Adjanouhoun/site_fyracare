<?php
namespace App\Command;
use App\Entity\Service;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCommand(name: 'app:seed-services', description: 'Installe les prestations initiales FyraCare')]
class SeedServicesCommand extends Command
{
    public function __construct(private EntityManagerInterface $em, private ServiceRepository $services, private TranslatorInterface $translator) { parent::__construct(); }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $items = [
            ['pelvic_rehab','Rééducation périnéale et abdominale','Pelvic floor and abdominal rehabilitation','عادة تأهيل منطقة العجان والبطن','800'],
            ['birth_support','Accompagnement à la Naissance','Birth support','الدعم عند الوالدة','2000'],
            ['rebozo','Closing of the Bones','Closing of the Bones','تضميد العظام','1200'],
            ['damp','Damp traditional pour les femmes en post nuptials, partum, ou abortum','Traditional Damp care','لطب التقليدي للنساء في مرحلة ما بعد الزواج أو الولادة أو الإجهاض','600'],
            ['breastfeeding','Consultation en Allaitement','Breastfeeding consultation','استشارة حول الرضاعة الطبيعية','600'],
            ['perineum','Préparation du Périnée','Perineal preparation','تحضير العجان','600'],
            ['birth_prep','Préparation à la naissance','Birth preparation','التحضير للوالدة','800'],
            ['prenatal_massage','Massage Prénatal','Prenatal massage','تدليك ما قبل الولادة','800'],
        ];
        foreach ($items as $index => [$code,$fr,$en,$ar,$price]) {
            $key = 'services.catalog.'.$code.'.text';
            $service = $this->services->findOneBy(['code' => $code]) ?? (new Service())->setCode($code);
            $service->setTitleFr($fr)->setTitleEn($en)->setTitleAr($ar)->setDescriptionFr($this->translator->trans($key, locale: 'fr'))->setDescriptionEn($this->translator->trans($key, locale: 'en'))->setDescriptionAr($this->translator->trans($key, locale: 'ar'))->setPrice($price)->setDisplayOrder($index + 1)->setActive(true)->setFeatured(in_array($code, ['birth_support','birth_prep','prenatal_massage'], true));
            $this->em->persist($service);
        }
        $this->em->flush();
        $output->writeln('<info>Prestations initiales installées.</info>');
        return Command::SUCCESS;
    }
}

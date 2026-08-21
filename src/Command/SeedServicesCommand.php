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
            ['pelvic_rehab','Rééducation périnéale et abdominale','Pelvic floor and abdominal rehabilitation','إعادة تأهيل العجان والبطن'],
            ['birth_support','Accompagnement à la naissance','Birth support','المرافقة أثناء الولادة'],
            ['rebozo','Closing of the Bones — Rebozo','Closing of the Bones — Rebozo','طقس إغلاق العظام — ريبوزو'],
            ['damp','Damp traditionnel','Traditional Damp care','عناية دامب التقليدية'],
            ['breastfeeding','Consultation en allaitement','Breastfeeding consultation','استشارة الرضاعة الطبيعية'],
            ['perineum','Préparation du périnée','Perineal preparation','تحضير منطقة العجان'],
            ['birth_prep','Préparation à la naissance','Birth preparation','التحضير للولادة'],
            ['prenatal_massage','Massage prénatal','Prenatal massage','تدليك ما قبل الولادة'],
        ];
        foreach ($items as $index => [$code,$fr,$en,$ar]) {
            if ($this->services->findOneBy(['code' => $code])) continue;
            $key = 'services.catalog.'.$code.'.text';
            $service = (new Service())->setCode($code)->setTitleFr($fr)->setTitleEn($en)->setTitleAr($ar)->setDescriptionFr($this->translator->trans($key, locale: 'fr'))->setDescriptionEn($this->translator->trans($key, locale: 'en'))->setDescriptionAr($this->translator->trans($key, locale: 'ar'))->setDisplayOrder($index + 1)->setActive(true)->setFeatured(in_array($code, ['birth_support','birth_prep','prenatal_massage'], true));
            $this->em->persist($service);
        }
        $this->em->flush();
        $output->writeln('<info>Prestations initiales installées.</info>');
        return Command::SUCCESS;
    }
}

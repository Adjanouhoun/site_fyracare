<?php
namespace App\Command;
use App\Entity\Availability;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
#[AsCommand(name:'app:seed-availabilities',description:'Crée des créneaux de démonstration pour les 14 prochains jours')]
final class SeedAvailabilitiesCommand extends Command {
 public function __construct(private EntityManagerInterface $em){parent::__construct();}
 protected function execute(InputInterface $i,OutputInterface $o):int{$count=0;$today=new \DateTimeImmutable('today');for($d=1;$d<=14;$d++){ $date=$today->modify("+$d days");if(in_array((int)$date->format('N'),[5,7],true))continue;foreach(['09:00','11:00','15:00','17:00'] as $time){$slot=new Availability();$slot->setStartsAt(new \DateTimeImmutable($date->format('Y-m-d').' '.$time));$this->em->persist($slot);$count++;}}$this->em->flush();$o->writeln("$count créneaux créés.");return Command::SUCCESS;}
}

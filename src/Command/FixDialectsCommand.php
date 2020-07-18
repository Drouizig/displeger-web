<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use App\Entity\Verb;
use App\Entity\VerbLocalization;
use App\Entity\VerbTranslation;
use App\Repository\VerbLocalizationRepository;

class FixDialectsCommand extends Command
{
    protected static $defaultName = 'app:fix-dialect';

    /** @var EntityManagerInterface */
    protected $em;

    protected $batchBuffer = [];

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('Import verbs from old csv file')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var VerbLocalizationRepository */
        $repo = $this->em->getRepository(VerbLocalization::class);
        $qb = $repo->createQueryBuilder('a');
        $qb->where('a.dialectCode is not null');

        $result = $qb->getQuery()->getResult();

        $c = 0;
        $batchSize = 10;
        /** @var VerbLocalization $verbLocalization  */
        foreach($result as $verbLocalization) {
            dump($verbLocalization->getInfinitive());
            if(count($verbLocalization->getDialectCode()) === 1
            && substr($verbLocalization->getDialectCode()[0], 0, 2) === 'a:') {
                $verbLocalization->setDialectCode(unserialize($verbLocalization->getDialectCode()[0]));
                $this->em->persist($verbLocalization);
                $c++;
                if($c == $batchSize) {
                    $c = 0;
                    $this->em->flush();
                }
            }
        }

        $this->em->flush();
    }
}

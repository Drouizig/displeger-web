<?php

namespace App\Command;

use App\Entity\VerbLocalization;
use App\Util\VerbouManager;
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
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class ExportConjugationsCommand extends Command
{
    protected static $defaultName = 'app:export-conjugations';

    /** @var EntityManagerInterface */
    protected $em;

    /** @var VerbouManager */
    protected $verbouManager;

    public function __construct(EntityManagerInterface $em, VerbouManager $verbouManager)
    {
        parent::__construct();
        $this->em = $em;
        $this->verbouManager = $verbouManager;
    }

    protected function configure()
    {
        $this
        ->setDescription('Add a short description for your command')
        ->addArgument('file', InputArgument::REQUIRED, 'Argument description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        
        $data=[];
        $verbou = $this->em->getRepository(Verb::class)->findBy([], []);
        /** @var Verb $verb */
        foreach ($verbou as $verb) {
            /** @var VerbLocalization $verbLocalization */
            foreach($verb->getLocalizations() as $verbLocalization) {

                $endings = $this->verbouManager->getEndings($verbLocalization->getCategory(), $verbLocalization->getDialectCode());
                $flatEndings = iterator_to_array($this->extractFromDialect($endings['standard']));
                foreach($endings['localized'] as $dialect) {
                    $flatDialectEndings = $this->extractFromDialect($dialect);

                    foreach($flatDialectEndings as $dialectEnding) {
                        if(!in_array($dialectEnding, $flatEndings)) {
                            $flatEndings[] = $dialectEnding;
                    }
                    }
                }
            }
            $data[] = $verbLocalization->getInfinitive();
            foreach($flatEndings as $ending) {
                $data[] = $verbLocalization->getBase().$ending;
            }
        }


                
        file_put_contents(
            $input->getArgument('file'),
            implode("\n", $data)
        );

        return 1;
    }

    private function extractFromDialect($times)
    {
        foreach($times as $time) {
            if(is_array($time)) {
                foreach($time as $person) {
                    foreach($person as $ending) {
                        yield $ending;
                    }
                }
            } else if(is_string($time)) {
                yield $time;
            }
        }
    }
}

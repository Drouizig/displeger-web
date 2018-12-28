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

class ImportVerbsCommand extends Command
{
    protected static $defaultName = 'app:import-verbs';

    const ANV_VERB = 'anv_verb';
    const DIAZ_VERB = 'diaz_verb';
    const RUMMAD = 'rummad';
    const GALLEG = 'galleg';
    const SOAZNEG = 'saozneg'; 

    /** @var EntityManagerInterface */
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
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
        $filename = $input->getArgument('file');


        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder([CsvEncoder::DELIMITER_KEY => ';'])]);

        $c = 0;
        $bashSize = 10;
        $data = $serializer->decode(file_get_contents($filename), 'csv');
        foreach($data as $line) {
            $c++;
            $verb = new Verb();
            $verb->setAnvVerb($line[self::ANV_VERB]);
            $verb->setPennrann($line[self::DIAZ_VERB]);
            $verb->setCategory($line[self::RUMMAD]);
            if($line[self::GALLEG] !== '#galleg') {
                $verb->setGalleg($line[self::GALLEG]);
            }
            if($line[self::SOAZNEG] !== '#saozneg') {
                $verb->setSaozneg($line[self::SOAZNEG]);
            }
            $this->em->persist($verb);
            if($c == $bashSize) {
                $c = 0;
                $this->em->flush();
            }
            
        }
        $this->em->flush();

        
        
    }
}

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

class ExportCsvCommand extends Command
{
    protected static $defaultName = 'app:export-csv';

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
        $io = new SymfonyStyle($input, $output);
        
        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder([CsvEncoder::DELIMITER_KEY => ';'])]);

        $data=[];
        foreach ($this->em->getRepository(Verb::class)->findBy([], ['category' => 'ASC', 'anvVerb' => 'ASC', 'pennrann' => 'ASC']) as $verb) {
            $rowdata['anv_verb'] = $verb->getAnvVerb() ? $verb->getAnvVerb() : '#anv-verb';
            $rowdata['diaz_verb'] = $verb->getPennrann();
            $rowdata['rummad'] = $verb->getCategory();
            $rowdata['galleg'] = $verb->getGalleg() ? $verb->getGalleg() : '#galleg';
            $rowdata['saozneg'] = $verb->getSaozneg() ? $verb->getSaozneg() : '#saozneg';
            $data[] = $rowdata;
        }
        
        file_put_contents(
            $input->getArgument('file'),
            $serializer->encode($data, 'csv')
        );


    }
}

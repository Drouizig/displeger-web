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
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

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
        
        $data=[];
        $verbou = $this->em->getRepository(Verb::class)->findBy([], []);
        // foreach ( as $verb) {
        //     $rowdata['anv_verb'] = $verb->getAnvVerb() ? $verb->getAnvVerb() : '#anv-verb';
        //     $rowdata['diaz_verb'] = $verb->getPennrann();
        //     $rowdata['rummad'] = $verb->getCategory();
        //     $rowdata['galleg'] = $verb->getGalleg() ? $verb->getGalleg() : '#galleg';
        //     $rowdata['saozneg'] = $verb->getSaozneg() ? $verb->getSaozneg() : '#saozneg';
        //     $data[] = $rowdata;
        // }


        $manytomanyCallback = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
            return $innerObject instanceof Verb ? $innerObject->getId() : '';
        };
        
        $defaultContext = [
            AbstractNormalizer::CALLBACKS => [
                'auxilliaries' => $manytomanyCallback,
            ],
        ];
        
        $sourceCallback = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
            $ret = [];
            foreach($innerObject as $source) {
                $ret[] = $source->getCode();
            }
            return $ret;
        };
        
        $defaultContext = [
            AbstractNormalizer::CALLBACKS => [
                'sources' => $sourceCallback,
            ],
        ];


        $normalizer = new GetSetMethodNormalizer(null, null, null, null, null, $defaultContext);

        $encoders = [new JsonEncoder(new JsonEncode(JSON_PRETTY_PRINT))];
        // $normalizers = [new ObjectNormalizer()];
        $normalizers = [$normalizer];

        $serializer = new Serializer($normalizers, $encoders);

        $data = $serializer->serialize($verbou, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        
            ]
        );
                
        file_put_contents(
            $input->getArgument('file'),
            $data
        );


    }
}

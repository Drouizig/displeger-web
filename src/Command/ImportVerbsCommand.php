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

class ImportVerbsCommand extends Command
{
    protected static $defaultName = 'app:import-verbs';

    const ANV_VERB = 'anv_verb';
    const PENNRANN = 'diaz_verb';
    const RUMMAD = 'rummad';
    const GALLEG = 'galleg';
    const SAOZNEG = 'saozneg'; 

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
            ->setDescription('Import verbs from old csv file')
            ->addArgument('file', InputArgument::REQUIRED, 'filepath')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('file');

        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder([CsvEncoder::DELIMITER_KEY => ';'])]);

        $deleteCommand = 'DELETE FROM verbTranslation;DELETE FROM verbLocalization; DELETE FROM verb';
        $this->em->getConnection()->exec($deleteCommand);

        $verbLocalizationRepository = $this->em->getRepository(VerbLocalization::class);
        $data = $serializer->decode(file_get_contents($filename), 'csv');
        foreach($data as $line) {

            $verb = new Verb();
            /** @var VerbLocalization */
            $commonBaseVerbLocalization = $verbLocalizationRepository->findOneBy(['base' => $line[self::PENNRANN]]);
            if(null !== $commonBaseVerbLocalization) {
                $verb = $commonBaseVerbLocalization->getVerb();
            }

            $verbLocalization = new VerbLocalization();
            $verbLocalization->setInfinitive($line[self::ANV_VERB]);
            $verbLocalization->setBase($line[self::PENNRANN]);
            $verb->addLocalization($verbLocalization);
            $verb->setCategory($line[self::RUMMAD]);
            
            if (!$verb->hasTranslationInLanguage('fr_FR') && $line[self::GALLEG] !== '#galleg') {
                $verbTranslationFr = new VerbTranslation();
                $verbTranslationFr->setTranslation($line[self::GALLEG]);
                $verbTranslationFr->setLanguageCode('fr_FR');
                $verb->addTranslation($verbTranslationFr);
            }
            if (!$verb->hasTranslationInLanguage('en_GB') && $line[self::SAOZNEG] !== '#saozneg') {
                $verbTranslationFr = new VerbTranslation();
                $verbTranslationFr->setTranslation($line[self::SAOZNEG]);
                $verbTranslationFr->setLanguageCode('en_GB');
                $verb->addTranslation($verbTranslationFr);
            }
            $this->em->persist($verb);
            $this->em->flush();
            
        }
    }
}

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
        $counter = 0;
        $batchSize = 10;
        foreach($data as $line) {
            $counter++;

            $verb = new Verb();
            /** @var VerbLocalization */
            $commonBaseVerbLocalization = $verbLocalizationRepository->findOneBy(['base' => $line[self::PENNRANN]]);
            if(null !== $commonBaseVerbLocalization) {
                $verb = $commonBaseVerbLocalization->getVerb();
            } else {
                $batchVerbLocalization = $this->getVerbalBaseInBatchBuffer($line[self::PENNRANN]);
                if(null !== $batchVerbLocalization) {
                    $verb = $batchVerbLocalization->getVerb();
                }
            }

            $newVerb = false;
            foreach($verb->getLocalizations() as $localization) {
                if($localization->getCategory() != $line[self::RUMMAD]) {
                    $newVerb=true;
                }
            }
            if($newVerb) {
                $verb = new Verb();
            }
            
            $verbLocalization = new VerbLocalization();
            $verbLocalization->setInfinitive($line[self::ANV_VERB]);
            $verbLocalization->setBase($line[self::PENNRANN]);
            $verb->addLocalization($verbLocalization);
            $verbLocalization->setCategory($line[self::RUMMAD]);

            $this->batchBuffer[] = $verbLocalization;
            
            if (!$verb->hasTranslationInLanguage('fr') && $line[self::GALLEG] !== '#galleg') {
                $verbTranslation = new VerbTranslation();
                $verbTranslation->setTranslation($line[self::GALLEG]);
                $verbTranslation->setLanguageCode('fr');
                $verb->addTranslation($verbTranslation);
            }
            if (!$verb->hasTranslationInLanguage('en') && $line[self::SAOZNEG] !== '#saozneg') {
                $verbTranslation = new VerbTranslation();
                $verbTranslation->setTranslation($line[self::SAOZNEG]);
                $verbTranslation->setLanguageCode('en');
                $verb->addTranslation($verbTranslation);
            }
            $this->em->persist($verb);
            if ($counter >= $batchSize) {
                $this->em->flush();
                $counter = 0;
                $batchBuffer = [];
            }
            $verb = null;
            $verbLocalization = null;
            $verbTranslation = null;

        }
        $this->em->flush();
    }

    private function getVerbalBaseInBatchBuffer($verbalBase)
    {
        /** @var VerbLocalization $batchVerbLocalization */
        foreach($this->batchBuffer as $batchVerbLocalization) {
            if($batchVerbLocalization->getBase() === $verbalBase) {
                return $batchVerbLocalization;
            }
        }
        return null;
    } 
}

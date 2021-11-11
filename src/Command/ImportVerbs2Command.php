<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Verb;
use App\Entity\VerbLocalization;
use App\Entity\VerbTranslation;
use App\Entity\Source;
use App\Entity\Tag;
use App\Entity\VerbTag;
use App\Repository\VerbLocalizationRepository;
use App\Repository\SourceRepository;
use App\Repository\TagRepository;
use App\Repository\VerbRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImportVerbs2Command extends Command
{
    protected static $defaultName = 'app:import-verbs';

    const ANV_VERB = 'anv_verb';
    const PENNRANN = 'diaz_verb';
    const RUMMAD = 'rummad';
    const GALLEG = 'galleg';
    const SAOZNEG = 'saozneg'; 

    /** @var EntityManagerInterface */
    protected $em;
    /** @var ParameterBagInterface */
    protected $parameterBag;
    /** @var VerbRepository */
    protected $verbRepository;
    /** @var VerbLocalizationRepository */
    protected $verbLocalizationRepository;
    /** @var SourceRepository */
    protected $sourceRepository;
    /** @var TagRepository */
    protected $tagRepository;

    protected $batchBuffer = [];

    public function __construct(
        EntityManagerInterface $em, 
        ParameterBagInterface $parameterBag,
        VerbRepository $verbRepository,
        VerbLocalizationRepository $verbLocalizationRepository,
        SourceRepository $sourceRepository
        )
    {
        parent::__construct();
        $this->em = $em;
        $this->parameterBag = $parameterBag;
        $this->verbRepository = $verbRepository;
        $this->verbLocalizationRepository = $verbLocalizationRepository;
        $this->sourceRepository = $sourceRepository;
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

        if ($filename) {
            $handle = fopen($this->parameterBag->get('csv_directory').'/'.$filename, "r"); 
            $csvHandle = fgetcsv($handle, 0, ';');

            $batchSize = 10;
            $batchIndex = 0;
            // 1 : infinitive
            // 2 : pennrann
            // 3 : rummad
            // 4 : bzhg
            // 5 : galleg
            // 6 : saozneg
            // 8 : Mammenn
            // 9 : tikedennn
            while(($csvHandle = fgetcsv($handle, 0, ';'))!== FALSE) {
                /** @var VerbLocalization */
                $verb = $this->verbRepository->findOneBy(['infinitive' => $csvHandle[1]]);
                $tmpVerb = [];
                if($verb != null) {
                    $isModified = false;
                    if($csvHandle[2] !== $verb->getBase()) {
                         $verb->setBase($csvHandle[2]);
                         $isModified = true;
                    }
                    if($csvHandle[3] !== $verb->getCategory()) {
                        $verb->setCategory($csvHandle[3]);
                        $isModified = true;
                    }
                    if($verb->getVerb()->getTranslation('br') 
                    && $csvHandle[4] !== $verb->getVerb()->getTranslation('br')->getTranslation() 
                    && $csvHandle[4] !== '#brezhoneg') {
                        $verbTranslation = $verb->getVerb()->getTranslation('br');
                        if (null !== $verbTranslation) {
                            $verbTranslation = new VerbTranslation();
                            $verbTranslation->setLanguageCode('br');
                            $verb->getVerb()->addTranslation($verbTranslation);
                        }
                        $verbTranslation->setTranslation($csvHandle[4]);
                        $isModified = true;
                    }
                    if($verb->getVerb()->getTranslation('fr')
                    && $csvHandle[5] !== $verb->getVerb()->getTranslation('fr')->getTranslation() 
                    && $csvHandle[5] !== '#galleg') {
                        $verbTranslation = $verb->getVerb()->getTranslation('fr');
                        if (null !== $verbTranslation) {
                            $verbTranslation = new VerbTranslation();
                            $verbTranslation->setLanguageCode('fr');
                            $verb->getVerb()->addTranslation($verbTranslation);
                        }
                        $verbTranslation->setTranslation($csvHandle[5]);
                        $isModified = true;
                    }
                    if($csvHandle[6] !== $verb->getVerb()->getTranslation('en') && $csvHandle[6] !== '#saozneg') {
                        $verbTranslation = $verb->getVerb()->getTranslation('en');
                        if (null !== $verbTranslation) {
                            $verbTranslation = new VerbTranslation();
                            $verbTranslation->setLanguageCode('en');
                            $verb->getVerb()->addTranslation($verbTranslation);
                        }
                        $verbTranslation->setTranslation($csvHandle[6]);
                        $isModified = true;
                    }
                    $mamennou = explode(',', $csvHandle[7]);
                    foreach($mamennou as $mammenn) {
                        $mammenn = trim($mammenn);
                        /** @var Source $source */
                        $source = $this->sourceRepository->findOneBy(['code' => $mammenn]);
                        if(!$source || !$verb->hasSource($source)) {
                            if(!$source) {
                                $source = new Source();
                                $source->setCode($mammenn);
                                $this->em->persist($source);
                                $this->em->flush();
                            }
                            $verb->addSource($source);
                            $isModified = true;
                        }
                        
                    }
                    $tags = explode(',', $csvHandle[8]);
                    foreach($tags as $tag) {
                        $tag = trim($tag);
                        /** @var Tag */
                        $tagObject = $this->tagRepository->findOneBy(['code' => $tag]);
                        if(!$tagObject || !$verb->getVerb()->hasTag($tagObject)) {
                            if(!$tagObject) {
                                $tagObject = new Tag();
                                $tagObject->setCode($tag);
                                $this->em->persist($tagObject);
                                $this->em->flush();
                            }
                            $verbTag = $verb->getVerb()->getVerbTag($tagObject);
                            if(!$verbTag) {
                                $verbTag = new VerbTag();
                                $verbTag->setTag($tagObject);
                                $verbTag->setVerb($verb->getVerb());
                            }
                            $verb->getVerb()->addTag($verbTag);
                            $isModified = true;
                        }
                        
                    }
                    if($isModified) {
                        $this->em->persist($verb);
                        $this->em->persist($verb->getVerb());
                    }
                } else {
                    $verb = new Verb();
                    $verbLocalization = new VerbLocalization;
                    $verbLocalization->setVerb($verb);
                    $verbLocalization->setInfinitive($csvHandle[1]);
                    $verbLocalization->setBase($csvHandle[2]);
                    $verbLocalization->setCategory($csvHandle[3]);
                    if($csvHandle[4] != '' && $csvHandle[4] != '#brezhoneg') {
                        $brTranslation = new VerbTranslation();
                        $brTranslation->setTranslation($csvHandle[4]);
                        $brTranslation->setLanguageCode('br');
                        $verb->addTranslation($brTranslation);
                        $this->em->persist($brTranslation);
                    }
                    if($csvHandle[5] != '' && $csvHandle[5] != '#galleg') {
                        $frTranslation = new VerbTranslation();
                        $frTranslation->setTranslation($csvHandle[5]);
                        $frTranslation->setLanguageCode('fr');
                        $verb->addTranslation($frTranslation);
                        $this->em->persist($frTranslation);
                    }
                    if($csvHandle[6] != '' && $csvHandle[6] != '#saozneg') {
                        $enTranslation = new VerbTranslation();
                        $enTranslation->setTranslation($csvHandle[6]);
                        $enTranslation->setLanguageCode('en');
                        $verb->addTranslation($enTranslation);
                        $this->em->persist($enTranslation);
                    }
                    $mamennou = explode(',', $csvHandle[7]);
                    foreach($mamennou as $mammenn) {
                        $mammenn = trim($mammenn);
                        /** @var Source */
                        $source = $this->sourceRepository->findOneBy(['code' => $mammenn]);
                        if(!$source) {
                            $source = new Source();
                            $source->setCode($mammenn);
                            $this->em->persist($source);
                            $this->em->flush();
                        }
                        $verbLocalization->addSource($source);
                    }
                    $tags = explode(',', $csvHandle[8]);
                    foreach($tags as $tag) {
                        $tag = trim($tag);
                        /** @var Tag */
                        $tagObject = $this->tagRepository->findOneBy(['code' => $tag]);
                        if(!$tagObject) {
                            $tagObject = new Tag();
                            $tagObject->setCode($tag);
                            $this->em->persist($tag);
                            $this->em->flush();
                        }
                        $verbTag = new VerbTag();
                        $verbTag->setTag($tagObject);
                        $verbTag->setVerb($verb);
                        $verb->addTag($verbTag);
                        $this->em->persist($verbTag);
                    }
                    $verb->addLocalization($verbLocalization);
                    $this->em->persist($verbLocalization);
                    $this->em->persist($verb);
                }
                $batchIndex++;
                if($batchIndex > $batchSize) {
                    $batchIndex = 0;
                    $this->em->flush();
                }
            }

        }
    }
}
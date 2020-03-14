<?php

namespace App\Util;

use App\Repository\VerbLocalizationRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class StatisticsManager
{
    /** @var VerbLocalizationRepository */
    protected $verbLocalizationRepository;

    public function __construct(VerbLocalizationRepository $verbLocalizationRepository)
    {
        $this->verbLocalizationRepository = $verbLocalizationRepository;
    }

    public function getTotal() {
        return $this->verbLocalizationRepository->count([]);
    }


    public function getPercentage($criteria) {
        return $this->verbLocalizationRepository->count($criteria)/$this->getTotal();
    }

    public function getCategoryData()
    {
        $stats = $this->verbLocalizationRepository->findCategoryStatistics();
        $names = [];
        $total = 0;
        foreach($stats as $key => $stat) {
            if (!preg_match('/d[0-9]/', $stat['name'])) {
                $total += $stat['y'];
                $names[] = $stat['name'];
                unset($stats[$key]);
            } else {
                $stats[$key]['y'] = (int)$stat['y'];
            }
        }
        $stats[] = [
            'name' => 'Verboù direizh ('.join(',', $names).')',
            'y' => $total,
        ];
        return json_encode(array_values($stats));
    }
}
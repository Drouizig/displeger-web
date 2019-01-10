<?php

namespace App\Util;

use App\Repository\VerbRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class StatisticsManager
{
    /** @var VerbRepository */
    protected $verbRepository;

    public function __construct(VerbRepository $verbRepository)
    {
        $this->verbRepository = $verbRepository;
    }

    public function getTotal() {
        return $this->verbRepository->count([]);
    }


    public function getPercentage($criteria) {
        return $this->verbRepository->count($criteria)/$this->getTotal();
    }

    public function getCategoryData()
    {
        $stats = $this->verbRepository->findCategoryStatistics();
        $names = [];
        $total = 0;
        foreach($stats as $key => $stat) {
            if (!preg_match('/d[0-9]/', $stat['name'])) {
                $total += $stat['y'];
                $names[] = $stat['name'];
                unset($stats[$key]);
            }
        }
        $stats[] = [
            'name' => 'Verboù direizh ('.join(',', $names).')',
            'y' => $total,
        ];
        return json_encode(array_values($stats));
    }
}
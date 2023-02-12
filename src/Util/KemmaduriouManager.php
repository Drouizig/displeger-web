<?php

namespace App\Util;

class KemmaduriouManager
{
    protected $kemmaduriou;

    const BLOTAAT = 'blotaat';
    const BLOTAAT_GM = 'blotaat_gm';
    const CHWEZHAN = 'chwezhan';
    const KALETAAT = 'kaletaat';
    const KEMMESKET = 'kemmesket';
    const DIGLOK = 'diglok';
    const DIGLOK_K = 'diglok_k';

    public function __construct($kemmaduriou)
    {
        $this->kemmaduriou = $kemmaduriou;
    }

    public function mutateWord($word, $mutationType, $gouMutation = false)
    {
        $mutations = $this->kemmaduriou[$mutationType];
        $newLetter = '';
        $origin = '';
        if($gouMutation && ($mutationType === self::BLOTAAT || $mutationType === self::KEMMESKET || $mutationType === self::DIGLOK)) {
            $newLetter = '';
            $origin = 'g';
        } else {
            foreach ($mutations as $original => $result) {
                if (substr($word, 0, strlen($original)) === $original) {
                    $newLetter = $result;
                    $origin = $original;
                }
            }
        }
        return $newLetter.substr($word, strlen($origin));
    }
}
<?php

namespace App\Repository;

interface AdminRepositoryInterface
{
    public function getBackSearchQuery($search, $offset = null, $maxResults = null);
}

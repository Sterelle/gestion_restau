<?php

namespace App\Repositories;

use App\Models\Categorie;

interface CategorieRepositoryInterface
{
    /** @return Categorie[] */
    public function toutes(): array;
}

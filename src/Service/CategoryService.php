<?php

// src/Service/CategoryService.php
namespace App\Service;

use App\Repository\CategoryRepository;

class CategoryService
{
    public function __construct(private CategoryRepository $categoryRepository)
    {
    }

    public function getTopCategories()
    {
        $topCategories = $this->categoryRepository->findTopCategories();
        $mainCategory = array_shift($topCategories); // Récupère la première catégorie
        $firstTwoCategories = array_slice($topCategories, 0, 3); // Récupère les deux suivantes
        $lastTwoCategories = array_slice($topCategories, 3, 3); // Récupère les deux dernières

        return [
            'mainCategory' => $mainCategory,
            'firstTwoCategories' => $firstTwoCategories,
            'lastTwoCategories' => $lastTwoCategories,
            'categories' => $this->categoryRepository->findAll(),
        ];
    }
}

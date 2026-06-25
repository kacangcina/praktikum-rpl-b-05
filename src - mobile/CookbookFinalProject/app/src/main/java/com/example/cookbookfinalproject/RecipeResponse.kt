package com.example.cookbookfinalproject

// Pembungkus untuk Halaman Beranda
data class RecipeResponse(
    val recipes: List<Recipe>,
    val featured: Recipe? // Tambahkan baris ini untuk menangkap resep unggulan
)

// Pembungkus untuk Halaman Detail
data class RecipeDetailResponse(
    val recipe: Recipe
)
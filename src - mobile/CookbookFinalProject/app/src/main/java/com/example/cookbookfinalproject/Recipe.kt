package com.example.cookbookfinalproject

data class Recipe(
    val id: Int,
    val title: String,
    val description: String,
    val difficulty: String,
    val estimated_time: Int,
    val thumbnail_url: String,
    val video_url: String?, // <-- TAMBAHKAN BARIS INI (Bisa null jika tidak ada video)
    val creator: Creator?,
    val ingredients: List<Ingredient>?,
    val steps: List<Step>?
)
data class Creator(
    val name: String,
    val role_label: String
)

data class Ingredient(
    val id: Int,
    val name: String,
    val quantity: String
)

data class Step(
    val id: Int,
    val number: Int,
    val title: String,
    val description: String
)

data class RecipeRequest(
    val title: String,
    val difficulty: String,
    val estimated_time: Int,
    val tools: List<String>,       // Daftar alat
    val ingredients: List<String>, // Daftar bahan
    val steps: List<String>        // Daftar langkah
)
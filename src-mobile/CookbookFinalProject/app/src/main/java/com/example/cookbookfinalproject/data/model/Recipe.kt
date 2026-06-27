package com.example.cookbookfinalproject.data.model

data class Recipe(
    val id: Int,
    val title: String,
    val description: String = "",
    val difficulty: String,
    val estimated_time: Int,
    val thumbnail_url: String? = null,
    val video_url: String? = null,
    val video: RecipeVideo? = null,
    val has_video: Boolean = false,
    val creator: Creator? = null,
    val tools: List<RecipeTool>? = null,
    val ingredients: List<Ingredient>? = null,
    val steps: List<Step>? = null,
    val reviews: List<RecipeReview>? = null,
    val my_review: MyReview? = null,
    val is_saved: Boolean = false,
    val average_rating: Double? = null,
    val reviews_count: Int = 0
)

data class RecipeReview(
    val id: Int,
    val rating: Int,
    val comment: String,
    val created_at: String,
    val user: ReviewUser
)

data class ReviewUser(
    val id: Int,
    val name: String,
    val username: String,
    val avatar_url: String? = null
)

data class MyReview(
    val rating: Int,
    val comment: String
)

data class RecipeTool(
    val id: Int,
    val name: String
)

data class RecipeVideo(
    val title: String,
    val description: String,
    val difficulty: String,
    val url: String
)

data class Creator(
    val id: Int,
    val name: String,
    val username: String,
    val role_label: String,
    val avatar_url: String? = null
)

data class Ingredient(
    val id: Int,
    val name: String,
    val quantity: String = ""
)

data class Step(
    val id: Int,
    val number: Int,
    val title: String,
    val description: String
)

package com.example.cookbookfinalproject.data.model

// Pembungkus untuk Halaman Beranda
data class RecipeResponse(
    val recipes: List<Recipe> = emptyList(),
    val featured: Recipe? = null
)

// Pembungkus untuk Halaman Detail
data class RecipeDetailResponse(
    val recipe: Recipe
)

data class CookingConsultationRequest(
    val question: String
)

data class RecipeReviewRequest(
    val rating: Int,
    val comment: String
)

data class CookingConsultationResponse(
    val in_scope: Boolean,
    val answer: String,
    val related_recipes: List<RelatedRecipe> = emptyList()
)

data class RelatedRecipe(
    val id: Int,
    val title: String,
    val thumbnail_url: String?
)

data class MobileLoginRequest(
    val email: String,
    val password: String,
    val device_name: String = "CuBu Android"
)

data class MobileLoginResponse(
    val message: String,
    val token: String,
    val user_id: Int
)

data class MobileRegisterRequest(
    val username: String,
    val email: String,
    val password: String,
    val password_confirmation: String,
    val device_name: String = "CuBu Android"
)

data class SessionResponse(
    val user: MobileUser?
)

data class MobileUser(
    val id: Int,
    val name: String,
    val username: String,
    val email: String? = null,
    val bio: String? = null,
    val avatar_url: String? = null,
    val role: String,
    val role_label: String,
    val is_verified: Boolean,
    val can_publish_recipes: Boolean,
    val can_upload_videos: Boolean,
    val is_admin: Boolean
)

data class CollectionResponse(
    val collection: CollectionInfo,
    val recipes: List<Recipe> = emptyList()
)

data class CollectionInfo(
    val id: Int,
    val name: String
)

data class ProfileResponse(
    val profile: MobileUser,
    val recipes: List<Recipe> = emptyList(),
    val is_owner: Boolean,
    val notifications: List<AppNotification> = emptyList(),
    val unread_notifications_count: Int = 0
)

data class AppNotification(
    val id: String,
    val type: String,
    val level: String = "info",
    val title: String,
    val message: String,
    val reason: String? = null,
    val action_url: String? = null,
    val action_label: String? = null,
    val read_at: String? = null,
    val created_at: String
)

data class MessageResponse(
    val message: String
)

data class CreateRecipeResponse(
    val message: String,
    val recipe_id: Int
)

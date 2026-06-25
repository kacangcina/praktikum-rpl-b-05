package com.example.cookbookfinalproject

import retrofit2.Response
import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.Headers
import retrofit2.http.POST
import retrofit2.http.Path
import retrofit2.http.Query

interface ApiService {
    @GET("api/recipes")
    suspend fun getRecipes(): Response<RecipeResponse>

    @GET("api/recipes/{id}")
    suspend fun getRecipeDetail(@Path("id") id: Int): Response<RecipeDetailResponse>

    @GET("api/recipes")
    suspend fun searchRecipes(
        @Query("search") keyword: String
    ): Response<RecipeResponse>

    // BARU: Mengirim data resep baru ke Laravel
    @Headers("Accept: application/json")
    @POST("api/recipes")
    suspend fun createRecipe(
        @Body request: RecipeRequest
    ): Response<RecipeDetailResponse> // Sesuaikan kembaliannya dengan backend (bisa juga Response<Any>)
}
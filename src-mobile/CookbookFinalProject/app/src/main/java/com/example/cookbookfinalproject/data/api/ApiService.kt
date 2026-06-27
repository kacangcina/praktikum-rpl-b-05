package com.example.cookbookfinalproject.data.api

import com.example.cookbookfinalproject.data.model.*
import retrofit2.Response
import retrofit2.http.Body
import retrofit2.http.DELETE
import retrofit2.http.GET
import retrofit2.http.Headers
import retrofit2.http.Multipart
import retrofit2.http.Part
import retrofit2.http.POST
import retrofit2.http.Path
import retrofit2.http.Query
import okhttp3.MultipartBody

interface ApiService {
    @Headers("Accept: application/json")
    @POST("api/mobile/login")
    suspend fun mobileLogin(
        @Body request: MobileLoginRequest
    ): Response<MobileLoginResponse>

    @Headers("Accept: application/json")
    @POST("api/mobile/register")
    suspend fun mobileRegister(
        @Body request: MobileRegisterRequest
    ): Response<MobileLoginResponse>

    @GET("api/mobile/session")
    suspend fun getSession(): Response<SessionResponse>

    @Headers("Accept: application/json")
    @POST("api/mobile/logout")
    suspend fun mobileLogout(): Response<MessageResponse>

    @GET("api/mobile/recipes")
    suspend fun getRecipes(
        @Query("q") keyword: String? = null,
        @Query("sort") sort: String = "latest"
    ): Response<RecipeResponse>

    @GET("api/mobile/recipes/{id}")
    suspend fun getRecipeDetail(@Path("id") id: Int): Response<RecipeDetailResponse>

    @GET("api/mobile/collection")
    suspend fun getCollection(): Response<CollectionResponse>

    @Headers("Accept: application/json")
    @POST("api/mobile/collection/{id}")
    suspend fun saveRecipe(@Path("id") id: Int): Response<MessageResponse>

    @Headers("Accept: application/json")
    @DELETE("api/mobile/collection/{id}")
    suspend fun removeSavedRecipe(@Path("id") id: Int): Response<MessageResponse>

    @GET("api/mobile/profiles/{id}")
    suspend fun getProfile(@Path("id") id: Int): Response<ProfileResponse>

    @Headers("Accept: application/json")
    @Multipart
    @POST("api/mobile/profile/update")
    suspend fun updateProfile(
        @Part parts: List<MultipartBody.Part>
    ): Response<MessageResponse>

    @Headers("Accept: application/json")
    @POST("api/mobile/notifications/read")
    suspend fun markNotificationsRead(): Response<MessageResponse>

    @Headers("Accept: application/json")
    @Multipart
    @POST("api/mobile/verify-creator")
    suspend fun verifyCreator(
        @Part parts: List<MultipartBody.Part>
    ): Response<MessageResponse>

    @Headers("Accept: application/json")
    @Multipart
    @POST("api/mobile/recipes")
    suspend fun createRecipe(
        @Part parts: List<MultipartBody.Part>
    ): Response<CreateRecipeResponse>

    @Headers("Accept: application/json")
    @Multipart
    @POST("api/mobile/recipes/{id}/update")
    suspend fun updateRecipe(
        @Path("id") id: Int,
        @Part parts: List<MultipartBody.Part>
    ): Response<CreateRecipeResponse>

    @Headers("Accept: application/json")
    @DELETE("api/mobile/recipes/{id}")
    suspend fun deleteRecipe(@Path("id") id: Int): Response<MessageResponse>

    @Headers("Accept: application/json")
    @POST("api/mobile/recipes/{id}/reviews")
    suspend fun saveReview(
        @Path("id") id: Int,
        @Body request: RecipeReviewRequest
    ): Response<MessageResponse>

    @Headers("Accept: application/json")
    @POST("api/mobile/cooking-consultation")
    suspend fun askCookingAi(
        @Body request: CookingConsultationRequest
    ): Response<CookingConsultationResponse>
}

package com.example.cookbookfinalproject.data.api

import android.content.Context
import okhttp3.OkHttpClient
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import java.util.concurrent.TimeUnit

object RetrofitClient {
    private const val BASE_URL = "http://192.168.0.105:8000/"
    private const val PREFS_NAME = "cubu_session"
    private const val TOKEN_KEY = "auth_token"
    private var preferences: android.content.SharedPreferences? = null
    var authToken: String? = null
        private set

    fun initialize(context: Context) {
        preferences = context.applicationContext.getSharedPreferences(
            PREFS_NAME,
            Context.MODE_PRIVATE
        )
        authToken = preferences?.getString(TOKEN_KEY, null)
    }

    fun saveToken(token: String) {
        authToken = token
        preferences?.edit()?.putString(TOKEN_KEY, token)?.apply()
    }

    fun clearToken() {
        authToken = null
        preferences?.edit()?.remove(TOKEN_KEY)?.apply()
    }

    private val httpClient = OkHttpClient.Builder()
        .connectTimeout(30, TimeUnit.SECONDS)
        .readTimeout(10, TimeUnit.MINUTES)
        .writeTimeout(10, TimeUnit.MINUTES)
        .callTimeout(15, TimeUnit.MINUTES)
        .addInterceptor { chain ->
            val request = chain.request()
                .newBuilder()
                .apply {
                    authToken?.let { header("Authorization", "Bearer $it") }
                }
                .build()

            chain.proceed(request)
        }
        .build()

    val instance: ApiService by lazy {
        val retrofit = Retrofit.Builder()
            .baseUrl(BASE_URL)
            .client(httpClient)
            .addConverterFactory(GsonConverterFactory.create())
            .build()

        retrofit.create(ApiService::class.java)
    }
}

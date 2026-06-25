package com.example.cookbookfinalproject

import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory


object RetrofitClient {
    // PENTING: Jika kamu pakai Emulator Android Studio, gunakan "10.0.2.2".
    // Jika kamu run aplikasinya di HP fisik (colok kabel USB/WiFi), ganti dengan IP Address WiFi laptopmu (contoh: 192.168.1.x)
    private const val BASE_URL = "http://192.168.0.107:8000/"

    val instance: ApiService by lazy {
        val retrofit = Retrofit.Builder()
            .baseUrl(BASE_URL)
            .addConverterFactory(GsonConverterFactory.create())
            .build()

        retrofit.create(ApiService::class.java)
    }
}

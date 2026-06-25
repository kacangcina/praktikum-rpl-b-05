package com.example.cookbookfinalproject

import android.util.Log
import androidx.compose.runtime.mutableStateOf
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import kotlinx.coroutines.launch

class RecipeViewModel : ViewModel() {
    val recipes = mutableStateOf<List<Recipe>>(emptyList())
    val selectedRecipe = mutableStateOf<Recipe?>(null)
    val featuredRecipe = mutableStateOf<Recipe?>(null)

    val searchQuery = mutableStateOf("")
    val searchResults = mutableStateOf<List<Recipe>>(emptyList())

    init {
        fetchRecipes()
    }

    fun fetchRecipes() {
        viewModelScope.launch {
            try {
                val response = RetrofitClient.instance.getRecipes()
                if (response.isSuccessful) {
                    recipes.value = response.body()?.recipes ?: emptyList()
                    featuredRecipe.value = response.body()?.featured
                } else {
                    Log.e("API_ERROR", "Gagal memuat resep: ${response.code()}")
                }
            } catch (e: Exception) {
                Log.e("API_ERROR", "Koneksi bermasalah: ${e.message}")
            }
        }
    }

    fun fetchDetail(id: Int) {
        viewModelScope.launch {
            try {
                val response = RetrofitClient.instance.getRecipeDetail(id)
                if (response.isSuccessful) {
                    selectedRecipe.value = response.body()?.recipe
                } else {
                    Log.e("API_ERROR", "Gagal memuat detail resep")
                }
            } catch (e: Exception) {
                Log.e("API_ERROR", "Koneksi bermasalah: ${e.message}")
            }
        }
    }

    fun performSearch(query: String) {
        searchQuery.value = query
        if (query.isEmpty()) {
            searchResults.value = emptyList()
            return
        }
        searchResults.value = recipes.value.filter { resep ->
            resep.title.contains(query, ignoreCase = true)
        }
    }

    // BARU: Fungsi untuk mengirim data resep ke server
    fun createRecipe(
        title: String,
        difficulty: String,
        time: String,
        alat: List<String>,
        bahan: List<String>,
        langkah: List<String>,
        onSuccess: () -> Unit
    ) {
        if (title.isBlank()) {
            Log.e("API_ERROR", "Judul resep tidak boleh kosong")
            return
        }

        val request = RecipeRequest(
            title = title,
            difficulty = difficulty.lowercase(), // Dikirim dalam huruf kecil agar sesuai format umum DB
            estimated_time = time.toIntOrNull() ?: 0,
            tools = alat,
            ingredients = bahan,
            steps = langkah.filter { it.isNotBlank() }
        )

        viewModelScope.launch {
            try {
                val response = RetrofitClient.instance.createRecipe(request)
                if (response.isSuccessful) {
                    Log.d("API_SUCCESS", "Resep berhasil dibuat!")
                    fetchRecipes() // Segarkan data resep di beranda
                    onSuccess()    // Pindah halaman
                } else {
                    Log.e("API_ERROR", "Gagal membuat resep: ${response.errorBody()?.string()}")
                }
            } catch (e: Exception) {
                Log.e("API_ERROR", "Koneksi bermasalah saat mengirim data: ${e.message}")
            }
        }
    }
}
package com.example.cookbookfinalproject.ui.viewmodel

import android.util.Log
import android.content.Context
import android.net.Uri
import androidx.compose.runtime.mutableStateOf
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.cookbookfinalproject.data.api.RetrofitClient
import com.example.cookbookfinalproject.data.model.*
import kotlinx.coroutines.Job
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.toRequestBody
import okhttp3.MediaType
import okhttp3.RequestBody
import okio.BufferedSink
import okio.source
import org.json.JSONObject
import retrofit2.Response

class RecipeViewModel : ViewModel() {
    val recipes = mutableStateOf<List<Recipe>>(emptyList())
    val selectedRecipe = mutableStateOf<Recipe?>(null)
    val featuredRecipe = mutableStateOf<Recipe?>(null)
    val collectionRecipes = mutableStateOf<List<Recipe>>(emptyList())
    val currentUser = mutableStateOf<MobileUser?>(null)
    val profile = mutableStateOf<ProfileResponse?>(null)

    val searchQuery = mutableStateOf("")
    val recipeSort = mutableStateOf("latest")
    val isLoading = mutableStateOf(false)
    val errorMessage = mutableStateOf<String?>(null)
    val isLoggedIn = mutableStateOf(RetrofitClient.authToken != null)
    val loginLoading = mutableStateOf(false)
    val loginError = mutableStateOf<String?>(null)
    val registerLoading = mutableStateOf(false)
    val registerError = mutableStateOf<String?>(null)
    val createLoading = mutableStateOf(false)
    val createError = mutableStateOf<String?>(null)

    private var searchJob: Job? = null

    init {
        fetchRecipes()
        restoreSession()
    }

    fun fetchRecipes(query: String? = null) {
        viewModelScope.launch {
            isLoading.value = true
            try {
                val response = RetrofitClient.instance.getRecipes(
                    keyword = query?.trim()?.takeIf { it.isNotEmpty() },
                    sort = recipeSort.value
                )
                if (response.isSuccessful) {
                    val savedIds = collectionRecipes.value.map { it.id }.toSet()
                    recipes.value = response.body()?.recipes.orEmpty().map {
                        it.copy(is_saved = it.id in savedIds)
                    }
                    featuredRecipe.value = response.body()?.featured?.let {
                        it.copy(is_saved = it.id in savedIds)
                    }
                    errorMessage.value = null
                } else {
                    errorMessage.value = "Gagal memuat resep (${response.code()})."
                }
            } catch (exception: Exception) {
                errorMessage.value = "Tidak dapat terhubung ke server CuBu."
                Log.e("API_ERROR", "Gagal memuat resep", exception)
            } finally {
                isLoading.value = false
            }
        }
    }

    fun setRecipeSort(sort: String) {
        if (sort == recipeSort.value) return
        recipeSort.value = sort
        fetchRecipes(searchQuery.value)
    }

    fun performSearch(query: String) {
        searchQuery.value = query
        searchJob?.cancel()
        searchJob = viewModelScope.launch {
            delay(350)
            fetchRecipes(query)
        }
    }

    fun fetchDetail(id: Int) {
        selectedRecipe.value = null
        viewModelScope.launch {
            try {
                val response = RetrofitClient.instance.getRecipeDetail(id)
                if (response.isSuccessful) {
                    selectedRecipe.value = response.body()?.recipe?.let { recipe ->
                        recipe.copy(
                            is_saved = collectionRecipes.value.any { it.id == recipe.id }
                        )
                    }
                    errorMessage.value = null
                } else {
                    errorMessage.value = "Resep tidak dapat dimuat."
                }
            } catch (exception: Exception) {
                errorMessage.value = "Tidak dapat terhubung ke server CuBu."
            }
        }
    }

    fun submitReview(
        recipeId: Int,
        rating: Int,
        comment: String,
        onSuccess: () -> Unit
    ) {
        if (!isLoggedIn.value) {
            errorMessage.value = "Silakan masuk untuk memberi ulasan."
            return
        }
        if (rating !in 1..5 || comment.isBlank()) {
            errorMessage.value = "Pilih rating dan tulis komentar."
            return
        }

        viewModelScope.launch {
            try {
                val response = RetrofitClient.instance.saveReview(
                    recipeId,
                    RecipeReviewRequest(rating, comment.trim())
                )
                if (response.isSuccessful) {
                    fetchDetail(recipeId)
                    fetchRecipes(searchQuery.value)
                    errorMessage.value = null
                    onSuccess()
                } else {
                    errorMessage.value = response.validationMessage()
                        ?: "Ulasan gagal disimpan."
                }
            } catch (_: Exception) {
                errorMessage.value = "Tidak dapat mengirim ulasan."
            }
        }
    }

    fun restoreSession() {
        if (RetrofitClient.authToken == null) return

        viewModelScope.launch {
            try {
                val response = RetrofitClient.instance.getSession()
                if (response.isSuccessful && response.body()?.user != null) {
                    currentUser.value = response.body()?.user
                    isLoggedIn.value = true
                    fetchCollection()
                } else {
                    clearLocalSession()
                }
            } catch (_: Exception) {
                // Keep the saved token for temporary offline/network failures.
            }
        }
    }

    fun login(email: String, password: String, onSuccess: () -> Unit) {
        if (email.isBlank() || password.isBlank()) {
            loginError.value = "Email dan kata sandi wajib diisi."
            return
        }

        loginLoading.value = true
        loginError.value = null

        viewModelScope.launch {
            try {
                val response = RetrofitClient.instance.mobileLogin(
                    MobileLoginRequest(email.trim(), password)
                )
                val result = response.body()

                if (response.isSuccessful && result != null) {
                    RetrofitClient.saveToken(result.token)
                    isLoggedIn.value = true
                    loadSessionThen(onSuccess)
                } else {
                    loginError.value = when (response.code()) {
                        403 -> "Akun sedang diblokir atau telah ditutup."
                        422 -> "Email atau kata sandi tidak sesuai."
                        else -> "Gagal masuk (${response.code()})."
                    }
                }
            } catch (_: Exception) {
                loginError.value = "Tidak dapat terhubung ke server."
            } finally {
                loginLoading.value = false
            }
        }
    }

    fun register(
        username: String,
        email: String,
        password: String,
        confirmation: String,
        onSuccess: () -> Unit
    ) {
        if (username.isBlank() || email.isBlank() || password.length < 8) {
            registerError.value = "Isi username, email, dan kata sandi minimal 8 karakter."
            return
        }
        if (password != confirmation) {
            registerError.value = "Konfirmasi kata sandi tidak cocok."
            return
        }

        registerLoading.value = true
        registerError.value = null

        viewModelScope.launch {
            try {
                val response = RetrofitClient.instance.mobileRegister(
                    MobileRegisterRequest(username.trim(), email.trim(), password, confirmation)
                )
                val result = response.body()

                if (response.isSuccessful && result != null) {
                    RetrofitClient.saveToken(result.token)
                    isLoggedIn.value = true
                    loadSessionThen(onSuccess)
                } else {
                    registerError.value = when (response.code()) {
                        422 -> "Username atau email sudah digunakan, atau data belum valid."
                        else -> "Gagal membuat akun (${response.code()})."
                    }
                }
            } catch (_: Exception) {
                registerError.value = "Tidak dapat terhubung ke server."
            } finally {
                registerLoading.value = false
            }
        }
    }

    fun logout(onComplete: () -> Unit) {
        viewModelScope.launch {
            try {
                RetrofitClient.instance.mobileLogout()
            } catch (_: Exception) {
                // Local logout must still succeed if the server cannot be reached.
            } finally {
                clearLocalSession()
                onComplete()
            }
        }
    }

    fun continueAsGuest(onComplete: () -> Unit) {
        clearLocalSession()
        onComplete()
    }

    fun fetchCollection() {
        if (!isLoggedIn.value) return

        viewModelScope.launch {
            try {
                val response = RetrofitClient.instance.getCollection()
                if (response.isSuccessful) {
                    collectionRecipes.value = response.body()?.recipes.orEmpty()
                    val savedIds = collectionRecipes.value.map { it.id }.toSet()
                    recipes.value = recipes.value.map { it.copy(is_saved = it.id in savedIds) }
                    selectedRecipe.value = selectedRecipe.value?.let {
                        it.copy(is_saved = it.id in savedIds)
                    }
                } else if (response.code() == 401) {
                    clearLocalSession()
                }
            } catch (_: Exception) {
                errorMessage.value = "Koleksi tidak dapat dimuat."
            }
        }
    }

    fun toggleSaved(recipe: Recipe) {
        if (!isLoggedIn.value) {
            errorMessage.value = "Silakan masuk untuk menyimpan resep."
            return
        }

        viewModelScope.launch {
            try {
                val response = if (recipe.is_saved) {
                    RetrofitClient.instance.removeSavedRecipe(recipe.id)
                } else {
                    RetrofitClient.instance.saveRecipe(recipe.id)
                }

                if (response.isSuccessful) {
                    val updated = recipe.copy(is_saved = !recipe.is_saved)
                    selectedRecipe.value = selectedRecipe.value?.let {
                        if (it.id == updated.id) updated else it
                    }
                    featuredRecipe.value = featuredRecipe.value?.let {
                        if (it.id == updated.id) updated else it
                    }
                    recipes.value = recipes.value.map {
                        if (it.id == updated.id) updated else it
                    }
                    collectionRecipes.value = if (updated.is_saved) {
                        (collectionRecipes.value.filterNot { it.id == updated.id } + updated)
                    } else {
                        collectionRecipes.value.filterNot { it.id == updated.id }
                    }
                    fetchCollection()
                }
            } catch (_: Exception) {
                errorMessage.value = "Resep gagal diperbarui di koleksi."
            }
        }
    }

    fun fetchMyProfile() {
        val userId = currentUser.value?.id ?: return

        viewModelScope.launch {
            try {
                val response = RetrofitClient.instance.getProfile(userId)
                if (response.isSuccessful) {
                    profile.value = response.body()
                }
            } catch (_: Exception) {
                errorMessage.value = "Profil tidak dapat dimuat."
            }
        }
    }

    fun markAllNotificationsRead() {
        viewModelScope.launch {
            try {
                val response = RetrofitClient.instance.markNotificationsRead()
                if (response.isSuccessful) {
                    profile.value = profile.value?.copy(
                        notifications = profile.value?.notifications.orEmpty().map {
                            it.copy(read_at = it.read_at ?: "read")
                        },
                        unread_notifications_count = 0
                    )
                } else {
                    errorMessage.value = "Notifikasi gagal ditandai sudah dibaca."
                }
            } catch (_: Exception) {
                errorMessage.value = "Tidak dapat memperbarui notifikasi."
            }
        }
    }

    fun saveRecipe(
        recipeId: Int? = null,
        context: Context,
        imageUri: Uri?,
        videoUri: Uri?,
        title: String,
        description: String,
        difficulty: String,
        time: String,
        alat: List<String>,
        bahan: List<String>,
        judulLangkah: List<String>,
        langkah: List<String>,
        onSuccess: () -> Unit
    ) {
        val cleanSteps = judulLangkah.zip(langkah)
            .map { (stepTitle, stepDescription) ->
                stepTitle.trim() to stepDescription.trim()
            }
            .filter { (stepTitle, stepDescription) ->
                stepTitle.isNotEmpty() || stepDescription.isNotEmpty()
            }
        val ingredients = bahan.mapNotNull { value ->
            val name = value.substringBefore(" - [").trim()
            val quantity = value.substringAfter(" - [", "").removeSuffix("]").trim()
            if (name.isNotEmpty() && quantity.isNotEmpty()) name to quantity else null
        }

        val missingFields = buildList {
            if (title.isBlank()) add("judul")
            if (description.isBlank()) add("deskripsi")
            if (difficulty == "Pilih") add("kesulitan")
            if (time.toIntOrNull() == null) add("waktu")
            if (alat.none { it.isNotBlank() }) add("alat")
            if (ingredients.isEmpty()) add("bahan beserta takaran")
            if (cleanSteps.isEmpty() || cleanSteps.any { it.first.isEmpty() || it.second.isEmpty() }) {
                add("judul dan deskripsi setiap langkah")
            }
        }

        if (missingFields.isNotEmpty()) {
            createError.value = "Lengkapi: ${missingFields.joinToString(", ")}."
            return
        }

        createLoading.value = true
        createError.value = null

        viewModelScope.launch {
            try {
                val parts = mutableListOf<MultipartBody.Part>()

                fun textPart(name: String, value: String) {
                    parts += MultipartBody.Part.createFormData(
                        name,
                        value
                    )
                }

                textPart("title", title.trim())
                textPart("description", description.trim())
                textPart("difficulty", difficulty.lowercase())
                textPart("estimated_time", time)
                alat.forEach { textPart("tools[]", it.trim()) }
                ingredients.forEach { (name, quantity) ->
                    textPart("ingredient_names[]", name)
                    textPart("ingredient_quantities[]", quantity)
                }
                cleanSteps.forEach { (stepTitle, stepDescription) ->
                    textPart("step_titles[]", stepTitle)
                    textPart("steps[]", stepDescription)
                }

                imageUri?.let { uri ->
                    val bytes = context.contentResolver.openInputStream(uri)?.use { it.readBytes() }
                    if (bytes != null) {
                        val mimeType = context.contentResolver.getType(uri) ?: "image/jpeg"
                        val extension = when (mimeType) {
                            "image/png" -> "png"
                            "image/webp" -> "webp"
                            else -> "jpg"
                        }
                        parts += MultipartBody.Part.createFormData(
                            "thumbnail",
                            "recipe-${System.currentTimeMillis()}.$extension",
                            bytes.toRequestBody(mimeType.toMediaTypeOrNull())
                        )
                    }
                }

                videoUri?.let { uri ->
                    val mimeType = context.contentResolver.getType(uri) ?: "video/mp4"
                    parts += MultipartBody.Part.createFormData(
                        "video",
                        "recipe-${System.currentTimeMillis()}.mp4",
                        ContentUriRequestBody(context, uri, mimeType)
                    )
                }

                val response = if (recipeId == null) {
                    RetrofitClient.instance.createRecipe(parts)
                } else {
                    RetrofitClient.instance.updateRecipe(recipeId, parts)
                }
                if (response.isSuccessful) {
                    fetchRecipes()
                    onSuccess()
                } else {
                    createError.value = when (response.code()) {
                        403 -> "Akun ini belum diizinkan mempublikasikan resep."
                        413 -> "Ukuran foto atau video melebihi batas server."
                        422 -> response.validationMessage()
                            ?: "Data resep belum memenuhi ketentuan CuBu."
                        else -> response.validationMessage()
                            ?: "Resep gagal disimpan (${response.code()})."
                    }
                }
            } catch (exception: Exception) {
                Log.e("RECIPE_UPLOAD", "Gagal menyimpan resep", exception)
                createError.value = if (
                    exception is java.net.SocketTimeoutException ||
                    exception is java.io.InterruptedIOException
                ) {
                    "Unggahan terlalu lama. Pastikan koneksi WiFi stabil lalu coba lagi."
                } else {
                    "Tidak dapat menyimpan resep ke server: ${exception.localizedMessage ?: "kesalahan jaringan"}."
                }
            } finally {
                createLoading.value = false
            }
        }
    }

    private fun <T> Response<T>.validationMessage(): String? {
        val body = errorBody()?.string()?.takeIf { it.isNotBlank() } ?: return null
        return runCatching {
            val json = JSONObject(body)
            val errors = json.optJSONObject("errors")
            if (errors != null && errors.keys().hasNext()) {
                val first = errors.optJSONArray(errors.keys().next())
                first?.optString(0)?.takeIf { it.isNotBlank() }
            } else {
                json.optString("message").takeIf { it.isNotBlank() }
            }
        }.getOrNull()
    }

    fun deleteRecipe(recipeId: Int, onSuccess: () -> Unit) {
        createLoading.value = true
        createError.value = null

        viewModelScope.launch {
            try {
                val response = RetrofitClient.instance.deleteRecipe(recipeId)
                if (response.isSuccessful) {
                    selectedRecipe.value = null
                    fetchRecipes()
                    fetchMyProfile()
                    onSuccess()
                } else {
                    createError.value = when (response.code()) {
                        403 -> "Hanya pemilik yang dapat menghapus resep ini."
                        404 -> "Resep sudah tidak tersedia."
                        else -> "Resep gagal dihapus (${response.code()})."
                    }
                }
            } catch (_: Exception) {
                createError.value = "Tidak dapat menghapus resep dari server."
            } finally {
                createLoading.value = false
            }
        }
    }

    fun updateProfile(
        context: Context,
        name: String,
        username: String,
        bio: String,
        avatarUri: Uri?,
        onSuccess: () -> Unit
    ) {
        if (name.isBlank() || username.isBlank()) {
            createError.value = "Nama dan nama pengguna wajib diisi."
            return
        }

        createLoading.value = true
        createError.value = null

        viewModelScope.launch {
            try {
                val parts = mutableListOf<MultipartBody.Part>()
                parts += MultipartBody.Part.createFormData("name", name.trim())
                parts += MultipartBody.Part.createFormData("username", username.trim())
                parts += MultipartBody.Part.createFormData("bio", bio.trim())

                avatarUri?.let { uri ->
                    val bytes = context.contentResolver.openInputStream(uri)?.use { it.readBytes() }
                    if (bytes != null) {
                        val mimeType = context.contentResolver.getType(uri) ?: "image/jpeg"
                        val extension = when (mimeType) {
                            "image/png" -> "png"
                            "image/webp" -> "webp"
                            else -> "jpg"
                        }
                        parts += MultipartBody.Part.createFormData(
                            "avatar",
                            "avatar-${System.currentTimeMillis()}.$extension",
                            bytes.toRequestBody(mimeType.toMediaTypeOrNull())
                        )
                    }
                }

                val response = RetrofitClient.instance.updateProfile(parts)
                if (response.isSuccessful) {
                    val session = RetrofitClient.instance.getSession()
                    currentUser.value = session.body()?.user
                    fetchMyProfile()
                    onSuccess()
                } else {
                    createError.value = response.validationMessage() ?: "Gagal memperbarui profil."
                }
            } catch (e: Exception) {
                createError.value = "Terjadi kesalahan: ${e.localizedMessage}"
            } finally {
                createLoading.value = false
            }
        }
    }

    fun submitVerification(
        context: Context,
        fullName: String,
        expertise: String,
        bio: String,
        documentUri: Uri?,
        portfolioUrl: String,
        onSuccess: () -> Unit
    ) {
        if (
            fullName.isBlank() || expertise == "Pilih keahlian" ||
            bio.isBlank() || documentUri == null
        ) {
            createError.value =
                "Nama lengkap, keahlian, deskripsi diri, dan dokumen wajib diisi."
            return
        }

        createLoading.value = true
        createError.value = null

        viewModelScope.launch {
            try {
                val parts = mutableListOf<MultipartBody.Part>()
                parts += MultipartBody.Part.createFormData(
                    "notes",
                    "Nama sesuai identitas: ${fullName.trim()}\n" +
                        "Keahlian: $expertise\n\n${bio.trim()}"
                )
                portfolioUrl.trim().takeIf { it.isNotEmpty() }?.let {
                    parts += MultipartBody.Part.createFormData("portfolio_url", it)
                }

                documentUri.let { uri ->
                    val mimeType = context.contentResolver.getType(uri) ?: "application/pdf"
                    val extension = when (mimeType) {
                        "image/jpeg" -> "jpg"
                        "image/png" -> "png"
                        else -> "pdf"
                    }
                    parts += MultipartBody.Part.createFormData(
                        "document",
                        "creator-document-${System.currentTimeMillis()}.$extension",
                        ContentUriRequestBody(context, uri, mimeType)
                    )
                }

                val response = RetrofitClient.instance.verifyCreator(parts)
                if (response.isSuccessful) {
                    fetchMyProfile()
                    onSuccess()
                } else {
                    createError.value = response.validationMessage() ?: "Gagal mengirim pengajuan."
                }
            } catch (e: Exception) {
                createError.value = "Terjadi kesalahan: ${e.localizedMessage}"
            } finally {
                createLoading.value = false
            }
        }
    }

    private class ContentUriRequestBody(
        private val context: Context,
        private val uri: Uri,
        private val mimeType: String
    ) : RequestBody() {
        override fun contentType(): MediaType? = mimeType.toMediaTypeOrNull()

        override fun contentLength(): Long =
            context.contentResolver.openAssetFileDescriptor(uri, "r")?.use {
                it.length
            } ?: -1L

        override fun writeTo(sink: BufferedSink) {
            context.contentResolver.openInputStream(uri)?.use { input ->
                sink.writeAll(input.source())
            } ?: error("File tidak dapat dibaca.")
        }
    }

    private suspend fun loadSessionThen(onSuccess: () -> Unit) {
        val session = RetrofitClient.instance.getSession()
        currentUser.value = session.body()?.user
        fetchCollection()
        onSuccess()
    }

    private fun clearLocalSession() {
        RetrofitClient.clearToken()
        isLoggedIn.value = false
        currentUser.value = null
        profile.value = null
        collectionRecipes.value = emptyList()
    }
}

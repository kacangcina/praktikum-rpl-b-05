package com.example.cookbookfinalproject.ui.screens

import android.net.Uri
import android.widget.MediaController
import android.widget.VideoView
import androidx.activity.compose.BackHandler
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.gestures.rememberTransformableState
import androidx.compose.foundation.gestures.transformable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material.icons.outlined.BookmarkBorder
import androidx.compose.material.icons.outlined.StarBorder
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clipToBounds
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.graphicsLayer
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.viewinterop.AndroidView
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavHostController
import coil.compose.AsyncImage
import com.example.cookbookfinalproject.data.api.RetrofitClient
import com.example.cookbookfinalproject.data.model.MyReview
import com.example.cookbookfinalproject.data.model.Recipe
import com.example.cookbookfinalproject.ui.viewmodel.RecipeViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun DetailScreen(
    navController: NavHostController,
    recipeId: Int,
    viewModel: RecipeViewModel = viewModel()
) {
    val recipe = viewModel.selectedRecipe.value
    var isFullScreen by remember { mutableStateOf(false) }
    var scale by remember { mutableStateOf(1f) }
    var offset by remember { mutableStateOf(Offset.Zero) }

    // Tombol Back Sistem (Fisik/Gesture)
    BackHandler {
        if (isFullScreen) {
            isFullScreen = false
            scale = 1f
            offset = Offset.Zero
        } else {
            navController.popBackStack()
        }
    }

    // Memuat data saat halaman dibuka
    LaunchedEffect(recipeId) { viewModel.fetchDetail(recipeId) }

    var showDeleteConfirmation by remember(recipeId) { mutableStateOf(false) }
    var showReviews by remember(recipeId) { mutableStateOf(false) }

    if (recipe == null) {
        Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            CircularProgressIndicator(color = Color(0xFFF97316))
        }
    } else {
        Box(modifier = Modifier.fillMaxSize().background(Color.White)) {
            Column(
                modifier = if (isFullScreen) Modifier.fillMaxSize() else Modifier
                    .fillMaxSize()
                    .verticalScroll(rememberScrollState())
            ) {
                // 1. Header Video / Gambar Utama
                Box(
                    modifier = if (isFullScreen) {
                        Modifier.fillMaxSize()
                    } else {
                        Modifier.fillMaxWidth().height(280.dp)
                    }.background(Color.Black)
                ) {
                    val videoData = recipe.video
                    val canPlayVideo = videoData != null && viewModel.isLoggedIn.value
                    val transformState = rememberTransformableState { zoomChange, offsetChange, _ ->
                        scale = (scale * zoomChange).coerceIn(1f, 5f)
                        offset += offsetChange
                    }

                    if (canPlayVideo) {
                        var isBuffering by remember { mutableStateOf(true) }
                        
                        Box(
                            modifier = Modifier
                                .fillMaxSize()
                                .then(if (isFullScreen) Modifier.clipToBounds().transformable(state = transformState) else Modifier),
                            contentAlignment = Alignment.Center
                        ) {
                            AndroidView(
                                modifier = Modifier
                                    .fillMaxSize()
                                    .graphicsLayer(
                                        scaleX = scale,
                                        scaleY = scale,
                                        translationX = offset.x,
                                        translationY = offset.y
                                    ),
                                factory = { ctx ->
                                    val videoView = VideoView(ctx)
                                    val mediaController = MediaController(ctx)

                                    videoView.apply {
                                        mediaController.setAnchorView(this)
                                        setMediaController(mediaController)
                                        
                                        val headers = RetrofitClient.authToken?.let {
                                            mapOf("Authorization" to "Bearer $it")
                                        } ?: emptyMap()
                                        
                                        setVideoURI(Uri.parse(videoData.url), headers)
                                        
                                        setOnPreparedListener { mp ->
                                            isBuffering = false
                                            mp.start()
                                        }
                                        
                                        setOnErrorListener { _, _, _ ->
                                            isBuffering = false
                                            false
                                        }
                                    }
                                },
                                update = { /* VideoView handled in factory/apply */ },
                                onRelease = { it.stopPlayback() }
                            )
                            
                            if (isBuffering) {
                                CircularProgressIndicator(color = Color(0xFFF97316))
                            }
                        }
                    } else {
                        Box(modifier = Modifier.fillMaxSize()) {
                            AsyncImage(
                                model = recipe.thumbnail_url,
                                contentDescription = null,
                                contentScale = ContentScale.Crop,
                                modifier = Modifier.fillMaxSize()
                            )

                            if (videoData != null) {
                                Surface(
                                    shape = RoundedCornerShape(50),
                                    color = Color.Black.copy(alpha = 0.72f),
                                    modifier = Modifier
                                        .align(Alignment.BottomStart)
                                        .padding(16.dp)
                                ) {
                                    Row(
                                        modifier = Modifier.padding(horizontal = 12.dp, vertical = 7.dp),
                                        verticalAlignment = Alignment.CenterVertically
                                    ) {
                                        Icon(
                                            Icons.Default.PlayArrow,
                                            contentDescription = null,
                                            tint = Color.White,
                                            modifier = Modifier.size(16.dp)
                                        )
                                        Spacer(Modifier.width(5.dp))
                                        Text(
                                            if (viewModel.isLoggedIn.value) "Video" else "Masuk untuk menonton video",
                                            color = Color.White,
                                            fontSize = 11.sp,
                                            fontWeight = FontWeight.Bold
                                        )
                                    }
                                }
                            }
                        }
                    }

                    // Overlay gradient untuk visibilitas tombol back
                    Box(
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(90.dp)
                            .background(
                                Brush.verticalGradient(
                                    colors = listOf(Color.Black.copy(alpha = 0.6f), Color.Transparent)
                                )
                            )
                    )

                    // Tombol Navigasi
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(16.dp),
                        horizontalArrangement = Arrangement.SpaceBetween
                    ) {
                        IconButton(
                            onClick = { 
                                if (isFullScreen) {
                                    isFullScreen = false
                                    scale = 1f
                                    offset = Offset.Zero
                                } else {
                                    navController.popBackStack()
                                }
                            },
                            modifier = Modifier.size(40.dp),
                            colors = IconButtonDefaults.iconButtonColors(
                                containerColor = Color.Black.copy(alpha = 0.3f),
                                contentColor = Color.White
                            )
                        ) {
                            Icon(
                                if (isFullScreen) Icons.Default.Close else Icons.Default.ArrowBack, 
                                contentDescription = "Kembali"
                            )
                        }

                        if (canPlayVideo) {
                            IconButton(
                                onClick = { 
                                    isFullScreen = !isFullScreen
                                    if (!isFullScreen) {
                                        scale = 1f
                                        offset = Offset.Zero
                                    }
                                },
                                modifier = Modifier.size(40.dp),
                                colors = IconButtonDefaults.iconButtonColors(
                                    containerColor = Color.Black.copy(alpha = 0.3f),
                                    contentColor = Color.White
                                )
                            ) {
                                Icon(
                                    if (isFullScreen) Icons.Default.FullscreenExit else Icons.Default.Fullscreen,
                                    contentDescription = "Fullscreen"
                                )
                            }
                        }
                    }
                }

                // 2. Konten Utama dengan Sudut Membulat Atas
                if (!isFullScreen) {
                    Column(
                        modifier = Modifier
                            .fillMaxWidth()
                            .offset(y = (-24).dp)
                            .background(
                                Color.White,
                                shape = RoundedCornerShape(topStart = 24.dp, topEnd = 24.dp)
                            )
                            .padding(24.dp)
                    ) {
                        // Judul Asli
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.Top
                        ) {
                            Text(
                                text = recipe.title.replaceFirstChar { it.uppercase() },
                                fontSize = 24.sp,
                                fontWeight = FontWeight.ExtraBold,
                                color = Color(0xFF1E293B),
                                modifier = Modifier.weight(1f)
                            )
                            if (viewModel.currentUser.value?.id == recipe.creator?.id) {
                                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                                    OutlinedIconButton(
                                        onClick = { viewModel.toggleSaved(recipe) },
                                        shape = RoundedCornerShape(12.dp),
                                        modifier = Modifier.size(44.dp)
                                    ) {
                                        Icon(
                                            if (recipe.is_saved) Icons.Default.Bookmark else Icons.Outlined.BookmarkBorder,
                                            contentDescription = if (recipe.is_saved) "Hapus dari koleksi" else "Simpan",
                                            tint = if (recipe.is_saved) Color(0xFFF97316) else Color.Black
                                        )
                                    }
                                    OutlinedIconButton(
                                        onClick = { navController.navigate("edit/${recipe.id}") },
                                        shape = RoundedCornerShape(12.dp),
                                        modifier = Modifier.size(44.dp)
                                    ) {
                                        Icon(Icons.Default.Edit, contentDescription = "Edit resep")
                                    }
                                    OutlinedIconButton(
                                        onClick = { showDeleteConfirmation = true },
                                        shape = RoundedCornerShape(12.dp),
                                        modifier = Modifier.size(44.dp),
                                        colors = IconButtonDefaults.outlinedIconButtonColors(
                                            contentColor = MaterialTheme.colorScheme.error
                                        )
                                    ) {
                                        Icon(Icons.Default.DeleteOutline, contentDescription = "Hapus resep")
                                    }
                                }
                            } else {
                                OutlinedIconButton(
                                    onClick = { viewModel.toggleSaved(recipe) },
                                    shape = RoundedCornerShape(12.dp),
                                    modifier = Modifier.size(44.dp)
                                ) {
                                    Icon(
                                        if (recipe.is_saved) Icons.Default.Bookmark else Icons.Outlined.BookmarkBorder,
                                        contentDescription = if (recipe.is_saved) "Hapus dari koleksi" else "Simpan",
                                        tint = if (recipe.is_saved) Color(0xFFF97316) else Color.Black
                                    )
                                }
                            }
                        }

                        Spacer(modifier = Modifier.height(12.dp))

                        // Deskripsi Asli
                        Text(
                            text = recipe.description,
                            fontSize = 14.sp,
                            color = Color.Gray,
                            lineHeight = 20.sp
                        )

                        Spacer(modifier = Modifier.height(20.dp))

                        // 3. Info Koki Asli
                        Card(
                            modifier = Modifier.fillMaxWidth(),
                            shape = RoundedCornerShape(16.dp),
                            colors = CardDefaults.cardColors(containerColor = Color(0xFFF8FAFC))
                        ) {
                            Row(
                                modifier = Modifier.padding(16.dp),
                                verticalAlignment = Alignment.CenterVertically,
                                horizontalArrangement = Arrangement.SpaceBetween
                            ) {
                                Row(verticalAlignment = Alignment.CenterVertically) {
                                    Surface(
                                        modifier = Modifier.size(40.dp),
                                        shape = CircleShape,
                                        color = Color(0xFFE2E8F0)
                                    ) {
                                        Box(contentAlignment = Alignment.Center) {
                                            Icon(Icons.Default.Person, contentDescription = "Koki", tint = Color.Gray)
                                        }
                                    }
                                    Spacer(modifier = Modifier.width(12.dp))
                                    Column {
                                        Text(text = recipe.creator?.name ?: "Anonim", fontWeight = FontWeight.Bold, fontSize = 14.sp)
                                        Text(text = recipe.creator?.role_label ?: "Koki", color = Color(0xFF3B82F6), fontSize = 11.sp, fontWeight = FontWeight.Medium)
                                    }
                                }
                                Column(horizontalAlignment = Alignment.End) {
                                    Text(text = "${recipe.estimated_time} Min", fontWeight = FontWeight.Bold, fontSize = 14.sp)
                                    Text(text = recipe.difficulty.replaceFirstChar { it.uppercase() }, color = Color.Gray, fontSize = 12.sp)
                                }
                            }
                        }

                        Spacer(modifier = Modifier.height(24.dp))

                        // 4. Bahan-Bahan Asli
                        Text(text = "Bahan-Bahan", fontSize = 18.sp, fontWeight = FontWeight.Bold)
                        Spacer(modifier = Modifier.height(12.dp))

                        // Looping data bahan dari Laravel
                        recipe.ingredients.orEmpty().forEach { bahan ->
                            Row(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .padding(vertical = 4.dp)
                                    .border(1.dp, Color(0xFFF1F5F9), RoundedCornerShape(8.dp))
                                    .padding(16.dp),
                                horizontalArrangement = Arrangement.SpaceBetween
                            ) {
                                Text(text = bahan.name.replaceFirstChar { it.uppercase() }, fontWeight = FontWeight.Medium)
                                Text(text = bahan.quantity, color = Color.Gray)
                            }
                        }

                        Spacer(modifier = Modifier.height(24.dp))

                        // 5. Langkah Memasak Asli
                        Text(text = "Langkah Memasak", fontSize = 18.sp, fontWeight = FontWeight.Bold)
                        Spacer(modifier = Modifier.height(12.dp))

                        // Looping data langkah dari Laravel
                        recipe.steps.orEmpty().forEach { langkah ->
                            Row(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .padding(vertical = 8.dp),
                                verticalAlignment = Alignment.Top
                            ) {
                                Surface(
                                    modifier = Modifier.size(28.dp),
                                    shape = CircleShape,
                                    color = Color(0xFFF1F5F9)
                                ) {
                                    Box(contentAlignment = Alignment.Center) {
                                        Text(text = langkah.number.toString(), fontWeight = FontWeight.Bold, fontSize = 12.sp)
                                    }
                                }
                                Spacer(modifier = Modifier.width(12.dp))
                                Column(modifier = Modifier.weight(1f)) {
                                    Text(
                                        text = langkah.title,
                                        fontSize = 14.sp,
                                        fontWeight = FontWeight.Bold,
                                        color = Color(0xFF334155)
                                    )
                                    Text(
                                        text = langkah.description,
                                        fontSize = 14.sp,
                                        color = Color.Gray,
                                        lineHeight = 20.sp,
                                        modifier = Modifier.padding(top = 4.dp)
                                    )
                                }
                            }
                        }

                        Spacer(modifier = Modifier.height(24.dp))

                        Card(
                            modifier = Modifier
                                .fillMaxWidth()
                                .clickable { showReviews = true },
                            shape = RoundedCornerShape(16.dp),
                            colors = CardDefaults.cardColors(containerColor = Color(0xFFF8FAFC)),
                            border = BoxBorderDefaults()
                        ) {
                            Row(
                                modifier = Modifier.padding(16.dp),
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                Column(modifier = Modifier.weight(1f)) {
                                    Text(
                                        "Komentar & Ulasan",
                                        fontSize = 18.sp,
                                        fontWeight = FontWeight.Bold
                                    )
                                    Row(
                                        verticalAlignment = Alignment.CenterVertically,
                                        modifier = Modifier.padding(top = 5.dp)
                                    ) {
                                        Icon(
                                            Icons.Default.Star,
                                            contentDescription = null,
                                            tint = Color(0xFFFBBF24),
                                            modifier = Modifier.size(17.dp)
                                        )
                                        Spacer(Modifier.width(5.dp))
                                        Text(
                                            "${recipe.average_rating ?: "-"} · ${recipe.reviews_count} ulasan",
                                            fontSize = 13.sp,
                                            color = Color.Gray
                                        )
                                    }
                                    recipe.reviews.orEmpty().firstOrNull()?.let {
                                        Text(
                                            "@${it.user.username}: ${it.comment}",
                                            maxLines = 1,
                                            fontSize = 12.sp,
                                            color = Color(0xFF64748B),
                                            modifier = Modifier.padding(top = 7.dp)
                                        )
                                    }
                                }
                                Icon(
                                    Icons.Default.ChevronRight,
                                    contentDescription = "Buka ulasan",
                                    tint = Color.Gray
                                )
                            }
                        }

                        Spacer(modifier = Modifier.height(100.dp))
                    }
                }
            }
        }

        if (showDeleteConfirmation) {
            AlertDialog(
                onDismissRequest = { showDeleteConfirmation = false },
                icon = { Icon(Icons.Default.DeleteOutline, contentDescription = null) },
                title = { Text("Hapus resep?") },
                text = { Text("Resep yang sudah dihapus tidak dapat dikembalikan.") },
                confirmButton = {
                    TextButton(
                        onClick = {
                            showDeleteConfirmation = false
                            viewModel.deleteRecipe(recipe.id) {
                                navController.navigate("home") {
                                    popUpTo("home") { inclusive = true }
                                }
                            }
                        }
                    ) {
                        Text("Hapus", color = MaterialTheme.colorScheme.error)
                    }
                },
                dismissButton = {
                    TextButton(onClick = { showDeleteConfirmation = false }) {
                        Text("Batal")
                    }
                }
            )
        }

        if (showReviews) {
            ReviewBottomSheet(
                recipe = recipe,
                isLoggedIn = viewModel.isLoggedIn.value,
                currentUserId = viewModel.currentUser.value?.id,
                errorMessage = viewModel.errorMessage.value,
                onDismiss = { showReviews = false },
                onLogin = { navController.navigate("login") },
                onSubmit = { rating, comment ->
                    viewModel.submitReview(recipe.id, rating, comment) {
                        showReviews = false
                    }
                }
            )
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun ReviewBottomSheet(
    recipe: Recipe,
    isLoggedIn: Boolean,
    currentUserId: Int?,
    errorMessage: String?,
    onDismiss: () -> Unit,
    onLogin: () -> Unit,
    onSubmit: (Int, String) -> Unit
) {
    val existingReview = recipe.my_review ?: recipe.reviews.orEmpty()
        .firstOrNull { it.user.id == currentUserId }
        ?.let { MyReview(it.rating, it.comment) }
    var rating by remember(recipe.id, existingReview) {
        mutableStateOf(existingReview?.rating ?: 5)
    }
    var comment by remember(recipe.id, existingReview) {
        mutableStateOf(existingReview?.comment ?: "")
    }

    ModalBottomSheet(
        onDismissRequest = onDismiss,
        containerColor = Color.White
    ) {
        Column(
            modifier = Modifier
                .fillMaxHeight(0.85f)
                .verticalScroll(rememberScrollState())
                .padding(horizontal = 20.dp)
        ) {
            Text("Komentar & Ulasan", fontSize = 21.sp, fontWeight = FontWeight.ExtraBold)
            Text(
                "${recipe.average_rating ?: "-"} dari ${recipe.reviews_count} ulasan",
                color = Color.Gray,
                fontSize = 13.sp
            )

            Spacer(Modifier.height(16.dp))
            if (isLoggedIn) {
                Text(
                    if (existingReview == null) "Beri ulasan" else "Perbarui ulasan",
                    fontWeight = FontWeight.Bold
                )
                Row(modifier = Modifier.padding(vertical = 8.dp)) {
                    (1..5).forEach { value ->
                        IconButton(onClick = { rating = value }) {
                            Icon(
                                if (value <= rating) Icons.Default.Star else Icons.Outlined.StarBorder,
                                contentDescription = "$value bintang",
                                tint = if (value <= rating) Color(0xFFFBBF24) else Color.LightGray
                            )
                        }
                    }
                }
                OutlinedTextField(
                    value = comment,
                    onValueChange = { comment = it },
                    placeholder = { Text("Bagaimana hasil masakanmu?") },
                    minLines = 2,
                    maxLines = 4,
                    modifier = Modifier.fillMaxWidth(),
                    shape = RoundedCornerShape(12.dp)
                )
                errorMessage?.let {
                    Text(it, color = MaterialTheme.colorScheme.error, fontSize = 12.sp)
                }
                Button(
                    onClick = { onSubmit(rating, comment) },
                    modifier = Modifier.fillMaxWidth().padding(top = 10.dp),
                    colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFF97316))
                ) {
                    Text("Kirim ulasan")
                }
            } else {
                TextButton(onClick = onLogin) {
                    Text("Masuk untuk memberi ulasan", color = Color(0xFFF97316))
                }
            }

            Divider(modifier = Modifier.padding(vertical = 16.dp))
            if (recipe.reviews.orEmpty().isEmpty()) {
                Text("Belum ada ulasan.", color = Color.Gray)
            } else {
                recipe.reviews.orEmpty().forEach { review ->
                    Row(
                        modifier = Modifier.fillMaxWidth().padding(vertical = 10.dp),
                        verticalAlignment = Alignment.Top
                    ) {
                        Surface(
                            modifier = Modifier.size(38.dp),
                            shape = CircleShape,
                            color = Color(0xFFE2E8F0)
                        ) {
                            Box(contentAlignment = Alignment.Center) {
                                Icon(Icons.Default.Person, contentDescription = null)
                            }
                        }
                        Spacer(Modifier.width(10.dp))
                        Column(modifier = Modifier.weight(1f)) {
                            Row(
                                modifier = Modifier.fillMaxWidth(),
                                horizontalArrangement = Arrangement.SpaceBetween
                            ) {
                                Text("@${review.user.username}", fontWeight = FontWeight.Bold)
                                Text("★ ${review.rating}", color = Color(0xFFF59E0B))
                            }
                            Text(review.comment, fontSize = 13.sp, lineHeight = 18.sp)
                        }
                    }
                }
            }
            Spacer(Modifier.height(28.dp))
        }
    }
}

@Composable
fun BoxBorderDefaults() = androidx.compose.foundation.BorderStroke(1.dp, Color(0xFFE2E8F0))

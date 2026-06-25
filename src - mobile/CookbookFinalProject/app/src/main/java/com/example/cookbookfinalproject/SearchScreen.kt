package com.example.cookbookfinalproject

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardActions
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.PlayArrow
import androidx.compose.material.icons.filled.Star
import androidx.compose.material.icons.outlined.BookmarkBorder
import androidx.compose.material.icons.outlined.Search
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.platform.LocalFocusManager
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.ImeAction
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavHostController
import coil.compose.AsyncImage

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun SearchScreen(
    navController: NavHostController,
    viewModel: RecipeViewModel = viewModel()
) {
    val searchQuery = viewModel.searchQuery.value
    val searchResults = viewModel.searchResults.value
    val focusManager = LocalFocusManager.current

    // Logika penentuan daftar resep yang akan ditampilkan
    val displayRecipes = if (searchQuery.isEmpty()) viewModel.recipes.value else searchResults

    LazyColumn(
        modifier = Modifier
            .fillMaxSize()
            .background(Color.White)
            .padding(horizontal = 16.dp)
    ) {
        // 1. Header Halaman
        item {
            Spacer(modifier = Modifier.height(16.dp))
            Text(
                text = "Cari",
                fontSize = 24.sp,
                fontWeight = FontWeight.ExtraBold,
                color = Color.Black
            )
            Spacer(modifier = Modifier.height(16.dp))

            // 2. Search Bar (Kolom Pencarian)
            OutlinedTextField(
                value = searchQuery,
                onValueChange = { newText -> viewModel.performSearch(newText) },
                placeholder = { Text("Cari resep", color = Color.Gray) },
                leadingIcon = { Icon(Icons.Outlined.Search, contentDescription = "Search", tint = Color.DarkGray) },
                modifier = Modifier
                    .fillMaxWidth()
                    .height(56.dp),
                shape = RoundedCornerShape(12.dp),
                colors = TextFieldDefaults.outlinedTextFieldColors(
                    containerColor = Color(0xFFE2E8F0),
                    unfocusedBorderColor = Color.Transparent,
                    focusedBorderColor = Color(0xFFF97316)
                ),
                singleLine = true, // Memastikan input hanya 1 baris
                keyboardOptions = KeyboardOptions(imeAction = ImeAction.Search), // Tombol enter jadi ikon cari
                keyboardActions = KeyboardActions(
                    onSearch = { focusManager.clearFocus() } // Menutup keyboard saat ditekan
                )
            )
            Spacer(modifier = Modifier.height(24.dp))
        }

        // 3. Section: Mulai Jelajahi (Kategori)
        item {
            Text(text = "Mulai Jelajahi", fontSize = 18.sp, fontWeight = FontWeight.Bold)
            Spacer(modifier = Modifier.height(12.dp))

            Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                CategoryBox(title = "Masakan Nusantara", modifier = Modifier.weight(1f))
                CategoryBox(title = "Makanan Ringan", modifier = Modifier.weight(1f))
            }
            Spacer(modifier = Modifier.height(12.dp))
            Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                CategoryBox(title = "Kue & Pastri", modifier = Modifier.weight(1f))
                CategoryBox(title = "Minuman Tradisional & Modern", modifier = Modifier.weight(1f))
            }

            Spacer(modifier = Modifier.height(24.dp))
            Divider(color = Color.LightGray, thickness = 1.dp)
            Spacer(modifier = Modifier.height(16.dp))
        }

        // 4. Section: Sub-Judul Dinamis
        item {
            Text(
                text = if (searchQuery.isEmpty()) "Rekomendasi terkini" else "Hasil Pencarian",
                fontSize = 18.sp,
                fontWeight = FontWeight.Bold
            )
            Text(
                text = if (searchQuery.isEmpty()) "Resep pilihan terbaik untukmu hari ini" else "Menampilkan resep untuk '$searchQuery'",
                fontSize = 12.sp,
                color = Color.Gray
            )
            Spacer(modifier = Modifier.height(16.dp))
        }

        // 5. Grid Daftar Resep & Logika Pesan Kosong
        if (searchQuery.isNotEmpty() && displayRecipes.isEmpty()) {
            item {
                Box(modifier = Modifier.fillMaxWidth().padding(top = 40.dp), contentAlignment = Alignment.Center) {
                    Text(
                        text = "Resep tidak ditemukan.",
                        color = Color.Gray,
                        fontSize = 14.sp,
                        textAlign = TextAlign.Center
                    )
                }
            }
        } else {
            items(displayRecipes.chunked(2)) { rowRecipes ->
                Row(
                    modifier = Modifier.fillMaxWidth().padding(bottom = 16.dp),
                    horizontalArrangement = Arrangement.spacedBy(16.dp)
                ) {
                    for (recipe in rowRecipes) {
                        Box(modifier = Modifier.weight(1f)) {
                            SearchRecipeCard(
                                recipe = recipe,
                                onClick = { navController.navigate("detail/${recipe.id}") }
                            )
                        }
                    }
                    if (rowRecipes.size == 1) {
                        Spacer(modifier = Modifier.weight(1f))
                    }
                }
            }
        }

        item {
            Spacer(modifier = Modifier.height(80.dp)) // Ruang pengganjal agar tidak tertutup bottom bar
        }
    }
}

// Komponen Kotak Kategori Beranda
@Composable
fun CategoryBox(title: String, modifier: Modifier = Modifier) {
    Box(
        modifier = modifier
            .border(1.dp, Color.Black, RoundedCornerShape(8.dp))
            .clickable { /* Aksi Kategori */ }
            .padding(vertical = 12.dp, horizontal = 8.dp),
        contentAlignment = Alignment.CenterStart
    ) {
        Text(text = title, fontSize = 12.sp, color = Color.DarkGray, maxLines = 2)
    }
}

// Komponen Kartu Resep Khusus Halaman Cari
@Composable
fun SearchRecipeCard(recipe: Recipe, onClick: () -> Unit) {
    Card(
        modifier = Modifier
            .fillMaxWidth()
            .height(200.dp)
            .clickable { onClick() }
            .border(1.dp, Color.Black, RoundedCornerShape(16.dp)),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        shape = RoundedCornerShape(16.dp)
    ) {
        Column {
            // Bagian Atas: Gambar dan Badge
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .height(100.dp)
            ) {
                AsyncImage(
                    model = "http://10.159.139.75:8000${recipe.thumbnail_url}",
                    contentDescription = null,
                    contentScale = ContentScale.Crop,
                    modifier = Modifier.fillMaxSize().background(Color(0xFFF1F5F9))
                )

                // Tombol Bookmark Kiri Atas
                Icon(
                    imageVector = Icons.Outlined.BookmarkBorder,
                    contentDescription = "Simpan",
                    modifier = Modifier.padding(8.dp).size(24.dp).align(Alignment.TopStart),
                    tint = Color.Black
                )

                // REVISI LOGIKA: Label Video Hanya Muncul Jika Link Video Tersedia
                if (!recipe.video_url.isNullOrEmpty()) {
                    Surface(
                        color = Color.Black,
                        shape = RoundedCornerShape(12.dp),
                        modifier = Modifier.padding(8.dp).align(Alignment.TopEnd)
                    ) {
                        Row(
                            verticalAlignment = Alignment.CenterVertically,
                            modifier = Modifier.padding(horizontal = 6.dp, vertical = 2.dp)
                        ) {
                            Icon(Icons.Default.PlayArrow, contentDescription = null, tint = Color.White, modifier = Modifier.size(12.dp))
                            Spacer(modifier = Modifier.width(2.dp))
                            Text("Video", color = Color.White, fontSize = 9.sp)
                        }
                    }
                }

                // Label Rating Kiri Bawah
                Surface(
                    color = Color.White,
                    shape = RoundedCornerShape(12.dp),
                    border = androidx.compose.foundation.BorderStroke(0.5.dp, Color.Gray),
                    modifier = Modifier.padding(8.dp).align(Alignment.BottomStart)
                ) {
                    Row(
                        verticalAlignment = Alignment.CenterVertically,
                        modifier = Modifier.padding(horizontal = 6.dp, vertical = 2.dp)
                    ) {
                        Icon(Icons.Default.Star, contentDescription = null, tint = Color(0xFFFBBF24), modifier = Modifier.size(10.dp))
                        Spacer(modifier = Modifier.width(2.dp))
                        Text("4.8", fontSize = 9.sp, fontWeight = FontWeight.Bold)
                    }
                }
            }

            // Bagian Bawah: Informasi Teks Resep
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(8.dp),
                verticalArrangement = Arrangement.SpaceBetween
            ) {
                Column {
                    Text(text = recipe.title.replaceFirstChar { it.uppercase() }, fontWeight = FontWeight.Bold, fontSize = 12.sp, maxLines = 1)
                    Text(text = recipe.creator?.name ?: "Creator", color = Color.Gray, fontSize = 10.sp, maxLines = 1)
                }

                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Text(text = "${recipe.estimated_time} Min", color = Color.Gray, fontSize = 10.sp)
                    Surface(
                        shape = RoundedCornerShape(12.dp),
                        border = androidx.compose.foundation.BorderStroke(0.5.dp, Color.Gray)
                    ) {
                        Text(
                            text = recipe.difficulty.uppercase(),
                            fontSize = 8.sp,
                            modifier = Modifier.padding(horizontal = 6.dp, vertical = 2.dp),
                            color = Color.DarkGray
                        )
                    }
                }
            }
        }
    }
}
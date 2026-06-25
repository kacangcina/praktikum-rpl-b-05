package com.example.cookbookfinalproject

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Star
import androidx.compose.material.icons.outlined.Bookmark
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavHostController
import coil.compose.AsyncImage

@Composable
fun CollectionScreen(
    navController: NavHostController,
    viewModel: RecipeViewModel = viewModel()
) {
    // Sementara menggunakan data resep umum untuk melihat bentuk desainnya
    val savedRecipes = viewModel.recipes.value

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFFF8FAFC)) // Warna background abu-abu sangat terang
    ) {
        // 1. Header Halaman
        Surface(
            modifier = Modifier.fillMaxWidth(),
            color = Color.White,
            shadowElevation = 2.dp
        ) {
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(16.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                Icon(Icons.Outlined.Bookmark, contentDescription = "Koleksi", tint = Color(0xFFF97316))
                Spacer(modifier = Modifier.width(12.dp))
                Text(
                    text = "Koleksi Resepku",
                    fontSize = 20.sp,
                    fontWeight = FontWeight.Bold,
                    color = Color(0xFF1E293B)
                )
            }
        }

        // 2. Konten Grid Resep
        if (savedRecipes.isEmpty()) {
            // Tampilan jika koleksi kosong
            Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                Text("Belum ada resep yang disimpan.", color = Color.Gray)
            }
        } else {
            // Tampilan Grid 2 Kolom
            LazyVerticalGrid(
                columns = GridCells.Fixed(2),
                contentPadding = PaddingValues(16.dp),
                horizontalArrangement = Arrangement.spacedBy(16.dp),
                verticalArrangement = Arrangement.spacedBy(16.dp),
                modifier = Modifier.fillMaxSize()
            ) {
                items(savedRecipes) { recipe ->
                    SavedRecipeCard(recipe = recipe, onClick = {
                        navController.navigate("detail/${recipe.id}")
                    })
                }
            }
        }
    }
}

// Komponen Kartu Khusus untuk Halaman Koleksi
@Composable
fun SavedRecipeCard(recipe: Recipe, onClick: () -> Unit) {
    Card(
        modifier = Modifier
            .fillMaxWidth()
            .height(210.dp)
            .clickable { onClick() },
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        elevation = CardDefaults.cardElevation(2.dp)
    ) {
        Column {
            // Bagian Gambar
            AsyncImage(
                // Ganti IP ini dengan IP yang aktif di RetrofitClient.kt milikmu!
                model = "http://192.168.0.107:8000${recipe.thumbnail_url}",
                contentDescription = recipe.title,
                contentScale = ContentScale.Crop,
                modifier = Modifier
                    .fillMaxWidth()
                    .height(110.dp)
                    .background(Color.LightGray)
            )

            // Bagian Teks
            Column(modifier = Modifier.padding(12.dp)) {
                Text(
                    text = recipe.title.replaceFirstChar { it.uppercase() },
                    fontWeight = FontWeight.Bold,
                    fontSize = 13.sp,
                    maxLines = 1
                )
                Spacer(modifier = Modifier.height(2.dp))
                Text(
                    text = recipe.creator?.name ?: "Chef CuBu",
                    color = Color.Gray,
                    fontSize = 10.sp,
                    maxLines = 1
                )
                Spacer(modifier = Modifier.weight(1f))
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Icon(Icons.Default.Star, contentDescription = "Rating", tint = Color(0xFFFBBF24), modifier = Modifier.size(12.dp))
                        Spacer(modifier = Modifier.width(4.dp))
                        Text(text = "4.8", fontSize = 11.sp, fontWeight = FontWeight.Bold)
                    }
                    Text(
                        text = recipe.difficulty.uppercase(),
                        color = Color(0xFFF97316),
                        fontSize = 9.sp,
                        fontWeight = FontWeight.Bold
                    )
                }
            }
        }
    }
}
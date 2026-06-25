package com.example.cookbookfinalproject

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.horizontalScroll
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Search
import androidx.compose.material.icons.filled.Star
import androidx.compose.material.icons.outlined.DarkMode
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavHostController
import coil.compose.AsyncImage
import androidx.compose.ui.graphics.Brush
import androidx.compose.material.icons.filled.ChevronRight

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun HomeScreen(
    navController: NavHostController,
    viewModel: RecipeViewModel = viewModel()
) {
    // Menarik data resep dari ViewModel (Laravel API)
    val recipeList = viewModel.recipes.value

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color.White)
            .verticalScroll(rememberScrollState())
            .padding(16.dp)
    ) {
        // 1. Header CuBu
        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Text(
                text = "CuBu",
                fontSize = 24.sp,
                fontWeight = FontWeight.ExtraBold,
                color = Color(0xFF1E293B)
            )
//            IconButton(onClick = { /* Todo: Dark mode toggle */ }) {
//                Icon(
//                    imageVector = Icons.Outlined.DarkMode,
//                    contentDescription = "Dark Mode",
//                    tint = Color(0xFF1E293B)
//                )
//            }
        }

        Spacer(modifier = Modifier.height(16.dp))

        // 2. Featured Recipe Card
        val featured = viewModel.featuredRecipe.value

        if (featured != null) {
            Card(
                modifier = Modifier
                    .fillMaxWidth()
                    .height(200.dp) // Sesuaikan tinggi kartu agar proporsional
                    .padding(16.dp)
                    .clickable { navController.navigate("detail/${featured.id}") },
                shape = RoundedCornerShape(20.dp),
                elevation = CardDefaults.cardElevation(4.dp)
            ) {
                Box(modifier = Modifier.fillMaxSize()) {

                    // 1. Gambar Background dari Laravel
                    AsyncImage(
                        model = "http://192.168.0.107:8000${featured.thumbnail_url}",
                        contentDescription = null,
                        contentScale = ContentScale.Crop, // Agar gambar memenuhi kotak
                        modifier = Modifier.fillMaxSize()
                    )

                    // 2. Lapisan Gradasi (Overlay)
                    // Dari Kiri (Gelap) ke Kanan (Transparan)
                    Box(
                        modifier = Modifier
                            .fillMaxSize()
                            .background(
                                brush = Brush.horizontalGradient(
                                    colors = listOf(
                                        Color.Black.copy(alpha = 0.8f), // Kiri sangat gelap
                                        Color.Black.copy(alpha = 0.4f), // Tengah agak gelap
                                        Color.Transparent             // Kanan bening
                                    ),
                                    startX = 0f,
                                    endX = Float.POSITIVE_INFINITY
                                )
                            )
                    )

                    // 3. Konten Teks dan Tombol
                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .padding(20.dp),
                        verticalArrangement = Arrangement.Center // Teks di tengah secara vertikal
                    ) {
                        // Badge "Resep Nusantara"
                        Surface(
                            shape = RoundedCornerShape(12.dp),
                            color = Color.White.copy(alpha = 0.2f),
                            modifier = Modifier.padding(bottom = 8.dp)
                        ) {
                            Text(
                                text = "Resep Nusantara",
                                color = Color.White,
                                fontSize = 11.sp,
                                fontWeight = FontWeight.Medium,
                                modifier = Modifier.padding(horizontal = 10.dp, vertical = 4.dp)
                            )
                        }

                        // Judul Resep
                        Text(
                            text = featured.title.replaceFirstChar { it.uppercase() },
                            color = Color.White,
                            fontSize = 24.sp,
                            fontWeight = FontWeight.ExtraBold
                        )

                        // Deskripsi
                        Text(
                            text = featured.description,
                            color = Color.White.copy(alpha = 0.8f),
                            fontSize = 14.sp,
                            modifier = Modifier.padding(bottom = 12.dp)
                        )

                        // Tombol "Lihat resep >"
                        Button(
                            onClick = { navController.navigate("detail/${featured.id}") },
                            colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFF97316)), // Warna Orange
                            shape = RoundedCornerShape(12.dp),
                            contentPadding = PaddingValues(horizontal = 16.dp, vertical = 8.dp),
                            modifier = Modifier.height(36.dp)
                        ) {
                            Row(verticalAlignment = Alignment.CenterVertically) {
                                Text(text = "Lihat resep", color = Color.White, fontSize = 12.sp, fontWeight = FontWeight.Bold)
                                Spacer(modifier = Modifier.width(4.dp))
                                Icon(
                                    imageVector = Icons.Default.ChevronRight,
                                    contentDescription = null,
                                    tint = Color.White,
                                    modifier = Modifier.size(16.dp)
                                )
                            }
                        }
                    }
                }
            }
        }

        Spacer(modifier = Modifier.height(24.dp))

        // 3. Search Bar
        OutlinedTextField(
            value = "",
            onValueChange = {},
            placeholder = { Text("Cari resep") },
            leadingIcon = { Icon(Icons.Default.Search, contentDescription = "Search") },
            modifier = Modifier.fillMaxWidth(),
            shape = RoundedCornerShape(16.dp),
            colors = TextFieldDefaults.colors(
                unfocusedContainerColor = Color.Transparent,
                focusedContainerColor = Color.Transparent,
                unfocusedIndicatorColor = Color(0xFFE2E8F0),
                focusedIndicatorColor = Color(0xFFF97316)
            )
        )

        Spacer(modifier = Modifier.height(24.dp))

        // 4. Filter Bahan Kulkas
        Text(
            text = "Punya bahan apa di kulkas?",
            fontSize = 16.sp,
            fontWeight = FontWeight.Bold
        )
        Text(
            text = "Pilih bahan yang kamu miliki, Cubu carikan resepnya.",
            fontSize = 12.sp,
            color = Color.Gray
        )
        Spacer(modifier = Modifier.height(12.dp))
        Row(
            modifier = Modifier.horizontalScroll(rememberScrollState()),
            horizontalArrangement = Arrangement.spacedBy(8.dp)
        ) {
            val ingredients = listOf("Ayam", "Sapi", "Telur", "Tahu", "Tempe")
            ingredients.forEach { item ->
                FilterChip(
                    selected = item == "Ayam",
                    onClick = { /* Todo: Filter logic */ },
                    label = { Text(item) },
                    colors = FilterChipDefaults.filterChipColors(
                        selectedContainerColor = Color.White,
                        selectedLabelColor = Color.Black
                    ),
                    border = FilterChipDefaults.filterChipBorder(
                        borderColor = if (item == "Ayam") Color.Gray else Color(0xFFE2E8F0)
                    )
                )
            }
        }

        Spacer(modifier = Modifier.height(24.dp))

        // 5. Rekomendasi Terkini (Grid 2 Kolom)
        Text(
            text = "Rekomendasi terkini",
            fontSize = 18.sp,
            fontWeight = FontWeight.Bold
        )
        Text(
            text = "Resep pilihan terbaik untukmu hari ini",
            fontSize = 12.sp,
            color = Color.Gray
        )
        Spacer(modifier = Modifier.height(16.dp))

        val chunkedRecipes = recipeList.chunked(2)

        Column(verticalArrangement = Arrangement.spacedBy(16.dp)) {
            chunkedRecipes.forEach { rowRecipes ->
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(16.dp)
                ) {
                    // Mengirimkan navController ke dalam kartu agar bisa diklik
                    RecipeCard(
                        recipe = rowRecipes[0],
                        navController = navController,
                        modifier = Modifier.weight(1f)
                    )

                    if (rowRecipes.size > 1) {
                        RecipeCard(
                            recipe = rowRecipes[1],
                            navController = navController,
                            modifier = Modifier.weight(1f)
                        )
                    } else {
                        Spacer(modifier = Modifier.weight(1f))
                    }
                }
            }
        }

        Spacer(modifier = Modifier.height(80.dp))
    }
}

// 6. Komponen Desain Kartu Resep yang bisa diklik
@Composable
fun RecipeCard(
    recipe: Recipe,
    navController: NavHostController, // Parameter Navigasi
    modifier: Modifier = Modifier
) {
    Card(
        modifier = Modifier
            .width(160.dp) // Sesuaikan dengan ukuran kartumu
            .padding(end = 16.dp)
            .clickable { navController.navigate("detail/${recipe.id}") }, // Bisa diklik
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        elevation = CardDefaults.cardElevation(2.dp)
    ) {
        Column {
            // --- INI ADALAH KUNCI UNTUK MEMUNCULKAN GAMBARNYA ---
            AsyncImage(
                model = "http://10.159.139.75:8000${recipe.thumbnail_url}",
                contentDescription = "Thumbnail ${recipe.title}",
                contentScale = ContentScale.Crop, // Agar gambar terpotong rapi memenuhi kotak
                modifier = Modifier
                    .fillMaxWidth()
                    .height(130.dp) // Sesuaikan tinggi gambar agar pas
                    .background(Color.LightGray) // Warna sementara saat gambar loading
            )

            // --- BAGIAN TEKS DI BAWAH GAMBAR ---
            Column(
                modifier = Modifier.padding(12.dp)
            ) {
                Text(
                    text = recipe.title.replaceFirstChar { it.uppercase() },
                    fontWeight = FontWeight.Bold,
                    fontSize = 14.sp,
                    maxLines = 1
                )

                Text(
                    text = recipe.creator?.name ?: "Chef CuBu", // Nama Koki asli
                    color = Color.Gray,
                    fontSize = 10.sp
                )

                Spacer(modifier = Modifier.height(8.dp))

                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    // Rating
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Icon(
                            Icons.Default.Star,
                            contentDescription = "Rating",
                            tint = Color(0xFFFBBF24),
                            modifier = Modifier.size(14.dp)
                        )
                        Spacer(modifier = Modifier.width(4.dp))
                        Text(text = "4.8", fontSize = 12.sp, fontWeight = FontWeight.Bold)
                    }

                    // Tingkat Kesulitan
                    Text(
                        text = recipe.difficulty.uppercase(),
                        color = Color(0xFFF97316),
                        fontSize = 10.sp,
                        fontWeight = FontWeight.Bold
                    )
                }
            }
        }
    }
}
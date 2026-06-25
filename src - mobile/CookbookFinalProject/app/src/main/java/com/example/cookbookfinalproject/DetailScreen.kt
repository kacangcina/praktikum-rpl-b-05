package com.example.cookbookfinalproject

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material.icons.outlined.BookmarkBorder
import androidx.compose.material.icons.outlined.ChatBubbleOutline
import androidx.compose.material.icons.outlined.MoreVert
import androidx.compose.material.icons.outlined.StarBorder
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavHostController
import coil.compose.AsyncImage

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun DetailScreen(
    navController: NavHostController,
    recipeId: Int,
    viewModel: RecipeViewModel = viewModel()
) {
    // Memuat data saat halaman dibuka
    LaunchedEffect(recipeId) { viewModel.fetchDetail(recipeId) }

    val recipe = viewModel.selectedRecipe.value

    if (recipe == null) {
        // Tampilkan loading berputar jika data belum masuk
        Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            CircularProgressIndicator(color = Color(0xFFF97316))
        }
    } else {
        // Data sudah masuk, tampilkan UI lengkap!
        Box(modifier = Modifier.fillMaxSize().background(Color.White)) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .verticalScroll(rememberScrollState())
            ) {
                // 1. Header Video / Gambar Utama
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(260.dp)
                        .background(Color(0xFF1E293B))
                ) {
                    // Gambar Asli dari Laravel
                    AsyncImage(
                        model = "http://10.159.139.75:8000${recipe.thumbnail_url}",
                        contentDescription = null,
                        contentScale = ContentScale.Crop,
                        modifier = Modifier.fillMaxSize()
                    )

                    // Tombol Navigasi
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(top = 16.dp, start = 16.dp, end = 16.dp),
                        horizontalArrangement = Arrangement.SpaceBetween
                    ) {
                        IconButton(
                            onClick = { navController.popBackStack() },
                            colors = IconButtonDefaults.iconButtonColors(containerColor = Color.Black.copy(alpha = 0.4f))
                        ) {
                            Icon(Icons.Default.ArrowBack, contentDescription = "Kembali", tint = Color.White)
                        }
                        IconButton(
                            onClick = { /* Menu aksi */ },
                            colors = IconButtonDefaults.iconButtonColors(containerColor = Color.Black.copy(alpha = 0.4f))
                        ) {
                            Icon(Icons.Outlined.MoreVert, contentDescription = "Menu", tint = Color.White)
                        }
                    }
                }

                // 2. Konten Utama dengan Sudut Membulat Atas
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
                        OutlinedIconButton(
                            onClick = { /* Simpan ke koleksi */ },
                            shape = RoundedCornerShape(12.dp),
                            modifier = Modifier.size(44.dp)
                        ) {
                            Icon(Icons.Outlined.BookmarkBorder, contentDescription = "Simpan")
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
                    recipe.ingredients?.forEach { bahan ->
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
                    recipe.steps?.forEach { langkah ->
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

                    // 6. Komentar & Ulasan (Tetap menggunakan desain aslimu)
                    Text(text = "Komentar & Ulasan", fontSize = 18.sp, fontWeight = FontWeight.Bold)
                    Spacer(modifier = Modifier.height(12.dp))

                    Card(
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(16.dp),
                        colors = CardDefaults.cardColors(containerColor = Color.White),
                        border = BoxBorderDefaults()
                    ) {
                        Column(modifier = Modifier.padding(16.dp)) {
                            Text(text = "Beri Rating:", fontSize = 14.sp, color = Color.Gray)
                            Row(modifier = Modifier.padding(vertical = 8.dp)) {
                                repeat(5) {
                                    Icon(Icons.Outlined.StarBorder, contentDescription = "Star", tint = Color.LightGray, modifier = Modifier.size(24.dp))
                                }
                            }
                            OutlinedTextField(
                                value = "",
                                onValueChange = {},
                                placeholder = { Text("Bagaimana hasil masakanmu?", fontSize = 12.sp) },
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .height(100.dp),
                                shape = RoundedCornerShape(12.dp)
                            )
                            Spacer(modifier = Modifier.height(12.dp))
                            Button(
                                onClick = { /* Kirim ulasan */ },
                                colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFF97316)),
                                shape = RoundedCornerShape(8.dp),
                                modifier = Modifier.align(Alignment.End)
                            ) {
                                Text(text = "Kirim", color = Color.White)
                            }
                        }
                    }

                    Spacer(modifier = Modifier.height(100.dp))
                }
            }

            // 7. Banner Asisten AI CuBu
            Card(
                modifier = Modifier
                    .fillMaxWidth()
                    .align(Alignment.BottomCenter)
                    .padding(16.dp),
                shape = RoundedCornerShape(16.dp)
            ) {
                Row(
                    modifier = Modifier
                        .background(
                            brush = Brush.horizontalGradient(
                                colors = listOf(Color(0xFF2563EB), Color(0xFF4F46E5))
                            )
                        )
                        .padding(16.dp)
                        .fillMaxWidth(),
                    verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.SpaceBetween
                ) {
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Icon(Icons.Outlined.ChatBubbleOutline, contentDescription = "AI", tint = Color.White)
                        Spacer(modifier = Modifier.width(12.dp))
                        Column {
                            Text(text = "Asisten AI CuBu", color = Color.White, fontWeight = FontWeight.Bold, fontSize = 14.sp)
                            Text(text = "Tanyakan solusi masalah masakan.", color = Color.White.copy(alpha = 0.8f), fontSize = 11.sp)
                        }
                    }
                    Button(
                        onClick = { /* Aksi panggil AI */ },
                        colors = ButtonDefaults.buttonColors(containerColor = Color.White),
                        shape = RoundedCornerShape(12.dp),
                        contentPadding = PaddingValues(horizontal = 16.dp, vertical = 8.dp)
                    ) {
                        Text(text = "Tanya AI", color = Color(0xFF2563EB), fontWeight = FontWeight.Bold, fontSize = 12.sp)
                    }
                }
            }
        }
    }
}

@Composable
fun BoxBorderDefaults() = androidx.compose.foundation.BorderStroke(1.dp, Color(0xFFE2E8F0))
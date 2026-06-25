package com.example.cookbookfinalproject

import androidx.compose.foundation.Image // Import untuk Image
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ExitToApp
import androidx.compose.material.icons.filled.Person
import androidx.compose.material.icons.filled.Restaurant
import androidx.compose.material.icons.filled.Settings
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.res.painterResource // Import untuk memuat gambar dari drawable
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.navigation.NavHostController

@Composable
fun ProfileScreen(navController: NavHostController, viewModel: RecipeViewModel) {
    val dummyRecipes = listOf(1, 2, 3, 4)

    LazyColumn(
        modifier = Modifier
            .fillMaxSize()
            .background(Color.White)
    ) {
        // 1. Header (Logo CuBu Asli & Tombol Logout)
        item {
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 16.dp, vertical = 16.dp),
                verticalAlignment = Alignment.CenterVertically,
                horizontalArrangement = Arrangement.SpaceBetween
            ) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    // REVISI: Menggunakan gambar logo.png dari folder drawable
                    Image(
                        painter = painterResource(id = R.drawable.logo),
                        contentDescription = "Logo CuBu",
                        modifier = Modifier
                            .size(50.dp) // Ukuran disesuaikan agar proporsional
                            .clip(RoundedCornerShape(8.dp))
                    )
                    Spacer(modifier = Modifier.width(8.dp))
                    Text(text = "CuBu", fontSize = 20.sp, fontWeight = FontWeight.Bold)
                }

                IconButton(onClick = {
                    viewModel.isLoggedIn.value = false
                    navController.navigate("home") {
                        popUpTo("home") { inclusive = true }
                    }
                }) {
                    Icon(
                        imageVector = Icons.Default.ExitToApp,
                        contentDescription = "Logout",
                        tint = Color.Red
                    )
                }
            }
        }

        // 2. Bagian Info Profil Utama
        item {
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 16.dp),
                horizontalArrangement = Arrangement.SpaceBetween
            ) {
                // KIRI: Teks Info
                Column(modifier = Modifier.weight(1f)) {
                    Text(text = "Profil", fontSize = 20.sp, fontWeight = FontWeight.ExtraBold)
                    Spacer(modifier = Modifier.height(12.dp))

                    Text(text = "Nama", fontSize = 24.sp, fontWeight = FontWeight.ExtraBold)
                    Text(text = "@Nama Pengguna", color = Color.DarkGray, fontSize = 12.sp)

                    Spacer(modifier = Modifier.height(16.dp))

                    // Statistik (Hanya Resep)
                    Column {
                        Text(text = "0", fontWeight = FontWeight.ExtraBold, fontSize = 16.sp)
                        Text(text = "Resep", color = Color.Black, fontSize = 12.sp)
                    }

                    Spacer(modifier = Modifier.height(16.dp))

                    Text(text = "biodata.", fontSize = 14.sp, color = Color.Black)
                }

                Spacer(modifier = Modifier.width(16.dp))

                // KANAN: Tombol Edit & Foto Profil
                Column(horizontalAlignment = Alignment.End) {
                    // Tombol Edit Profil
                    Surface(
                        color = Color(0xFFE2E8F0),
                        shape = RoundedCornerShape(16.dp),
                        modifier = Modifier.clickable { /* Aksi Edit */ }
                    ) {
                        Row(
                            verticalAlignment = Alignment.CenterVertically,
                            modifier = Modifier.padding(horizontal = 12.dp, vertical = 6.dp)
                        ) {
                            Icon(Icons.Default.Settings, contentDescription = "Edit", modifier = Modifier.size(12.dp))
                            Spacer(modifier = Modifier.width(4.dp))
                            Text("Edit profil", fontSize = 10.sp, fontWeight = FontWeight.Bold)
                        }
                    }

                    Spacer(modifier = Modifier.height(24.dp))

                    // Kotak Foto Profil dengan Badge "?"
                    Box {
                        Surface(
                            shape = CircleShape,
                            color = Color(0xFFE2E8F0),
                            modifier = Modifier.size(80.dp)
                        ) {
                            Icon(Icons.Default.Person, contentDescription = null, tint = Color.Gray, modifier = Modifier.padding(16.dp))
                        }

                        // Badge Lingkaran Oranye "?"
                        Surface(
                            shape = CircleShape,
                            color = Color(0xFFF97316),
                            modifier = Modifier
                                .size(24.dp)
                                .align(Alignment.BottomEnd)
                                .offset(x = 4.dp, y = 4.dp)
                        ) {
                            Box(contentAlignment = Alignment.Center) {
                                Text("?", color = Color.White, fontSize = 14.sp, fontWeight = FontWeight.Bold)
                            }
                        }
                    }
                }
            }
            Spacer(modifier = Modifier.height(24.dp))
        }

        // 3. Tab Indikator (Ikon Garpu Pisau)
        item {
            Column(
                modifier = Modifier.fillMaxWidth(),
                horizontalAlignment = Alignment.CenterHorizontally
            ) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Icon(Icons.Default.Restaurant, contentDescription = "Resep", modifier = Modifier.size(20.dp))
                    Spacer(modifier = Modifier.width(8.dp))
                    Text(text = "Resep", fontWeight = FontWeight.Bold, fontSize = 16.sp)
                }
                Spacer(modifier = Modifier.height(12.dp))
                Divider(color = Color.Black, thickness = 1.dp)
                Spacer(modifier = Modifier.height(16.dp))
            }
        }

        // 4. Grid Resep Dummy
        items(dummyRecipes.chunked(2)) { rowItems ->
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 16.dp, vertical = 8.dp),
                horizontalArrangement = Arrangement.spacedBy(16.dp)
            ) {
                for (item in rowItems) {
                    Box(modifier = Modifier.weight(1f)) {
                        ProfileRecipePlaceholder()
                    }
                }
                if (rowItems.size == 1) {
                    Spacer(modifier = Modifier.weight(1f))
                }
            }
        }

        item {
            Spacer(modifier = Modifier.height(80.dp))
        }
    }
}

@Composable
fun ProfileRecipePlaceholder() {
    Card(
        modifier = Modifier
            .fillMaxWidth()
            .height(180.dp)
            .border(1.dp, Color.Black, RoundedCornerShape(16.dp)),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        shape = RoundedCornerShape(16.dp)
    ) {
        Column {
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .height(90.dp)
                    .background(Color(0xFFF1F5F9))
            )

            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(8.dp),
                verticalArrangement = Arrangement.SpaceBetween
            ) {
                Column {
                    Text(text = "(nama makanan)", fontWeight = FontWeight.Bold, fontSize = 12.sp)
                    Text(text = "(Creator)", color = Color.DarkGray, fontSize = 10.sp)
                }

                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Text(text = "(Waktu)", color = Color.DarkGray, fontSize = 10.sp)
                    Surface(
                        shape = RoundedCornerShape(12.dp),
                        border = androidx.compose.foundation.BorderStroke(1.dp, Color.Black),
                        color = Color.Transparent
                    ) {
                        Text(
                            text = "(Tingkat Kesulitan)",
                            fontSize = 8.sp,
                            modifier = Modifier.padding(horizontal = 6.dp, vertical = 2.dp),
                            color = Color.Black
                        )
                    }
                }
            }
        }
    }
}
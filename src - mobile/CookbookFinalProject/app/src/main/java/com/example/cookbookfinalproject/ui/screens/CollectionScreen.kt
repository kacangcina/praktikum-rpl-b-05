package com.example.cookbookfinalproject.ui.screens

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavHostController
import com.example.cookbookfinalproject.R
import com.example.cookbookfinalproject.ui.viewmodel.RecipeViewModel

@Composable
fun CollectionScreen(
    navController: NavHostController,
    viewModel: RecipeViewModel = viewModel()
) {
    LaunchedEffect(Unit) { viewModel.fetchCollection() }
    val savedRecipes = viewModel.collectionRecipes.value

    LazyColumn(
        modifier = Modifier
            .fillMaxSize()
            .background(Color.White)
            .padding(horizontal = 16.dp)
    ) {
        // 1. Header Aplikasi (Logo + Nama)
        item {
            Spacer(modifier = Modifier.height(16.dp))
            Row(
                modifier = Modifier.fillMaxWidth(),
                verticalAlignment = Alignment.CenterVertically
            ) {
                Image(
                    painter = painterResource(id = R.drawable.logo),
                    contentDescription = "Logo CuBu",
                    modifier = Modifier
                        .size(50.dp)
                        .clip(RoundedCornerShape(12.dp))
                )
                Spacer(modifier = Modifier.width(10.dp))
                Text(text = "CuBu", fontSize = 24.sp, fontWeight = FontWeight.Bold, color = Color.Black)
            }
            Spacer(modifier = Modifier.height(20.dp))
        }

        // 2. Judul Halaman Koleksi
        item {
            Text(
                text = "Koleksi Resep",
                fontSize = 24.sp,
                fontWeight = FontWeight.ExtraBold,
                color = Color(0xFF1E293B)
            )
            Text(
                text = "Daftar resep kuliner yang telah kamu simpan.",
                fontSize = 14.sp,
                color = Color.Gray
            )
            Spacer(modifier = Modifier.height(24.dp))
        }

        // 3. Grid Daftar Koleksi Tersimpan
        if (savedRecipes.isEmpty()) {
            item {
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(top = 60.dp),
                    contentAlignment = Alignment.Center
                ) {
                    Text(
                        text = "Belum ada resep yang disimpan.",
                        color = Color.Gray,
                        fontSize = 14.sp
                    )
                }
            }
        } else {
            items(savedRecipes.chunked(2)) { rowRecipes ->
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(bottom = 16.dp),
                    horizontalArrangement = Arrangement.spacedBy(16.dp)
                ) {
                    for (recipe in rowRecipes) {
                        RecipeCard(
                            recipe = recipe,
                            navController = navController,
                            modifier = Modifier.weight(1f),
                            viewModel = viewModel
                        )
                    }
                    if (rowRecipes.size == 1) {
                        Spacer(modifier = Modifier.weight(1f))
                    }
                }
            }
        }

        item {
            Spacer(modifier = Modifier.height(80.dp))
        }
    }
}

package com.example.cookbookfinalproject.ui.screens

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.horizontalScroll
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Bookmark
import androidx.compose.material.icons.outlined.BookmarkBorder
import androidx.compose.material.icons.filled.Search
import androidx.compose.material.icons.filled.Star
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
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.draw.clip
import androidx.compose.ui.res.painterResource
import com.example.cookbookfinalproject.R
import com.example.cookbookfinalproject.data.model.Recipe
import com.example.cookbookfinalproject.ui.viewmodel.RecipeViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun HomeScreen(
    navController: NavHostController,
    viewModel: RecipeViewModel = viewModel()
) {
    val recipeList = viewModel.recipes.value

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color.White)
            .verticalScroll(rememberScrollState())
            .padding(16.dp)
    ) {
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
            Text(
                text = "CuBu",
                fontSize = 24.sp,
                fontWeight = FontWeight.Bold,
                color = Color.Black
            )
        }

        Spacer(modifier = Modifier.height(16.dp))

        val featured = viewModel.featuredRecipe.value

        if (featured != null) {
            Card(
                modifier = Modifier
                    .fillMaxWidth()
                    .height(220.dp)
                    .clickable { navController.navigate("detail/${featured.id}") },
                shape = RoundedCornerShape(20.dp),
                elevation = CardDefaults.cardElevation(4.dp)
            ) {
                Box(modifier = Modifier.fillMaxSize()) {
                    AsyncImage(
                        model = featured.thumbnail_url,
                        contentDescription = null,
                        contentScale = ContentScale.Crop,
                        modifier = Modifier.fillMaxSize()
                    )

                    Box(
                        modifier = Modifier
                            .fillMaxSize()
                            .background(
                                brush = Brush.horizontalGradient(
                                    colors = listOf(
                                        Color.Black.copy(alpha = 0.8f),
                                        Color.Black.copy(alpha = 0.4f),
                                        Color.Transparent
                                    ),
                                    startX = 0f,
                                    endX = Float.POSITIVE_INFINITY
                                )
                            )
                    )

                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .padding(16.dp),
                        verticalArrangement = Arrangement.Center
                    ) {
                        Row(
                            modifier = Modifier
                                .fillMaxWidth()
                                .horizontalScroll(rememberScrollState())
                                .padding(bottom = 8.dp),
                            horizontalArrangement = Arrangement.spacedBy(6.dp)
                        ) {
                            featured.ingredients.orEmpty().take(3).forEach { ingredient ->
                                IngredientTag(
                                    name = ingredient.name,
                                    darkBackground = true
                                )
                            }
                        }

                        Text(
                            text = featured.title.replaceFirstChar { it.uppercase() },
                            color = Color.White,
                            fontSize = 21.sp,
                            fontWeight = FontWeight.ExtraBold,
                            maxLines = 2,
                            lineHeight = 24.sp
                        )

                        Text(
                            text = featured.description,
                            color = Color.White.copy(alpha = 0.8f),
                            fontSize = 12.sp,
                            maxLines = 2,
                            lineHeight = 16.sp,
                            modifier = Modifier.padding(bottom = 12.dp)
                        )
                    }
                }
            }
        }

        Spacer(modifier = Modifier.height(24.dp))

        OutlinedTextField(
            value = viewModel.searchQuery.value,
            onValueChange = viewModel::performSearch,
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
                    selected = viewModel.searchQuery.value.equals(item, ignoreCase = true),
                    onClick = {
                        viewModel.performSearch(
                            if (viewModel.searchQuery.value.equals(item, ignoreCase = true)) "" else item
                        )
                    },
                    label = { Text(item) },
                    colors = FilterChipDefaults.filterChipColors(
                        selectedContainerColor = Color.White,
                        selectedLabelColor = Color.Black
                    ),
                    border = FilterChipDefaults.filterChipBorder(
                        borderColor = if (viewModel.searchQuery.value.equals(item, ignoreCase = true)) Color.Gray else Color(0xFFE2E8F0)
                    )
                )
            }
        }

        Spacer(modifier = Modifier.height(24.dp))

        Text(
            text = if (viewModel.recipeSort.value == "popular") {
                "Resep terpopuler"
            } else {
                "Resep terbaru"
            },
            fontSize = 18.sp,
            fontWeight = FontWeight.Bold
        )
        Text(
            text = if (viewModel.recipeSort.value == "popular") {
                "Paling banyak disimpan pengguna CuBu"
            } else {
                "Resep yang baru dipublikasikan"
            },
            fontSize = 12.sp,
            color = Color.Gray
        )
        Spacer(modifier = Modifier.height(12.dp))
        Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
            listOf("latest" to "Terbaru", "popular" to "Terpopuler").forEach { (value, label) ->
                FilterChip(
                    selected = viewModel.recipeSort.value == value,
                    onClick = { viewModel.setRecipeSort(value) },
                    label = { Text(label) },
                    colors = FilterChipDefaults.filterChipColors(
                        selectedContainerColor = Color(0xFFFFEDD5),
                        selectedLabelColor = Color(0xFFEA580C)
                    )
                )
            }
        }
        Spacer(modifier = Modifier.height(16.dp))

        val chunkedRecipes = recipeList.chunked(2)

        if (viewModel.isLoading.value && recipeList.isEmpty()) {
            Box(
                modifier = Modifier.fillMaxWidth().padding(vertical = 48.dp),
                contentAlignment = Alignment.Center
            ) {
                CircularProgressIndicator(color = Color(0xFFF97316))
            }
        } else if (viewModel.errorMessage.value != null && recipeList.isEmpty()) {
            Column(
                modifier = Modifier.fillMaxWidth().padding(vertical = 32.dp),
                horizontalAlignment = Alignment.CenterHorizontally
            ) {
                Text(
                    viewModel.errorMessage.value ?: "Resep tidak dapat dimuat.",
                    color = Color.Gray,
                    fontSize = 13.sp
                )
                TextButton(onClick = { viewModel.fetchRecipes(viewModel.searchQuery.value) }) {
                    Text("Coba lagi", color = Color(0xFFF97316))
                }
            }
        } else if (recipeList.isEmpty()) {
            Text(
                "Tidak ada resep yang cocok.",
                color = Color.Gray,
                fontSize = 13.sp,
                modifier = Modifier.padding(vertical = 28.dp)
            )
        }

        Column(verticalArrangement = Arrangement.spacedBy(16.dp)) {
            chunkedRecipes.forEach { rowRecipes ->
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(16.dp)
                ) {
                    RecipeCard(
                        recipe = rowRecipes[0],
                        navController = navController,
                        modifier = Modifier.weight(1f),
                        viewModel = viewModel
                    )

                    if (rowRecipes.size > 1) {
                        RecipeCard(
                            recipe = rowRecipes[1],
                            navController = navController,
                            modifier = Modifier.weight(1f),
                            viewModel = viewModel
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

@Composable
fun RecipeCard(
    recipe: Recipe,
    navController: NavHostController,
    modifier: Modifier = Modifier,
    viewModel: RecipeViewModel? = null
) {
    Card(
        modifier = modifier
            .clickable { navController.navigate("detail/${recipe.id}") },
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        elevation = CardDefaults.cardElevation(2.dp)
    ) {
        Column {
            Box {
                AsyncImage(
                    model = recipe.thumbnail_url,
                    contentDescription = "Thumbnail ${recipe.title}",
                    contentScale = ContentScale.Crop,
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(130.dp)
                        .background(Color.LightGray)
                )

                if (recipe.has_video) {
                    Surface(
                        shape = RoundedCornerShape(50),
                        color = Color.Black.copy(alpha = 0.78f),
                        border = androidx.compose.foundation.BorderStroke(
                            1.dp,
                            Color.Black.copy(alpha = 0.2f)
                        ),
                        modifier = Modifier
                            .align(Alignment.TopEnd)
                            .padding(8.dp)
                    ) {
                        Text(
                            text = "Video",
                            color = Color.White,
                            fontSize = 10.sp,
                            fontWeight = FontWeight.Bold,
                            modifier = Modifier.padding(horizontal = 9.dp, vertical = 4.dp)
                        )
                    }
                }

                if (viewModel != null) {
                    Surface(
                        shape = RoundedCornerShape(50),
                        color = if (recipe.is_saved) Color(0xFFF97316) else Color.Black.copy(alpha = 0.72f),
                        onClick = { viewModel.toggleSaved(recipe) },
                        modifier = Modifier
                            .align(Alignment.TopStart)
                            .padding(8.dp)
                            .size(34.dp)
                    ) {
                        Box(contentAlignment = Alignment.Center) {
                            Icon(
                                imageVector = if (recipe.is_saved) Icons.Filled.Bookmark else Icons.Outlined.BookmarkBorder,
                                contentDescription = if (recipe.is_saved) "Hapus dari koleksi" else "Simpan ke koleksi",
                                tint = Color.White,
                                modifier = Modifier.size(19.dp)
                            )
                        }
                    }
                }
            }

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
                    text = recipe.creator?.name ?: "Chef CuBu",
                    color = Color.Gray,
                    fontSize = 10.sp
                )

                Spacer(modifier = Modifier.height(8.dp))

                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Icon(
                            Icons.Default.Star,
                            contentDescription = "Rating",
                            tint = Color(0xFFFBBF24),
                            modifier = Modifier.size(14.dp)
                        )
                        Spacer(modifier = Modifier.width(4.dp))
                        Text(
                            text = recipe.average_rating?.toString() ?: "-",
                            fontSize = 12.sp,
                            fontWeight = FontWeight.Bold
                        )
                    }

                    Surface(
                        shape = RoundedCornerShape(50),
                        color = Color.White,
                        border = androidx.compose.foundation.BorderStroke(
                            1.dp,
                            Color(0xFF78716C)
                        )
                    ) {
                        Text(
                            text = recipe.difficulty.replaceFirstChar { it.uppercase() },
                            color = Color(0xFF44403C),
                            fontSize = 10.sp,
                            fontWeight = FontWeight.Bold,
                            modifier = Modifier.padding(horizontal = 9.dp, vertical = 3.dp)
                        )
                    }
                }
            }
        }
    }
}

@Composable
private fun IngredientTag(
    name: String,
    darkBackground: Boolean = false
) {
    Surface(
        shape = RoundedCornerShape(50),
        color = if (darkBackground) {
            Color.Black.copy(alpha = 0.45f)
        } else {
            Color.White
        },
        border = androidx.compose.foundation.BorderStroke(
            1.dp,
            if (darkBackground) Color.White else Color(0xFF78716C)
        )
    ) {
        Text(
            text = name.replaceFirstChar { it.uppercase() },
            color = if (darkBackground) Color.White else Color(0xFF44403C),
            fontSize = 10.sp,
            fontWeight = FontWeight.Bold,
            maxLines = 1,
            modifier = Modifier.padding(horizontal = 10.dp, vertical = 4.dp)
        )
    }
}

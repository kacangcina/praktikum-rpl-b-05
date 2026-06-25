package com.example.cookbookfinalproject

import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.outlined.*
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.navigation.NavHostController
import androidx.navigation.NavType
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.currentBackStackEntryAsState
import androidx.navigation.compose.rememberNavController
import androidx.navigation.navArgument

@Composable
fun MainScreen() {
    val navController = rememberNavController()

    val navBackStackEntry by navController.currentBackStackEntryAsState()
    val currentRoute = navBackStackEntry?.destination?.route

    Scaffold(
        bottomBar = {
            if (currentRoute != "detail/{recipeId}") {
                FlatBottomBar(navController)
            }
        }
    ) { innerPadding ->
        NavHost(
            navController = navController,
            startDestination = "home",
            modifier = Modifier.padding(innerPadding)
        ) {
            composable("home") { HomeScreen(navController = navController) }
            // Menambahkan Rute Baru: Cari
            composable("search") { SearchScreen(navController = navController) }
            composable("create") {
                CreateRecipeScreen(navController = navController)
            }
            composable("collection") { CollectionScreen(navController = navController) }
            composable("profile") { Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) { Text("Halaman Profil") } }

            composable(
                route = "detail/{recipeId}",
                arguments = listOf(navArgument("recipeId") { type = NavType.IntType })
            ) { backStackEntry ->
                val id = backStackEntry.arguments?.getInt("recipeId") ?: 0
                DetailScreen(navController = navController, recipeId = id)
            }
        }
    }
}

// Desain Bottom Bar Baru (Sejajar & Rata)
@Composable
fun FlatBottomBar(navController: NavHostController) {
    val navBackStackEntry by navController.currentBackStackEntryAsState()
    val currentRoute = navBackStackEntry?.destination?.route

    Surface(
        modifier = Modifier.fillMaxWidth().height(70.dp),
        color = Color.White,
        shadowElevation = 8.dp
    ) {
        Row(
            modifier = Modifier.fillMaxSize(),
            horizontalArrangement = Arrangement.SpaceEvenly,
            verticalAlignment = Alignment.CenterVertically
        ) {
            BottomBarItem(
                icon = Icons.Outlined.Home,
                label = "Beranda",
                isSelected = currentRoute == "home",
                onClick = { navController.navigate("home") { popUpTo("home") { inclusive = true } } }
            )
            BottomBarItem(
                icon = Icons.Outlined.Search,
                label = "Cari",
                isSelected = currentRoute == "search",
                onClick = { navController.navigate("search") }
            )
            BottomBarItem(
                icon = Icons.Default.Add, // Ikon + biasa
                label = "Buat",
                isSelected = currentRoute == "create",
                onClick = { navController.navigate("create") }
            )
            BottomBarItem(
                icon = Icons.Outlined.BookmarkBorder,
                label = "Koleksi",
                isSelected = currentRoute == "collection",
                onClick = { navController.navigate("collection") }
            )
            BottomBarItem(
                icon = Icons.Outlined.Person,
                label = "Profil",
                isSelected = currentRoute == "profile",
                onClick = { navController.navigate("profile") }
            )
        }
    }
}

@Composable
fun BottomBarItem(icon: ImageVector, label: String, isSelected: Boolean, onClick: () -> Unit) {
    val color = if (isSelected) Color(0xFFF97316) else Color.DarkGray
    Column(
        horizontalAlignment = Alignment.CenterHorizontally,
        modifier = Modifier
            .clickable(onClick = onClick)
            .padding(horizontal = 8.dp, vertical = 8.dp)
    ) {
        Icon(icon, contentDescription = label, tint = color, modifier = Modifier.size(28.dp))
        Spacer(modifier = Modifier.height(2.dp))
        Text(label, color = color, fontSize = 10.sp, fontWeight = if (isSelected) FontWeight.Bold else FontWeight.Normal)
    }
}
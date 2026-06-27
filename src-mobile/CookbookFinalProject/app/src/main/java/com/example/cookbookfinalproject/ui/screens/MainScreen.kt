package com.example.cookbookfinalproject.ui.screens

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
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavHostController
import androidx.navigation.NavType
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.currentBackStackEntryAsState
import androidx.navigation.compose.rememberNavController
import androidx.navigation.navArgument
import com.example.cookbookfinalproject.ui.viewmodel.RecipeViewModel

@Composable
fun MainScreen(viewModel: RecipeViewModel = viewModel()) {
    val navController = rememberNavController()

    val navBackStackEntry by navController.currentBackStackEntryAsState()
    val currentRoute = navBackStackEntry?.destination?.route

    val hideBottomBarRoutes = listOf(
        "login",
        "register",
        "detail/{recipeId}",
        "edit/{recipeId}",
        "edit_profile",
        "verify_creator",
        "notifications",
        "splash"
    )
    val shouldShowBottomBar = currentRoute !in hideBottomBarRoutes

    Scaffold(
        bottomBar = {
            if (shouldShowBottomBar) {
                FlatBottomBar(navController, viewModel)
            }
        }
    ) { innerPadding ->
        NavHost(
            navController = navController,
            startDestination = "splash",
            modifier = Modifier.padding(innerPadding)
        ) {
            composable("splash") { SplashScreen(navController = navController) }
            composable("login") { LoginScreen(navController = navController, viewModel = viewModel) }
            composable("register") { RegisterScreen(navController = navController, viewModel = viewModel) }
            composable("home") { HomeScreen(navController = navController, viewModel = viewModel) }
            composable("search") { SearchScreen(navController = navController) }
            composable("create") { CreateRecipeScreen(navController = navController, viewModel = viewModel) }
            composable("collection") { CollectionScreen(navController = navController, viewModel = viewModel) }
            composable("profile") { ProfileScreen(navController = navController, viewModel = viewModel) }
            composable("edit_profile") { EditProfileScreen(navController = navController, viewModel = viewModel) }
            composable("verify_creator") { VerifyCreatorScreen(navController = navController, viewModel = viewModel) }
            composable("notifications") {
                NotificationScreen(navController = navController, viewModel = viewModel)
            }

            composable(
                route = "detail/{recipeId}",
                arguments = listOf(navArgument("recipeId") { type = NavType.IntType })
            ) { backStackEntry ->
                val id = backStackEntry.arguments?.getInt("recipeId") ?: 0
                DetailScreen(navController = navController, recipeId = id, viewModel = viewModel)
            }
            composable(
                route = "edit/{recipeId}",
                arguments = listOf(navArgument("recipeId") { type = NavType.IntType })
            ) { backStackEntry ->
                val id = backStackEntry.arguments?.getInt("recipeId") ?: 0
                CreateRecipeScreen(
                    navController = navController,
                    viewModel = viewModel,
                    recipeId = id
                )
            }
        }
    }
}

@Composable
fun FlatBottomBar(navController: NavHostController, viewModel: RecipeViewModel) {
    val navBackStackEntry by navController.currentBackStackEntryAsState()
    val currentRoute = navBackStackEntry?.destination?.route
    val isLoggedIn = viewModel.isLoggedIn.value

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
            BottomBarItem(icon = Icons.Outlined.Home, label = "Beranda", isSelected = currentRoute == "home", onClick = { navController.navigate("home") { popUpTo("home") { inclusive = true } } })
            BottomBarItem(icon = Icons.Outlined.SmartToy, label = "Tanya AI", isSelected = currentRoute == "search", onClick = {
                navController.navigate(if (isLoggedIn) "search" else "login")
            })
            BottomBarItem(icon = Icons.Default.Add, label = "Buat", isSelected = currentRoute == "create", onClick = {
                navController.navigate(if (isLoggedIn) "create" else "login")
            })
            BottomBarItem(icon = Icons.Outlined.BookmarkBorder, label = "Koleksi", isSelected = currentRoute == "collection", onClick = {
                navController.navigate(if (isLoggedIn) "collection" else "login")
            })

            BottomBarItem(
                icon = Icons.Outlined.Person,
                label = "Profil",
                isSelected = currentRoute == "profile",
                onClick = {
                    if (isLoggedIn) {
                        navController.navigate("profile")
                    } else {
                        navController.navigate("login")
                    }
                }
            )
        }
    }
}

@Composable
fun BottomBarItem(icon: ImageVector, label: String, isSelected: Boolean, onClick: () -> Unit) {
    val color = if (isSelected) Color(0xFFF97316) else Color.DarkGray
    Column(
        horizontalAlignment = Alignment.CenterHorizontally,
        modifier = Modifier.clickable(onClick = onClick).padding(horizontal = 8.dp, vertical = 8.dp)
    ) {
        Icon(icon, contentDescription = label, tint = color, modifier = Modifier.size(28.dp))
        Spacer(modifier = Modifier.height(2.dp))
        Text(label, color = color, fontSize = 10.sp, fontWeight = if (isSelected) FontWeight.Bold else FontWeight.Normal)
    }
}

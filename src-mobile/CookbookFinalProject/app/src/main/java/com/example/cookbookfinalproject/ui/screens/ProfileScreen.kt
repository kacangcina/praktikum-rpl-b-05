package com.example.cookbookfinalproject.ui.screens

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.PaddingValues
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.offset
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.ExitToApp
import androidx.compose.material.icons.filled.Person
import androidx.compose.material.icons.outlined.Edit
import androidx.compose.material.icons.outlined.Notifications
import androidx.compose.material.icons.outlined.Restaurant
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.Divider
import androidx.compose.material3.Icon
import androidx.compose.material3.Surface
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.navigation.NavHostController
import coil.compose.AsyncImage
import com.example.cookbookfinalproject.R
import com.example.cookbookfinalproject.data.model.MobileUser
import com.example.cookbookfinalproject.ui.viewmodel.RecipeViewModel

private val ProfileOrange = Color(0xFFF97316)

@Composable
fun ProfileScreen(navController: NavHostController, viewModel: RecipeViewModel) {
    LaunchedEffect(viewModel.currentUser.value?.id) {
        viewModel.fetchMyProfile()
    }

    val data = viewModel.profile.value
    if (data == null) {
        Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            CircularProgressIndicator(color = ProfileOrange)
        }
        return
    }

    val profile = data.profile

    LazyColumn(
        modifier = Modifier
            .fillMaxSize()
            .background(Color.White),
        contentPadding = PaddingValues(bottom = 92.dp)
    ) {
        item {
            Column(modifier = Modifier.padding(horizontal = 18.dp)) {
                Spacer(Modifier.height(16.dp))
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Image(
                        painter = painterResource(R.drawable.logo),
                        contentDescription = "Logo CuBu",
                        modifier = Modifier
                            .size(50.dp)
                            .clip(RoundedCornerShape(12.dp))
                    )
                    Spacer(Modifier.width(10.dp))
                    Text("CuBu", fontSize = 24.sp, fontWeight = FontWeight.Bold)
                    Spacer(Modifier.weight(1f))
                    Box {
                        Surface(
                            modifier = Modifier.size(42.dp),
                            shape = CircleShape,
                            color = Color(0xFFF5F5F4),
                            onClick = { navController.navigate("notifications") }
                        ) {
                            Box(contentAlignment = Alignment.Center) {
                                Icon(
                                    Icons.Outlined.Notifications,
                                    contentDescription = "Notifikasi",
                                    modifier = Modifier.size(22.dp)
                                )
                            }
                        }
                        if (data.unread_notifications_count > 0) {
                            Surface(
                                modifier = Modifier
                                    .align(Alignment.TopEnd)
                                    .offset(x = 5.dp, y = (-5).dp),
                                shape = CircleShape,
                                color = ProfileOrange
                            ) {
                                Text(
                                    text = data.unread_notifications_count.coerceAtMost(99).toString(),
                                    color = Color.White,
                                    fontSize = 9.sp,
                                    fontWeight = FontWeight.Bold,
                                    modifier = Modifier.padding(horizontal = 5.dp, vertical = 2.dp)
                                )
                            }
                        }
                    }
                }

                Spacer(Modifier.height(26.dp))
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Text("Profil", fontSize = 25.sp, fontWeight = FontWeight.ExtraBold)
                    Surface(
                        color = Color(0xFFE7E5E4),
                        shape = RoundedCornerShape(18.dp),
                        onClick = { navController.navigate("edit_profile") }
                    ) {
                        Row(
                            modifier = Modifier.padding(horizontal = 12.dp, vertical = 8.dp),
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Icon(
                                Icons.Outlined.Edit,
                                contentDescription = null,
                                modifier = Modifier.size(15.dp)
                            )
                            Spacer(Modifier.width(5.dp))
                            Text("Edit profil", fontSize = 12.sp, fontWeight = FontWeight.SemiBold)
                        }
                    }
                }

                Spacer(Modifier.height(14.dp))
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Column(modifier = Modifier.weight(1f)) {
                        Text(profile.name, fontSize = 26.sp, fontWeight = FontWeight.ExtraBold)
                        Text(
                            "@${profile.username}",
                            color = Color(0xFF78716C),
                            fontSize = 14.sp
                        )

                        Spacer(Modifier.height(18.dp))
                        Text(
                            data.recipes.size.toString(),
                            fontSize = 19.sp,
                            fontWeight = FontWeight.ExtraBold
                        )
                        Text("Resep", fontSize = 13.sp, color = Color(0xFF44403C))

                        Spacer(Modifier.height(18.dp))
                        Text(
                            profile.bio?.takeIf { it.isNotBlank() } ?: "Belum ada biodata.",
                            fontSize = 15.sp,
                            lineHeight = 21.sp,
                            color = Color(0xFF292524)
                        )
                    }

                    Spacer(Modifier.width(18.dp))
                    ProfileAvatar(
                        profile = profile,
                        onVerifyClick = { navController.navigate("verify_creator") }
                    )
                }

                Spacer(Modifier.height(16.dp))
                TextButton(
                    onClick = {
                        viewModel.logout {
                            navController.navigate("home") {
                                popUpTo("home") { inclusive = true }
                            }
                        }
                    },
                    contentPadding = PaddingValues(horizontal = 0.dp)
                ) {
                    Icon(
                        Icons.Default.ExitToApp,
                        contentDescription = null,
                        tint = Color(0xFFDC2626),
                        modifier = Modifier.size(17.dp)
                    )
                    Spacer(Modifier.width(6.dp))
                    Text("Keluar dari akun", color = Color(0xFFDC2626), fontSize = 12.sp)
                }

                Spacer(Modifier.height(10.dp))
                Divider(color = Color(0xFFE7E5E4))
                Spacer(Modifier.height(18.dp))
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Icon(
                        Icons.Outlined.Restaurant,
                        contentDescription = null,
                        tint = ProfileOrange,
                        modifier = Modifier.size(21.dp)
                    )
                    Spacer(Modifier.width(8.dp))
                    Text(
                        "Resep ${profile.username}",
                        fontSize = 20.sp,
                        fontWeight = FontWeight.ExtraBold
                    )
                }
                Spacer(Modifier.height(10.dp))
            }
        }

        if (data.recipes.isEmpty()) {
            item {
                Surface(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 18.dp, vertical = 16.dp),
                    color = Color(0xFFF5F5F4),
                    shape = RoundedCornerShape(16.dp)
                ) {
                    Text(
                        "Belum ada resep yang dipublikasikan.",
                        color = Color(0xFF78716C),
                        modifier = Modifier.padding(22.dp)
                    )
                }
            }
        } else {
            items(data.recipes.chunked(2)) { rowRecipes ->
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 18.dp, vertical = 8.dp),
                    horizontalArrangement = Arrangement.spacedBy(14.dp)
                ) {
                    rowRecipes.forEach { recipe ->
                        RecipeCard(recipe, navController, Modifier.weight(1f))
                    }
                    if (rowRecipes.size == 1) Spacer(Modifier.weight(1f))
                }
            }
        }
    }
}

@Composable
private fun ProfileAvatar(profile: MobileUser, onVerifyClick: () -> Unit) {
    Box(
        modifier = Modifier.size(116.dp),
        contentAlignment = Alignment.Center
    ) {
        Surface(
            modifier = Modifier.size(104.dp),
            shape = CircleShape,
            color = Color(0xFFE7E5E4)
        ) {
            if (profile.avatar_url != null) {
                AsyncImage(
                    model = profile.avatar_url,
                    contentDescription = "Foto profil",
                    contentScale = ContentScale.Crop
                )
            } else {
                Icon(
                    Icons.Default.Person,
                    contentDescription = null,
                    tint = Color(0xFF292524),
                    modifier = Modifier.padding(25.dp)
                )
            }
        }

        val isCreator = profile.role == "creator"

        Surface(
            modifier = Modifier
                .size(38.dp)
                .align(Alignment.BottomEnd)
                .offset(x = (-1).dp, y = (-1).dp),
            shape = CircleShape,
            color = ProfileOrange,
            border = androidx.compose.foundation.BorderStroke(3.dp, Color.White),
            onClick = { if (!isCreator) onVerifyClick() }
        ) {
            Box(contentAlignment = Alignment.Center) {
                if (isCreator) {
                    Image(
                        painter = painterResource(R.drawable.logo),
                        contentDescription = "Ikon chef",
                        modifier = Modifier
                            .size(29.dp)
                            .clip(CircleShape)
                    )
                } else {
                    Icon(
                        Icons.Default.Add,
                        contentDescription = "Verifikasi Creator",
                        tint = Color.White,
                        modifier = Modifier.size(24.dp)
                    )
                }
            }
        }
    }
}

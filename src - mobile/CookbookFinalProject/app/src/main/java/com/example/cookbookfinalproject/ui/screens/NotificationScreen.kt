package com.example.cookbookfinalproject.ui.screens

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.outlined.Info
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
import androidx.navigation.NavHostController
import com.example.cookbookfinalproject.R
import com.example.cookbookfinalproject.data.model.AppNotification
import com.example.cookbookfinalproject.ui.viewmodel.RecipeViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun NotificationScreen(
    navController: NavHostController,
    viewModel: RecipeViewModel
) {
    LaunchedEffect(Unit) { viewModel.fetchMyProfile() }
    val data = viewModel.profile.value

    Scaffold(
        topBar = {
            TopAppBar(
                title = {
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Image(
                            painter = painterResource(id = R.drawable.logo),
                            contentDescription = "Logo",
                            modifier = Modifier
                                .size(50.dp)
                                .clip(RoundedCornerShape(12.dp))
                        )
                        Spacer(modifier = Modifier.width(10.dp))
                        Text(
                            text = "CuBu",
                            fontSize = 24.sp,
                            fontWeight = FontWeight.Bold
                        )
                    }
                },
                navigationIcon = {
                    IconButton(onClick = { navController.popBackStack() }) {
                        Icon(Icons.Default.ArrowBack, contentDescription = "Kembali")
                    }
                },
                actions = {
                    if ((data?.unread_notifications_count ?: 0) > 0) {
                        TextButton(onClick = viewModel::markAllNotificationsRead) {
                            Text("Tandai dibaca", color = Color(0xFFF97316), fontSize = 12.sp)
                        }
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = Color.White)
            )
        }
    ) { padding ->
        when {
            data == null -> Box(
                modifier = Modifier.fillMaxSize().padding(padding),
                contentAlignment = Alignment.Center
            ) {
                CircularProgressIndicator(color = Color(0xFFF97316))
            }

            data.notifications.isEmpty() -> Box(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(padding)
                    .background(Color(0xFFF8FAFC)),
                contentAlignment = Alignment.Center
            ) {
                Text("Belum ada notifikasi.", color = Color.Gray)
            }

            else -> LazyColumn(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(padding)
                    .background(Color(0xFFF8FAFC)),
                contentPadding = PaddingValues(16.dp),
                verticalArrangement = Arrangement.spacedBy(10.dp)
            ) {
                items(data.notifications, key = { it.id }) { notification ->
                    NotificationCard(notification)
                }
            }
        }
    }
}

@Composable
private fun NotificationCard(notification: AppNotification) {
    val colors = when (notification.level) {
        "success" -> Color(0xFFECFDF5) to Color(0xFF047857)
        "warning" -> Color(0xFFFFFBEB) to Color(0xFF92400E)
        "danger" -> Color(0xFFFEF2F2) to Color(0xFFB91C1C)
        else -> Color(0xFFEFF6FF) to Color(0xFF1D4ED8)
    }

    Surface(
        shape = RoundedCornerShape(14.dp),
        color = colors.first,
        border = androidx.compose.foundation.BorderStroke(
            1.dp,
            colors.second.copy(alpha = 0.2f)
        ),
        shadowElevation = if (notification.read_at == null) 2.dp else 0.dp
    ) {
        Row(
            modifier = Modifier.fillMaxWidth().padding(14.dp),
            verticalAlignment = Alignment.Top
        ) {
            Icon(
                Icons.Outlined.Info,
                contentDescription = null,
                tint = colors.second,
                modifier = Modifier.size(22.dp)
            )
            Spacer(Modifier.width(10.dp))
            Column(modifier = Modifier.weight(1f)) {
                Text(
                    notification.title,
                    fontWeight = FontWeight.ExtraBold,
                    color = colors.second
                )
                Text(
                    notification.message,
                    fontSize = 13.sp,
                    lineHeight = 18.sp,
                    color = colors.second,
                    modifier = Modifier.padding(top = 4.dp)
                )
                notification.reason?.takeIf { it.isNotBlank() }?.let {
                    Text(
                        "Alasan admin: $it",
                        fontSize = 12.sp,
                        fontWeight = FontWeight.SemiBold,
                        color = colors.second,
                        modifier = Modifier.padding(top = 8.dp)
                    )
                }
                Text(
                    notification.created_at.replace("T", " ").take(16),
                    fontSize = 10.sp,
                    color = colors.second.copy(alpha = 0.65f),
                    modifier = Modifier.padding(top = 8.dp)
                )
            }
            if (notification.read_at == null) {
                Box(
                    modifier = Modifier
                        .padding(start = 8.dp, top = 3.dp)
                        .size(9.dp)
                        .background(Color(0xFFF97316), CircleShape)
                )
            }
        }
    }
}

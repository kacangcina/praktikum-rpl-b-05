package com.example.cookbookfinalproject

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.navigation.NavHostController

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun LoginScreen(navController: NavHostController, viewModel: RecipeViewModel) {
    var email by remember { mutableStateOf("") }
    var password by remember { mutableStateOf("") }

    Column(
        modifier = Modifier.fillMaxSize().background(Color.White).padding(24.dp),
        horizontalAlignment = Alignment.CenterHorizontally,
        verticalArrangement = Arrangement.Center
    ) {
        // Menggunakan Logo Asli CuBu
        Image(
            painter = painterResource(id = R.drawable.logo),
            contentDescription = "Logo CuBu",
            modifier = Modifier
                .size(80.dp)
                .clip(RoundedCornerShape(24.dp))
        )
        Spacer(modifier = Modifier.height(32.dp))

        Text("Selamat Datang!", fontSize = 24.sp, fontWeight = FontWeight.ExtraBold, color = Color(0xFF1E293B))
        Text("Silakan masuk ke akun Cubu kamu.", color = Color.Gray, fontSize = 14.sp)
        Spacer(modifier = Modifier.height(32.dp))

        OutlinedTextField(
            value = email, onValueChange = { email = it }, label = { Text("Email") },
            modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(12.dp)
        )
        Spacer(modifier = Modifier.height(16.dp))

        OutlinedTextField(
            value = password, onValueChange = { password = it }, label = { Text("Kata Sandi") },
            visualTransformation = PasswordVisualTransformation(),
            modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(12.dp)
        )

        TextButton(onClick = { }, modifier = Modifier.align(Alignment.End)) {
            Text("Lupa sandi?", color = Color(0xFFF97316), fontWeight = FontWeight.Bold)
        }
        Spacer(modifier = Modifier.height(16.dp))

        // AKSI MASUK
        Button(
            onClick = {
                viewModel.isLoggedIn.value = true // Nyalakan saklar login
                navController.navigate("home") { popUpTo("login") { inclusive = true } }
            },
            modifier = Modifier.fillMaxWidth().height(56.dp),
            shape = RoundedCornerShape(16.dp), colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFF97316))
        ) {
            Text("Masuk ke CuBu", color = Color.White, fontSize = 16.sp, fontWeight = FontWeight.Bold)
        }

        Spacer(modifier = Modifier.height(24.dp))
        Text("atau", color = Color.Gray)
        Spacer(modifier = Modifier.height(24.dp))

        // AKSI TAMU
        OutlinedButton(
            onClick = {
                viewModel.isLoggedIn.value = false // Matikan saklar login
                navController.navigate("home") { popUpTo("login") { inclusive = true } }
            },
            modifier = Modifier.fillMaxWidth().height(56.dp), shape = RoundedCornerShape(16.dp)
        ) {
            Text("Masuk sebagai tamu", color = Color.Black, fontWeight = FontWeight.Bold)
        }

        Spacer(modifier = Modifier.height(24.dp))
        Row(verticalAlignment = Alignment.CenterVertically) {
            Text("Belum punya akun?", color = Color.Gray)
            TextButton(onClick = { navController.navigate("register") }) {
                Text("Daftar sekarang", color = Color(0xFFF97316), fontWeight = FontWeight.Bold)
            }
        }
    }
}
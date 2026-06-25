package com.example.cookbookfinalproject.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.input.VisualTransformation
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Visibility
import androidx.compose.material.icons.filled.VisibilityOff
import androidx.compose.material3.IconButton
import androidx.compose.material3.Icon
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.navigation.NavHostController
import com.example.cookbookfinalproject.ui.viewmodel.RecipeViewModel

@Composable
fun RegisterScreen(navController: NavHostController, viewModel: RecipeViewModel) {
    var username by remember { mutableStateOf("") }
    var email by remember { mutableStateOf("") }
    var password by remember { mutableStateOf("") }
    var confirmation by remember { mutableStateOf("") }
    var passwordVisible by remember { mutableStateOf(false) }
    var confirmationVisible by remember { mutableStateOf(false) }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color.White)
            .padding(24.dp),
        horizontalAlignment = Alignment.CenterHorizontally,
        verticalArrangement = Arrangement.Center
    ) {
        Text("Buat akun CuBu", fontSize = 26.sp, fontWeight = FontWeight.ExtraBold)
        Text("Simpan resep, konsultasi AI, dan bagikan masakanmu.", color = Color.Gray)
        Spacer(Modifier.height(28.dp))

        OutlinedTextField(username, { username = it }, label = { Text("Username") }, modifier = Modifier.fillMaxWidth())
        Spacer(Modifier.height(12.dp))
        OutlinedTextField(email, { email = it }, label = { Text("Email") }, modifier = Modifier.fillMaxWidth())
        Spacer(Modifier.height(12.dp))
        OutlinedTextField(
            value = password,
            onValueChange = { password = it },
            label = { Text("Kata sandi") },
            visualTransformation = if (passwordVisible) VisualTransformation.None else PasswordVisualTransformation(),
            trailingIcon = {
                val image = if (passwordVisible) Icons.Filled.Visibility else Icons.Filled.VisibilityOff
                val description = if (passwordVisible) "Sembunyikan sandi" else "Lihat sandi"

                IconButton(onClick = { passwordVisible = !passwordVisible }) {
                    Icon(imageVector = image, contentDescription = description)
                }
            },
            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Password),
            modifier = Modifier.fillMaxWidth()
        )
        Spacer(Modifier.height(12.dp))
        OutlinedTextField(
            value = confirmation,
            onValueChange = { confirmation = it },
            label = { Text("Konfirmasi kata sandi") },
            visualTransformation = if (confirmationVisible) VisualTransformation.None else PasswordVisualTransformation(),
            trailingIcon = {
                val image = if (confirmationVisible) Icons.Filled.Visibility else Icons.Filled.VisibilityOff
                val description = if (confirmationVisible) "Sembunyikan sandi" else "Lihat sandi"

                IconButton(onClick = { confirmationVisible = !confirmationVisible }) {
                    Icon(imageVector = image, contentDescription = description)
                }
            },
            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Password),
            modifier = Modifier.fillMaxWidth()
        )

        viewModel.registerError.value?.let {
            Text(
                it,
                color = MaterialTheme.colorScheme.error,
                fontSize = 12.sp,
                modifier = Modifier.fillMaxWidth().padding(top = 8.dp)
            )
        }

        Spacer(Modifier.height(20.dp))
        Button(
            onClick = {
                viewModel.register(username, email, password, confirmation) {
                    navController.navigate("home") {
                        popUpTo("register") { inclusive = true }
                    }
                }
            },
            enabled = !viewModel.registerLoading.value,
            modifier = Modifier.fillMaxWidth().height(54.dp),
            shape = RoundedCornerShape(16.dp),
            colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFF97316))
        ) {
            if (viewModel.registerLoading.value) {
                CircularProgressIndicator(
                    modifier = Modifier.height(22.dp),
                    color = Color.White,
                    strokeWidth = 2.dp
                )
            } else {
                Text("Daftar", fontWeight = FontWeight.Bold)
            }
        }

        TextButton(onClick = { navController.popBackStack() }) {
            Text("Sudah punya akun? Masuk", color = Color(0xFFF97316))
        }
    }
}

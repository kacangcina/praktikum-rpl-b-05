package com.example.cookbookfinalproject.ui.screens

import android.net.Uri
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.KeyboardArrowDown
import androidx.compose.material.icons.outlined.Badge
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.navigation.NavHostController
import com.example.cookbookfinalproject.R
import com.example.cookbookfinalproject.ui.viewmodel.RecipeViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun VerifyCreatorScreen(navController: NavHostController, viewModel: RecipeViewModel) {
    var fullName by remember { mutableStateOf("") }
    var expertise by remember { mutableStateOf("Pilih keahlian") }
    var bio by remember { mutableStateOf("") }
    var portfolioUrl by remember { mutableStateOf("") }
    var documentUri by remember { mutableStateOf<Uri?>(null) }
    var expandedExpertise by remember { mutableStateOf(false) }
    val context = LocalContext.current
    val documentPicker = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.GetContent(),
        onResult = { documentUri = it }
    )

    val expertiseOptions = listOf("Koki Profesional", "Home Cook", "Food Enthusiast", "Lainnya")

    Scaffold(
        topBar = {
            TopAppBar(
                title = {
                    Row(
                        verticalAlignment = Alignment.CenterVertically,
                        horizontalArrangement = Arrangement.SpaceBetween,
                        modifier = Modifier.fillMaxWidth().padding(end = 16.dp)
                    ) {
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
                        Surface(
                            color = Color(0xFFFCA5A5), // Light red/pink
                            shape = RoundedCornerShape(16.dp)
                        ) {
                            Text(
                                "Belum diajukan", 
                                modifier = Modifier.padding(horizontal = 12.dp, vertical = 4.dp),
                                fontSize = 12.sp,
                                color = Color(0xFF991B1B)
                            )
                        }
                    }
                },
                navigationIcon = {
                    IconButton(onClick = { navController.popBackStack() }) {
                        Icon(Icons.Default.ArrowBack, contentDescription = "Back")
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = Color.White)
            )
        }
    ) { padding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(padding)
                .background(Color.White)
                .verticalScroll(rememberScrollState())
                .padding(20.dp)
        ) {
            // Info Box
            Surface(
                modifier = Modifier.fillMaxWidth(),
                color = Color(0xFFFFEDD5), // Light orange
                shape = RoundedCornerShape(8.dp),
                border = androidx.compose.foundation.BorderStroke(1.dp, Color(0xFFFED7AA))
            ) {
                Text(
                    "Jadi creator untuk unggah video kelas memasak.",
                    modifier = Modifier.padding(12.dp),
                    color = Color(0xFF9A3412),
                    fontSize = 14.sp
                )
            }

            Spacer(Modifier.height(24.dp))

            // Persyaratan
            Text("Persyaratan", fontWeight = FontWeight.Bold, fontSize = 16.sp)
            Spacer(Modifier.height(12.dp))
            RequirementItem(number = "1", text = "Email terverifikasi")
            RequirementItem(number = "2", text = "Upload KTP")
            RequirementItem(number = "3", text = "Upload portofolio")

            Spacer(Modifier.height(24.dp))

            // Form Fields
            Text("Nama lengkap (sesuai KTP)", fontWeight = FontWeight.Bold, fontSize = 14.sp)
            OutlinedTextField(
                value = fullName,
                onValueChange = { fullName = it },
                modifier = Modifier.fillMaxWidth().padding(top = 8.dp),
                placeholder = { Text("Nama lengkap...", color = Color.Gray) },
                shape = RoundedCornerShape(12.dp)
            )

            Spacer(Modifier.height(16.dp))

            Text("Keahlian memasak", fontWeight = FontWeight.Bold, fontSize = 14.sp)
            ExposedDropdownMenuBox(
                expanded = expandedExpertise,
                onExpandedChange = { expandedExpertise = !expandedExpertise },
                modifier = Modifier.fillMaxWidth().padding(top = 8.dp)
            ) {
                OutlinedTextField(
                    value = expertise,
                    onValueChange = {},
                    readOnly = true,
                    modifier = Modifier.fillMaxWidth().menuAnchor(),
                    trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded = expandedExpertise) },
                    shape = RoundedCornerShape(12.dp),
                    colors = OutlinedTextFieldDefaults.colors()
                )
                ExposedDropdownMenu(
                    expanded = expandedExpertise,
                    onDismissRequest = { expandedExpertise = false }
                ) {
                    expertiseOptions.forEach { option ->
                        DropdownMenuItem(
                            text = { Text(option) },
                            onClick = {
                                expertise = option
                                expandedExpertise = false
                            }
                        )
                    }
                }
            }

            Spacer(Modifier.height(16.dp))

            Text("Deskripsi diri", fontWeight = FontWeight.Bold, fontSize = 14.sp)
            OutlinedTextField(
                value = bio,
                onValueChange = { bio = it },
                modifier = Modifier.fillMaxWidth().height(120.dp).padding(top = 8.dp),
                placeholder = { Text("Ceritakan pengalamanmu...", color = Color.Gray) },
                shape = RoundedCornerShape(12.dp)
            )

            Spacer(Modifier.height(24.dp))

            Text("URL portofolio (opsional)", fontWeight = FontWeight.Bold, fontSize = 14.sp)
            OutlinedTextField(
                value = portfolioUrl,
                onValueChange = { portfolioUrl = it },
                modifier = Modifier.fillMaxWidth().padding(top = 8.dp),
                placeholder = { Text("https://...", color = Color.Gray) },
                shape = RoundedCornerShape(12.dp),
                singleLine = true
            )

            Spacer(Modifier.height(20.dp))

            // Documents
            Text("Dokumen KTP", fontWeight = FontWeight.Bold, fontSize = 14.sp)
            UploadBox(
                icon = Icons.Outlined.Badge,
                label = if (documentUri == null) {
                    "Unggah KTP atau portofolio"
                } else {
                    "Dokumen sudah dipilih"
                },
                hint = "PDF, JPG, atau PNG - Maksimal 10 MB",
                onClick = { documentPicker.launch("*/*") }
            )

            Spacer(Modifier.height(32.dp))

            viewModel.createError.value?.let {
                Text(
                    text = it,
                    color = MaterialTheme.colorScheme.error,
                    fontSize = 12.sp,
                    modifier = Modifier.padding(bottom = 10.dp)
                )
            }

            Button(
                onClick = {
                    viewModel.submitVerification(
                        context = context,
                        fullName = fullName,
                        expertise = expertise,
                        bio = bio,
                        documentUri = documentUri,
                        portfolioUrl = portfolioUrl,
                        onSuccess = { navController.popBackStack() }
                    )
                },
                enabled = !viewModel.createLoading.value,
                modifier = Modifier.fillMaxWidth().height(56.dp),
                shape = RoundedCornerShape(12.dp),
                colors = ButtonDefaults.buttonColors(containerColor = Color.White),
                border = androidx.compose.foundation.BorderStroke(1.dp, Color.Black)
            ) {
                if (viewModel.createLoading.value) {
                    CircularProgressIndicator(
                        modifier = Modifier.size(22.dp),
                        strokeWidth = 2.dp
                    )
                } else {
                    Text(
                        "Kirim pengajuan",
                        color = Color.Black,
                        fontSize = 16.sp,
                        fontWeight = FontWeight.Medium
                    )
                }
            }
            
            Spacer(Modifier.height(40.dp))
        }
    }
}

@Composable
fun RequirementItem(number: String, text: String) {
    Row(
        modifier = Modifier.padding(vertical = 4.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        Surface(
            modifier = Modifier.size(28.dp),
            shape = RoundedCornerShape(8.dp),
            border = androidx.compose.foundation.BorderStroke(1.dp, Color.Black),
            color = Color.White
        ) {
            Box(contentAlignment = Alignment.Center) {
                Text(number, fontWeight = FontWeight.Bold, fontSize = 14.sp)
            }
        }
        Spacer(Modifier.width(12.dp))
        Text(text, fontSize = 14.sp)
    }
}

@Composable
fun UploadBox(icon: androidx.compose.ui.graphics.vector.ImageVector, label: String, hint: String, onClick: () -> Unit) {
    Column {
        Surface(
            modifier = Modifier
                .fillMaxWidth()
                .padding(top = 8.dp)
                .height(100.dp),
            shape = RoundedCornerShape(12.dp),
            border = androidx.compose.foundation.BorderStroke(1.dp, Color.Black),
            color = Color.White,
            onClick = onClick
        ) {
            Column(
                horizontalAlignment = Alignment.CenterHorizontally,
                verticalArrangement = Arrangement.Center
            ) {
                Icon(icon, contentDescription = null, modifier = Modifier.size(32.dp))
                Spacer(Modifier.height(8.dp))
                Text(label, fontSize = 14.sp, fontWeight = FontWeight.Medium)
            }
        }
        Text(
            hint,
            fontSize = 11.sp,
            color = Color.Gray,
            modifier = Modifier.padding(top = 4.dp, start = 4.dp)
        )
    }
}

package com.example.cookbookfinalproject

import android.net.Uri
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.PickVisualMediaRequest
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.Image // Import untuk menampilkan logo.png
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.Close
import androidx.compose.material.icons.filled.DeleteOutline
import androidx.compose.material.icons.outlined.Image
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.draw.drawBehind
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.PathEffect
import androidx.compose.ui.graphics.drawscope.Stroke
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource // Import untuk memuat resource gambar
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavHostController
import coil.compose.AsyncImage

@OptIn(ExperimentalMaterial3Api::class, ExperimentalLayoutApi::class)
@Composable
fun CreateRecipeScreen(
    navController: NavHostController,
    viewModel: RecipeViewModel = viewModel()
) {
    var title by remember { mutableStateOf("") }
    var difficulty by remember { mutableStateOf("Pilih") }
    var expandedDifficulty by remember { mutableStateOf(false) }
    var time by remember { mutableStateOf("") }

    var alatInput by remember { mutableStateOf("") }
    val alatList = remember { mutableStateListOf<String>() }

    var bahanName by remember { mutableStateOf("") }
    var bahanQty by remember { mutableStateOf("") }
    val bahanList = remember { mutableStateListOf<String>() }

    val stepsList = remember { mutableStateListOf("") }

    var imageUri by remember { mutableStateOf<Uri?>(null) }

    val photoPickerLauncher = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.PickVisualMedia(),
        onResult = { uri -> imageUri = uri }
    )

    LazyColumn(
        modifier = Modifier
            .fillMaxSize()
            .background(Color.White)
            .padding(horizontal = 20.dp)
    ) {
        // REVISI: Header dengan Logo CuBu Asli
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
                        .size(36.dp)
                        .clip(RoundedCornerShape(8.dp))
                )
                Spacer(modifier = Modifier.width(8.dp))
                Text(text = "CuBu", fontSize = 20.sp, fontWeight = FontWeight.Bold)
            }
            Spacer(modifier = Modifier.height(24.dp))
            Text(text = "Buat resep baru", fontSize = 22.sp, fontWeight = FontWeight.ExtraBold)
            Spacer(modifier = Modifier.height(16.dp))
        }

        // Kotak Unggah Foto
        item {
            val stroke = Stroke(width = 4f, pathEffect = PathEffect.dashPathEffect(floatArrayOf(15f, 15f), 0f))
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .height(120.dp)
                    .then(
                        if (imageUri == null) {
                            Modifier.drawBehind {
                                drawRoundRect(color = Color.Gray, style = stroke, cornerRadius = androidx.compose.ui.geometry.CornerRadius(24f))
                            }
                        } else {
                            Modifier.clip(RoundedCornerShape(8.dp))
                        }
                    )
                    .clickable {
                        photoPickerLauncher.launch(
                            PickVisualMediaRequest(ActivityResultContracts.PickVisualMedia.ImageOnly)
                        )
                    },
                contentAlignment = Alignment.Center
            ) {
                if (imageUri != null) {
                    AsyncImage(
                        model = imageUri,
                        contentDescription = "Foto Resep Terpilih",
                        contentScale = ContentScale.Crop,
                        modifier = Modifier.fillMaxSize()
                    )
                } else {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        Icon(Icons.Outlined.Image, contentDescription = "Unggah", modifier = Modifier.size(32.dp), tint = Color.DarkGray)
                        Spacer(modifier = Modifier.height(8.dp))
                        Text("Unggah Foto", color = Color.DarkGray, fontSize = 14.sp)
                    }
                }
            }
            Spacer(modifier = Modifier.height(20.dp))
        }

        item {
            InputLabel("Judul resep")
            OutlinedTextField(
                value = title,
                onValueChange = { title = it },
                placeholder = { Text("Nama resep...", color = Color.Gray) },
                modifier = Modifier.fillMaxWidth(),
                shape = RoundedCornerShape(8.dp)
            )
            Spacer(modifier = Modifier.height(16.dp))
        }

        item {
            Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.spacedBy(16.dp)) {
                Column(modifier = Modifier.weight(1f)) {
                    InputLabel("Kesulitan")
                    ExposedDropdownMenuBox(
                        expanded = expandedDifficulty,
                        onExpandedChange = { expandedDifficulty = it }
                    ) {
                        OutlinedTextField(
                            value = difficulty,
                            onValueChange = {},
                            readOnly = true,
                            trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded = expandedDifficulty) },
                            modifier = Modifier.menuAnchor().fillMaxWidth(),
                            shape = RoundedCornerShape(8.dp)
                        )
                        ExposedDropdownMenu(
                            expanded = expandedDifficulty,
                            onDismissRequest = { expandedDifficulty = false }
                        ) {
                            listOf("Mudah", "Sedang", "Sulit").forEach { selectionOption ->
                                DropdownMenuItem(
                                    text = { Text(selectionOption) },
                                    onClick = {
                                        difficulty = selectionOption
                                        expandedDifficulty = false
                                    }
                                )
                            }
                        }
                    }
                }
                Column(modifier = Modifier.weight(1f)) {
                    InputLabel("Waktu (mnt)")
                    OutlinedTextField(
                        value = time,
                        onValueChange = { time = it },
                        placeholder = { Text("Estimasi waktu", color = Color.Gray) },
                        keyboardOptions = androidx.compose.foundation.text.KeyboardOptions(keyboardType = KeyboardType.Number),
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(8.dp)
                    )
                }
            }
            Spacer(modifier = Modifier.height(16.dp))
        }

        item {
            InputLabel("Alat masak")
            Row(verticalAlignment = Alignment.CenterVertically) {
                OutlinedTextField(
                    value = alatInput,
                    onValueChange = { alatInput = it },
                    placeholder = { Text("Nama alat...", color = Color.Gray) },
                    modifier = Modifier.weight(1f),
                    shape = RoundedCornerShape(8.dp)
                )
                Spacer(modifier = Modifier.width(8.dp))
                IconButton(
                    onClick = {
                        if (alatInput.isNotBlank()) {
                            alatList.add(alatInput)
                            alatInput = ""
                        }
                    },
                    modifier = Modifier.border(1.dp, Color.Gray, RoundedCornerShape(8.dp))
                ) {
                    Icon(Icons.Default.Add, contentDescription = "Tambah Alat")
                }
            }
            Spacer(modifier = Modifier.height(8.dp))
            FlowRow(horizontalArrangement = Arrangement.spacedBy(8.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
                alatList.forEach { alat ->
                    ChipItem(text = alat, onRemove = { alatList.remove(alat) })
                }
            }
            Spacer(modifier = Modifier.height(16.dp))
        }

        item {
            InputLabel("Bahan")
            Row(verticalAlignment = Alignment.CenterVertically) {
                OutlinedTextField(
                    value = bahanName,
                    onValueChange = { bahanName = it },
                    placeholder = { Text("Bahan...", color = Color.Gray) },
                    modifier = Modifier.weight(1.5f),
                    shape = RoundedCornerShape(8.dp)
                )
                Spacer(modifier = Modifier.width(8.dp))
                OutlinedTextField(
                    value = bahanQty,
                    onValueChange = { bahanQty = it },
                    placeholder = { Text("Takaran", color = Color.Gray) },
                    modifier = Modifier.weight(1f),
                    shape = RoundedCornerShape(8.dp)
                )
                Spacer(modifier = Modifier.width(8.dp))
                IconButton(
                    onClick = {
                        if (bahanName.isNotBlank() && bahanQty.isNotBlank()) {
                            bahanList.add("$bahanName - [$bahanQty]")
                            bahanName = ""
                            bahanQty = ""
                        }
                    },
                    modifier = Modifier.border(1.dp, Color.Gray, RoundedCornerShape(8.dp))
                ) {
                    Icon(Icons.Default.Add, contentDescription = "Tambah Bahan")
                }
            }
            Spacer(modifier = Modifier.height(8.dp))
            FlowRow(horizontalArrangement = Arrangement.spacedBy(8.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
                bahanList.forEach { bahan ->
                    ChipItem(text = bahan, onRemove = { bahanList.remove(bahan) })
                }
            }
            Spacer(modifier = Modifier.height(16.dp))
        }

        item {
            InputLabel("Langkah memasak")
        }
        items(stepsList.size) { index ->
            Row(
                modifier = Modifier.fillMaxWidth().padding(bottom = 8.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                OutlinedTextField(
                    value = stepsList[index],
                    onValueChange = { newValue -> stepsList[index] = newValue },
                    placeholder = { Text("Langkah ke-${index + 1}...", color = Color.Gray) },
                    modifier = Modifier.weight(1f),
                    shape = RoundedCornerShape(8.dp),
                    leadingIcon = {
                        Text(text = "${index + 1}", fontWeight = FontWeight.Bold, modifier = Modifier.padding(start = 8.dp))
                    }
                )
                Spacer(modifier = Modifier.width(8.dp))
                IconButton(
                    onClick = { stepsList.removeAt(index) },
                    modifier = Modifier.background(Color(0xFFE2E8F0), RoundedCornerShape(8.dp))
                ) {
                    Icon(Icons.Default.DeleteOutline, contentDescription = "Hapus Langkah", tint = Color.DarkGray)
                }
            }
        }
        item {
            Button(
                onClick = { stepsList.add("") },
                modifier = Modifier.fillMaxWidth(),
                colors = ButtonDefaults.buttonColors(containerColor = Color.Gray),
                shape = RoundedCornerShape(8.dp)
            ) {
                Text("+ Tambah langkah", color = Color.White)
            }
            Spacer(modifier = Modifier.height(24.dp))
        }

        item {
            Button(
                onClick = {
                    viewModel.createRecipe(
                        title = title,
                        difficulty = difficulty,
                        time = time,
                        alat = alatList,
                        bahan = bahanList,
                        langkah = stepsList,
                        onSuccess = {
                            navController.navigate("home") {
                                popUpTo("home") { inclusive = true }
                            }
                        }
                    )
                },
                modifier = Modifier.fillMaxWidth().height(50.dp),
                shape = RoundedCornerShape(8.dp),
                colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF1E293B))
            ) {
                Text("Publikasikan", color = Color.White, fontWeight = FontWeight.Bold)
            }
            Spacer(modifier = Modifier.height(100.dp))
        }
    }
}

@Composable
fun InputLabel(text: String) {
    Text(text = text, fontSize = 14.sp, color = Color.DarkGray, modifier = Modifier.padding(bottom = 4.dp))
}

@Composable
fun ChipItem(text: String, onRemove: () -> Unit) {
    Surface(
        color = Color(0xFFE2E8F0),
        shape = RoundedCornerShape(16.dp)
    ) {
        Row(
            verticalAlignment = Alignment.CenterVertically,
            modifier = Modifier.padding(horizontal = 12.dp, vertical = 6.dp)
        ) {
            Text(text = text, fontSize = 12.sp, color = Color.Black)
            Spacer(modifier = Modifier.width(4.dp))
            Icon(
                Icons.Default.Close,
                contentDescription = "Hapus",
                modifier = Modifier.size(14.dp).clickable { onRemove() },
                tint = Color.DarkGray
            )
        }
    }
}
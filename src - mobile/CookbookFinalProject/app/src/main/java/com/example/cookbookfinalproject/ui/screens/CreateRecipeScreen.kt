package com.example.cookbookfinalproject.ui.screens

import android.net.Uri
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.PickVisualMediaRequest
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.Image
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
import androidx.compose.material.icons.outlined.VideoFile
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
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavHostController
import coil.compose.AsyncImage
import com.example.cookbookfinalproject.R
import com.example.cookbookfinalproject.ui.viewmodel.RecipeViewModel

@OptIn(ExperimentalMaterial3Api::class, ExperimentalLayoutApi::class)
@Composable
fun CreateRecipeScreen(
    navController: NavHostController,
    viewModel: RecipeViewModel = viewModel(),
    recipeId: Int? = null
) {
    var title by remember(recipeId) { mutableStateOf("") }
    var description by remember(recipeId) { mutableStateOf("") }
    var difficulty by remember(recipeId) { mutableStateOf("Pilih") }
    var expandedDifficulty by remember { mutableStateOf(false) }
    var time by remember(recipeId) { mutableStateOf("") }

    var alatInput by remember { mutableStateOf("") }
    val alatList = remember(recipeId) { mutableStateListOf<String>() }

    var bahanName by remember { mutableStateOf("") }
    var bahanQty by remember { mutableStateOf("") }
    val bahanList = remember(recipeId) { mutableStateListOf<String>() }

    val stepTitlesList = remember(recipeId) { mutableStateListOf("") }
    val stepsList = remember(recipeId) { mutableStateListOf("") }

    var imageUri by remember(recipeId) { mutableStateOf<Uri?>(null) }
    var videoUri by remember(recipeId) { mutableStateOf<Uri?>(null) }
    var formInitialized by remember(recipeId) { mutableStateOf(false) }
    val context = LocalContext.current
    val canUploadVideo = viewModel.currentUser.value?.can_upload_videos == true
    val existingRecipe = viewModel.selectedRecipe.value?.takeIf { it.id == recipeId }

    LaunchedEffect(recipeId) {
        if (recipeId != null && viewModel.selectedRecipe.value?.id != recipeId) {
            viewModel.fetchDetail(recipeId)
        }
    }

    LaunchedEffect(recipeId, existingRecipe?.id) {
        if (recipeId != null && existingRecipe != null && !formInitialized) {
            title = existingRecipe.title
            description = existingRecipe.description
            difficulty = existingRecipe.difficulty.replaceFirstChar { it.uppercase() }
            time = existingRecipe.estimated_time.toString()
            alatList.addAll(existingRecipe.tools.orEmpty().map { it.name })
            bahanList.addAll(
                existingRecipe.ingredients.orEmpty().map { "${it.name} - [${it.quantity}]" }
            )
            stepTitlesList.clear()
            stepsList.clear()
            stepTitlesList.addAll(existingRecipe.steps.orEmpty().map { it.title })
            stepsList.addAll(existingRecipe.steps.orEmpty().map { it.description })
            formInitialized = true
        }
    }

    val photoPickerLauncher = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.PickVisualMedia(),
        onResult = { uri -> imageUri = uri }
    )

    val videoPickerLauncher = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.GetContent(),
        onResult = { uri -> videoUri = uri }
    )

    LazyColumn(
        modifier = Modifier
            .fillMaxSize()
            .background(Color.White)
            .padding(horizontal = 20.dp)
    ) {
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
                Text(text = "CuBu", fontSize = 24.sp, fontWeight = FontWeight.Bold)
            }
            Spacer(modifier = Modifier.height(24.dp))
            Text(
                text = if (recipeId == null) "Buat resep baru" else "Edit resep",
                fontSize = 22.sp,
                fontWeight = FontWeight.ExtraBold
            )
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
                if (imageUri != null || existingRecipe?.thumbnail_url != null) {
                    AsyncImage(
                        model = imageUri ?: existingRecipe?.thumbnail_url,
                        contentDescription = "Foto resep",
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

        if (canUploadVideo) {
            item {
                InputLabel("Video resep (opsional)")
                Surface(
                    modifier = Modifier
                        .fillMaxWidth()
                        .clickable { videoPickerLauncher.launch("video/mp4") },
                    color = if (videoUri == null) Color(0xFFF8FAFC) else Color(0xFFFFF7ED),
                    shape = RoundedCornerShape(12.dp),
                    border = androidx.compose.foundation.BorderStroke(
                        1.dp,
                        if (videoUri == null) Color(0xFFCBD5E1) else Color(0xFFF97316)
                    )
                ) {
                    Row(
                        modifier = Modifier.padding(16.dp),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Icon(
                            Icons.Outlined.VideoFile,
                            contentDescription = null,
                            tint = Color(0xFFF97316),
                            modifier = Modifier.size(28.dp)
                        )
                        Spacer(Modifier.width(12.dp))
                        Column(Modifier.weight(1f)) {
                            Text(
                                if (videoUri == null) "Pilih video MP4" else "Video MP4 sudah dipilih",
                                fontWeight = FontWeight.Bold
                            )
                            Text(
                                "Maksimal 500 MB",
                                color = Color.Gray,
                                fontSize = 12.sp
                            )
                        }
                        if (videoUri != null) {
                            IconButton(onClick = { videoUri = null }) {
                                Icon(Icons.Default.Close, contentDescription = "Hapus video")
                            }
                        }
                    }
                }
                Spacer(modifier = Modifier.height(20.dp))
            }
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
            InputLabel("Deskripsi")
            OutlinedTextField(
                value = description,
                onValueChange = { description = it },
                placeholder = { Text("Ceritakan resep dan hasil masakannya...") },
                minLines = 3,
                maxLines = 5,
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
            Card(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(bottom = 12.dp),
                shape = RoundedCornerShape(12.dp),
                colors = CardDefaults.cardColors(containerColor = Color(0xFFF8FAFC)),
                border = androidx.compose.foundation.BorderStroke(1.dp, Color(0xFFE2E8F0))
            ) {
                Column(modifier = Modifier.padding(12.dp)) {
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Surface(
                            modifier = Modifier.size(30.dp),
                            shape = CircleShape,
                            color = Color(0xFFF97316)
                        ) {
                            Box(contentAlignment = Alignment.Center) {
                                Text(
                                    text = "${index + 1}",
                                    color = Color.White,
                                    fontWeight = FontWeight.Bold
                                )
                            }
                        }
                        Spacer(modifier = Modifier.width(10.dp))
                        Text(
                            text = "Langkah ${index + 1}",
                            modifier = Modifier.weight(1f),
                            fontWeight = FontWeight.Bold
                        )
                        IconButton(
                            onClick = {
                                if (stepsList.size == 1) {
                                    stepTitlesList[0] = ""
                                    stepsList[0] = ""
                                } else {
                                    stepTitlesList.removeAt(index)
                                    stepsList.removeAt(index)
                                }
                            }
                        ) {
                            Icon(
                                Icons.Default.DeleteOutline,
                                contentDescription = "Hapus langkah",
                                tint = Color.DarkGray
                            )
                        }
                    }

                    OutlinedTextField(
                        value = stepTitlesList[index],
                        onValueChange = { stepTitlesList[index] = it },
                        label = { Text("Judul langkah") },
                        placeholder = { Text("Contoh: Tumis bumbu") },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(8.dp)
                    )
                    Spacer(modifier = Modifier.height(8.dp))
                    OutlinedTextField(
                        value = stepsList[index],
                        onValueChange = { stepsList[index] = it },
                        label = { Text("Deskripsi langkah") },
                        placeholder = { Text("Jelaskan proses memasaknya...") },
                        minLines = 3,
                        maxLines = 5,
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(8.dp)
                    )
                }
            }
        }
        item {
            Button(
                onClick = {
                    stepTitlesList.add("")
                    stepsList.add("")
                },
                modifier = Modifier.fillMaxWidth(),
                colors = ButtonDefaults.buttonColors(containerColor = Color.Gray),
                shape = RoundedCornerShape(8.dp)
            ) {
                Text("+ Tambah langkah", color = Color.White)
            }
            Spacer(modifier = Modifier.height(24.dp))
        }

        item {
            viewModel.createError.value?.let {
                Text(
                    it,
                    color = MaterialTheme.colorScheme.error,
                    fontSize = 12.sp,
                    modifier = Modifier.padding(bottom = 8.dp)
                )
            }
            Button(
                onClick = {
                    val submittedTools = alatList.toMutableList().apply {
                        alatInput.trim().takeIf { it.isNotEmpty() }?.let(::add)
                    }
                    val submittedIngredients = bahanList.toMutableList().apply {
                        if (bahanName.isNotBlank() && bahanQty.isNotBlank()) {
                            add("${bahanName.trim()} - [${bahanQty.trim()}]")
                        }
                    }

                    viewModel.saveRecipe(
                        recipeId = recipeId,
                        context = context,
                        imageUri = imageUri,
                        videoUri = videoUri,
                        title = title,
                        description = description,
                        difficulty = difficulty,
                        time = time,
                        alat = submittedTools,
                        bahan = submittedIngredients,
                        judulLangkah = stepTitlesList,
                        langkah = stepsList,
                        onSuccess = {
                            if (recipeId == null) {
                                navController.navigate("home") {
                                    popUpTo("home") { inclusive = true }
                                }
                            } else {
                                navController.navigate("detail/$recipeId") {
                                    popUpTo("edit/$recipeId") { inclusive = true }
                                }
                            }
                        }
                    )
                },
                enabled = !viewModel.createLoading.value,
                modifier = Modifier.fillMaxWidth().height(50.dp),
                shape = RoundedCornerShape(8.dp),
                colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF1E293B))
            ) {
                if (viewModel.createLoading.value) {
                    CircularProgressIndicator(
                        modifier = Modifier.size(22.dp),
                        color = Color.White,
                        strokeWidth = 2.dp
                    )
                } else {
                    Text(
                        if (recipeId == null) "Publikasikan" else "Simpan perubahan",
                        color = Color.White,
                        fontWeight = FontWeight.Bold
                    )
                }
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

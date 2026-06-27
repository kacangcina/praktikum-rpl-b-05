package com.example.cookbookfinalproject.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.Image
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.imePadding
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.rememberLazyListState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardActions
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Send
import androidx.compose.material.icons.outlined.Restaurant
import androidx.compose.material.icons.outlined.SmartToy
import androidx.compose.material.icons.outlined.WarningAmber
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.Divider
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material3.Icon
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Surface
import androidx.compose.material3.Text
import androidx.compose.material3.TextFieldDefaults
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalFocusManager
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.ImeAction
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.navigation.NavHostController
import com.example.cookbookfinalproject.R
import com.example.cookbookfinalproject.data.api.RetrofitClient
import com.example.cookbookfinalproject.data.model.CookingConsultationRequest
import com.example.cookbookfinalproject.data.model.RelatedRecipe
import kotlinx.coroutines.launch

private val CuBuOrange = Color(0xFFF97316)
private val PageBackground = Color(0xFFF8FAFC)

private data class ConsultationMessage(
    val role: MessageRole,
    val text: String,
    val rejected: Boolean = false,
    val recipes: List<RelatedRecipe> = emptyList()
)

private enum class MessageRole {
    USER,
    ASSISTANT
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun SearchScreen(navController: NavHostController) {
    var question by remember { mutableStateOf("") }
    var messages by remember {
        mutableStateOf(
            listOf(
                ConsultationMessage(
                    role = MessageRole.ASSISTANT,
                    text = "Ceritakan masalah memasakmu. Contoh: kenapa ayam goreng saya matang di luar tetapi masih mentah di dalam?"
                )
            )
        )
    }
    var isLoading by remember { mutableStateOf(false) }
    var errorMessage by remember { mutableStateOf<String?>(null) }

    val scope = rememberCoroutineScope()
    val listState = rememberLazyListState()
    val focusManager = LocalFocusManager.current

    fun submitQuestion() {
        val value = question.trim()

        if (isLoading) return
        if (value.length < 5) {
            errorMessage = "Pertanyaan minimal 5 karakter."
            return
        }

        question = ""
        errorMessage = null
        isLoading = true
        messages = messages + ConsultationMessage(MessageRole.USER, value)
        focusManager.clearFocus()

        scope.launch {
            try {
                val response = RetrofitClient.instance.askCookingAi(
                    CookingConsultationRequest(value)
                )

                if (response.isSuccessful) {
                    val result = response.body()
                    if (result != null) {
                        messages = messages + ConsultationMessage(
                            role = MessageRole.ASSISTANT,
                            text = result.answer,
                            rejected = !result.in_scope,
                            recipes = result.related_recipes
                        )
                    } else {
                        errorMessage = "Gagal memproses jawaban AI."
                    }
                } else {
                    errorMessage = "Error ${response.code()}: ${response.message()}"
                }
            } catch (e: Exception) {
                errorMessage = "Gagal terhubung ke server. Cek koneksi & IP API."
            } finally {
                isLoading = false
                // Auto scroll ke bawah setelah pesan baru muncul
                listState.animateScrollToItem(messages.lastIndex)
            }
        }
    }

    LaunchedEffect(messages.size, isLoading) {
        if (messages.isNotEmpty()) {
            listState.animateScrollToItem(messages.lastIndex)
        }
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(PageBackground)
            .imePadding()
    ) {
        ConsultationHeader()

        LazyColumn(
            state = listState,
            modifier = Modifier
                .weight(1f)
                .fillMaxWidth(),
            contentPadding = androidx.compose.foundation.layout.PaddingValues(
                horizontal = 16.dp,
                vertical = 18.dp
            ),
            verticalArrangement = Arrangement.spacedBy(16.dp)
        ) {
            items(messages.size) { index ->
                ConsultationBubble(
                    message = messages[index],
                    onRecipeClick = { recipeId ->
                        navController.navigate("detail/$recipeId")
                    }
                )
            }

            if (isLoading) {
                item {
                    Row(
                        verticalAlignment = Alignment.CenterVertically,
                        modifier = Modifier.padding(start = 46.dp)
                    ) {
                        CircularProgressIndicator(
                            modifier = Modifier.size(16.dp),
                            strokeWidth = 2.dp,
                            color = CuBuOrange
                        )
                        Spacer(Modifier.width(8.dp))
                        Text(
                            "CuBu sedang menyusun jawaban...",
                            color = Color(0xFF64748B),
                            fontSize = 13.sp
                        )
                    }
                }
            }
        }

        ConsultationInput(
            question = question,
            onQuestionChange = {
                if (it.length <= 1000) {
                    question = it
                    errorMessage = null
                }
            },
            errorMessage = errorMessage,
            isLoading = isLoading,
            onSubmit = ::submitQuestion
        )
    }
}

@Composable
private fun ConsultationHeader() {
    Surface(
        color = Color.White,
        shadowElevation = 3.dp,
        modifier = Modifier.fillMaxWidth()
    ) {
        Row(
            modifier = Modifier.padding(horizontal = 18.dp, vertical = 14.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Image(
                painter = painterResource(id = R.drawable.logo),
                contentDescription = "Logo CuBu",
                modifier = Modifier
                    .size(50.dp)
                    .clip(RoundedCornerShape(12.dp))
            )
            Spacer(Modifier.width(10.dp))
            Column {
                Text(
                    "CuBu",
                    fontSize = 24.sp,
                    fontWeight = FontWeight.Bold,
                    color = Color(0xFF0F172A)
                )
                Text(
                    "Tanya AI · Konsultasi Masak",
                    fontSize = 11.sp,
                    color = Color(0xFF64748B)
                )
            }
        }
    }
}

@Composable
private fun ConsultationBubble(
    message: ConsultationMessage,
    onRecipeClick: (Int) -> Unit
) {
    val isUser = message.role == MessageRole.USER
    val bubbleColor = when {
        isUser -> Color(0xFF1E293B)
        message.rejected -> Color(0xFFFFF1F2)
        else -> Color.White
    }
    val textColor = when {
        isUser -> Color.White
        message.rejected -> Color(0xFF9F1239)
        else -> Color(0xFF334155)
    }

    Row(
        modifier = Modifier.fillMaxWidth(),
        horizontalArrangement = if (isUser) Arrangement.End else Arrangement.Start,
        verticalAlignment = Alignment.Top
    ) {
        if (!isUser) {
            Surface(
                modifier = Modifier.size(34.dp),
                shape = CircleShape,
                color = if (message.rejected) Color(0xFFFFE4E6) else Color(0xFFFFEDD5)
            ) {
                Icon(
                    imageVector = if (message.rejected) {
                        Icons.Outlined.WarningAmber
                    } else {
                        Icons.Outlined.SmartToy
                    },
                    contentDescription = null,
                    tint = if (message.rejected) Color(0xFFE11D48) else CuBuOrange,
                    modifier = Modifier.padding(7.dp)
                )
            }
            Spacer(Modifier.width(9.dp))
        }

        Card(
            modifier = Modifier.fillMaxWidth(if (isUser) 0.82f else 0.86f),
            shape = RoundedCornerShape(
                topStart = if (isUser) 20.dp else 5.dp,
                topEnd = if (isUser) 5.dp else 20.dp,
                bottomStart = 20.dp,
                bottomEnd = 20.dp
            ),
            colors = CardDefaults.cardColors(containerColor = bubbleColor),
            elevation = CardDefaults.cardElevation(
                defaultElevation = if (isUser) 0.dp else 1.dp
            )
        ) {
            Column(modifier = Modifier.padding(14.dp)) {
                Text(
                    text = message.text,
                    color = textColor,
                    fontSize = 14.sp,
                    lineHeight = 21.sp
                )

                if (message.recipes.isNotEmpty()) {
                    Spacer(Modifier.height(12.dp))
                    Divider(color = Color(0xFFE2E8F0))
                    Spacer(Modifier.height(10.dp))
                    Text(
                        "Resep CuBu yang berkaitan",
                        color = Color(0xFF64748B),
                        fontSize = 11.sp,
                        fontWeight = FontWeight.SemiBold
                    )
                    Spacer(Modifier.height(7.dp))

                    message.recipes.forEach { recipe ->
                        Surface(
                            modifier = Modifier
                                .fillMaxWidth()
                                .padding(bottom = 7.dp)
                                .clickable { onRecipeClick(recipe.id) },
                            color = Color(0xFFFFF7ED),
                            shape = RoundedCornerShape(10.dp)
                        ) {
                            Row(
                                modifier = Modifier.padding(
                                    horizontal = 11.dp,
                                    vertical = 9.dp
                                ),
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                Icon(
                                    Icons.Outlined.Restaurant,
                                    contentDescription = null,
                                    tint = CuBuOrange,
                                    modifier = Modifier.size(17.dp)
                                )
                                Spacer(Modifier.width(8.dp))
                                Text(
                                    recipe.title,
                                    color = Color(0xFF9A3412),
                                    fontSize = 12.sp,
                                    fontWeight = FontWeight.Bold
                                )
                            }
                        }
                    }
                }
            }
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun ConsultationInput(
    question: String,
    onQuestionChange: (String) -> Unit,
    errorMessage: String?,
    isLoading: Boolean,
    onSubmit: () -> Unit
) {
    Surface(
        color = Color.White,
        shadowElevation = 8.dp,
        modifier = Modifier.fillMaxWidth()
    ) {
        Column(modifier = Modifier.padding(14.dp)) {
            if (errorMessage != null) {
                Text(
                    text = errorMessage,
                    color = Color(0xFFDC2626),
                    fontSize = 12.sp,
                    modifier = Modifier.padding(bottom = 8.dp)
                )
            }

            Row(
                verticalAlignment = Alignment.Bottom,
                horizontalArrangement = Arrangement.spacedBy(9.dp)
            ) {
                OutlinedTextField(
                    value = question,
                    onValueChange = onQuestionChange,
                    modifier = Modifier.weight(1f),
                    placeholder = {
                        Text(
                            "Contoh: Mengapa donat saya tidak mengembang?",
                            fontSize = 13.sp
                        )
                    },
                    minLines = 1,
                    maxLines = 4,
                    shape = RoundedCornerShape(16.dp),
                    keyboardOptions = KeyboardOptions(imeAction = ImeAction.Send),
                    keyboardActions = KeyboardActions(onSend = { onSubmit() }),
                    colors = TextFieldDefaults.outlinedTextFieldColors(
                        focusedBorderColor = CuBuOrange,
                        unfocusedBorderColor = Color(0xFFCBD5E1),
                        containerColor = Color(0xFFF8FAFC)
                    )
                )

                Button(
                    onClick = onSubmit,
                    enabled = !isLoading && question.isNotBlank(),
                    modifier = Modifier.size(52.dp),
                    shape = RoundedCornerShape(16.dp),
                    contentPadding = androidx.compose.foundation.layout.PaddingValues(0.dp),
                    colors = ButtonDefaults.buttonColors(
                        containerColor = CuBuOrange,
                        disabledContainerColor = Color(0xFFFED7AA)
                    )
                ) {
                    if (isLoading) {
                        CircularProgressIndicator(
                            modifier = Modifier.size(20.dp),
                            color = Color.White,
                            strokeWidth = 2.dp
                        )
                    } else {
                        Icon(Icons.Default.Send, contentDescription = "Kirim pertanyaan")
                    }
                }
            }

            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(top = 6.dp),
                horizontalArrangement = Arrangement.SpaceBetween
            ) {
                Text(
                    "CuBu AI hanya menjawab seputar memasak.",
                    color = Color(0xFF94A3B8),
                    fontSize = 10.sp
                )
                Text(
                    "${question.length}/1000",
                    color = Color(0xFF94A3B8),
                    fontSize = 10.sp
                )
            }
        }
    }
}

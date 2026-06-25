package com.example.cookbookfinalproject

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import com.example.cookbookfinalproject.data.api.RetrofitClient
import com.example.cookbookfinalproject.ui.screens.MainScreen

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        RetrofitClient.initialize(applicationContext)
        setContent {
            MainScreen()
        }
    }
}

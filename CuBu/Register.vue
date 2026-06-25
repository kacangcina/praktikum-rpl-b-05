<template>
    <div class="container">
        <h2>Daftar Akun Baru</h2>
        <form @submit.prevent="handleRegister">
            <div v-if="errors" class="errors">
                <p v-for="(error, field) in errors" :key="field">{{ error[0] }}</p>
            </div>
            <div>
                <label for="name">Nama</label>
                <input type="text" v-model="form.name" id="name" required>
            </div>
            <div>
                <label for="email">Email</label>
                <input type="email" v-model="form.email" id="email" required>
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" v-model="form.password" id="password" required>
            </div>
            <div>
                <label for="password_confirmation">Konfirmasi Password</label>
                <input type="password" v-model="form.password_confirmation" id="password_confirmation" required>
            </div>
            <button type="submit">Daftar</button>
        </form>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
});

const errors = ref(null);

const handleRegister = async () => {
    errors.value = null;
    try {
        // 1. Ambil CSRF cookie dari Sanctum
        await axios.get('/sanctum/csrf-cookie');

        // 2. Kirim data registrasi
        const response = await axios.post('/api/register', form.value);

        console.log(response.data.message);
        // Di sini Anda bisa mengarahkan pengguna ke halaman login atau dashboard
        // window.location.href = "/dashboard";

    } catch (error) {
        if (error.response && error.response.status === 422) {
            // Tangani error validasi dari Laravel
            errors.value = error.response.data.errors;
            console.error('Validation errors:', errors.value);
        } else {
            // Tangani error lainnya
            console.error('An error occurred:', error);
        }
    }
};
</script>

<style scoped>
.container { max-width: 500px; margin: auto; padding: 20px; }
.errors { color: red; margin-bottom: 1rem; }
div { margin-bottom: 15px; }
label { display: block; margin-bottom: 5px; }
input { width: 100%; padding: 8px; }
button { padding: 10px 15px; }
</style>
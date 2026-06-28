<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Recipe;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $now = now();

        // ==========================================
        // 1. CREATE USERS
        // ==========================================
        $admin = User::updateOrCreate(
            ['email' => 'admin@cubu.test'],
            ['name' => 'Admin CuBu', 'username' => 'admin_cubu', 'password' => bcrypt('password123'), 'role' => 'admin', 'is_verified' => true, 'created_at' => $now, 'updated_at' => $now]
        );

        $dummyUser1 = User::updateOrCreate(
            ['email' => 'dummy@example.com'],
            ['name' => 'Dummy User', 'username' => 'dummyuser', 'password' => bcrypt('password123'), 'role' => 'user', 'is_verified' => true, 'created_at' => $now, 'updated_at' => $now]
        );

        $dummyUser2 = User::updateOrCreate(
            ['email' => 'chef@example.com'],
            ['name' => 'Chef Dummy', 'username' => 'chefdummy', 'password' => bcrypt('password123'), 'role' => 'creator', 'is_verified' => true, 'created_at' => $now, 'updated_at' => $now]
        );

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'username' => 'testuser', 'password' => bcrypt('password123'), 'role' => 'user', 'is_verified' => true, 'created_at' => $now, 'updated_at' => $now]
        );


        // ==========================================
        // 2. CREATE RECIPES
        // ==========================================
        $recipe1 = Recipe::updateOrCreate(
            ['title' => 'Nasi Kuning Lezat'],
            [
                'user_id' => $dummyUser1->id,
                'description' => 'Resep nasi kuning tradisional yang lezat dan wangi dengan rempah-rempah pilihan.',
                'difficulty' => 'mudah',
                'estimated_time' => 45,
                'thumbnail' => 'https://images.unsplash.com/photo-1572656306390-40a9fc3899f7?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'moderation_status' => 'published',
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now
            ]
        );

        $recipe2 = Recipe::updateOrCreate(
            ['title' => 'Rendang Daging Padang'],
            [
                'user_id' => $dummyUser2->id,
                'description' => 'Rendang daging yang autentik dengan cita rasa Padang yang kaya.',
                'difficulty' => 'sedang',
                'estimated_time' => 120,
                'thumbnail' => 'https://img-global.cpcdn.com/recipes/5d82787e46397d30/1360x1562f0.500443_0.513533_1.04396q80/rendang-daging-asli-padang-foto-resep-utama.webp',
                'moderation_status' => 'published',
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now
            ]
        );

        $recipe3 = Recipe::updateOrCreate(
            ['title' => 'Gado-Gado Jakarta'],
            [
                'user_id' => $dummyUser1->id,
                'description' => 'Gado-gado khas Jakarta dengan bumbu kacang yang kental dan lezat. Berbagai sayuran segar dalam satu hidangan yang nikmat.',
                'difficulty' => 'mudah',
                'estimated_time' => 30,
                'thumbnail' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'moderation_status' => 'published',
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now
            ]
        );

        $recipe4 = Recipe::updateOrCreate(
            ['title' => 'Bakso Daging Homemade'],
            [
                'user_id' => $dummyUser2->id,
                'description' => 'Bakso daging sapi buatan sendiri yang lezat dengan tekstur yang pas. Sempurna untuk masakan rumahan atau dijual secara komersial.',
                'difficulty' => 'sedang',
                'estimated_time' => 90,
                'thumbnail' => 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'moderation_status' => 'published',
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now
            ]
        );


        // ==========================================
        // 3. RECIPE TOOLS
        // ==========================================
        DB::table('recipe_tools')->insert([
            // Nasi Kuning
            ['recipe_id' => $recipe1->id, 'tool_name' => 'Rice Cooker / Dandang'],
            ['recipe_id' => $recipe1->id, 'tool_name' => 'Wajan untuk menumis'],
            // Rendang
            ['recipe_id' => $recipe2->id, 'tool_name' => 'Wajan Besar (Kuali)'],
            ['recipe_id' => $recipe2->id, 'tool_name' => 'Blender / Ulekan'],
            // Gado-Gado
            ['recipe_id' => $recipe3->id, 'tool_name' => 'Panci Rebus'],
            ['recipe_id' => $recipe3->id, 'tool_name' => 'Ulekan Batu Besar'],
            // Bakso
            ['recipe_id' => $recipe4->id, 'tool_name' => 'Panci Rebus Besar'],
            ['recipe_id' => $recipe4->id, 'tool_name' => 'Food Processor / Chopper'],
        ]);


        // ==========================================
        // 4. RECIPE INGREDIENTS
        // ==========================================
        DB::table('recipe_ingredients')->insert([
            // Nasi Kuning
            ['recipe_id' => $recipe1->id, 'ingredient_name' => 'Beras berkualitas', 'quantity' => '500 gram'],
            ['recipe_id' => $recipe1->id, 'ingredient_name' => 'Santan kental', 'quantity' => '250 ml'],
            ['recipe_id' => $recipe1->id, 'ingredient_name' => 'Kunyit (dihaluskan)', 'quantity' => '2 ruas'],
            // Rendang
            ['recipe_id' => $recipe2->id, 'ingredient_name' => 'Daging Sapi (potong kotak)', 'quantity' => '1 kg'],
            ['recipe_id' => $recipe2->id, 'ingredient_name' => 'Santan kelapa', 'quantity' => '1.5 liter'],
            ['recipe_id' => $recipe2->id, 'ingredient_name' => 'Bumbu Rendang Instan/Halus', 'quantity' => '200 gram'],
            // Gado-Gado
            ['recipe_id' => $recipe3->id, 'ingredient_name' => 'Kacang Tanah (digoreng)', 'quantity' => '200 gram'],
            ['recipe_id' => $recipe3->id, 'ingredient_name' => 'Sayuran (Kangkung, Tauge, Kacang Panjang)', 'quantity' => 'Secukupnya'],
            ['recipe_id' => $recipe3->id, 'ingredient_name' => 'Gula Merah', 'quantity' => '50 gram'],
            // Bakso
            ['recipe_id' => $recipe4->id, 'ingredient_name' => 'Daging Sapi Giling', 'quantity' => '500 gram'],
            ['recipe_id' => $recipe4->id, 'ingredient_name' => 'Tepung Tapioka', 'quantity' => '100 gram'],
            ['recipe_id' => $recipe4->id, 'ingredient_name' => 'Es Batu (hancurkan)', 'quantity' => '50 gram'],
        ]);


        // ==========================================
        // 5. RECIPE STEPS
        // ==========================================
        DB::table('recipe_steps')->insert([
            // Nasi Kuning
            ['recipe_id' => $recipe1->id, 'step_number' => 1, 'description' => 'Cuci beras hingga bersih dan tiriskan airnya.'],
            ['recipe_id' => $recipe1->id, 'step_number' => 2, 'description' => 'Tumis bumbu halus dan kunyit hingga harum.'],
            ['recipe_id' => $recipe1->id, 'step_number' => 3, 'description' => 'Masukkan beras, bumbu tumis, dan santan ke dalam rice cooker. Masak hingga matang.'],
            // Rendang
            ['recipe_id' => $recipe2->id, 'step_number' => 1, 'description' => 'Haluskan bumbu rendang dan tumis hingga harum.'],
            ['recipe_id' => $recipe2->id, 'step_number' => 2, 'description' => 'Masukkan daging sapi, aduk rata hingga daging berubah warna.'],
            ['recipe_id' => $recipe2->id, 'step_number' => 3, 'description' => 'Tuangkan santan, masak dengan api kecil sambil terus diaduk perlahan hingga bumbu meresap dan mengering.'],
            // Gado-Gado
            ['recipe_id' => $recipe3->id, 'step_number' => 1, 'description' => 'Rebus semua sayuran secara terpisah hingga matang, lalu tiriskan.'],
            ['recipe_id' => $recipe3->id, 'step_number' => 2, 'description' => 'Haluskan kacang tanah goreng, gula merah, dan bumbu pelengkap menggunakan ulekan.'],
            ['recipe_id' => $recipe3->id, 'step_number' => 3, 'description' => 'Campurkan bumbu kacang dengan sedikit air matang, lalu siram ke atas sayuran rebus.'],
            // Bakso
            ['recipe_id' => $recipe4->id, 'step_number' => 1, 'description' => 'Campurkan daging sapi giling, tepung tapioka, bumbu, dan es batu ke dalam chopper. Giling hingga adonan kalis dan lengket.'],
            ['recipe_id' => $recipe4->id, 'step_number' => 2, 'description' => 'Didihkan air di panci besar, lalu matikan atau kecilkan api. Bentuk adonan menjadi bulatan bakso dengan tangan dan sendok, lalu cemplungkan ke dalam air panas.'],
            ['recipe_id' => $recipe4->id, 'step_number' => 3, 'description' => 'Nyalakan kembali api sedang. Rebus bakso hingga mengapung yang menandakan bakso sudah matang. Angkat dan tiriskan.'],
        ]);


        // ==========================================
        // 6. COMMENTS & RATINGS
        // ==========================================
        DB::table('comments')->insert([
            ['user_id' => $dummyUser2->id, 'recipe_id' => $recipe1->id, 'content' => 'Warnanya cantik dan harum banget! Izin recook ya.', 'created_at' => $now],
            ['user_id' => $dummyUser1->id, 'recipe_id' => $recipe2->id, 'content' => 'Dagingnya empuk pol, bumbunya meresap sampai ke dalam.', 'created_at' => $now],
            // Tambahan komentar untuk Gado-Gado ($recipe3)
            ['user_id' => $admin->id, 'recipe_id' => $recipe3->id, 'content' => 'Wah, bumbu kacangnya kelihatan medok banget. Cocok buat makan siang!', 'created_at' => $now],
            // Tambahan komentar untuk Bakso ($recipe4)
            ['user_id' => $dummyUser1->id, 'recipe_id' => $recipe4->id, 'content' => 'Terima kasih resepnya chef! Anak-anak pada suka baksonya kenyal.', 'created_at' => $now],
        ]);

        DB::table('ratings')->insert([
            ['user_id' => $dummyUser2->id, 'recipe_id' => $recipe1->id, 'type' => 'upvote', 'created_at' => $now],
            ['user_id' => $dummyUser1->id, 'recipe_id' => $recipe2->id, 'type' => 'upvote', 'created_at' => $now],
            // Tambahan rating untuk Gado-Gado ($recipe3)
            ['user_id' => $admin->id, 'recipe_id' => $recipe3->id, 'type' => 'upvote', 'created_at' => $now],
            // Tambahan rating untuk Bakso ($recipe4)
            ['user_id' => $dummyUser1->id, 'recipe_id' => $recipe4->id, 'type' => 'upvote', 'created_at' => $now],
            ['user_id' => $admin->id, 'recipe_id' => $recipe4->id, 'type' => 'upvote', 'created_at' => $now],
        ]);


        // ==========================================
        // 7. COLLECTIONS & ITEMS
        // ==========================================
        $collectionId = DB::table('collections')->insertGetId([
            'user_id' => $dummyUser1->id,
            'name' => 'Menu Akhir Pekan',
            'created_at' => $now,
        ]);

        DB::table('collection_items')->insert([
            ['collection_id' => $collectionId, 'recipe_id' => $recipe2->id, 'saved_at' => $now],
            ['collection_id' => $collectionId, 'recipe_id' => $recipe4->id, 'saved_at' => $now],
        ]);


        // ==========================================
        // 8. VIDEOS
        // ==========================================
        DB::table('videos')->insert([
            [
                'user_id' => $dummyUser2->id,
                'title' => 'Masterclass: Teknik Menggiling Daging Bakso',
                'description' => 'Video panduan cara menggiling daging sapi untuk bakso agar kenyal maksimal tanpa bahan kimia pengenyal.',
                'difficulty' => 'sedang',
                'file_path' => '/storage/videos/masterclass-bakso.mp4',
                'created_at' => $now,
            ]
        ]);


        // ==========================================
        // 9. CREATOR VERIFICATIONS
        // ==========================================
        DB::table('creator_verifications')->insert([
            [
                'user_id' => $dummyUser1->id,
                'document_path' => '/storage/documents/ktp-dummyuser.pdf',
                'status' => 'pending',
                'rejection_reason' => null,
                'submitted_at' => $now,
                'reviewed_at' => null,
            ]
        ]);
    }
}

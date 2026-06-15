<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     * FR-02: Guest can browse public recipes.
     * FR-06: Search for recipes.
     */
    public function index(Request $request): JsonResponse
    {
        // Logika untuk mencari dan menampilkan daftar resep
        // TODO: Implementasi pencarian berdasarkan kata kunci dari $request
        return new JsonResponse(['message' => 'Recipe list'], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     * FR-04: Creators can publish recipes.
     * FR-05: Users can publish recipes.
     */
    public function store(Request $request): JsonResponse
    {
        // Logika untuk validasi dan menyimpan resep baru
        // TODO: Validasi input (judul, bahan, langkah, dll.)
        // TODO: Simpan resep ke database
        return new JsonResponse(['message' => 'Recipe created successfully'], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     * FR-02: Guest can view recipe details.
     */
    public function show(string $id): JsonResponse
    {
        // Logika untuk menampilkan detail satu resep
        // TODO: Cari resep berdasarkan $id dan kembalikan datanya
        return new JsonResponse(['message' => "Showing recipe with id: {$id}"], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        // Logika untuk menghapus resep
        // TODO: Tambahkan pengecekan otorisasi (hanya pemilik resep atau admin yang bisa menghapus)
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
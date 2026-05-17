import React, { useState } from 'react';
import { Search, Image as ImageIcon } from 'lucide-react';
import RecipeCard from '../components/RecipeCard';
import { mockRecipes, popularIngredients } from '../utils/mockData';

export default function Home() {
  const [activeIngredients, setActiveIngredients] = useState([]);

  const toggleIngredient = (ing) => {
    if (activeIngredients.includes(ing)) {
      setActiveIngredients(activeIngredients.filter(i => i !== ing));
    } else {
      setActiveIngredients([...activeIngredients, ing]);
    }
  };

  return (
    <div className="max-w-7xl mx-auto px-6 py-8 min-h-screen">
      {/* Banner */}
      <div className="bg-gradient-to-r from-gray-800 to-gray-700 rounded-3xl p-10 mb-8 flex flex-col justify-center shadow-lg relative overflow-hidden min-h-[300px]">
        <div className="relative z-10">
          <span className="bg-white/20 text-white px-3 py-1 rounded-full text-xs font-bold tracking-wider backdrop-blur-sm mb-4 inline-block">Featured Recipe</span>
          <h1 className="text-4xl md:text-5xl font-extrabold text-white mb-2 leading-tight">Resep Nusantara</h1>
          <p className="text-gray-300 text-lg max-w-xl">Eksplorasi hidangan lezat dan mudah dibuat di rumah.</p>
        </div>
        <div className="absolute right-0 top-0 bottom-0 w-1/2 bg-gray-600/50 flex items-center justify-center">
          <ImageIcon size={64} className="text-white/20" />
        </div>
      </div>

      {/* Filter Bahan */}
      <div className="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl p-6 md:p-8 mb-10 shadow-sm transition-colors duration-300">
        <h2 className="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2 mb-2">
          <Search size={24} className="text-orange-500" /> Punya bahan apa di kulkas?
        </h2>
        <p className="text-gray-500 dark:text-gray-400 mb-6 text-sm">Pilih bahan yang kamu miliki, Cubu carikan resepnya.</p>
        
        <div className="flex flex-wrap gap-3">
          {popularIngredients.map(ing => (
            <button
              key={ing}
              onClick={() => toggleIngredient(ing)}
              className={`px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 border ${
                activeIngredients.includes(ing)
                  ? 'bg-orange-500 text-white border-orange-500 shadow-md shadow-orange-500/30'
                  : 'bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-orange-50 hover:border-orange-200'
              }`}
            >
              {activeIngredients.includes(ing) && <span className="mr-1">✓</span>} {ing}
            </button>
          ))}
          <button className="px-4 py-2 rounded-xl text-sm font-semibold bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 border border-dashed border-gray-300 dark:border-gray-600">
            + Ketik Bahan Lain
          </button>
        </div>
      </div>

      {/* Grid Resep */}
      <div className="flex justify-between items-end mb-6">
        <div>
          <h2 className="text-2xl font-bold text-gray-800 dark:text-white">Rekomendasi terkini</h2>
          <p className="text-gray-500 dark:text-gray-400 mt-1 text-sm">Resep pilihan terbaik untukmu hari ini</p>
        </div>
        <div className="flex gap-2">
           <button className="px-4 py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-semibold transition">Terbaru</button>
           <button className="px-4 py-2 bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 rounded-lg text-sm font-semibold transition">Terpopuler</button>
        </div>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 pb-12">
        {mockRecipes.map(recipe => (
          <RecipeCard key={recipe.id} recipe={recipe} />
        ))}
      </div>
    </div>
  );
}

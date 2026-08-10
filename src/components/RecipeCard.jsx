import React from 'react';
import { User, Clock, ChefHat, PlayCircle, Star, Image as ImageIcon } from 'lucide-react';
import { Link } from 'react-router-dom';

export default function RecipeCard({ recipe }) {
  return (
    <Link 
      to={`/detail/${recipe.id}`}
      className="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden hover:shadow-xl hover:-translate-y-1 transition duration-300 flex flex-col group"
    >
      <div className="bg-gray-200 dark:bg-gray-800 h-40 md:h-48 flex items-center justify-center text-gray-400 dark:text-gray-600 relative overflow-hidden transition-colors">
        <ImageIcon size={48} className="group-hover:scale-110 transition duration-500" />
        {recipe.isCreator && (
          <div className="absolute top-3 right-3 bg-black/70 text-white px-2 py-1 rounded-md text-xs font-bold flex items-center gap-1 backdrop-blur-sm">
            <PlayCircle size={14} /> Video
          </div>
        )}
        <div className="absolute bottom-3 left-3 bg-white/90 dark:bg-gray-900/90 text-gray-800 dark:text-gray-200 px-2 py-1 rounded-md text-xs font-bold flex items-center gap-1 backdrop-blur-sm transition-colors">
          <Star className="text-yellow-500" size={12} fill="currentColor" /> {recipe.rating}
        </div>
      </div>
      <div className="p-4 md:p-5 flex-grow flex flex-col justify-between">
        <div>
          <h3 className="font-bold text-base md:text-lg text-gray-800 dark:text-gray-100 line-clamp-1 mb-1 group-hover:text-orange-500 transition">{recipe.title}</h3>
          <p className="text-sm text-gray-500 dark:text-gray-400 mb-4 flex items-center gap-1">
            <User size={14}/> Oleh <span className="font-medium text-gray-700 dark:text-gray-300">{recipe.author}</span>
          </p>
        </div>
        <div className="flex items-center justify-between border-t border-gray-100 dark:border-gray-800 pt-3 md:pt-4 transition-colors mt-auto">
          <span className="flex items-center gap-1 md:gap-1.5 text-xs md:text-sm text-gray-600 dark:text-gray-400 font-medium">
            <Clock size={14} className="text-blue-500 dark:text-blue-400"/> {recipe.time} mnt
          </span>
          <span className={`flex items-center gap-1 md:gap-1.5 text-xs md:text-sm font-medium capitalize px-2 py-1 rounded-md
            ${recipe.difficulty === 'Mudah' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 
              recipe.difficulty === 'Sedang' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400' : 
              'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'}`}
          >
            <ChefHat size={14}/> {recipe.difficulty}
          </span>
        </div>
      </div>
    </Link>
  );
}

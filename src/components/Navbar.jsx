import React from 'react';
import { Search, User, LogOut, Sun, Moon } from 'lucide-react';
import { Link } from 'react-router-dom';

export default function Navbar({ isDarkMode, setIsDarkMode, isLoggedIn, setIsLoggedIn }) {
  return (
    <nav className="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 sticky top-0 z-50 shadow-sm transition-colors duration-300">
      <div className="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
        <div className="flex items-center gap-10">
          <Link to="/" className="font-bold text-2xl text-gray-800 dark:text-white tracking-tight">CuBu</Link>
          <div className="hidden md:flex items-center gap-8 font-medium text-gray-600 dark:text-gray-300">
            <Link to="/" className="hover:text-orange-500 transition">Beranda</Link>
            <Link to="/collection" className="hover:text-orange-500 transition">Koleksi Saya</Link>
          </div>
        </div>
        
        <div className="flex items-center gap-4 md:gap-6">
          <div className="relative hidden md:block">
            <input 
              type="text" 
              placeholder="Cari resep" 
              className="bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-white text-sm rounded-full pl-10 pr-4 py-2 w-56 md:w-64 focus:outline-none focus:ring-2 focus:ring-orange-500/50 transition-colors"
            />
            <Search className="text-gray-400 dark:text-gray-500 absolute left-3 top-2" size={18} />
          </div>

          <div className="h-6 w-px bg-gray-300 dark:bg-gray-700 hidden md:block"></div>

          <button onClick={() => setIsDarkMode(!isDarkMode)} className="text-gray-500 dark:text-gray-400 hover:text-orange-500 transition">
            {isDarkMode ? <Sun size={20} /> : <Moon size={20} />}
          </button>

          {isLoggedIn ? (
            <div className="flex items-center gap-4">
              <div className="flex items-center gap-3 cursor-pointer group">
                <div className="bg-gray-200 dark:bg-gray-800 p-2 rounded-full text-gray-600 dark:text-gray-300 group-hover:bg-gray-300 transition">
                  <User size={20} />
                </div>
              </div>
              <button onClick={() => setIsLoggedIn(false)} className="text-gray-400 hover:text-red-500 transition">
                <LogOut size={20} />
              </button>
            </div>
          ) : (
            <div className="flex items-center gap-2 md:gap-3">
              <Link to="/login" className="text-gray-600 dark:text-gray-300 font-semibold hover:text-orange-500 px-3 py-2 transition">Masuk</Link>
              <Link to="/register" className="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded-xl transition shadow-md shadow-orange-500/20">Daftar</Link>
            </div>
          )}
        </div>
      </div>
    </nav>
  );
}
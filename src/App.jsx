import React, { useState, useEffect } from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Navbar from './components/Navbar';
import Home from './pages/Home';

const Collection = () => (
  <div className="max-w-7xl mx-auto px-6 py-20 text-center">
    <h1 className="text-3xl font-extrabold text-gray-800 dark:text-white mb-2">Halaman Koleksi Saya</h1>
    <p className="text-gray-500 dark:text-gray-400 text-sm">Status: Progress Development P6 (Syafiq - Frontend)</p>
  </div>
);

const Detail = () => (
  <div className="max-w-7xl mx-auto px-6 py-20 text-center">
    <h1 className="text-3xl font-extrabold text-gray-800 dark:text-white mb-2">Halaman Detail Resep</h1>
    <p className="text-gray-500 dark:text-gray-400 text-sm">Status: Progress Development P6 (Syafiq - Frontend)</p>
  </div>
);

const Login = ({ setIsLoggedIn }) => (
  <div className="max-w-7xl mx-auto px-6 py-20 text-center">
    <h1 className="text-3xl font-extrabold text-gray-800 dark:text-white mb-2">Halaman Login</h1>
    <p className="text-gray-500 dark:text-gray-400 text-sm">Status: Progress Development P6 (Syafiq - Frontend)</p>
  </div>
);

const Register = () => (
  <div className="max-w-7xl mx-auto px-6 py-20 text-center">
    <h1 className="text-3xl font-extrabold text-gray-900 dark:text-white mb-2">Halaman Register</h1>
    <p className="text-gray-500 dark:text-gray-400 text-sm">Status: Progress Development P6 (Syafiq - Frontend)</p>
  </div>
);

// =====================================================================
// UTAMA: App Component
// =====================================================================
export default function App() {
  const [isDarkMode, setIsDarkMode] = useState(false);
  const [isLoggedIn, setIsLoggedIn] = useState(false);

  // Pasang sistem dark mode ke DOM global HTML
  useEffect(() => {
    if (isDarkMode) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  }, [isDarkMode]);

  return (
    <Router>
      <div className={`${isDarkMode ? 'dark' : ''} w-full`}>
        <div className="bg-gray-50 dark:bg-gray-950 min-h-screen font-sans w-full text-gray-900 dark:text-gray-100 transition-colors duration-300 flex flex-col">
          
          {/* Sembunyikan Navbar di rute halaman autentikasi */}
          <Routes>
            <Route path="/login" element={null} />
            <Route path="/register" element={null} />
            <Route path="*" element={
              <Navbar 
                isDarkMode={isDarkMode} setIsDarkMode={setIsDarkMode} 
                isLoggedIn={isLoggedIn} setIsLoggedIn={setIsLoggedIn} 
              />
            } />
          </Routes>
          
          {/* Area Konten Utama Halaman */}
          <main className="flex-grow">
            <Routes>
              <Route path="/" element={<Home />} />
              <Route path="/collection" element={<Collection />} />
              <Route path="/detail/:id" element={<Detail />} />
              <Route path="/login" element={<Login setIsLoggedIn={setIsLoggedIn} />} />
              <Route path="/register" element={<Register />} />
            </Routes>
          </main>

        </div>
      </div>
    </Router>
  );
}

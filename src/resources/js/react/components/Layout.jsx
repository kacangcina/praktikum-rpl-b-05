import { Outlet } from 'react-router-dom';
import Navbar from './Navbar.jsx';

export default function Layout() {
    return (
        <div className="app-shell">
            <Navbar />
            <main className="page-container page-section">
                <Outlet />
            </main>
            <footer className="mt-12 border-t border-stone-300 bg-white py-8 text-center text-sm text-stone-500">
                CuBu · Temukan, masak, dan bagikan resep favoritmu.
            </footer>
        </div>
    );
}

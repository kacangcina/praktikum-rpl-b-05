import { Outlet } from 'react-router-dom';
import Alert from './Alert.jsx';
import Navbar from './Navbar.jsx';

export default function Layout() {
    const flash = window.__CUBU_FLASH__ || {};

    return (
        <div className="app-shell">
            <Navbar />
            <main className="page-container page-section">
                {flash.status && <Alert tone="success">{flash.status}</Alert>}
                {flash.error && <Alert>{flash.error}</Alert>}
                <Outlet />
            </main>
            <footer className="mt-12 border-t border-stone-300 bg-white py-8 text-center text-sm text-stone-500">
                CuBu | Temukan, masak, dan bagikan resep favoritmu.
            </footer>
        </div>
    );
}

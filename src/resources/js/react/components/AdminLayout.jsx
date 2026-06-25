import { Bot, ChefHat, ShieldCheck, Users } from 'lucide-react';
import { NavLink, Navigate, Outlet } from 'react-router-dom';
import { useAuth } from '../context/AuthContext.jsx';

const links = [
    { to: '/admin/creator-verifications', label: 'Verifikasi creator', icon: ShieldCheck },
    { to: '/admin/ai-settings', label: 'Prompt AI', icon: Bot },
    { to: '/admin/users', label: 'Pengguna', icon: Users },
    { to: '/admin/recipes', label: 'Moderasi resep', icon: ChefHat },
];

export default function AdminLayout() {
    const { user } = useAuth();

    if (!user?.is_admin) return <Navigate to="/" replace />;

    return (
        <div className="grid gap-6 lg:grid-cols-[240px_minmax(0,1fr)]">
            <aside className="h-fit rounded-2xl border border-stone-200 bg-white p-3 lg:sticky lg:top-28">
                <p className="px-3 py-2 text-xs font-black uppercase tracking-widest text-orange-600">Admin CuBu</p>
                <nav className="mt-1 grid gap-1">
                    {links.map(({ to, label, icon: Icon }) => (
                        <NavLink
                            key={to}
                            to={to}
                            className={({ isActive }) => `flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-bold transition ${isActive ? 'bg-stone-900 text-white' : 'text-stone-700 hover:bg-stone-100'}`}
                        >
                            <Icon size={18} /> {label}
                        </NavLink>
                    ))}
                </nav>
            </aside>
            <main className="min-w-0"><Outlet /></main>
        </div>
    );
}

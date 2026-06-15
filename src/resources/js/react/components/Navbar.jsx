import { Bookmark, Home, LogOut, Menu, Search, ShieldCheck, UserRound } from 'lucide-react';
import { useState } from 'react';
import { Link, NavLink, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext.jsx';

export default function Navbar() {
    const { user, logout } = useAuth();
    const navigate = useNavigate();
    const [open, setOpen] = useState(false);

    const submitSearch = (event) => {
        event.preventDefault();
        const query = new FormData(event.currentTarget).get('q');
        navigate(query ? `/recipes?q=${encodeURIComponent(query)}` : '/recipes');
    };

    return (
        <header className="sticky top-0 z-50 border-b border-stone-300 bg-white/95 backdrop-blur">
            <div className="page-container relative flex min-h-20 items-center gap-4 sm:gap-6">
                <Link to="/" className="flex shrink-0 items-center gap-2 text-2xl font-medium tracking-tight sm:gap-3 sm:text-3xl">
                    <img src="/images/cubu-logo.svg" alt="" className="size-12 rounded-2xl sm:size-14" />
                    <span>CuBu</span>
                </Link>

                <nav className="hidden items-center gap-5 text-sm font-semibold lg:flex">
                    <NavItem to="/recipes" icon={<Home size={18} />}>Beranda</NavItem>
                    {user && <NavItem to="/collections" icon={<Bookmark size={18} />}>Koleksi Saya</NavItem>}
                    {user?.is_admin && <NavItem to="/admin/creator-verifications" icon={<ShieldCheck size={18} />}>Admin</NavItem>}
                </nav>

                <form onSubmit={submitSearch} className="ml-auto hidden max-w-sm flex-1 md:flex">
                    <div className="flex w-full items-center rounded-full bg-stone-200 px-4">
                        <Search size={17} className="text-stone-500" />
                        <input name="q" className="w-full bg-transparent px-3 py-2.5 text-sm outline-none" placeholder="Cari resep" />
                    </div>
                </form>

                {user ? (
                    <div className="hidden items-center gap-2 border-l border-stone-400 pl-5 md:flex">
                        <Link to={`/profile/${user.id}`} className="flex items-center gap-2 rounded-xl px-2 py-2 font-semibold hover:bg-stone-100">
                            <span className="grid size-10 place-items-center overflow-hidden rounded-full bg-stone-200">
                                {user.avatar_url ? <img src={user.avatar_url} alt="" className="h-full w-full object-cover" /> : <UserRound size={22} />}
                            </span>
                            <span className="hidden xl:inline">{user.username}</span>
                        </Link>
                        <button
                            type="button"
                            onClick={async () => {
                                await logout();
                                navigate('/');
                            }}
                            className="rounded-xl p-2 hover:bg-stone-100"
                            aria-label="Keluar"
                        >
                            <LogOut size={18} />
                        </button>
                    </div>
                ) : (
                    <div className="hidden items-center gap-2 border-l border-stone-400 pl-5 text-sm font-semibold md:flex">
                        <Link to="/login" className="px-3 py-2">Masuk</Link>
                        <Link to="/register" className="rounded-full bg-stone-200 px-5 py-3 hover:bg-orange-500 hover:text-white">Daftar</Link>
                    </div>
                )}

                <button onClick={() => setOpen(!open)} className="absolute right-0 rounded-xl p-2 hover:bg-stone-100 md:hidden" aria-label="Menu"><Menu /></button>
                {open && (
                    <div className="absolute top-[calc(100%+1px)] right-0 left-0 z-50 border border-stone-300 bg-white p-4 shadow-lg md:hidden">
                        <div className="grid gap-2 font-semibold">
                            <Link onClick={() => setOpen(false)} to="/recipes" className="rounded-xl px-3 py-2 hover:bg-stone-100">Beranda</Link>
                            {user && <Link onClick={() => setOpen(false)} to="/collections" className="rounded-xl px-3 py-2 hover:bg-stone-100">Koleksi Saya</Link>}
                            {user ? <Link onClick={() => setOpen(false)} to={`/profile/${user.id}`} className="rounded-xl px-3 py-2 hover:bg-stone-100">Profil</Link> : <>
                                <Link onClick={() => setOpen(false)} to="/login" className="rounded-xl px-3 py-2 hover:bg-stone-100">Masuk</Link>
                                <Link onClick={() => setOpen(false)} to="/register" className="rounded-xl bg-orange-500 px-3 py-2 text-white">Daftar</Link>
                            </>}
                        </div>
                    </div>
                )}
            </div>
        </header>
    );
}

function NavItem({ to, icon, children }) {
    return (
        <NavLink
            to={to}
            className={({ isActive }) => `flex items-center gap-1.5 transition ${isActive ? 'text-orange-500' : 'text-stone-800 hover:text-orange-500'}`}
        >
            {icon}{children}
        </NavLink>
    );
}

import { LogOut, UserRound } from 'lucide-react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext.jsx';

export default function Navbar() {
    const { user, logout } = useAuth();
    const navigate = useNavigate();

    return (
        <header className="border-b border-stone-300 bg-white">
            <div className="page-container flex min-h-20 items-center gap-4">
                <Link to="/" className="flex items-center gap-3 text-2xl font-medium tracking-tight">
                    <img src="/images/cubu-logo.svg" alt="" className="size-12 rounded-2xl" />
                    <span>CuBu</span>
                </Link>

                {user && (
                    <div className="ml-auto flex items-center gap-3">
                        <div className="hidden items-center gap-2 text-sm font-semibold sm:flex">
                            <span className="grid size-9 place-items-center rounded-full bg-stone-100">
                                <UserRound size={19} />
                            </span>
                            <span>{user.username || user.name}</span>
                        </div>
                        <button
                            type="button"
                            onClick={async () => {
                                await logout();
                                navigate('/login');
                            }}
                            className="wire-button"
                        >
                            <LogOut size={18} />
                            Keluar
                        </button>
                    </div>
                )}
            </div>
        </header>
    );
}

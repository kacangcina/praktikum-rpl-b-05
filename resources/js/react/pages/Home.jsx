import { LogIn, UserRound } from 'lucide-react';
import { useAuth } from '../context/AuthContext.jsx';

export default function Home() {
    const { user } = useAuth();

    return (
        <section className="mx-auto flex min-h-[620px] max-w-4xl items-center justify-center">
            <div className="wire-panel w-full px-8 py-16 text-center sm:px-16">
                <div className="mx-auto grid size-24 place-items-center rounded-full bg-orange-100 text-orange-600">
                    <UserRound size={46} />
                </div>
                <p className="mt-8 text-sm font-bold uppercase tracking-[0.25em] text-orange-600">
                    Berhasil masuk
                </p>
                <h1 className="mt-3 text-4xl font-medium sm:text-5xl">
                    Selamat datang, {user?.username || user?.name}
                </h1>
                <p className="mx-auto mt-5 max-w-xl text-stone-600">
                    Anda sudah masuk ke halaman beranda CuBu.
                </p>
                <div className="mx-auto mt-8 flex w-fit items-center gap-2 rounded-full bg-stone-100 px-5 py-3 text-sm font-semibold text-stone-700">
                    <LogIn size={18} />
                    {user?.email}
                </div>
            </div>
        </section>
    );
}

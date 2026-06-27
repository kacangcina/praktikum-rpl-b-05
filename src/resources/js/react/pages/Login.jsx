import { cloneElement, useState } from 'react';
import { Eye, EyeOff } from 'lucide-react';
import { Link, Navigate, useLocation, useNavigate } from 'react-router-dom';
import { api, firstError } from '../api.js';
import Alert from '../components/Alert.jsx';
import { useAuth } from '../context/AuthContext.jsx';

export default function Login() {
    const { user, refresh } = useAuth();
    const navigate = useNavigate();
    const location = useLocation();
    const [error, setError] = useState(null);
    const [busy, setBusy] = useState(false);

    if (user) return <Navigate to="/" replace />;

    const submit = async (event) => {
        event.preventDefault();
        setBusy(true);
        setError(null);
        const form = new FormData(event.currentTarget);

        try {
            await api('/api/login', {
                method: 'POST',
                body: {
                    email: form.get('email'),
                    password: form.get('password'),
                    remember: form.get('remember') === '1',
                },
            });
            await refresh();
            navigate(location.state?.from || '/');
        } catch (caught) {
            setError(firstError(caught, 'email') || caught.message);
        } finally {
            setBusy(false);
        }
    };

    return (
        <AuthShell title="Halo, koki!" subtitle="Gunakan email dan kata sandi yang sudah didaftarkan.">
            {error && <Alert>{error}</Alert>}
            <form onSubmit={submit} className="space-y-4">
                <Field label="Email"><input aria-label="Email" type="email" name="email" required /></Field>
                <PasswordField label="Kata sandi" name="password" required />
                <label className="flex items-center gap-2 text-sm"><input type="checkbox" name="remember" value="1" /> Ingat saya</label>
                <button type="submit" disabled={busy} className="wire-button wire-button-primary w-full">{busy ? 'Memproses...' : 'Masuk ke CuBu'}</button>
            </form>
            <p className="mt-5 text-sm">Belum punya akun? <Link to="/register" className="font-bold text-orange-600">Daftar sekarang</Link></p>
        </AuthShell>
    );
}

export function AuthShell({ title, subtitle, children }) {
    return (
        <div className="mx-auto grid min-h-[650px] max-w-5xl overflow-hidden border border-stone-400 bg-white md:grid-cols-2">
            <div className="hidden flex-col items-center justify-center border-r border-stone-400 p-12 text-center md:flex">
                <img src="/images/cubu-logo.svg" alt="" className="size-36 rounded-3xl" />
                <h1 className="mt-8 text-4xl font-medium">Masak lebih percaya diri</h1>
                <p className="mt-5 max-w-sm text-stone-600">Simpan inspirasi, temukan resep baru, dan bagikan masakan andalanmu bersama komunitas CuBu.</p>
            </div>
            <div className="flex flex-col justify-center p-8 sm:p-14">
                <Link to="/" className="mb-10 text-right text-stone-400">Beranda &gt;</Link>
                <h2 className="text-4xl font-medium">{title}</h2>
                <p className="mt-3 mb-8 text-stone-600">{subtitle}</p>
                {children}
            </div>
        </div>
    );
}

export function Field({ label, children, error }) {
    return <label className="block text-sm font-semibold"><span>{label}</span><div className="mt-2">{cloneElement(children, { className: `${children.props.className || ''} wire-input` })}</div>{error && <small className="mt-1 block text-red-600">{error}</small>}</label>;
}

export function PasswordField({ label, name, ...inputProps }) {
    const [visible, setVisible] = useState(false);

    return (
        <label className="block text-sm font-semibold">
            <span>{label}</span>
            <span className="relative mt-2 block">
                <input
                    {...inputProps}
                    type={visible ? 'text' : 'password'}
                    name={name}
                    className="wire-input pr-12"
                />
                <button
                    type="button"
                    onClick={() => setVisible((current) => !current)}
                    aria-label={visible ? `Sembunyikan ${label.toLowerCase()}` : `Tampilkan ${label.toLowerCase()}`}
                    aria-pressed={visible}
                    className="absolute inset-y-0 right-0 grid w-12 place-items-center text-stone-500 hover:text-orange-600"
                >
                    {visible ? <EyeOff size={20} /> : <Eye size={20} />}
                </button>
            </span>
        </label>
    );
}

import { cloneElement, useState } from 'react';
import { Navigate, useLocation, useNavigate } from 'react-router-dom';
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
        <AuthShell title="Selamat datang" subtitle="Masukkan email dan kata sandi untuk melanjutkan.">
            {error && <Alert>{error}</Alert>}
            <form onSubmit={submit} className="space-y-4">
                <Field label="Email"><input type="email" name="email" required /></Field>
                <Field label="Kata sandi"><input type="password" name="password" required /></Field>
                <label className="flex items-center gap-2 text-sm"><input type="checkbox" name="remember" value="1" /> Ingat saya</label>
                <button disabled={busy} className="wire-button wire-button-primary w-full">{busy ? 'Memproses...' : 'Masuk'}</button>
            </form>
        </AuthShell>
    );
}

export function AuthShell({ title, subtitle, children }) {
    return (
        <div className="mx-auto grid min-h-[650px] max-w-5xl overflow-hidden border border-stone-400 bg-white md:grid-cols-2">
            <div className="hidden flex-col items-center justify-center border-r border-stone-400 p-12 text-center md:flex">
                <img src="/images/cubu-logo.svg" alt="" className="size-36 rounded-3xl" />
                <h1 className="mt-8 text-4xl font-medium">CuBu</h1>
                <p className="mt-5 max-w-sm text-stone-600">Silakan masuk untuk membuka halaman beranda.</p>
            </div>
            <div className="flex flex-col justify-center p-8 sm:p-14">
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

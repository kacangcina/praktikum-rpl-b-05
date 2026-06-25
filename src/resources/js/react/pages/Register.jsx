import { useState } from 'react';
import { Link, Navigate, useNavigate } from 'react-router-dom';
import { api, firstError } from '../api.js';
import Alert from '../components/Alert.jsx';
import { useAuth } from '../context/AuthContext.jsx';
import { AuthShell, Field } from './Login.jsx';

export default function Register() {
    const { user, refresh } = useAuth();
    const navigate = useNavigate();
    const [error, setError] = useState(null);
    const [busy, setBusy] = useState(false);

    if (user) return <Navigate to="/" replace />;

    const submit = async (event) => {
        event.preventDefault();
        setBusy(true);
        setError(null);
        const form = new FormData(event.currentTarget);

        try {
            await api('/api/register', {
                method: 'POST',
                body: Object.fromEntries(form),
            });
            const current = await refresh();
            navigate(`/profile/${current.id}`);
        } catch (caught) {
            setError(firstError(caught, 'email') || firstError(caught, 'username') || firstError(caught, 'password') || caught.message);
        } finally {
            setBusy(false);
        }
    };

    return (
        <AuthShell title="Buat akun" subtitle="Mulai simpan dan bagikan resep favoritmu.">
            {error && <Alert>{error}</Alert>}
            <form onSubmit={submit} className="space-y-4">
                <Field label="Username"><input name="username" required /></Field>
                <Field label="Email"><input type="email" name="email" required /></Field>
                <Field label="Kata sandi"><input type="password" name="password" minLength="8" required /></Field>
                <Field label="Konfirmasi kata sandi"><input type="password" name="password_confirmation" minLength="8" required /></Field>
                <button disabled={busy} className="wire-button wire-button-primary w-full">{busy ? 'Memproses...' : 'Daftar'}</button>
            </form>
            <p className="mt-5 text-sm">Sudah punya akun? <Link to="/login" className="font-bold text-orange-600">Masuk</Link></p>
        </AuthShell>
    );
}

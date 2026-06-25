import { Pencil, UserRound, X } from 'lucide-react';
import { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { api } from '../api.js';
import Alert from '../components/Alert.jsx';
import Loading from '../components/Loading.jsx';
import { useAuth } from '../context/AuthContext.jsx';
import { Field } from './Login.jsx';

export default function ProfileEdit() {
    const { user, refresh } = useAuth();
    const navigate = useNavigate();
    const [profile, setProfile] = useState(null);
    const [error, setError] = useState(null);

    useEffect(() => { if (user) api(`/api/profiles/${user.id}`).then((data) => setProfile(data.profile)); }, [user]);
    if (!profile) return <Loading />;

    const submit = async (event) => {
        event.preventDefault();
        setError(null);
        try {
            await api('/api/profile', { method: 'POST', headers: { 'X-HTTP-Method-Override': 'PUT' }, body: new FormData(event.currentTarget) });
            await refresh();
            navigate(`/profile/${user.id}`);
        } catch (caught) {
            setError(Object.values(caught.errors || {})[0]?.[0] || caught.message);
        }
    };

    return (
        <div className="mx-auto max-w-5xl">
            <div className="flex items-center justify-between border-b border-stone-500 pb-5">
                <h1 className="text-4xl font-black sm:text-5xl">Edit profil</h1>
                <Link to={`/profile/${user.id}`} aria-label="Tutup"><X size={44} strokeWidth={1.3} /></Link>
            </div>

            <form onSubmit={submit} className="mt-7">
                {error && <Alert>{error}</Alert>}
                <div className="border-b border-stone-400 pb-8">
                    <h2 className="text-2xl">Foto profil</h2>
                    <label className="relative mx-auto grid size-48 cursor-pointer place-items-center rounded-full bg-stone-200" aria-label="Unggah foto profil">
                        {profile.avatar_url ? <img src={profile.avatar_url} alt="" className="h-full w-full rounded-full object-cover" /> : <UserRound size={80} strokeWidth={1.2} />}
                        <span className="absolute right-0 bottom-0 rounded-full border border-stone-700 bg-white p-3"><Pencil /></span>
                        <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" aria-label="Unggah foto profil" className="hidden" />
                    </label>
                </div>

                <div className="divide-y divide-stone-300">
                    <EditRow label="Nama Pengguna"><Field label="Username"><input aria-label="Nama pengguna" className="profile-edit-control" name="username" defaultValue={profile.username} required /></Field></EditRow>
                    <EditRow label="Nama"><Field label="Nama"><input aria-label="Nama" className="profile-edit-control" name="name" defaultValue={profile.name} required /></Field></EditRow>
                    <EditRow label="Bio"><Field label="Bio"><textarea aria-label="Bio" className="profile-edit-control" name="bio" rows="5" defaultValue={profile.bio || ''} /></Field></EditRow>
                </div>

                <div className="mt-8 flex justify-end gap-4 border-t border-stone-400 pt-8">
                    <Link to={`/profile/${user.id}`} className="wire-button bg-stone-200">Batal</Link>
                    <button type="submit" className="wire-button wire-button-primary">Simpan</button>
                </div>
            </form>
        </div>
    );
}

function EditRow({ label, children }) {
    return <div className="grid gap-4 py-7 sm:grid-cols-[220px_1fr] sm:items-start"><h2 className="text-2xl">{label}</h2>{children}</div>;
}

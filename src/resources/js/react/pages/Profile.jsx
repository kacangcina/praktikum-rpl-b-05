import { Bell, ChefHat, Pencil, Plus, ShieldCheck, UserRound, Video } from 'lucide-react';
import { useEffect, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import { api } from '../api.js';
import Loading from '../components/Loading.jsx';
import RecipeCard from '../components/RecipeCard.jsx';
import { useAuth } from '../context/AuthContext.jsx';

export default function Profile() {
    const { id } = useParams();
    const { user } = useAuth();
    const [data, setData] = useState(null);

    useEffect(() => { api(`/api/profiles/${id}`).then(setData); }, [id]);
    if (!data) return <Loading />;

    const profile = data.profile;
    const isCreator = profile.can_upload_videos;

    const toggleFollow = async () => {
        await api(`/api/profiles/${profile.id}/follow`, { method: data.is_following ? 'DELETE' : 'POST' });
        setData(await api(`/api/profiles/${profile.id}`));
    };

    return (
        <>
            <section className={`flex flex-col gap-8 py-8 sm:flex-row sm:items-center ${profile.is_admin ? 'mx-auto max-w-4xl' : ''}`}>
                <div className="relative shrink-0">
                    <div className={`${profile.is_admin ? 'size-40 sm:size-44' : 'size-48 sm:size-56'} grid place-items-center overflow-hidden rounded-full bg-stone-200`}>
                        {profile.avatar_url ? <img src={profile.avatar_url} alt="" className="h-full w-full object-cover" /> : <UserRound size={profile.is_admin ? 76 : 100} strokeWidth={1.2} />}
                    </div>
                    <ProfileBadge profile={profile} isCreator={isCreator} isOwner={data.is_owner} />
                </div>

                <div className="flex-1">
                    <div className="flex flex-wrap items-center gap-3">
                        <h1 className="text-3xl font-black sm:text-4xl">{profile.name}</h1>
                        <span className="border-l border-stone-400 pl-4 text-2xl font-light text-stone-500 sm:text-3xl">{profile.username}</span>
                    </div>
                    <p className="mt-1 flex items-center gap-1.5 text-sm font-bold text-orange-600">
                        {profile.is_admin && <ShieldCheck size={16} />}
                        {isCreator && <ChefHat size={16} />}
                        {profile.role_label}
                    </p>

                    {!profile.is_admin && (
                        <div className="mt-4 flex flex-wrap gap-8 text-xl sm:gap-12 sm:text-2xl">
                            <strong>{data.following_count} <span className="font-normal">Mengikuti</span></strong>
                            <strong>{data.followers_count} <span className="font-normal">Pengikut</span></strong>
                            <strong>{data.recipes.length} <span className="font-normal">Resep</span></strong>
                        </div>
                    )}

                    {data.is_owner && <div className="mt-5 flex flex-wrap gap-3">
                        <Link to="/profile/edit" className="wire-button bg-stone-200"><Pencil size={18} /> Edit profil</Link>
                    </div>}
                    {data.can_follow && <button type="button" onClick={toggleFollow} className={`wire-button mt-5 ${data.is_following ? 'wire-button-secondary' : 'wire-button-primary'}`}>{data.is_following ? 'Mengikuti' : 'Ikuti'}</button>}
                    {!user && !data.is_owner && !profile.is_admin && <Link to="/login" className="wire-button wire-button-primary mt-5">Masuk untuk mengikuti</Link>}
                    <p className="mt-5 max-w-2xl text-lg">{profile.bio || (profile.is_admin ? 'Administrator CuBu.' : 'Belum ada biodata.')}</p>
                </div>
            </section>

            {data.is_owner && data.notifications.length > 0 && (
                <section className="wire-panel mt-5 p-6">
                    <h2 className="flex items-center gap-2 text-xl font-black"><Bell /> Notifikasi</h2>
                    <div className="mt-4 space-y-2">{data.notifications.map((notification) => <div key={notification.id} className="rounded-xl bg-stone-100 p-3 text-sm">{notification.data.message || 'Pembaruan akun CuBu'}</div>)}</div>
                </section>
            )}

            {!profile.is_admin && <>
                <div className="mt-12 flex items-end justify-between border-b border-stone-200 pb-5">
                    <div>
                        <p className="text-sm font-bold uppercase tracking-wide text-orange-600">Karya dapur</p>
                        <h2 className="mt-1 text-3xl font-black">Resep {profile.username}</h2>
                    </div>
                    {data.is_owner && profile.can_publish_recipes && <Link to="/recipes/create" className="wire-button wire-button-primary"><Plus size={18} /> Buat resep</Link>}
                </div>
                <section className="mt-7 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    {data.recipes.map((recipe) => <div key={recipe.id}><RecipeCard recipe={recipe} />{data.is_owner && isCreator && !recipe.has_video && <Link to={`/recipes/${recipe.id}/video`} className="mt-2 flex items-center justify-center gap-2 rounded-xl border border-stone-300 p-2 text-sm font-bold"><Video size={16} /> Tambah video</Link>}</div>)}
                </section>
                {!data.recipes.length && <div className="wire-panel mt-7 p-10 text-center text-stone-500">Belum ada resep yang dipublikasikan.</div>}
            </>}
        </>
    );
}

function ProfileBadge({ profile, isCreator, isOwner }) {
    const classes = 'absolute right-1 bottom-2 grid size-14 place-items-center rounded-full border-4 border-white bg-orange-500 text-white';

    if (profile.is_admin) {
        return isOwner
            ? <Link to="/admin/creator-verifications" className={classes} title="Buka dashboard admin"><ShieldCheck /></Link>
            : <span className={classes} title="Admin CuBu"><ShieldCheck /></span>;
    }

    if (isCreator) {
        return <span className={classes} title="Creator terverifikasi"><ChefHat /></span>;
    }

    if (isOwner) {
        return <Link to="/creator/apply" className={classes} title="Ajukan verifikasi creator"><Plus /></Link>;
    }

    return null;
}

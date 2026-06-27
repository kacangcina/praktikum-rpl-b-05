import { AlertTriangle, Bell, CheckCircle2, ChefHat, ChevronDown, ChevronUp, CircleAlert, Info, Pencil, Plus, ShieldCheck, UserRound, Video } from 'lucide-react';
import { useEffect, useState } from 'react';
import { Link, useLocation, useParams } from 'react-router-dom';
import { api } from '../api.js';
import Alert from '../components/Alert.jsx';
import Loading from '../components/Loading.jsx';
import RecipeCard from '../components/RecipeCard.jsx';
import { useAuth } from '../context/AuthContext.jsx';

export default function Profile() {
    const { id } = useParams();
    const location = useLocation();
    const { refresh } = useAuth();
    const [data, setData] = useState(null);
    const [notificationsOpen, setNotificationsOpen] = useState(false);
    const [showAllNotifications, setShowAllNotifications] = useState(false);

    const load = () => api(`/api/profiles/${id}`).then(setData);

    useEffect(() => {
        let active = true;

        api(`/api/profiles/${id}`).then((result) => {
            if (active) setData(result);
        });

        return () => {
            active = false;
        };
    }, [id]);
    if (!data) return <Loading />;

    const profile = data.profile;
    const isCreator = profile.can_upload_videos;

    return (
        <>
            {location.state?.message && <Alert tone="success">{location.state.message}</Alert>}
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
                        <div className="mt-4 text-xl sm:text-2xl">
                            <strong>{data.recipes.length} <span className="font-normal">Resep</span></strong>
                        </div>
                    )}

                    {data.is_owner && <div className="mt-5 flex flex-wrap gap-3">
                        <Link to="/profile/edit" className="wire-button bg-stone-200"><Pencil size={18} /> Edit profil</Link>
                    </div>}
                    <p className="mt-5 max-w-2xl text-lg">{profile.bio || (profile.is_admin ? 'Administrator CuBu.' : 'Belum ada biodata.')}</p>
                </div>
            </section>

            {data.is_owner && data.notifications.length > 0 && (
                <section className="wire-panel mt-5 overflow-hidden">
                    <button
                        type="button"
                        onClick={() => setNotificationsOpen((open) => !open)}
                        className="flex w-full items-center justify-between gap-4 p-5 text-left hover:bg-stone-50"
                        aria-expanded={notificationsOpen}
                    >
                        <div>
                            <h2 className="flex items-center gap-2 text-lg font-black">
                                <Bell size={20} /> Pusat aktivitas
                                {data.unread_notifications_count > 0 && <span className="rounded-full bg-orange-500 px-2 py-0.5 text-xs text-white">{data.unread_notifications_count} baru</span>}
                            </h2>
                            <p className="mt-1 text-sm text-stone-500">{data.notifications[0]?.title} · {new Date(data.notifications[0]?.created_at).toLocaleDateString('id-ID')}</p>
                        </div>
                        {notificationsOpen ? <ChevronUp /> : <ChevronDown />}
                    </button>
                    {notificationsOpen && <div className="border-t border-stone-200 p-5">
                        <div className="mb-4 flex flex-wrap items-center justify-between gap-3">
                            <p className="text-sm text-stone-500">Keputusan admin dan perubahan penting pada akun atau resepmu.</p>
                            {data.unread_notifications_count > 0 && <button
                                type="button"
                                onClick={async () => {
                                    await api('/api/notifications/read', { method: 'POST' });
                                    await Promise.all([load(), refresh()]);
                                }}
                                className="rounded-xl border border-stone-300 px-3 py-2 text-xs font-bold"
                            >
                                Tandai semua dibaca
                            </button>}
                        </div>
                        <div className="space-y-2">
                            {(showAllNotifications ? data.notifications : data.notifications.slice(0, 3))
                                .map((notification) => <NotificationCard key={notification.id} notification={notification} />)}
                        </div>
                        {data.notifications.length > 3 && <button
                            type="button"
                            onClick={() => setShowAllNotifications((show) => !show)}
                            className="mt-4 w-full rounded-xl bg-stone-100 px-4 py-2 text-sm font-bold hover:bg-stone-200"
                        >
                            {showAllNotifications ? 'Tampilkan lebih sedikit' : `Lihat semua (${data.notifications.length})`}
                        </button>}
                    </div>}
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

const notificationCardStyles = {
    success: 'border-emerald-200 bg-emerald-50 text-emerald-800',
    warning: 'border-amber-200 bg-amber-50 text-amber-900',
    danger: 'border-red-200 bg-red-50 text-red-900',
    info: 'border-blue-200 bg-blue-50 text-blue-900',
};
const notificationCardIcons = {
    success: CheckCircle2,
    warning: AlertTriangle,
    danger: CircleAlert,
    info: Info,
};

function NotificationCard({ notification }) {
    const Icon = notificationCardIcons[notification.level] || Info;

    return (
        <article className={`relative rounded-xl border p-3 ${notificationCardStyles[notification.level] || notificationCardStyles.info} ${notification.read_at ? 'opacity-75' : 'shadow-sm'}`}>
            {!notification.read_at && <span className="absolute top-4 right-4 size-2.5 rounded-full bg-orange-500" title="Belum dibaca" />}
            <div className="flex items-start gap-3 pr-5">
                <Icon className="mt-0.5 shrink-0" size={21} />
                <div className="min-w-0 flex-1">
                    <h3 className="font-black">{notification.title}</h3>
                    <p className="mt-1 text-sm leading-5">{notification.message}</p>
                    {notification.reason && <div className="mt-2 rounded-lg bg-white/70 px-3 py-2 text-xs"><strong>Alasan admin:</strong> {notification.reason}</div>}
                    <div className="mt-2 flex flex-wrap items-center justify-between gap-3">
                        <time className="text-xs opacity-70">{new Date(notification.created_at).toLocaleString('id-ID')}</time>
                        {notification.action_url && <a href={notification.action_url} className="rounded-lg bg-white px-3 py-2 text-xs font-black shadow-sm hover:text-orange-600">{notification.action_label || 'Lihat detail'}</a>}
                    </div>
                </div>
            </div>
        </article>
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

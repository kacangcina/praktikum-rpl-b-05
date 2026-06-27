import { BadgeCheck, Bookmark, BookmarkCheck, ChefHat, Lock, Pencil, PlayCircle, Star, Trash2, UserRound } from 'lucide-react';
import { useCallback, useEffect, useRef, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import { api } from '../api.js';
import Alert from '../components/Alert.jsx';
import Loading from '../components/Loading.jsx';
import { useAuth } from '../context/AuthContext.jsx';

export default function RecipeDetail() {
    const { id } = useParams();
    const { user } = useAuth();
    const navigate = useNavigate();
    const videoRef = useRef(null);

    const [state, setState] = useState({
        recipe: null,
        thumbnailFailed: false,
        message: '',
        connectionError: '',
        rating: 5,
        comment: '',
        reviewError: ''
    });

    const load = useCallback(async () => {
        const data = await api(`/api/recipes/${id}`);
        setState(prev => ({
            ...prev,
            recipe: data.recipe,
            thumbnailFailed: false,
            rating: data.recipe.my_review?.rating || 5,
            comment: data.recipe.my_review?.comment || ''
        }));
    }, [id]);

    useEffect(() => { load(); }, [load, user?.id]);

    useEffect(() => {
        const handleOffline = () => {
            videoRef.current?.pause();
            setState((current) => ({
                ...current,
                connectionError: 'Koneksi terputus. Periksa koneksi internet kamu.',
            }));
        };
        const handleOnline = () => {
            setState((current) => ({ ...current, connectionError: '' }));
        };

        window.addEventListener('offline', handleOffline);
        window.addEventListener('online', handleOnline);

        return () => {
            window.removeEventListener('offline', handleOffline);
            window.removeEventListener('online', handleOnline);
        };
    }, []);

    if (!state.recipe) return <Loading />;

    const toggleSaved = async () => {
        await api(`/api/collection/${state.recipe.id}`, { method: state.recipe.is_saved ? 'DELETE' : 'POST' });
        setState(prev => ({ ...prev, message: prev.recipe.is_saved ? 'Resep dihapus dari koleksi.' : 'Resep disimpan ke koleksi.' }));
        load();
    };

    const submitReview = async (event) => {
        event.preventDefault();
        setState(prev => ({ ...prev, reviewError: '' }));
        try {
            await api(`/api/recipes/${state.recipe.id}/reviews`, {
                method: 'POST',
                body: { rating: state.rating, comment: state.comment },
            });
            setState(prev => ({ ...prev, message: 'Ulasan berhasil disimpan.' }));
            await load();
        } catch (caught) {
            setState(prev => ({ ...prev, reviewError: Object.values(caught.errors || {})[0]?.[0] || caught.message }));
        }
    };

    return (
        <>
            {state.message && <Alert tone="success">{state.message}</Alert>}
            {state.connectionError && <Alert>{state.connectionError}</Alert>}
            <Link to="/recipes" className="text-sm font-bold text-orange-600">← Kembali ke beranda</Link>
            <article className="mt-5 grid gap-8 lg:grid-cols-[1fr_340px]">
                <div>
                    <div className="overflow-hidden rounded-3xl bg-stone-200">
                        {state.recipe.video && user ? (
                            <video
                                ref={videoRef}
                                aria-label="Video Resep"
                                controls
                                preload="metadata"
                                poster={state.recipe.thumbnail_url}
                                onError={() => {
                                    if (!navigator.onLine) {
                                        setState((current) => ({
                                            ...current,
                                            connectionError: 'Koneksi terputus. Periksa koneksi internet kamu.',
                                        }));
                                    }
                                }}
                                className="aspect-video w-full bg-black"
                            >
                                <source src={state.recipe.video.url} type="video/mp4" />
                                <track kind="captions" srcLang="id" label="Bahasa Indonesia" />
                            </video>
                        ) : state.recipe.video ? (
                            <div className="grid aspect-video place-items-center p-8 text-center">
                                <div><Lock className="mx-auto" /><h2 className="mt-3 text-xl font-black">Video khusus pengguna CuBu</h2><Link to="/login" className="mt-4 inline-block rounded-xl bg-orange-500 px-5 py-3 font-bold text-white">Masuk</Link></div>
                            </div>
                        ) : state.recipe.thumbnail_url && !state.thumbnailFailed ? (
                            <img
                                src={state.recipe.thumbnail_url}
                                alt={state.recipe.title}
                                onError={() => setState((current) => ({ ...current, thumbnailFailed: true }))}
                                className="aspect-video w-full object-cover"
                            />
                        ) : <div className="checkerboard grid aspect-video place-items-center text-4xl font-black text-stone-400">CuBu</div>}
                    </div>
                    {state.recipe.video && user && (
                        <section className="mt-4 rounded-2xl border border-stone-200 bg-white p-5">
                            <p className="text-xs font-bold uppercase tracking-wide text-orange-600">Video kelas memasak</p>
                            <h2 className="mt-1 text-2xl font-black">{state.recipe.video.title}</h2>
                            <p className="mt-1 text-sm text-stone-500">Oleh {state.recipe.creator.username}</p>
                            <p className="mt-3 text-stone-600">{state.recipe.video.description}</p>
                            <div className="mt-4 flex flex-wrap items-center gap-4">
                                <button
                                    type="button"
                                    onClick={() => {
                                        if (!navigator.onLine) {
                                            setState((current) => ({
                                                ...current,
                                                connectionError: 'Koneksi terputus. Periksa koneksi internet kamu.',
                                            }));
                                            return;
                                        }

                                        videoRef.current?.play();
                                    }}
                                    className="wire-button wire-button-primary"
                                >
                                    <PlayCircle size={18} /> Tonton Video
                                </button>
                                <a href="#komentar-ulasan" className="text-sm font-bold text-orange-600">Lihat komentar dan ulasan</a>
                            </div>
                        </section>
                    )}

                    <div className="mt-6 flex items-start justify-between gap-5">
                        <div><p className="font-bold text-orange-600 capitalize">Resep {state.recipe.difficulty}</p><h1 className="mt-2 text-4xl font-black">{state.recipe.title}</h1><p className="mt-4 text-stone-600">{state.recipe.description}</p></div>
                        {user && <div className="flex gap-2">
                            <button type="button" onClick={toggleSaved} className="rounded-xl border border-stone-300 p-3" title="Koleksi" aria-label="Simpan ke Koleksi">{state.recipe.is_saved ? <BookmarkCheck /> : <Bookmark />}</button>
                            {state.recipe.can_edit_recipe && <Link to={`/recipes/${state.recipe.id}/edit`} className="rounded-xl bg-orange-500 p-3 text-white" title="Edit resep" aria-label="Edit Resep"><Pencil /></Link>}
                            {state.recipe.can_delete && <button type="button" aria-label="Hapus Resep" onClick={async () => {
                                if (!confirm(`Hapus resep ${state.recipe.title}?`)) return;
                                await api(`/api/recipes/${state.recipe.id}`, { method: 'DELETE' });
                                navigate(`/profile/${user.id}`);
                            }} className="rounded-xl bg-red-600 p-3 text-white"><Trash2 /></button>}
                        </div>}
                    </div>

                    <section className="mt-10">
                        <h2 className="text-2xl font-black">Langkah memasak</h2>
                        <ol className="mt-5 space-y-4">
                            {state.recipe.steps.map((step) => (
                                <li key={step.id} className="flex gap-4 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                                    <span className="grid size-12 shrink-0 place-items-center rounded-xl bg-orange-500 text-lg font-black text-white">{step.number}</span>
                                    <div><h3 className="font-bold">{step.title}</h3><p className="mt-1 text-stone-600">{step.description}</p></div>
                                </li>
                            ))}
                        </ol>
                    </section>
                </div>

                <aside className="space-y-5">
                    <section className="rounded-2xl bg-stone-900 p-6 text-white">
                        <p className="text-sm text-stone-400">Dibuat oleh</p>
                        <Link to={`/profile/${state.recipe.creator.id}`} className="mt-2 flex items-center gap-3 text-xl font-black">
                            <span className="grid size-10 place-items-center rounded-full bg-white/10">{state.recipe.creator.can_upload_videos ? <ChefHat size={22} /> : <UserRound size={22} />}</span>
                            {state.recipe.creator.username}
                        </Link>
                        <div className={`mt-3 flex w-fit items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold ${state.recipe.creator.can_upload_videos ? 'bg-orange-500 text-white' : 'bg-white/10 text-stone-300'}`}>
                            {state.recipe.creator.can_upload_videos ? <><BadgeCheck size={14} /> Creator terverifikasi</> : <><UserRound size={14} /> Pengguna CuBu</>}
                        </div>
                        <div className="mt-5 grid grid-cols-2 gap-3 text-center"><div className="rounded-xl bg-white/10 p-3"><strong>{state.recipe.estimated_time}</strong><small className="block">Menit</small></div><div className="rounded-xl bg-white/10 p-3 capitalize"><strong>{state.recipe.difficulty}</strong><small className="block">Kesulitan</small></div></div>
                    </section>
                    <ListPanel title="Alat masak" items={state.recipe.tools.map((item) => item.name)} />
                    <section className="rounded-2xl border border-stone-200 bg-white p-6"><h2 className="text-xl font-black">Bahan-bahan</h2><ul className="mt-4 divide-y divide-stone-100">{state.recipe.ingredients.map((item) => <li key={item.id} className="flex justify-between gap-3 py-3"><span>{item.name}</span><strong>{item.quantity}</strong></li>)}</ul></section>
                </aside>
            </article>

            <section id="komentar-ulasan" className="mt-20 scroll-mt-24 border-t border-stone-200 pt-10">
                <h2 className="text-3xl font-black">Komentar & Ulasan</h2>
                {user ? (
                    <form onSubmit={submitReview} className="mt-7 flex gap-5">
                        <span className="grid size-14 shrink-0 place-items-center rounded-full bg-stone-200"><UserRound /></span>
                        <div className="flex-1">
                            <h3 className="text-xl font-bold">{state.recipe.my_review ? 'Perbarui ulasan' : 'Beri rating'}</h3>
                            <div className="mt-2 flex gap-1">
                                {[1, 2, 3, 4, 5].map((value) => <button type="button" key={value} onClick={() => setState(prev => ({ ...prev, rating: value }))} aria-label={`${value} bintang`}><Star className={value <= state.rating ? 'fill-yellow-400 text-yellow-400' : 'text-stone-300'} /></button>)}
                            </div>
                            <textarea aria-label="Tulis komentar ulasan" value={state.comment} onChange={(event) => setState(prev => ({ ...prev, comment: event.target.value }))} className="wire-input mt-3 min-h-32" placeholder="Bagaimana hasil masakanmu? Tulis ulasan..." required />
                            {state.reviewError && <p className="mt-2 text-sm text-red-600">{state.reviewError}</p>}
                            <button type="submit" className="wire-button wire-button-primary mt-3">Simpan ulasan</button>
                        </div>
                    </form>
                ) : (
                    <div className="mt-6 rounded-2xl border border-stone-200 bg-stone-50 p-7 text-center">
                        <p className="text-stone-600">Masuk ke akun CuBu untuk memberi rating dan komentar.</p>
                        <Link to="/login" state={{ from: `/recipes/${state.recipe.id}` }} className="wire-button wire-button-primary mt-4">Masuk untuk berkomentar</Link>
                    </div>
                )}

                <div className="mt-10 space-y-4">
                    {state.recipe.reviews.map((review) => (
                        <article key={review.id} className="rounded-2xl border border-stone-200 bg-white p-5">
                            <div className="flex items-center justify-between gap-4"><Link to={`/profile/${review.user.id}`} className="font-black">{review.user.username}</Link><span className="flex gap-0.5">{[1, 2, 3, 4, 5].map((value) => <Star key={value} size={16} className={value <= review.rating ? 'fill-yellow-400 text-yellow-400' : 'text-stone-300'} />)}</span></div>
                            <p className="mt-3 text-stone-600">{review.comment}</p>
                        </article>
                    ))}
                    {!state.recipe.reviews.length && <p className="rounded-2xl bg-stone-50 p-6 text-center text-stone-500">Belum ada ulasan untuk resep ini.</p>}
                </div>
            </section>
        </>
    );
}

function ListPanel({ title, items }) {
    return <section className="rounded-2xl border border-stone-200 bg-white p-6"><h2 className="text-xl font-black">{title}</h2><ul className="mt-4 list-inside list-disc space-y-2 text-stone-600">{items.map((item) => <li key={item}>{item}</li>)}</ul></section>;
}

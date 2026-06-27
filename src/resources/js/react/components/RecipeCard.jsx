import { Bookmark, Clock, Image as ImageIcon, PlayCircle, Star } from 'lucide-react';
import { useState } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { api } from '../api.js';
import { useAuth } from '../context/AuthContext.jsx';

export default function RecipeCard({ recipe, onSavedChange }) {
    const { user } = useAuth();
    const location = useLocation();
    const navigate = useNavigate();
    const [saved, setSaved] = useState(Boolean(recipe.is_saved));
    const [saving, setSaving] = useState(false);
    const [message, setMessage] = useState('');
    const [thumbnailFailed, setThumbnailFailed] = useState(false);

    async function toggleSaved() {
        if (!user) {
            navigate('/login', {
                state: { from: `${location.pathname}${location.search}` },
            });
            return;
        }

        if (saving) return;

        setSaving(true);
        try {
            const result = await api(`/api/collection/${recipe.id}`, {
                method: saved ? 'DELETE' : 'POST',
            });
            const nextSaved = !saved;
            setSaved(nextSaved);
            setMessage(result.message);
            onSavedChange?.(recipe.id, nextSaved, result.message);
        } finally {
            setSaving(false);
        }
    }

    return (
        <article className="group overflow-hidden rounded-2xl border border-stone-500 bg-white transition hover:-translate-y-1 hover:shadow-lg">
            <div className="checkerboard relative aspect-[4/3] overflow-hidden">
                <Link to={`/recipes/${recipe.id}`} aria-label={`Lihat resep ${recipe.title}`} className="block h-full">
                    {recipe.thumbnail_url && !thumbnailFailed
                        ? <img src={recipe.thumbnail_url} alt={recipe.title} onError={() => setThumbnailFailed(true)} className="h-full w-full object-cover transition group-hover:scale-105" />
                        : <div className="grid h-full place-items-center text-stone-400"><ImageIcon size={44} /></div>}
                </Link>
                <button
                    type="button"
                    onClick={toggleSaved}
                    disabled={saving}
                    aria-label={saved ? `Hapus ${recipe.title} dari koleksi` : `Simpan ${recipe.title} ke koleksi`}
                    aria-pressed={saved}
                    title={saved ? 'Hapus dari Koleksi Saya' : 'Simpan ke Koleksi Saya'}
                    className={`absolute top-3 left-3 grid size-10 place-items-center rounded-b-md text-white transition disabled:cursor-wait disabled:opacity-60 ${saved ? 'bg-orange-500' : 'bg-black hover:bg-orange-500'}`}
                >
                    <Bookmark size={20} className={saved ? 'fill-current' : ''} />
                </button>
                {recipe.has_video && <span className="absolute top-3 right-3 flex items-center gap-1 rounded-full bg-black px-3 py-1 text-xs font-bold text-white"><PlayCircle size={14} /> Video</span>}
                <span className="absolute bottom-2 left-2 flex items-center gap-1 rounded-full border border-stone-600 bg-white px-2 py-0.5 text-xs font-bold">
                    <Star size={13} className="fill-yellow-300 text-yellow-400" />
                    {recipe.average_rating !== null && recipe.average_rating !== undefined
                        ? `${recipe.average_rating} (${recipe.reviews_count})`
                        : 'Baru'}
                </span>
            </div>
            <div className="rounded-t-2xl border-t border-stone-500 p-4">
                <h3 className="text-lg font-bold"><Link to={`/recipes/${recipe.id}`} className="hover:text-orange-600">{recipe.title}</Link></h3>
                <p className="text-xs text-stone-600">{recipe.creator?.username || 'Koki CuBu'}</p>
                <div className="mt-5 flex items-center justify-between text-xs text-stone-500">
                    <span className="flex items-center gap-1"><Clock size={15} /> {recipe.estimated_time} menit</span>
                    <span className="rounded-full border border-stone-500 px-3 py-1 capitalize">{recipe.difficulty}</span>
                </div>
            </div>
            {message && <p role="status" className="border-t border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-800">{message}</p>}
        </article>
    );
}

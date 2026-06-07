import { Bookmark, Clock, Image as ImageIcon, PlayCircle, Star } from 'lucide-react';
import { Link } from 'react-router-dom';

export default function RecipeCard({ recipe }) {
    return (
        <Link to={`/recipes/${recipe.id}`} className="group overflow-hidden rounded-2xl border border-stone-500 bg-white transition hover:-translate-y-1 hover:shadow-lg">
            <div className="checkerboard relative aspect-[4/3] overflow-hidden">
                {recipe.thumbnail_url
                    ? <img src={recipe.thumbnail_url} alt={recipe.title} className="h-full w-full object-cover transition group-hover:scale-105" />
                    : <div className="grid h-full place-items-center text-stone-400"><ImageIcon size={44} /></div>}
                <span className="absolute top-3 left-3 grid size-9 place-items-center rounded-b-md bg-black text-white"><Bookmark size={20} /></span>
                {recipe.has_video && <span className="absolute top-3 right-3 flex items-center gap-1 rounded-full bg-black px-3 py-1 text-xs font-bold text-white"><PlayCircle size={14} /> Video</span>}
                <span className="absolute bottom-2 left-2 flex items-center gap-1 rounded-full border border-stone-600 bg-white px-2 py-0.5 text-xs font-bold"><Star size={13} className="fill-yellow-300 text-yellow-400" /> Baru</span>
            </div>
            <div className="rounded-t-2xl border-t border-stone-500 p-4">
                <h3 className="text-lg font-bold">{recipe.title}</h3>
                <p className="text-xs text-stone-600">{recipe.creator?.username || 'Koki CuBu'}</p>
                <div className="mt-5 flex items-center justify-between text-xs text-stone-500">
                    <span className="flex items-center gap-1"><Clock size={15} /> {recipe.estimated_time} menit</span>
                    <span className="rounded-full border border-stone-500 px-3 py-1 capitalize">{recipe.difficulty}</span>
                </div>
            </div>
        </Link>
    );
}

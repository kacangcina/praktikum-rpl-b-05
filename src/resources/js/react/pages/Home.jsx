import { ChevronRight, Search } from 'lucide-react';
import { useEffect, useState } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import { api } from '../api.js';
import Loading from '../components/Loading.jsx';
import RecipeCard from '../components/RecipeCard.jsx';

const ingredients = ['Ayam', 'Daging Sapi', 'Telur', 'Tahu', 'Tempe', 'Cabai', 'Bawang Merah'];

export default function Home() {
    const [params, setParams] = useSearchParams();
    const [data, setData] = useState(null);
    const [customIngredient, setCustomIngredient] = useState('');
    const [showIngredientInput, setShowIngredientInput] = useState(false);
    const query = params.get('q') || '';
    const sort = params.get('sort') === 'popular' ? 'popular' : 'latest';

    useEffect(() => {
        setData(null);
        const search = new URLSearchParams();
        if (query) search.set('q', query);
        search.set('sort', sort);
        api(`/api/recipes?${search}`).then(setData);
    }, [query, sort]);

    if (!data) return <Loading />;

    return (
        <>
            {!query && (
                <section className="relative min-h-[500px] overflow-hidden rounded-[3rem] border border-stone-500 bg-stone-200">
                    {data.featured?.thumbnail_url
                        ? <img src={data.featured.thumbnail_url} alt={data.featured.title} className="absolute inset-0 h-full w-full object-cover" />
                        : <div className="checkerboard absolute inset-0" />}
                    <div className="absolute inset-0 bg-gradient-to-r from-black/75 via-black/30 to-transparent" />
                    <div className="relative flex min-h-[500px] max-w-xl flex-col justify-end overflow-hidden p-8 text-white sm:p-12">
                        <span className="w-fit rounded-full border border-white/70 bg-white/15 px-5 py-2 text-sm font-bold backdrop-blur">Resep Nusantara</span>
                        <h1 className="mt-5 text-3xl font-black sm:text-5xl">{data.featured?.title || 'Temukan resep favoritmu'}</h1>
                        <p className="mt-3 line-clamp-3 max-w-full text-sm text-white/85 sm:text-base">{data.featured?.description || 'Inspirasi masakan rumahan pilihan untuk keluarga.'}</p>
                        {data.featured && <Link to={`/recipes/${data.featured.id}`} className="wire-button wire-button-primary mt-6 w-fit">Lihat resep <ChevronRight size={18} /></Link>}
                    </div>
                </section>
            )}

            <section className="wire-panel mt-8 p-6 sm:p-8">
                <div className="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 className="flex items-start gap-2 text-xl font-black sm:items-center sm:text-2xl"><Search className="mt-1 shrink-0 text-orange-500 sm:mt-0" /> Punya bahan apa di kulkas?</h2>
                        <p className="mt-1 text-stone-500">Pilih bahan yang kamu miliki, CuBu carikan resepnya.</p>
                    </div>
                    <button type="button" onClick={() => setParams(sort === 'popular' ? { sort } : {})} className="font-bold text-orange-500">Lihat Semua Bahan</button>
                </div>
                <div className="mt-5 flex flex-wrap gap-3">
                    {ingredients.map((ingredient) => (
                        <button key={ingredient} onClick={() => setParams({ q: ingredient, ...(sort === 'popular' ? { sort } : {}) })} className={`rounded-xl border px-4 py-2 text-sm font-semibold ${query.toLowerCase() === ingredient.toLowerCase() ? 'border-orange-500 bg-orange-50 text-orange-600' : 'border-stone-300 hover:border-orange-500 hover:text-orange-600'}`}>
                            {ingredient}
                        </button>
                    ))}
                    {showIngredientInput ? (
                        <form onSubmit={(event) => {
                            event.preventDefault();
                            const value = customIngredient.trim();
                            if (value) setParams({ q: value, ...(sort === 'popular' ? { sort } : {}) });
                        }} className="flex min-w-60">
                            <input autoFocus value={customIngredient} onChange={(event) => setCustomIngredient(event.target.value)} className="min-w-0 flex-1 rounded-l-xl border border-stone-400 px-3 py-2 text-sm outline-none focus:border-orange-500" placeholder="Contoh: Kentang" />
                            <button className="rounded-r-xl bg-orange-500 px-4 text-sm font-bold text-white">Cari</button>
                        </form>
                    ) : (
                        <button type="button" onClick={() => setShowIngredientInput(true)} className="rounded-xl border border-dashed border-stone-400 px-4 py-2 text-sm font-semibold text-stone-500">+ Ketik Bahan Lain</button>
                    )}
                </div>
            </section>

            <div className="mt-12 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 className="text-3xl font-medium">{query ? `Hasil untuk "${query}"` : 'Rekomendasi terkini'}</h2>
                    <p className="text-stone-600">{query ? 'Resep yang sesuai dengan pencarianmu' : 'Resep pilihan terbaik untukmu hari ini'}</p>
                </div>
                <div className="flex items-center gap-3">
                    <button onClick={() => setParams({ ...(query ? { q: query } : {}), sort: 'latest' })} className={`rounded-lg px-6 py-2 ${sort === 'latest' ? 'bg-orange-100 text-orange-600' : 'bg-stone-100 text-stone-500'}`}>Terbaru</button>
                    <button onClick={() => setParams({ ...(query ? { q: query } : {}), sort: 'popular' })} className={`rounded-lg px-6 py-2 ${sort === 'popular' ? 'bg-orange-100 text-orange-600' : 'bg-stone-100 text-stone-500'}`}>Terpopuler</button>
                </div>
            </div>

            {data.recipes.length ? (
                <section className="mt-7 grid gap-x-20 gap-y-16 sm:grid-cols-2 lg:grid-cols-3">
                    {data.recipes.map((recipe) => <RecipeCard key={recipe.id} recipe={recipe} />)}
                </section>
            ) : (
                <div className="wire-panel mt-8 p-12 text-center text-stone-500">Tidak ada resep yang ditemukan.</div>
            )}
        </>
    );
}

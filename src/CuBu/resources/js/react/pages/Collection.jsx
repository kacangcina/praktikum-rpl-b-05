import { useEffect, useState } from 'react';
import { api } from '../api.js';
import Loading from '../components/Loading.jsx';
import RecipeCard from '../components/RecipeCard.jsx';

export default function Collection() {
    const [data, setData] = useState(null);
    useEffect(() => { api('/api/collection').then(setData); }, []);
    if (!data) return <Loading />;

    return (
        <>
            <h1 className="py-10 text-center text-5xl font-black">{data.collection.name}</h1>
            <section className="mt-7 grid gap-x-20 gap-y-16 sm:grid-cols-2 lg:grid-cols-3">
                {data.recipes.map((recipe) => <RecipeCard key={recipe.id} recipe={recipe} />)}
            </section>
            {!data.recipes.length && <div className="mt-8 rounded-2xl bg-white p-10 text-center text-stone-500">Belum ada resep tersimpan.</div>}
        </>
    );
}

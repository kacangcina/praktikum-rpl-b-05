import { ImagePlus, Plus, Trash2, Video } from 'lucide-react';
import { useEffect, useState } from 'react';
import { Link, Navigate, useNavigate, useParams } from 'react-router-dom';
import { api } from '../api.js';
import Alert from '../components/Alert.jsx';
import Loading from '../components/Loading.jsx';
import { useAuth } from '../context/AuthContext.jsx';
import { Field } from './Login.jsx';

const blankIngredient = () => ({ name: '', quantity: '' });
const blankStep = () => ({ title: '', description: '' });

export default function RecipeForm() {
    const { user } = useAuth();
    const navigate = useNavigate();
    const { id } = useParams();
    const editing = Boolean(id);
    const [recipe, setRecipe] = useState(null);
    const [tools, setTools] = useState(['']);
    const [ingredients, setIngredients] = useState([blankIngredient()]);
    const [steps, setSteps] = useState([blankStep()]);
    const [error, setError] = useState(null);
    const [busy, setBusy] = useState(false);

    useEffect(() => {
        if (!editing) return;

        api(`/api/recipes/${id}`).then((data) => {
            setRecipe(data.recipe);
            setTools(data.recipe.tools.map((tool) => tool.name));
            setIngredients(data.recipe.ingredients.map((item) => ({ name: item.name, quantity: item.quantity })));
            setSteps(data.recipe.steps.map((step) => ({ title: step.title, description: step.description })));
        });
    }, [editing, id]);

    if (user && !user.can_publish_recipes) return <Navigate to={`/profile/${user.id}`} replace />;
    if (editing && !recipe) return <Loading />;
    if (editing && recipe && !recipe.can_edit_recipe) return <Navigate to={`/recipes/${id}`} replace />;

    const submit = async (event) => {
        event.preventDefault();
        setBusy(true);
        setError(null);
        const data = new FormData(event.currentTarget);
        tools.forEach((tool) => data.append('tools[]', tool));
        ingredients.forEach((item) => {
            data.append('ingredient_names[]', item.name);
            data.append('ingredient_quantities[]', item.quantity);
        });
        steps.forEach((step) => {
            data.append('step_titles[]', step.title);
            data.append('steps[]', step.description);
        });

        try {
            const result = await api(editing ? `/api/recipes/${id}` : '/api/recipes', {
                method: 'POST',
                headers: editing ? { 'X-HTTP-Method-Override': 'PUT' } : {},
                body: data,
            });
            navigate(`/recipes/${result.recipe_id}`);
        } catch (caught) {
            setError(Object.values(caught.errors || {})[0]?.[0] || caught.message);
        } finally {
            setBusy(false);
        }
    };

    return (
        <div>
            <h1 className="border-b border-stone-400 pb-6 text-4xl font-medium">{editing ? 'Edit resep' : 'Buat resep baru'}</h1>
            <form onSubmit={submit} className="mt-10">
                {error && <Alert>{error}</Alert>}
                <MediaUpload canUploadVideo={user?.can_upload_videos} recipe={recipe} />

                <div className="mt-10 grid gap-10 lg:grid-cols-2">
                    <section className="wire-panel space-y-6 p-6 sm:p-8">
                        <h2 className="border-b border-stone-200 pb-4 text-2xl font-black">Informasi resep</h2>
                        <Field label="Judul resep"><input name="title" defaultValue={recipe?.title || ''} placeholder="Nama resep..." required /></Field>
                        <Field label="Deskripsi"><textarea name="description" defaultValue={recipe?.description || ''} rows="4" placeholder="Ceritakan singkat tentang resep ini..." required /></Field>
                        <div className="grid gap-4 sm:grid-cols-2">
                            <Field label="Tingkat kesulitan"><select name="difficulty" defaultValue={recipe?.difficulty || ''} required><option value="">Pilih kesulitan</option><option value="mudah">Mudah</option><option value="sedang">Sedang</option><option value="sulit">Sulit</option></select></Field>
                            <Field label="Estimasi waktu (menit)"><input type="number" name="estimated_time" defaultValue={recipe?.estimated_time || ''} min="1" placeholder="30" required /></Field>
                        </div>
                    </section>

                    <section className="wire-panel space-y-9 p-6 sm:p-8">
                        <CompactPanel title="Alat masak" onAdd={() => setTools([...tools, ''])}>
                            {tools.map((tool, index) => <Row key={index} onRemove={() => setTools(tools.filter((_, itemIndex) => itemIndex !== index))}><input className="field-control" value={tool} onChange={(event) => setTools(tools.map((item, itemIndex) => itemIndex === index ? event.target.value : item))} placeholder="Nama alat..." required /></Row>)}
                        </CompactPanel>

                        <CompactPanel title="Bahan" onAdd={() => setIngredients([...ingredients, blankIngredient()])}>
                            {ingredients.map((item, index) => <Row key={index} onRemove={() => setIngredients(ingredients.filter((_, itemIndex) => itemIndex !== index))}><input className="field-control" value={item.name} onChange={(event) => setIngredients(ingredients.map((entry, itemIndex) => itemIndex === index ? { ...entry, name: event.target.value } : entry))} placeholder="Nama bahan..." required /><input className="field-control" value={item.quantity} onChange={(event) => setIngredients(ingredients.map((entry, itemIndex) => itemIndex === index ? { ...entry, quantity: event.target.value } : entry))} placeholder="Takaran..." required /></Row>)}
                        </CompactPanel>
                    </section>
                </div>

                <section className="wire-panel mt-10 p-6 sm:p-8">
                    <h2 className="border-b border-stone-200 pb-4 text-2xl font-black">Langkah Memasak</h2>
                    <div className="mt-5 space-y-5">
                        {steps.map((step, index) => (
                            <div key={index} className="flex items-stretch gap-4">
                                <div className="flex min-w-0 flex-1 items-center gap-5 rounded-[2rem] bg-stone-200 p-4">
                                    <span className="grid size-20 shrink-0 place-items-center rounded-2xl bg-orange-500 text-4xl font-black text-white">{index + 1}</span>
                                    <div className="grid flex-1 gap-2"><input className="field-control bg-white" value={step.title} onChange={(event) => setSteps(steps.map((entry, itemIndex) => itemIndex === index ? { ...entry, title: event.target.value } : entry))} placeholder="Judul langkah" required /><textarea className="field-control bg-white" value={step.description} onChange={(event) => setSteps(steps.map((entry, itemIndex) => itemIndex === index ? { ...entry, description: event.target.value } : entry))} placeholder="Deskripsi langkah..." required /></div>
                                </div>
                                <button type="button" onClick={() => setSteps(steps.filter((_, itemIndex) => itemIndex !== index))} className="rounded-[2rem] bg-stone-200 px-5 text-stone-700"><Trash2 /></button>
                            </div>
                        ))}
                    </div>
                    <button type="button" onClick={() => setSteps([...steps, blankStep()])} className="mt-6 w-full rounded-[2rem] bg-stone-600 py-5 text-xl font-medium text-white">+ Tambah langkah</button>
                </section>

                <div className="mt-10 flex justify-end gap-4">
                    <Link to={editing ? `/recipes/${id}` : '/profile'} className="wire-button bg-stone-200">Batal</Link>
                    <button disabled={busy} className="wire-button wire-button-primary">{busy ? 'Menyimpan...' : editing ? 'Simpan perubahan' : 'Publikasikan'}</button>
                </div>
            </form>
        </div>
    );
}

function MediaUpload({ canUploadVideo, recipe }) {
    return (
        <section className="wire-panel p-6 sm:p-8">
            <div><h2 className="text-2xl font-black">Media resep</h2><p className="mt-1 text-sm text-stone-500">{recipe ? 'Biarkan kosong jika media lama tidak ingin diganti.' : canUploadVideo ? 'Foto tetap menjadi thumbnail dan poster ketika video diputar.' : 'Tambahkan foto utama untuk resep.'}</p></div>
            {recipe && <div className="mt-4 flex flex-wrap gap-2 text-sm"><span className="rounded-full bg-stone-100 px-3 py-1">{recipe.thumbnail_url ? 'Foto sudah tersedia' : 'Belum ada foto'}</span>{canUploadVideo && <span className="rounded-full bg-stone-100 px-3 py-1">{recipe.has_video ? 'Video sudah tersedia' : 'Belum ada video'}</span>}</div>}
            <div className={`mt-6 grid gap-5 ${canUploadVideo ? 'md:grid-cols-2' : ''}`}>
                <label className="flex min-h-56 cursor-pointer flex-col items-center justify-center rounded-3xl border-2 border-dashed border-stone-400 bg-stone-50 px-6 text-center hover:border-orange-500">
                    <ImagePlus size={54} strokeWidth={1.3} />
                    <strong className="mt-4 text-lg">Pilih foto thumbnail</strong>
                    <span className="mt-1 text-xs text-stone-500">JPG, PNG, atau WEBP. Maksimal 5 MB</span>
                    <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp" className="mt-5 max-w-full text-sm" />
                </label>
                {canUploadVideo && <label className="flex min-h-56 cursor-pointer flex-col items-center justify-center rounded-3xl border-2 border-dashed border-stone-400 bg-stone-50 px-6 text-center hover:border-orange-500">
                    <Video size={54} strokeWidth={1.3} />
                    <strong className="mt-4 text-lg">Pilih video MP4</strong>
                    <span className="mt-1 text-xs text-stone-500">Video tidak menghapus foto. Maksimal 500 MB</span>
                    <input type="file" name="video" accept="video/mp4" className="mt-5 max-w-full text-sm" />
                </label>}
            </div>
        </section>
    );
}

function CompactPanel({ title, onAdd, children }) {
    return <section><h2 className="text-2xl">{title}</h2><div className="mt-3 space-y-3">{children}</div><button type="button" onClick={onAdd} className="mt-3 flex items-center gap-2 rounded-xl border border-stone-500 px-4 py-2 font-bold"><Plus size={18} /> Tambah</button></section>;
}

function Row({ onRemove, children }) {
    return <div className="flex flex-col items-stretch gap-2 sm:flex-row sm:items-start">{children}<button type="button" onClick={onRemove} className="self-end rounded-xl border border-stone-300 p-3 text-red-600 sm:self-auto"><Trash2 size={18} /></button></div>;
}

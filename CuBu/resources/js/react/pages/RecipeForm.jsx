import { Plus, Trash2 } from 'lucide-react';
import { useEffect, useState } from 'react';
import { Link, Navigate, useNavigate, useParams } from 'react-router-dom';
import { api } from '../api.js';
import Alert from '../components/Alert.jsx';
import FileUploadField from '../components/FileUploadField.jsx';
import Loading from '../components/Loading.jsx';
import { useAuth } from '../context/AuthContext.jsx';
import { Field } from './Login.jsx';

const blankTool = () => ({ id: crypto.randomUUID(), name: '' });
const blankIngredient = () => ({ id: crypto.randomUUID(), name: '', quantity: '' });
const blankStep = () => ({ id: crypto.randomUUID(), title: '', description: '' });

export default function RecipeForm() {
    const { user } = useAuth();
    const navigate = useNavigate();
    const { id } = useParams();
    const editing = Boolean(id);

    const [form, setForm] = useState({
        recipe: null,
        tools: [blankTool()],
        ingredients: [blankIngredient()],
        steps: [blankStep()],
        error: null,
        errors: {},
        busy: false
    });

    useEffect(() => {
        if (!editing) return;

        api(`/api/recipes/${id}`).then((data) => {
            setForm(prev => ({
                ...prev,
                recipe: data.recipe,
                tools: data.recipe.tools.map((tool) => ({ id: crypto.randomUUID(), name: tool.name })),
                ingredients: data.recipe.ingredients.map((item) => ({ id: crypto.randomUUID(), name: item.name, quantity: item.quantity })),
                steps: data.recipe.steps.map((step) => ({ id: crypto.randomUUID(), title: step.title, description: step.description }))
            }));
        });
    }, [editing, id]);

    if (user && !user.can_publish_recipes) return <Navigate to={`/profile/${user.id}`} replace />;
    if (editing && !form.recipe) return <Loading />;
    if (editing && form.recipe && !form.recipe.can_edit_recipe) return <Navigate to={`/recipes/${id}`} replace />;

    const submit = async (event) => {
        event.preventDefault();
        setForm(prev => ({ ...prev, busy: true, error: null, errors: {} }));
        const data = new FormData(event.currentTarget);
        form.tools.forEach((tool) => data.append('tools[]', tool.name));
        form.ingredients.forEach((item) => {
            data.append('ingredient_names[]', item.name);
            data.append('ingredient_quantities[]', item.quantity);
        });
        form.steps.forEach((step) => {
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
            const errors = caught.errors || {};
            setForm(prev => ({
                ...prev,
                error: Object.keys(errors).length ? null : caught.message,
                errors,
                busy: false,
            }));
        }
    };

    const fieldError = (...fields) => {
        for (const field of fields) {
            const message = form.errors[field]?.[0];
            if (message) return message;
        }

        return null;
    };

    return (
        <div>
            <h1 className="border-b border-stone-400 pb-6 text-4xl font-medium">{editing ? 'Edit resep' : 'Buat resep baru'}</h1>
            <form onSubmit={submit} noValidate className="mt-10">
                {form.error && <Alert>{form.error}</Alert>}
                <MediaUpload
                    canUploadVideo={user?.can_upload_videos}
                    recipe={form.recipe}
                    thumbnailError={fieldError('thumbnail')}
                    videoError={fieldError('video')}
                />

                <div className="mt-10 grid gap-10 lg:grid-cols-2">
                    <section className="wire-panel space-y-6 p-6 sm:p-8">
                        <h2 className="border-b border-stone-200 pb-4 text-2xl font-black">Informasi resep</h2>
                        <Field label="Judul resep" error={fieldError('title')}><input aria-label="Judul resep" name="title" defaultValue={form.recipe?.title || ''} placeholder="Nama resep..." required /></Field>
                        <Field label="Deskripsi" error={fieldError('description')}><textarea aria-label="Deskripsi Resep" name="description" defaultValue={form.recipe?.description || ''} rows="4" placeholder="Ceritakan singkat tentang resep ini..." required /></Field>
                        <div className="grid gap-4 sm:grid-cols-2">
                            <Field label="Tingkat kesulitan" error={fieldError('difficulty')}><select aria-label="Tingkat Kesulitan" name="difficulty" defaultValue={form.recipe?.difficulty || ''} required><option value="">Pilih kesulitan</option><option value="mudah">Mudah</option><option value="sedang">Sedang</option><option value="sulit">Sulit</option></select></Field>
                            <Field label="Estimasi waktu (menit)" error={fieldError('estimated_time')}><input aria-label="Estimasi Waktu" type="number" name="estimated_time" defaultValue={form.recipe?.estimated_time || ''} min="1" placeholder="30" required /></Field>
                        </div>
                    </section>

                    <section className="wire-panel space-y-9 p-6 sm:p-8">
                        <CompactPanel title="Alat masak" onAdd={() => setForm(prev => ({ ...prev, tools: [...prev.tools, blankTool()] }))}>
                            {form.tools.map((tool, index) => <Row key={tool.id} error={fieldError(`tools.${index}`, 'tools')} onRemove={() => setForm(prev => ({ ...prev, tools: prev.tools.filter((_, i) => i !== index) }))}><input aria-label="Nama Alat" className="field-control" value={tool.name} onChange={(event) => setForm(prev => ({ ...prev, tools: prev.tools.map((t, i) => i === index ? { ...t, name: event.target.value } : t) }))} placeholder="Nama alat..." required /></Row>)}
                        </CompactPanel>

                        <CompactPanel title="Bahan" onAdd={() => setForm(prev => ({ ...prev, ingredients: [...prev.ingredients, blankIngredient()] }))}>
                            {form.ingredients.map((item, index) => <Row key={item.id} error={fieldError(`ingredient_names.${index}`, `ingredient_quantities.${index}`, 'ingredient_names', 'ingredient_quantities')} onRemove={() => setForm(prev => ({ ...prev, ingredients: prev.ingredients.filter((_, i) => i !== index) }))}><input aria-label="Nama Bahan" className="field-control" value={item.name} onChange={(event) => setForm(prev => ({ ...prev, ingredients: prev.ingredients.map((entry, i) => i === index ? { ...entry, name: event.target.value } : entry) }))} placeholder="Nama bahan..." required /><input aria-label="Takaran Bahan" className="field-control" value={item.quantity} onChange={(event) => setForm(prev => ({ ...prev, ingredients: prev.ingredients.map((entry, i) => i === index ? { ...entry, quantity: event.target.value } : entry) }))} placeholder="Takaran..." required /></Row>)}
                        </CompactPanel>
                    </section>
                </div>

                <section className="wire-panel mt-10 p-6 sm:p-8">
                    <h2 className="border-b border-stone-200 pb-4 text-2xl font-black">Langkah Memasak</h2>
                    <div className="mt-5 space-y-5">
                        {form.steps.map((step, index) => (
                            <div key={step.id} className="flex items-stretch gap-4">
                                <div className="min-w-0 flex-1">
                                <div className="flex items-center gap-5 rounded-[2rem] bg-stone-200 p-4">
                                    <span className="grid size-20 shrink-0 place-items-center rounded-2xl bg-orange-500 text-4xl font-black text-white">{index + 1}</span>
                                    <div className="grid flex-1 gap-2"><input aria-label={`Judul langkah ${index + 1}`} className="field-control bg-white" value={step.title} onChange={(event) => setForm(prev => ({ ...prev, steps: prev.steps.map((entry, i) => i === index ? { ...entry, title: event.target.value } : entry) }))} placeholder="Judul langkah" required /><textarea aria-label={`Deskripsi langkah ${index + 1}`} className="field-control bg-white" value={step.description} onChange={(event) => setForm(prev => ({ ...prev, steps: prev.steps.map((entry, i) => i === index ? { ...entry, description: event.target.value } : entry) }))} placeholder="Deskripsi langkah..." required /></div>
                                </div>
                                {fieldError(`step_titles.${index}`, `steps.${index}`, 'step_titles', 'steps') && <p className="mt-1 px-4 text-sm text-red-600">{fieldError(`step_titles.${index}`, `steps.${index}`, 'step_titles', 'steps')}</p>}
                                </div>
                                <button type="button" aria-label="Hapus Langkah" onClick={() => setForm(prev => ({ ...prev, steps: prev.steps.filter((_, i) => i !== index) }))} className="rounded-[2rem] bg-stone-200 px-5 text-stone-700"><Trash2 /></button>
                            </div>
                        ))}
                    </div>
                    <button type="button" onClick={() => setForm(prev => ({ ...prev, steps: [...prev.steps, blankStep()] }))} className="mt-6 w-full rounded-[2rem] bg-stone-600 py-5 text-xl font-medium text-white">+ Tambah langkah</button>
                </section>

                <div className="mt-10 flex justify-end gap-4">
                    <Link to={editing ? `/recipes/${id}` : '/profile'} className="wire-button bg-stone-200">Batal</Link>
                    <button type="submit" disabled={form.busy} className="wire-button wire-button-primary">{form.busy ? 'Menyimpan...' : editing ? 'Simpan perubahan' : 'Publikasikan'}</button>
                </div>
            </form>
        </div>
    );
}

function MediaUpload({ canUploadVideo, recipe, thumbnailError, videoError }) {
    return (
        <section className="wire-panel p-6 sm:p-8">
            <div><h2 className="text-2xl font-black">Media resep</h2><p className="mt-1 text-sm text-stone-500">{recipe ? 'Biarkan kosong jika media lama tidak ingin diganti.' : canUploadVideo ? 'Foto tetap menjadi thumbnail dan poster ketika video diputar.' : 'Tambahkan foto utama untuk resep.'}</p></div>
            {recipe && <div className="mt-4 flex flex-wrap gap-2 text-sm"><span className="rounded-full bg-stone-100 px-3 py-1">{recipe.thumbnail_url ? 'Foto sudah tersedia' : 'Belum ada foto'}</span>{canUploadVideo && <span className="rounded-full bg-stone-100 px-3 py-1">{recipe.has_video ? 'Video sudah tersedia' : 'Belum ada video'}</span>}</div>}
            <div className={`mt-6 grid gap-5 ${canUploadVideo ? 'md:grid-cols-2' : ''}`}>
                <FileUploadField name="thumbnail" accept="image/jpeg,image/png,image/webp" label="Foto thumbnail" hint="JPG, PNG, atau WEBP. Maksimal 5 MB" error={thumbnailError} kind="image" />
                {canUploadVideo && <FileUploadField name="video" accept="video/mp4" label="Video resep" hint="MP4. Maksimal 500 MB" error={videoError} kind="video" />}
            </div>
        </section>
    );
}

function CompactPanel({ title, onAdd, children }) {
    return <section><h2 className="text-2xl">{title}</h2><div className="mt-3 space-y-3">{children}</div><button type="button" onClick={onAdd} className="mt-3 flex items-center gap-2 rounded-xl border border-stone-500 px-4 py-2 font-bold"><Plus size={18} /> Tambah</button></section>;
}

function Row({ onRemove, children, error }) {
    return <div><div className="flex flex-col items-stretch gap-2 sm:flex-row sm:items-start">{children}<button type="button" aria-label="Hapus Item" onClick={onRemove} className="self-end rounded-xl border border-stone-300 p-3 text-red-600 sm:self-auto"><Trash2 size={18} /></button></div>{error && <p className="mt-1 text-sm text-red-600">{error}</p>}</div>;
}

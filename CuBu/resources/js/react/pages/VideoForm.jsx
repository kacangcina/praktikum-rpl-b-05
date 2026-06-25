import { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { api } from '../api.js';
import Alert from '../components/Alert.jsx';
import FileUploadField from '../components/FileUploadField.jsx';
import Loading from '../components/Loading.jsx';
import { Field } from './Login.jsx';

export default function VideoForm() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [recipe, setRecipe] = useState(null);
    const [error, setError] = useState(null);
    useEffect(() => { api(`/api/recipes/${id}`).then((data) => setRecipe(data.recipe)); }, [id]);
    if (!recipe) return <Loading />;

    return (
        <div className="mx-auto max-w-3xl">
            <h1 className="border-b border-stone-400 pb-5 text-4xl font-black">{recipe.video ? 'Ganti' : 'Tambahkan'} video {recipe.title}</h1>
            <form onSubmit={async (event) => {
                event.preventDefault();
                setError(null);
                try {
                    await api(`/api/recipes/${id}/video`, { method: 'POST', body: new FormData(event.currentTarget) });
                    navigate(`/recipes/${id}`);
                } catch (caught) {
                    setError(Object.values(caught.errors || {})[0]?.[0] || caught.message);
                }
            }} className="wire-panel mt-7 space-y-5 p-7">
                {error && <Alert>{error}</Alert>}
                <Field label="Judul video"><input aria-label="Judul video" name="title" defaultValue={recipe.video?.title || recipe.title} required /></Field>
                <Field label="Deskripsi"><textarea aria-label="Deskripsi video" name="description" rows="5" defaultValue={recipe.video?.description || ''} required /></Field>
                <Field label="Kesulitan"><select name="difficulty" defaultValue={recipe.video?.difficulty || recipe.difficulty} required><option value="mudah">Mudah</option><option value="sedang">Sedang</option><option value="sulit">Sulit</option></select></Field>
                <FileUploadField name="video" accept="video/mp4" required={!recipe.video} label="File MP4" hint="Preview video dapat diputar sebelum disimpan. Maksimal 500 MB." kind="video" />
                <button type="submit" className="wire-button wire-button-primary">Simpan video</button>
            </form>
        </div>
    );
}

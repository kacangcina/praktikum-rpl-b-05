import { ChevronLeft, ChevronRight, EyeOff, RotateCcw, Search, Trash2 } from 'lucide-react';
import { useEffect, useReducer, useRef } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import { api } from '../api.js';
import Alert from '../components/Alert.jsx';
import Loading from '../components/Loading.jsx';

const allowedStatuses = ['all', 'published', 'unpublished', 'pending_review'];

const styles = {
    container: 'mt-6 flex flex-wrap gap-3',
    form: 'flex min-w-64 flex-1',
    input: 'wire-input rounded-r-none',
    searchButton: 'rounded-r-xl bg-stone-900 px-4 text-white',
    statusSelect: 'wire-input w-auto',
    recipeList: 'mt-5 overflow-hidden rounded-2xl border border-stone-200 bg-white',
    recipeCard: 'flex flex-wrap items-center gap-4 border-b border-stone-100 p-4 last:border-0',
    thumbnail: 'size-16 overflow-hidden rounded-xl bg-stone-100',
    badge: 'rounded-full px-3 py-1 text-xs font-bold',
    actionButton: 'rounded-lg border p-2',
    deleteButton: 'rounded-lg border border-red-200 p-2 text-red-600',
    pagination: 'mt-5 flex items-center justify-between',
    paginationButton: 'wire-button disabled:opacity-40',
};

const initialState = {
    query: '',
    status: 'all',
    page: 1,
    data: null,
    message: '',
    error: '',
};

function reducer(state, action) {
    switch (action.type) {
        case 'setQuery':
            return { ...state, query: action.payload };
        case 'setStatus':
            return { ...state, status: action.payload, page: 1 };
        case 'setPage':
            return {
                ...state,
                page: typeof action.payload === 'function' ? action.payload(state.page) : action.payload,
            };
        case 'fetchStart':
            return { ...state, data: null, error: '' };
        case 'fetchSuccess':
            return { ...state, data: action.payload, error: '' };
        case 'fetchFailure':
            return { ...state, error: action.payload };
        case 'setMessage':
            return { ...state, message: action.payload };
        default:
            return state;
    }
}

export default function AdminRecipes() {
    const [params, setParams] = useSearchParams();
    const [state, dispatch] = useReducer(
        reducer,
        initialState,
        (initial) => ({ ...initial, status: allowedStatuses.includes(params.get('status')) ? params.get('status') : 'all' })
    );
    const searchRef = useRef('');
    const { query, status, page, data, message, error } = state;

    const setPage = (payload) => dispatch({ type: 'setPage', payload });

    const load = async (requestedPage = page) => {
        dispatch({ type: 'fetchStart' });

        try {
            const result = await api(
                `/api/admin/recipes?q=${encodeURIComponent(searchRef.current)}&status=${status}&page=${requestedPage}`
            );
            dispatch({ type: 'fetchSuccess', payload: result });
        } catch (caught) {
            dispatch({ type: 'fetchFailure', payload: caught.message });
        }
    };

    useEffect(() => {
        let active = true;

        dispatch({ type: 'fetchStart' });
        api(`/api/admin/recipes?q=${encodeURIComponent(searchRef.current)}&status=${status}&page=${page}`)
            .then((result) => {
                if (active) dispatch({ type: 'fetchSuccess', payload: result });
            })
            .catch((caught) => {
                if (active) dispatch({ type: 'fetchFailure', payload: caught.message });
            });

        return () => {
            active = false;
        };
    }, [status, page]);

    const moderate = async (recipe) => {
        const publishing = recipe.status === 'unpublished';
        const reason = publishing ? null : window.prompt('Alasan resep diturunkan:');
        if (!publishing && !reason?.trim()) return;

        try {
            const result = await api(`/api/admin/recipes/${recipe.id}`, {
                method: 'PATCH',
                body: { status: publishing ? 'published' : 'unpublished', reason },
            });
            dispatch({ type: 'setMessage', payload: result.message });
            load();
        } catch (caught) {
            dispatch({ type: 'fetchFailure', payload: caught.message });
        }
    };

    const remove = async (recipe) => {
        const reason = window.prompt(`Alasan penghapusan resep "${recipe.title}":`);
        if (!reason?.trim()) return;
        if (!window.confirm(`Hapus resep "${recipe.title}" secara permanen?`)) return;

        try {
            const result = await api(`/api/admin/recipes/${recipe.id}`, { method: 'DELETE', body: { reason } });
            dispatch({ type: 'setMessage', payload: result.message });
            load();
        } catch (caught) {
            dispatch({ type: 'fetchFailure', payload: caught.message });
        }
    };

    return (
        <section>
            <p className="font-bold uppercase text-orange-600">Moderasi konten</p>
            <h1 className="mt-1 text-3xl font-black">Kelola resep</h1>
            <div className={styles.container}>
                <form
                    onSubmit={(event) => {
                        event.preventDefault();
                        searchRef.current = query.trim();
                        if (page === 1) {
                            load(1);
                        } else {
                            setPage(() => 1);
                        }
                    }}
                    className={styles.form}
                >
                    <input
                        aria-label="Cari judul resep"
                        value={query}
                        onChange={(event) => dispatch({ type: 'setQuery', payload: event.target.value })}
                        className={styles.input}
                        placeholder="Cari judul resep"
                    />
                    <button type="submit" className={styles.searchButton} aria-label="Cari resep">
                        <Search size={18} />
                    </button>
                </form>
                <select
                    value={status}
                    onChange={(event) => {
                        dispatch({ type: 'setStatus', payload: event.target.value });
                        setParams({ status: event.target.value });
                    }}
                    className={styles.statusSelect}
                >
                    <option value="all">Semua status</option>
                    <option value="pending_review">Perlu ditinjau</option>
                    <option value="published">Terbit</option>
                    <option value="unpublished">Diturunkan</option>
                </select>
            </div>
            {message && (
                <div className="mt-4">
                    <Alert tone="success">{message}</Alert>
                </div>
            )}
            {error && (
                <div className="mt-4">
                    <Alert>{error}</Alert>
                </div>
            )}
            {!data ? (
                <Loading />
            ) : (
                <>
                    <div className={styles.recipeList}>
                        {data.recipes.map((recipe) => (
                            <article key={recipe.id} className={styles.recipeCard}>
                                <div className={styles.thumbnail}>
                                    {recipe.thumbnail_url && (
                                        <img src={recipe.thumbnail_url} alt="" className="h-full w-full object-cover" />
                                    )}
                                </div>
                                <div className="min-w-52 flex-1">
                                    <Link to={`/recipes/${recipe.id}`} className="font-bold hover:text-orange-600">
                                        {recipe.title}
                                    </Link>
                                    <p className="text-sm text-stone-500">
                                        oleh @{recipe.creator?.username} · {recipe.reviews_count} ulasan · {recipe.collections_count} simpan
                                    </p>
                                    {recipe.moderation_reason && (
                                        <p className="mt-1 text-xs text-red-600">{recipe.moderation_reason}</p>
                                    )}
                                </div>
                                <span
                                    className={`${styles.badge} ${
                                        recipe.status === 'published'
                                            ? 'bg-green-100 text-green-700'
                                            : recipe.status === 'pending_review'
                                            ? 'bg-blue-100 text-blue-700'
                                            : 'bg-red-100 text-red-700'
                                    }`}
                                >
                                    {recipe.status === 'published'
                                        ? 'Terbit'
                                        : recipe.status === 'pending_review'
                                        ? 'Perlu ditinjau'
                                        : 'Diturunkan'}
                                </span>
                                <div className="flex gap-2">
                                    <button
                                        type="button"
                                        onClick={() => moderate(recipe)}
                                        className={styles.actionButton}
                                        title={recipe.status === 'published' ? 'Turunkan' : 'Terbitkan'}
                                        aria-label={recipe.status === 'published' ? 'Turunkan resep' : 'Terbitkan resep'}
                                    >
                                        {recipe.status === 'published' ? <EyeOff size={18} /> : <RotateCcw size={18} />}
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => remove(recipe)}
                                        className={styles.deleteButton}
                                        title="Hapus"
                                        aria-label="Hapus resep"
                                    >
                                        <Trash2 size={18} />
                                    </button>
                                </div>
                            </article>
                        ))}
                        {!data.recipes.length && <p className="p-8 text-center text-stone-500">Resep tidak ditemukan.</p>}
                    </div>
                    {data?.pagination?.last_page > 1 && (
                        <div className={styles.pagination}>
                            <button
                                type="button"
                                disabled={page <= 1}
                                onClick={() => setPage((current) => current - 1)}
                                className={styles.paginationButton}
                            >
                                <ChevronLeft size={18} /> Sebelumnya
                            </button>
                            <span className="text-sm text-stone-500">
                                Halaman {data.pagination.current_page} dari {data.pagination.last_page}
                            </span>
                            <button
                                type="button"
                                disabled={page >= data.pagination.last_page}
                                onClick={() => setPage((current) => current + 1)}
                                className={styles.paginationButton}
                            >
                                Berikutnya <ChevronRight size={18} />
                            </button>
                        </div>
                    )}
                </>
            )}
        </section>
    );
}

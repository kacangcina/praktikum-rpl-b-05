import { ChevronLeft, ChevronRight } from 'lucide-react';
import { useEffect, useReducer } from 'react';
import { Link, Navigate, useSearchParams } from 'react-router-dom';
import { api } from '../api.js';
import Alert from '../components/Alert.jsx';
import Loading from '../components/Loading.jsx';
import { useAuth } from '../context/AuthContext.jsx';

const statuses = ['pending', 'approved', 'rejected'];

const initialState = { data: null, error: '' };

function reducer(state, action) {
    switch (action.type) {
        case 'fetchStart':
            return { ...state, data: null, error: '' };
        case 'fetchSuccess':
            return { ...state, data: action.payload, error: '' };
        case 'fetchFailure':
            return { ...state, error: action.payload };
        default:
            return state;
    }
}

export default function AdminVerifications() {
    const { user } = useAuth();
    const [params, setParams] = useSearchParams();
    const [state, dispatch] = useReducer(reducer, initialState);
    const status = statuses.includes(params.get('status')) ? params.get('status') : 'pending';
    const page = Math.max(Number(params.get('page')) || 1, 1);

    useEffect(() => {
        let active = true;

        dispatch({ type: 'fetchStart' });
        api(`/api/admin/creator-verifications?status=${status}&page=${page}`)
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

    if (!user?.is_admin) return <Navigate to="/" replace />;
    if (state.error) return <Alert>{state.error}</Alert>;
    if (!state.data) return <Loading />;

    return (
        <>
            <p className="font-bold uppercase text-orange-600">Dashboard admin</p>
            <h1 className="text-4xl font-black">Verifikasi creator</h1>

            <div className="mt-6 flex flex-wrap gap-2">
                {statuses.map((item) => (
                    <button
                        key={item}
                        type="button"
                        onClick={() => setParams({ status: item, page: '1' })}
                        className={`rounded-full px-4 py-2 font-bold capitalize ${status === item ? 'bg-stone-900 text-white' : 'bg-white'}`}
                    >
                        {item} ({state.data.counts[item] || 0})
                    </button>
                ))}
            </div>

            <div className="mt-6 overflow-hidden rounded-2xl border border-stone-200 bg-white">
                {state.data.verifications.map((verification) => (
                    <Link
                        key={verification.id}
                        to={`/admin/creator-verifications/${verification.id}`}
                        className="flex items-center justify-between border-b border-stone-100 p-5 last:border-0"
                    >
                        <div>
                            <h2 className="font-bold">{verification.user?.name}</h2>
                            <p className="text-sm text-stone-500">{verification.user?.email}</p>
                            <p className="mt-1 text-xs text-stone-400">
                                {new Date(verification.submitted_at).toLocaleString('id-ID')}
                            </p>
                        </div>
                        <span className="capitalize text-orange-600">{verification.status} &gt;</span>
                    </Link>
                ))}
                {!state.data.verifications.length && <p className="p-8 text-center text-stone-500">Tidak ada pengajuan.</p>}
            </div>

            {state.data.pagination.last_page > 1 && (
                <nav className="mt-6 flex items-center justify-between gap-4 rounded-2xl border border-stone-200 bg-white p-4" aria-label="Paginasi verifikasi creator">
                    <button
                        type="button"
                        disabled={page <= 1}
                        onClick={() => setParams({ status, page: String(page - 1) })}
                        className="wire-button bg-stone-100 disabled:cursor-not-allowed disabled:opacity-40"
                    >
                        <ChevronLeft size={18} /> Sebelumnya
                    </button>
                    <span className="text-center text-sm text-stone-600">
                        Halaman {state.data.pagination.current_page} dari {state.data.pagination.last_page}
                        {' '}({state.data.pagination.total} pengajuan)
                    </span>
                    <button
                        type="button"
                        disabled={page >= state.data.pagination.last_page}
                        onClick={() => setParams({ status, page: String(page + 1) })}
                        className="wire-button bg-stone-100 disabled:cursor-not-allowed disabled:opacity-40"
                    >
                        Berikutnya <ChevronRight size={18} />
                    </button>
                </nav>
            )}
        </>
    );
}

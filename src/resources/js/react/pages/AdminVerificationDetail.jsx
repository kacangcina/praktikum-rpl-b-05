import { useEffect, useReducer } from 'react';
import { Navigate, useNavigate, useParams } from 'react-router-dom';
import { api } from '../api.js';
import Alert from '../components/Alert.jsx';
import Loading from '../components/Loading.jsx';
import { useAuth } from '../context/AuthContext.jsx';
import { Field } from './Login.jsx';

const initialState = { verification: null, error: null };

function reducer(state, action) {
    switch (action.type) {
        case 'fetchStart':
            return { ...state, verification: null, error: null };
        case 'fetchSuccess':
            return { ...state, verification: action.payload, error: null };
        case 'fetchFailure':
            return { ...state, error: action.payload };
        case 'clearError':
            return { ...state, error: null };
        default:
            return state;
    }
}

export default function AdminVerificationDetail() {
    const { user } = useAuth();
    const { id } = useParams();
    const navigate = useNavigate();
    const [state, dispatch] = useReducer(reducer, initialState);
    const { verification, error } = state;

    useEffect(() => {
        let active = true;

        dispatch({ type: 'fetchStart' });
        api(`/api/admin/creator-verifications/${id}`)
            .then((data) => {
                if (active) dispatch({ type: 'fetchSuccess', payload: data.verification });
            })
            .catch((caught) => {
                if (active) dispatch({ type: 'fetchFailure', payload: caught.message });
            });

        return () => {
            active = false;
        };
    }, [id]);

    if (!user?.is_admin) return <Navigate to="/" replace />;
    if (error && !verification) return <Alert>{error}</Alert>;
    if (!verification) return <Loading />;

    const review = async (action, body = {}) => {
        dispatch({ type: 'clearError' });
        try {
            await api(`/api/admin/creator-verifications/${id}/${action}`, { method: 'PATCH', body });
            navigate('/admin/creator-verifications');
        } catch (caught) {
            dispatch({ type: 'fetchFailure', payload: Object.values(caught.errors || {})[0]?.[0] || caught.message });
        }
    };

    return (
        <div className="mx-auto max-w-3xl">
            <p className="font-bold text-orange-600 uppercase">Detail pengajuan</p>
            <h1 className="text-4xl font-black">{verification.user?.name}</h1>
            {error && <Alert>{error}</Alert>}
            <section className="mt-7 space-y-4 rounded-2xl border border-stone-200 bg-white p-6">
                <p><strong>Email:</strong> {verification.user?.email}</p>
                <p><strong>Status:</strong> <span className="capitalize">{verification.status}</span></p>
                <p><strong>Catatan:</strong><br />{verification.notes}</p>
                {verification.portfolio_url && <p><a href={verification.portfolio_url} target="_blank" rel="noreferrer" className="font-bold text-orange-600">Buka portofolio</a></p>}
                <a href={verification.document_url} className="inline-block rounded-xl border border-stone-300 px-4 py-2 font-bold">Unduh dokumen</a>
            </section>
            {verification.status === 'pending' && <div className="mt-6 grid items-start gap-5 md:grid-cols-2">
                <section className="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                    <h2 className="text-lg font-black text-emerald-900">Setujui pengajuan</h2>
                    <p className="mt-1 text-sm leading-6 text-emerald-800">Pengguna akan memperoleh status creator dan akses untuk mengunggah video resep.</p>
                    <button
                        type="button"
                        onClick={() => review('approve')}
                        aria-label="Setujui creator"
                        className="mt-5 inline-flex min-h-12 items-center justify-center rounded-xl bg-emerald-600 px-5 py-3 font-bold text-white hover:bg-emerald-700"
                    >
                        Setujui creator
                    </button>
                </section>
                <form
                    onSubmit={(event) => {
                        event.preventDefault();
                        review('reject', { rejection_reason: new FormData(event.currentTarget).get('rejection_reason') });
                    }}
                    className="space-y-3 rounded-2xl border border-red-200 bg-white p-5"
                >
                    <Field label="Alasan penolakan"><textarea aria-label="Alasan penolakan" name="rejection_reason" required /></Field>
                    <button
                        type="submit"
                        aria-label="Tolak pengajuan"
                        className="inline-flex min-h-12 items-center justify-center rounded-xl bg-red-600 px-5 py-3 font-bold text-white hover:bg-red-700"
                    >
                        Tolak pengajuan
                    </button>
                </form>
            </div>}
        </div>
    );
}

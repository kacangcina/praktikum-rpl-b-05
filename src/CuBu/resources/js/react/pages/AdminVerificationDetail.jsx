import { useEffect, useState } from 'react';
import { Navigate, useNavigate, useParams } from 'react-router-dom';
import { api } from '../api.js';
import Alert from '../components/Alert.jsx';
import Loading from '../components/Loading.jsx';
import { useAuth } from '../context/AuthContext.jsx';
import { Field } from './Login.jsx';

export default function AdminVerificationDetail() {
    const { user } = useAuth();
    const { id } = useParams();
    const navigate = useNavigate();
    const [verification, setVerification] = useState(null);
    const [error, setError] = useState(null);
    useEffect(() => { api(`/api/admin/creator-verifications/${id}`).then((data) => setVerification(data.verification)); }, [id]);

    if (!user?.is_admin) return <Navigate to="/" replace />;
    if (!verification) return <Loading />;

    const review = async (action, body = {}) => {
        setError(null);
        try {
            await api(`/api/admin/creator-verifications/${id}/${action}`, { method: 'PATCH', body });
            navigate('/admin/creator-verifications');
        } catch (caught) {
            setError(Object.values(caught.errors || {})[0]?.[0] || caught.message);
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
            {verification.status === 'pending' && <div className="mt-6 grid gap-5 md:grid-cols-2">
                <button onClick={() => review('approve')} className="rounded-xl bg-emerald-600 px-5 py-3 font-bold text-white">Setujui creator</button>
                <form onSubmit={(event) => {
                    event.preventDefault();
                    review('reject', { rejection_reason: new FormData(event.currentTarget).get('rejection_reason') });
                }} className="space-y-3 rounded-2xl bg-white p-5">
                    <Field label="Alasan penolakan"><textarea name="rejection_reason" required /></Field>
                    <button className="rounded-xl bg-red-600 px-5 py-3 font-bold text-white">Tolak pengajuan</button>
                </form>
            </div>}
        </div>
    );
}

import { useEffect, useState } from 'react';
import { Link, Navigate, useSearchParams } from 'react-router-dom';
import { api } from '../api.js';
import Loading from '../components/Loading.jsx';
import { useAuth } from '../context/AuthContext.jsx';

export default function AdminVerifications() {
    const { user } = useAuth();
    const [params, setParams] = useSearchParams();
    const [data, setData] = useState(null);
    const status = params.get('status') || 'pending';
    useEffect(() => { setData(null); api(`/api/admin/creator-verifications?status=${status}`).then(setData); }, [status]);

    if (!user?.is_admin) return <Navigate to="/" replace />;
    if (!data) return <Loading />;

    return (
        <>
            <p className="font-bold text-orange-600 uppercase">Dashboard admin</p>
            <h1 className="text-4xl font-black">Verifikasi creator</h1>
            <div className="mt-6 flex gap-2">
                {['pending', 'approved', 'rejected'].map((item) => <button key={item} onClick={() => setParams({ status: item })} className={`rounded-full px-4 py-2 font-bold capitalize ${status === item ? 'bg-stone-900 text-white' : 'bg-white'}`}>{item} ({data.counts[item] || 0})</button>)}
            </div>
            <div className="mt-6 overflow-hidden rounded-2xl border border-stone-200 bg-white">
                {data.verifications.map((verification) => <Link key={verification.id} to={`/admin/creator-verifications/${verification.id}`} className="flex items-center justify-between border-b border-stone-100 p-5 last:border-0"><div><h2 className="font-bold">{verification.user?.name}</h2><p className="text-sm text-stone-500">{verification.user?.email}</p></div><span className="capitalize text-orange-600">{verification.status} →</span></Link>)}
                {!data.verifications.length && <p className="p-8 text-center text-stone-500">Tidak ada pengajuan.</p>}
            </div>
        </>
    );
}

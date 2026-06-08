import { CreditCard, FileText, X } from 'lucide-react';
import { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { api } from '../api.js';
import Alert from '../components/Alert.jsx';
import Loading from '../components/Loading.jsx';
import { Field } from './Login.jsx';

export default function CreatorApply() {
    const [data, setData] = useState(null);
    const [error, setError] = useState(null);
    const navigate = useNavigate();
    useEffect(() => { api('/api/creator-verification').then(setData); }, []);
    if (!data) return <Loading />;

    const latest = data.latest_verification;
    const blocked = latest?.status === 'pending' || latest?.status === 'approved';

    return (
        <div className="mx-auto max-w-4xl">
            <div className="flex items-center justify-between border-b border-stone-400 pb-5">
                <h1 className="text-4xl font-black">Verifikasi creator</h1>
                <div className="flex items-center gap-6"><span className="rounded-3xl border border-red-400 bg-red-200 px-5 py-3 capitalize">{latest?.status || 'Belum diajukan'}</span><Link to="/profile"><X size={40} /></Link></div>
            </div>
            <div className="mt-7 rounded-2xl border-2 border-orange-600 bg-orange-200 p-5 text-center text-xl text-orange-700">Jadi creator untuk unggah video kelas memasak.</div>

            <section className="mt-6 border-y border-stone-400 py-6">
                <h2 className="text-2xl">Persyaratan pengajuan</h2>
                <ol className="mt-4 space-y-4">
                    {['Akun sudah terverifikasi email', 'Upload dokumen KTP', 'Upload portofolio memasak'].map((item, index) => <li key={item} className="flex items-center gap-4"><span className="grid size-11 place-items-center rounded-full bg-stone-200 text-xl font-bold">{index + 1}</span><div className="text-xl">{item}<small className="block text-base text-stone-500">{index === 0 ? 'Sudah terpenuhi' : index === 1 ? 'Wajib dilampirkan' : 'Foto atau dokumen (PDF/JPG)'}</small></div></li>)}
                </ol>
            </section>

            {latest && <Alert tone={latest.status === 'rejected' ? 'error' : 'success'}>Status pengajuan terakhir: <strong className="capitalize">{latest.status}</strong>{latest.rejection_reason && ` · ${latest.rejection_reason}`}</Alert>}
            {!blocked && <form onSubmit={async (event) => {
                event.preventDefault();
                setError(null);
                try {
                    await api('/api/creator-verification', { method: 'POST', body: new FormData(event.currentTarget) });
                    navigate('/profile');
                } catch (caught) {
                    setError(Object.values(caught.errors || {})[0]?.[0] || caught.message);
                }
            }} className="mt-7 space-y-6">
                {error && <Alert>{error}</Alert>}
                <Field label="URL portofolio"><input type="url" name="portfolio_url" placeholder="https://..." /></Field>
                <Field label="Deskripsi diri sebagai chef"><textarea name="notes" rows="4" placeholder="Ceritakan pengalaman memasakmu..." required /></Field>
                <UploadBox title="Dokumen KTP" subtitle="Unggah foto KTP" icon={<CreditCard />}><input type="file" name="document" accept=".pdf,image/jpeg,image/png" required /></UploadBox>
                <UploadBox title="Portofolio memasak" subtitle="Gunakan URL portofolio di atas" icon={<FileText />} />
                <div className="flex justify-end gap-4 border-t border-stone-400 pt-7"><Link to="/profile" className="wire-button bg-stone-200">Batal</Link><button className="wire-button wire-button-primary">Kirim pengajuan</button></div>
            </form>}
        </div>
    );
}

function UploadBox({ title, subtitle, icon, children }) {
    return <label className="block"><span className="mb-2 block text-2xl">{title}</span><span className="flex min-h-44 cursor-pointer flex-col items-center justify-center rounded-3xl border-2 border-dashed border-stone-700 text-stone-600">{icon}<strong className="mt-3 font-normal">{subtitle}</strong>{children && <span className="hidden">{children}</span>}</span></label>;
}

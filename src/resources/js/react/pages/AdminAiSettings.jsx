import { Bot, Save } from 'lucide-react';
import { useEffect, useState } from 'react';
import { api, firstError } from '../api.js';
import Alert from '../components/Alert.jsx';
import Loading from '../components/Loading.jsx';

export default function AdminAiSettings() {
    const [state, setState] = useState({ data: null, prompt: '', busy: false, notice: '', error: '' });

    useEffect(() => {
        api('/api/admin/ai-settings')
            .then((result) => {
                setState((current) => ({ ...current, data: result, prompt: result.prompt }));
            })
            .catch((caught) => setState((current) => ({ ...current, error: caught.message })));
    }, []);

    const submit = async (event) => {
        event.preventDefault();
        setState((current) => ({ ...current, busy: true, notice: '', error: '' }));
        try {
            const result = await api('/api/admin/ai-settings', {
                method: 'PUT',
                body: { prompt: state.prompt },
            });
            setState((current) => ({ ...current, notice: result.message, data: { ...current.data, updated_at: result.updated_at }, busy: false }));
        } catch (caught) {
            setState((current) => ({ ...current, error: firstError(caught, 'prompt') || caught.message, busy: false }));
        }
    };

    if (!state.data && !state.error) return <Loading />;

    return (
        <section>
            <p className="font-bold uppercase text-orange-600">Sistem AI</p>
            <h1 className="mt-1 text-3xl font-black">System prompt Gemini</h1>
            <p className="mt-2 text-stone-600">Instruksi ini dipakai sebagai aturan dasar pada setiap konsultasi pengguna.</p>

            <div className="mt-6 rounded-2xl border border-stone-200 bg-white p-5 sm:p-7">
                <div className="mb-5 flex items-center gap-3 rounded-xl bg-orange-50 p-4 text-sm">
                    <Bot className="text-orange-600" />
                    <div><b>Model aktif:</b> {state.data?.model}</div>
                </div>
                {state.notice && <Alert tone="success">{state.notice}</Alert>}
                {state.error && <Alert>{state.error}</Alert>}
                <form onSubmit={submit}>
                    <label htmlFor="ai-prompt" className="font-bold">System prompt</label>
                    <textarea
                        id="ai-prompt"
                        value={state.prompt}
                        onChange={(event) => setState((current) => ({ ...current, prompt: event.target.value }))}
                        rows="18"
                        className="wire-input mt-2 w-full resize-y font-mono text-sm leading-6"
                    />
                    <div className="mt-4 flex flex-wrap items-center justify-between gap-3">
                        <p className="text-xs text-stone-500">
                            {state.data?.updated_at ? `Terakhir disimpan ${new Date(state.data.updated_at).toLocaleString('id-ID')}` : 'Belum pernah diubah dari dashboard.'}
                        </p>
                        <button type="submit" disabled={state.busy} className="wire-button wire-button-primary">
                            <Save size={18} /> {state.busy ? 'Menyimpan...' : 'Simpan prompt'}
                        </button>
                    </div>
                </form>
            </div>
        </section>
    );
}

import { Bot, ChefHat, Send, UserRound } from 'lucide-react';
import { useState } from 'react';
import { api, firstError } from '../api.js';
import Alert from '../components/Alert.jsx';

const welcomeMessage = {
    role: 'assistant',
    text: 'Ceritakan masalah memasakmu. Contoh: kenapa ayam goreng saya matang di luar tetapi masih mentah di dalam?',
};

export default function CookingConsultation() {
    const [messages, setMessages] = useState([welcomeMessage]);
    const [question, setQuestion] = useState('');
    const [error, setError] = useState('');
    const [busy, setBusy] = useState(false);

    const submit = async (event) => {
        event.preventDefault();
        const value = question.trim();

        if (!value || busy) return;

        setError('');
        setBusy(true);
        setQuestion('');
        setMessages((current) => [...current, { role: 'user', text: value }]);

        try {
            const result = await api('/api/cooking-consultation', {
                method: 'POST',
                body: { question: value },
            });
            setMessages((current) => [
                ...current,
                {
                    role: 'assistant',
                    text: result.answer,
                    rejected: !result.in_scope,
                    recipes: result.related_recipes || [],
                },
            ]);
        } catch (caught) {
            setQuestion(value);
            setError(firstError(caught, 'question') || caught.message);
        } finally {
            setBusy(false);
        }
    };

    return (
        <div className="mx-auto max-w-4xl">
            <header className="text-center">
                <span className="mx-auto grid size-16 place-items-center rounded-2xl bg-orange-500 text-white"><ChefHat size={34} /></span>
                <h1 className="mt-4 text-4xl font-black">Konsultasi Masak AI</h1>
                <p className="mt-2 text-stone-600">Tanyakan masalah seputar resep, bahan, teknik memasak, atau keamanan pangan.</p>
            </header>

            <section className="wire-panel mt-8 overflow-hidden">
                <div aria-live="polite" className="min-h-[420px] space-y-5 p-5 sm:p-8">
                    {messages.map((message, index) => (
                        <article key={`${message.role}-${index}`} className={`flex gap-3 ${message.role === 'user' ? 'justify-end' : ''}`}>
                            {message.role === 'assistant' && <span className="grid size-10 shrink-0 place-items-center rounded-full bg-orange-100 text-orange-600"><Bot size={20} /></span>}
                            <div className={`max-w-[80%] whitespace-pre-wrap rounded-2xl px-4 py-3 text-sm leading-6 ${message.role === 'user' ? 'bg-stone-900 text-white' : message.rejected ? 'border border-red-200 bg-red-50 text-red-800' : 'bg-stone-100 text-stone-800'}`}>
                                {message.text}
                                {!!message.recipes?.length && <div className="mt-3 flex flex-wrap gap-2">{message.recipes.map((recipe) => <a key={recipe.id} href={`/recipes/${recipe.id}`} className="rounded-full bg-white px-3 py-1 text-xs font-bold text-orange-600 shadow-sm">Lihat {recipe.title}</a>)}</div>}
                            </div>
                            {message.role === 'user' && <span className="grid size-10 shrink-0 place-items-center rounded-full bg-stone-200"><UserRound size={20} /></span>}
                        </article>
                    ))}
                    {busy && <p className="pl-14 text-sm text-stone-500">CuBu sedang menyusun jawaban...</p>}
                </div>

                <form onSubmit={submit} className="border-t border-stone-200 bg-white p-5">
                    {error && <Alert>{error}</Alert>}
                    <div className="flex items-end gap-3">
                        <textarea
                            value={question}
                            onChange={(event) => setQuestion(event.target.value)}
                            rows="3"
                            maxLength="1000"
                            aria-label="Pertanyaan konsultasi memasak"
                            placeholder="Contoh: Mengapa adonan donat saya tidak mengembang?"
                            className="wire-input min-h-24 flex-1 resize-y"
                        />
                        <button type="submit" disabled={busy || !question.trim()} className="wire-button wire-button-primary mb-0.5 disabled:cursor-not-allowed disabled:opacity-50">
                            <Send size={18} /> Kirim
                        </button>
                    </div>
                    <p className="mt-2 text-xs text-stone-500">CuBu AI hanya menjawab pertanyaan seputar memasak dan resep.</p>
                </form>
            </section>
        </div>
    );
}

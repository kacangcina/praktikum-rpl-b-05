import { Ban, ChevronLeft, ChevronRight, Search, Trash2, Undo2 } from 'lucide-react';
import { useEffect, useReducer, useRef } from 'react';
import { api } from '../api.js';
import Alert from '../components/Alert.jsx';
import Loading from '../components/Loading.jsx';

const initialState = {
    query: '',
    status: 'all',
    page: 1,
    data: null,
    error: '',
    notice: '',
};

function reducer(state, action) {
    switch (action.type) {
        case 'setQuery':
            return { ...state, query: action.payload };
        case 'setStatus':
            return { ...state, status: action.payload, page: 1 };
        case 'setPage':
            return { ...state, page: action.payload };
        case 'fetchStart':
            return { ...state, data: null, error: '' };
        case 'fetchSuccess':
            return { ...state, data: action.payload, error: '' };
        case 'fetchFailure':
            return { ...state, error: action.payload };
        case 'setNotice':
            return { ...state, notice: action.payload };
        default:
            return state;
    }
}

export default function AdminUsers() {
    const [state, dispatch] = useReducer(reducer, initialState);
    const searchRef = useRef('');
    const { query, status, page, data, error, notice } = state;

    const load = async (requestedPage = page) => {
        dispatch({ type: 'fetchStart' });

        try {
            const result = await api(
                `/api/admin/users?q=${encodeURIComponent(searchRef.current)}&status=${status}&page=${requestedPage}`
            );
            dispatch({ type: 'fetchSuccess', payload: result });
        } catch (caught) {
            dispatch({ type: 'fetchFailure', payload: caught.message });
        }
    };

    useEffect(() => {
        let active = true;

        dispatch({ type: 'fetchStart' });

        api(`/api/admin/users?q=${encodeURIComponent(searchRef.current)}&status=${status}&page=${page}`)
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

    const changeRole = async (user, role) => {
        try {
            const result = await api(`/api/admin/users/${user.id}`, {
                method: 'PATCH',
                body: { role },
            });
            dispatch({ type: 'setNotice', payload: result.message });
            load();
        } catch (caught) {
            dispatch({ type: 'fetchFailure', payload: caught.message });
        }
    };

    const toggleSuspension = async (user) => {
        const reason = user.is_suspended ? null : window.prompt('Alasan pemblokiran akun:');
        if (!user.is_suspended && !reason?.trim()) return;

        try {
            const result = await api(`/api/admin/users/${user.id}/suspension`, {
                method: 'PATCH',
                body: { suspended: !user.is_suspended, reason },
            });
            dispatch({ type: 'setNotice', payload: result.message });
            load();
        } catch (caught) {
            dispatch({ type: 'fetchFailure', payload: caught.message });
        }
    };

    const remove = async (user) => {
        const reason = window.prompt(`Alasan penutupan akun ${user.username}:`);
        if (!reason?.trim()) return;
        if (!window.confirm(`Tutup akun ${user.username}? Pengguna tidak akan dapat login.`)) return;

        try {
            const result = await api(`/api/admin/users/${user.id}`, {
                method: 'DELETE',
                body: { reason },
            });
            dispatch({ type: 'setNotice', payload: result.message });
            load();
        } catch (caught) {
            dispatch({ type: 'fetchFailure', payload: caught.message });
        }
    };

    return (
        <section>
            <p className="font-bold uppercase text-orange-600">Operasional</p>
            <h1 className="mt-1 text-3xl font-black">Manajemen pengguna</h1>
            <div className="mt-6 flex flex-wrap gap-3">
                <form
                    onSubmit={(event) => {
                        event.preventDefault();
                        searchRef.current = query.trim();
                        if (page === 1) {
                            load(1);
                        } else {
                            dispatch({ type: 'setPage', payload: 1 });
                        }
                    }}
                    className="flex min-w-64 flex-1"
                >
                    <input
                        aria-label="Cari nama, username, atau email"
                        value={query}
                        onChange={(event) => dispatch({ type: 'setQuery', payload: event.target.value })}
                        className="wire-input rounded-r-none"
                        placeholder="Cari nama, username, atau email"
                    />
                    <button type="submit" className="rounded-r-xl bg-stone-900 px-4 text-white" aria-label="Cari pengguna">
                        <Search size={18} />
                    </button>
                </form>
                <select
                    value={status}
                    onChange={(event) => dispatch({ type: 'setStatus', payload: event.target.value })}
                    className="wire-input w-auto"
                >
                    <option value="all">Semua status</option>
                    <option value="active">Aktif</option>
                    <option value="suspended">Diblokir</option>
                    <option value="closed">Ditutup</option>
                </select>
            </div>
            {notice && (
                <div className="mt-4">
                    <Alert tone="success">{notice}</Alert>
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
                    <div className="mt-5 overflow-x-auto rounded-2xl border border-stone-200 bg-white">
                        <table className="w-full min-w-[780px] text-left text-sm">
                            <thead className="bg-stone-100 text-xs uppercase text-stone-500">
                                <tr>
                                    <th className="p-4">Pengguna</th>
                                    <th>Role</th>
                                    <th>Resep</th>
                                    <th>Status</th>
                                    <th className="p-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {data.users.map((user) => (
                                    <tr key={user.id} className="border-t border-stone-100">
                                        <td className="p-4">
                                            <b>{user.name}</b>
                                            <div className="text-stone-500">@{user.username} · {user.email}</div>
                                        </td>
                                        <td>
                                            <select
                                                disabled={user.is_closed}
                                                value={user.role}
                                                onChange={(event) => changeRole(user, event.target.value)}
                                                className="rounded-lg border border-stone-300 px-2 py-1 disabled:opacity-50"
                                            >
                                                <option value="user">User</option>
                                                <option value="creator">Creator</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                        </td>
                                        <td>{user.recipes_count}</td>
                                        <td>
                                            <span
                                                className={`rounded-full px-3 py-1 text-xs font-bold ${user.is_closed || user.is_suspended ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'}`}
                                            >
                                                {user.is_closed ? 'Ditutup' : user.is_suspended ? 'Diblokir' : 'Aktif'}
                                            </span>
                                        </td>
                                        <td className="p-4">
                                            <div className="flex justify-end gap-2">
                                                {!user.is_closed && (
                                                    <button
                                                        type="button"
                                                        onClick={() => toggleSuspension(user)}
                                                        className="rounded-lg border p-2"
                                                        title={user.is_suspended ? 'Buka blokir' : 'Blokir'}
                                                        aria-label={user.is_suspended ? 'Buka blokir pengguna' : 'Blokir pengguna'}
                                                    >
                                                        {user.is_suspended ? <Undo2 size={17} /> : <Ban size={17} />}
                                                    </button>
                                                )}
                                                {!user.is_closed && (
                                                    <button
                                                        type="button"
                                                        onClick={() => remove(user)}
                                                        className="rounded-lg border border-red-200 p-2 text-red-600"
                                                        title="Tutup akun"
                                                        aria-label="Tutup akun pengguna"
                                                    >
                                                        <Trash2 size={17} />
                                                    </button>
                                                )}
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                        {!data.users.length && <p className="p-8 text-center text-stone-500">Pengguna tidak ditemukan.</p>}
                    </div>
                    <Pagination data={data.pagination} page={page} setPage={(value) => dispatch({ type: 'setPage', payload: value })} />
                </>
            )}
        </section>
    );
}

function Pagination({ data, page, setPage }) {
    if (data.last_page <= 1) return null;
    return (
        <div className="mt-5 flex items-center justify-between">
            <button type="button" disabled={page <= 1} onClick={() => setPage(page - 1)} className="wire-button disabled:opacity-40">
                <ChevronLeft size={18} /> Sebelumnya
            </button>
            <span className="text-sm text-stone-500">Halaman {data.current_page} dari {data.last_page}</span>
            <button type="button" disabled={page >= data.last_page} onClick={() => setPage(page + 1)} className="wire-button disabled:opacity-40">
                Berikutnya <ChevronRight size={18} />
            </button>
        </div>
    );
}

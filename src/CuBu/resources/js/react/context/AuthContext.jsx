import { createContext, useContext, useEffect, useMemo, useState } from 'react';
import { api } from '../api.js';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);

    const refresh = async () => {
        const data = await api('/api/session');
        setUser(data.user);
        return data.user;
    };

    useEffect(() => {
        refresh().finally(() => setLoading(false));
    }, []);

    const value = useMemo(() => ({
        user,
        loading,
        refresh,
        setUser,
        async logout() {
            await api('/api/logout', { method: 'POST' });
            setUser(null);
        },
    }), [user, loading]);

    return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth() {
    return useContext(AuthContext);
}

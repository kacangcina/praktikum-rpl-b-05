import { createContext, use, useEffect, useMemo, useReducer } from 'react';
import { api } from '../api.js';

const AuthContext = createContext(null);

const authReducer = (state, action) => {
    switch (action.type) {
        case 'SET_USER': return { ...state, user: action.payload, loading: false };
        case 'LOGOUT': return { ...state, user: null };
        default: return state;
    }
};

export function AuthProvider({ children }) {
    const [state, dispatch] = useReducer(authReducer, { user: null, loading: true });

    const refresh = async () => {
        const data = await api('/api/session');
        dispatch({ type: 'SET_USER', payload: data.user });
        return data.user;
    };

    useEffect(() => {
        refresh();
    }, []);

    const value = useMemo(() => ({
        user: state.user,
        loading: state.loading,
        refresh,
        setUser: (user) => dispatch({ type: 'SET_USER', payload: user }),
        async logout() {
            await api('/api/logout', { method: 'POST' });
            dispatch({ type: 'LOGOUT' });
        },
    }), [state.user, state.loading]);

    return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth() {
    return use(AuthContext);
}

import React from 'react';

export default class ErrorBoundary extends React.Component {
    constructor(props) {
        super(props);
        this.state = { error: null };
    }

    static getDerivedStateFromError(error) {
        return { error };
    }

    componentDidCatch(error, info) {
        console.error('React gagal merender aplikasi.', error, info);
    }

    render() {
        if (this.state.error) {
            return (
                <main className="grid min-h-screen place-items-center bg-stone-50 px-6">
                    <section className="w-full max-w-lg rounded-2xl border border-red-200 bg-white p-6 shadow-sm">
                        <h1 className="text-xl font-black text-red-700">Aplikasi gagal dimuat</h1>
                        <p className="mt-2 text-sm text-stone-600">
                            Muat ulang halaman. Jika pesan ini tetap muncul, periksa console browser.
                        </p>
                        <pre className="mt-4 overflow-auto rounded-xl bg-red-50 p-4 text-xs text-red-800">
                            {this.state.error.message}
                        </pre>
                    </section>
                </main>
            );
        }

        return this.props.children;
    }
}

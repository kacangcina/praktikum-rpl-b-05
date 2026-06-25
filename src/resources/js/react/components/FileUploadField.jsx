import { FileText, ImagePlus, Play, Upload, X } from 'lucide-react';
import { useEffect, useId, useState } from 'react';

export default function FileUploadField({ name, accept, required = false, label, hint, error, kind = 'auto' }) {
    const inputId = useId();
    const [file, setFile] = useState(null);
    const [previewUrl, setPreviewUrl] = useState('');
    const [inputVersion, setInputVersion] = useState(0);

    useEffect(() => {
        if (!file || (!file.type.startsWith('image/') && !file.type.startsWith('video/'))) {
            setPreviewUrl('');
            return undefined;
        }

        const objectUrl = URL.createObjectURL(file);
        setPreviewUrl(objectUrl);

        return () => URL.revokeObjectURL(objectUrl);
    }, [file]);

    const remove = () => {
        setFile(null);
        setInputVersion((version) => version + 1);
    };

    return (
        <div>
            {label && <p className="mb-2 font-bold">{label}</p>}
            <input
                key={inputVersion}
                id={inputId}
                type="file"
                name={name}
                aria-label={label || 'Pilih file'}
                accept={accept}
                required={required}
                onChange={(event) => setFile(event.target.files?.[0] || null)}
                className="sr-only"
            />
            {!file ? (
                <label htmlFor={inputId} className="flex min-h-48 cursor-pointer flex-col items-center justify-center rounded-3xl border-2 border-dashed border-stone-400 bg-stone-50 px-6 text-center transition hover:border-orange-500 hover:bg-orange-50/40">
                    <UploadIcon kind={kind} />
                    <strong className="mt-4 text-lg">Pilih file</strong>
                    <span className="sr-only">{label || 'Pilih file'}</span>
                    {hint && <span className="mt-1 text-xs text-stone-500">{hint}</span>}
                </label>
            ) : (
                <div className="relative overflow-hidden rounded-3xl border border-stone-300 bg-stone-50">
                    <button type="button" onClick={remove} className="absolute top-3 right-3 z-10 grid size-9 place-items-center rounded-full bg-stone-900 text-white shadow" aria-label={`Batalkan pilihan ${file.name}`}>
                        <X size={18} />
                    </button>
                    <FilePreview file={file} previewUrl={previewUrl} />
                    <div className="border-t border-stone-200 bg-white p-4 pr-14">
                        <p className="truncate font-bold">{file.name}</p>
                        <p className="mt-1 text-xs text-stone-500">{formatBytes(file.size)} · {file.type || 'Tipe file tidak diketahui'}</p>
                    </div>
                </div>
            )}
            {error && <small className="mt-2 block text-red-600">{error}</small>}
        </div>
    );
}

function FilePreview({ file, previewUrl }) {
    if (file.type.startsWith('image/')) {
        return previewUrl
            ? <img src={previewUrl} alt={`Preview ${file.name}`} className="h-64 w-full object-cover" />
            : <div className="h-64 animate-pulse bg-stone-200" aria-label="Menyiapkan preview gambar" />;
    }

    if (file.type.startsWith('video/')) {
        return previewUrl
            ? <video src={previewUrl} controls className="h-64 w-full bg-black object-contain" aria-label={`Preview video ${file.name}`}>
                <track kind="captions" srcLang="id" label="Bahasa Indonesia" src="data:text/vtt,WEBVTT\n\n00:00:00.000 --> 00:00:00.001\n." />
                Browser tidak mendukung preview video.
              </video>
            : <div className="h-64 animate-pulse bg-stone-900" aria-label="Menyiapkan preview video" />;
    }

    return (
        <div className="flex h-48 flex-col items-center justify-center bg-red-50 text-red-700">
            <FileText size={54} strokeWidth={1.4} />
            <p className="mt-3 font-bold">Dokumen siap diunggah</p>
            <p className="text-sm">Periksa nama dan ukuran file di bawah.</p>
        </div>
    );
}

function UploadIcon({ kind }) {
    if (kind === 'image') return <ImagePlus size={54} strokeWidth={1.3} />;
    if (kind === 'video') return <Play size={54} strokeWidth={1.3} />;
    if (kind === 'document') return <FileText size={54} strokeWidth={1.3} />;
    return <Upload size={54} strokeWidth={1.3} />;
}

function formatBytes(bytes) {
    if (!bytes) return '0 byte';

    const units = ['byte', 'KB', 'MB', 'GB'];
    const index = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
    const value = bytes / (1024 ** index);

    return `${value.toLocaleString('id-ID', { maximumFractionDigits: index === 0 ? 0 : 1 })} ${units[index]}`;
}

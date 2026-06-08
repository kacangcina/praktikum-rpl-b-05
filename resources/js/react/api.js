const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

export class ApiError extends Error {
    constructor(message, status, errors = {}) {
        super(message);
        this.status = status;
        this.errors = errors;
    }
}

export async function api(path, options = {}) {
    const headers = {
        Accept: 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        ...options.headers,
    };

    if (options.body && !(options.body instanceof FormData)) {
        headers['Content-Type'] = 'application/json';
    }

    const response = await fetch(path, {
        credentials: 'same-origin',
        ...options,
        headers,
        body: options.body && !(options.body instanceof FormData)
            ? JSON.stringify(options.body)
            : options.body,
    });

    const data = response.headers.get('content-type')?.includes('application/json')
        ? await response.json()
        : null;

    if (!response.ok) {
        throw new ApiError(
            data?.message || 'Permintaan tidak dapat diproses.',
            response.status,
            data?.errors || {},
        );
    }

    return data;
}

export function firstError(error, field) {
    return error?.errors?.[field]?.[0] || null;
}

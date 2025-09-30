const API_BASE = import.meta.env.VITE_API_BASE || 'http://127.0.0.1:8000/api';

async function request(path, { method='GET', body=null, token=null } = {}) {
    const headers = { Accept: 'application/json' };
    if (body) headers['Content-Type'] = 'application/json';
    if (token) headers['Authorization'] = `Bearer ${token}`;

    const res = await fetch(`${API_BASE}${path}`, {
        method,
        headers,
        body: body ? JSON.stringify(body) : null
    });

    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
        throw data.error || { code: `HTTP_${res.status}`, message: data.message || '요청 실패' };
    }
    return data;
}

export const api = {
    loginAdmin({ email, password }) {
        return request('/admin/auth/login', { method: 'POST', body: { email, password } });
    },
    me(token) {
        return request('/admin/me', { token });
    },
    logout(token) {
        return request('/admin/auth/logout', { method: 'POST', token });
    }
};

const LS_KEY = 'mewpi_admin_token';

export const auth = {
    token: null,
    getToken() { return this.token ?? (this.token = localStorage.getItem(LS_KEY)); },
    setToken(t) { this.token = t; t ? localStorage.setItem(LS_KEY, t) : localStorage.removeItem(LS_KEY); },
    logout() { this.setToken(null); }
};

class Auth extends Api {
    constructor() {
        super();
    }

    async login(usuario, contrasena) {
        const data = await this.post('auth', '/login', { usuario, contrasena });
        if (data.token) {
            localStorage.setItem('token',   data.token);
            localStorage.setItem('usuario', data.usuario);
            localStorage.setItem('nombre',  data.nombre);
            localStorage.setItem('rol',     data.rol);
        }
        return data;
    }

    async logout() {
        const token = this.getToken();
        await this.post('auth', '/logout', { token });
        localStorage.clear();
    }

    isLoggedIn() {
        return localStorage.getItem('token') !== null;
    }

    getRol() {
        return localStorage.getItem('rol');
    }

    getNombre() {
        return localStorage.getItem('nombre');
    }
}
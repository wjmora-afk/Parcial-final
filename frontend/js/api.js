class Api {
    constructor() {
        this.urls = {
            auth:          'http://127.0.0.1:8001',
            empleados:     'http://127.0.0.1:8002',
            incapacidades: 'http://127.0.0.1:8003',
            seguimiento:   'http://127.0.0.1:8004'
        };
    }

    getToken() {
        return localStorage.getItem('token');
    }

    getHeaders() {
        return {
            'Content-Type':  'application/json',
            'Authorization': this.getToken() || ''
        };
    }

    async get(servicio, endpoint) {
        const response = await fetch(`${this.urls[servicio]}${endpoint}`, {
            method:  'GET',
            headers: this.getHeaders()
        });
        return await response.json();
    }

    async post(servicio, endpoint, data) {
        const response = await fetch(`${this.urls[servicio]}${endpoint}`, {
            method:  'POST',
            headers: this.getHeaders(),
            body:    JSON.stringify(data)
        });
        return await response.json();
    }

    async put(servicio, endpoint, data) {
        const response = await fetch(`${this.urls[servicio]}${endpoint}`, {
            method:  'PUT',
            headers: this.getHeaders(),
            body:    JSON.stringify(data)
        });
        return await response.json();
    }

    async patch(servicio, endpoint, data) {
        const response = await fetch(`${this.urls[servicio]}${endpoint}`, {
            method:  'PATCH',
            headers: this.getHeaders(),
            body:    JSON.stringify(data)
        });
        return await response.json();
    }

    async delete(servicio, endpoint) {
        const response = await fetch(`${this.urls[servicio]}${endpoint}`, {
            method:  'DELETE',
            headers: this.getHeaders()
        });
        return await response.json();
    }
}
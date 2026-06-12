class Seguimiento extends Api {
    constructor() {
        super();
    }

    async listar() {
        return await this.get('seguimiento', '/seguimientos');
    }

    async obtener(id) {
        return await this.get('seguimiento', `/seguimiento/${id}`);
    }

    async porIncapacidad(id) {
        return await this.get('seguimiento', `/seguimientos/incapacidad/${id}`);
    }

    async crear(data) {
        return await this.post('seguimiento', '/seguimiento', data);
    }

    async modificar(id, data) {
        return await this.put('seguimiento', `/seguimiento/${id}`, data);
    }

    renderTabla(seguimientos) {
        if (seguimientos.length === 0) {
            return '<tr><td colspan="5" style="text-align:center">No hay seguimientos registrados</td></tr>';
        }
        return seguimientos.map(s => `
            <tr>
                <td>${s.id}</td>
                <td>${s.fecha}</td>
                <td>${s.comentario}</td>
                <td><span class="badge badge-${s.estado}">${s.estado.replace('_', ' ')}</span></td>
                <td>${s.usuario_responsable}</td>
            </tr>
        `).join('');
    }
}
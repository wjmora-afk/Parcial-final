class Incapacidades extends Api {
    constructor() {
        super();
    }

    async listar() {
        return await this.get('incapacidades', '/incapacidades');
    }

    async obtener(id) {
        return await this.get('incapacidades', `/incapacidad/${id}`);
    }

    async buscarPorEmpleado(id) {
        return await this.get('incapacidades', `/incapacidades/empleado/${id}`);
    }

    async buscarPorEstado(estado) {
        return await this.get('incapacidades', `/incapacidades/estado/${estado}`);
    }

    async buscarPorTipo(tipo) {
        return await this.get('incapacidades', `/incapacidades/tipo/${tipo}`);
    }

    async crear(data) {
        return await this.post('incapacidades', '/incapacidad', data);
    }

    async modificar(id, data) {
        return await this.put('incapacidades', `/incapacidad/${id}`, data);
    }

    async cambiarEstado(id, estado) {
        return await this.patch('incapacidades', `/incapacidad/${id}/estado`, { estado });
    }

    renderBadge(estado) {
        return `<span class="badge badge-${estado}">${estado.replace('_', ' ')}</span>`;
    }

    renderTabla(incapacidades) {
        if (incapacidades.length === 0) {
            return '<tr><td colspan="8" style="text-align:center">No hay incapacidades registradas</td></tr>';
        }
        return incapacidades.map(i => `
            <tr>
                <td>${i.id}</td>
                <td>${i.empleado_id}</td>
                <td>${i.fecha_inicio}</td>
                <td>${i.fecha_fin}</td>
                <td>${i.tipo.replace(/_/g, ' ')}</td>
                <td>${i.dias_incapacidad} días</td>
                <td>${this.renderBadge(i.estado)}</td>
                <td>
                    <button class="btn btn-warning" onclick="app.editarIncapacidad(${i.id})">Editar</button>
                    <button class="btn btn-info" onclick="app.cambiarEstadoIncapacidad(${i.id}, '${i.estado}')">Estado</button>
                    <button class="btn btn-success" onclick="app.verSeguimiento(${i.id})">Seguimiento</button>
                </td>
            </tr>
        `).join('');
    }
}
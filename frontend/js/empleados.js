class Empleados extends Api {
    constructor() {
        super();
    }

    async listar() {
        return await this.get('empleados', '/empleados');
    }

    async obtener(id) {
        return await this.get('empleados', `/empleado/${id}`);
    }

    async buscarPorDocumento(documento) {
        return await this.get('empleados', `/empleados/documento/${documento}`);
    }

    async buscarPorArea(area) {
        return await this.get('empleados', `/empleados/area/${area}`);
    }

    async buscarPorEstado(estado) {
        return await this.get('empleados', `/empleados/estado/${estado}`);
    }

    async crear(data) {
        return await this.post('empleados', '/empleado', data);
    }

    async modificar(id, data) {
        return await this.put('empleados', `/empleado/${id}`, data);
    }

    async cambiarEstado(id, estado) {
        return await this.patch('empleados', `/empleado/${id}/estado`, { estado });
    }

    async eliminar(id) {
        return await this.delete('empleados', `/empleado/${id}`);
    }

    renderBadge(estado) {
        return `<span class="badge badge-${estado}">${estado}</span>`;
    }

    renderTabla(empleados) {
        if (empleados.length === 0) {
            return '<tr><td colspan="8" style="text-align:center">No hay empleados registrados</td></tr>';
        }
        return empleados.map(e => `
            <tr>
                <td>${e.id}</td>
                <td>${e.nombres} ${e.apellidos}</td>
                <td>${e.documento}</td>
                <td>${e.cargo}</td>
                <td>${e.area}</td>
                <td>${e.fecha_ingreso}</td>
                <td>${this.renderBadge(e.estado)}</td>
                <td>
                    <button class="btn btn-warning" onclick="app.editarEmpleado(${e.id})">Editar</button>
                    <button class="btn btn-info" onclick="app.cambiarEstadoEmpleado(${e.id}, '${e.estado}')">Estado</button>
                    <button class="btn btn-danger" onclick="app.eliminarEmpleado(${e.id})">Eliminar</button>
                </td>
            </tr>
        `).join('');
    }
}
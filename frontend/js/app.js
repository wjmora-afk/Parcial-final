class App {
    constructor() {
        this.auth          = new Auth();
        this.empleados     = new Empleados();
        this.incapacidades = new Incapacidades();
        this.seguimiento   = new Seguimiento();
        this.empleadoEditId     = null;
        this.incapacidadEditId  = null;
        this.seguimientoEditId  = null;
    }

    init() {
        if (!this.auth.isLoggedIn()) {
            this.mostrarLogin();
        } else {
            this.mostrarDashboard();
        }
    }

    // ─── LOGIN ────────────────────────────────────────────
    mostrarLogin() {
        document.getElementById('app').innerHTML = this.templateLogin();
    }

    async handleLogin() {
        const usuario    = document.getElementById('usuario').value;
        const contrasena = document.getElementById('contrasena').value;
        const alerta     = document.getElementById('login-alerta');

        try {
            const data = await this.auth.login(usuario, contrasena);
            if (data.token) {
                this.mostrarDashboard();
            } else {
                alerta.textContent = data.msg || 'Credenciales incorrectas';
                alerta.classList.add('show');
            }
        } catch (e) {
            alerta.textContent = 'Error al conectar con el servidor';
            alerta.classList.add('show');
        }
    }

    async handleLogout() {
        await this.auth.logout();
        this.mostrarLogin();
    }

    // ─── DASHBOARD ────────────────────────────────────────
    mostrarDashboard() {
        document.getElementById('app').innerHTML = this.templateDashboard();
        this.navegarA('empleados');
    }

    navegarA(pagina) {
        document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.sidebar-item').forEach(s => s.classList.remove('active'));
        document.getElementById(`page-${pagina}`).classList.add('active');
        document.getElementById(`nav-${pagina}`).classList.add('active');

        if (pagina === 'empleados')     this.cargarEmpleados();
        if (pagina === 'incapacidades') this.cargarIncapacidades();
        if (pagina === 'seguimiento')   this.cargarSeguimientos();
    }

    // ─── EMPLEADOS ────────────────────────────────────────
    async cargarEmpleados() {
        const data = await this.empleados.listar();
        document.getElementById('tabla-empleados').innerHTML = this.empleados.renderTabla(Array.isArray(data) ? data : []);
    }

    async editarEmpleado(id) {
        this.empleadoEditId = id;
        const e = await this.empleados.obtener(id);
        document.getElementById('emp-nombres').value      = e.nombres;
        document.getElementById('emp-apellidos').value    = e.apellidos;
        document.getElementById('emp-documento').value    = e.documento;
        document.getElementById('emp-correo').value       = e.correo;
        document.getElementById('emp-telefono').value     = e.telefono;
        document.getElementById('emp-cargo').value        = e.cargo;
        document.getElementById('emp-area').value         = e.area;
        document.getElementById('emp-fecha').value        = e.fecha_ingreso;
        document.getElementById('emp-estado').value       = e.estado;
        document.getElementById('modal-emp-titulo').textContent = 'Editar Empleado';
        this.abrirModal('modal-empleado');
    }

    abrirModalNuevoEmpleado() {
        this.empleadoEditId = null;
        document.getElementById('form-empleado').reset();
        document.getElementById('modal-emp-titulo').textContent = 'Nuevo Empleado';
        this.abrirModal('modal-empleado');
    }

    async guardarEmpleado() {
        const data = {
            nombres:      document.getElementById('emp-nombres').value,
            apellidos:    document.getElementById('emp-apellidos').value,
            documento:    document.getElementById('emp-documento').value,
            correo:       document.getElementById('emp-correo').value,
            telefono:     document.getElementById('emp-telefono').value,
            cargo:        document.getElementById('emp-cargo').value,
            area:         document.getElementById('emp-area').value,
            fecha_ingreso: document.getElementById('emp-fecha').value,
            estado:       document.getElementById('emp-estado').value
        };

        let result;
        if (this.empleadoEditId) {
            result = await this.empleados.modificar(this.empleadoEditId, data);
        } else {
            result = await this.empleados.crear(data);
        }

        if (result.msg) {
            this.mostrarAlerta('alerta-empleados', result.msg, 'error');
        } else {
            this.cerrarModal('modal-empleado');
            this.mostrarAlerta('alerta-empleados', 'Empleado guardado correctamente', 'success');
            this.cargarEmpleados();
        }
    }

    async cambiarEstadoEmpleado(id, estadoActual) {
        const nuevoEstado = estadoActual === 'activo' ? 'inactivo' : 'activo';
        await this.empleados.cambiarEstado(id, nuevoEstado);
        this.cargarEmpleados();
    }

    async eliminarEmpleado(id) {
        if (!confirm('¿Está seguro de eliminar este empleado?')) return;
        await this.empleados.eliminar(id);
        this.mostrarAlerta('alerta-empleados', 'Empleado eliminado', 'success');
        this.cargarEmpleados();
    }

    // ─── INCAPACIDADES ────────────────────────────────────
    async cargarIncapacidades() {
        const data = await this.incapacidades.listar();
        document.getElementById('tabla-incapacidades').innerHTML = this.incapacidades.renderTabla(Array.isArray(data) ? data : []);
    }

    abrirModalNuevaIncapacidad() {
        this.incapacidadEditId = null;
        document.getElementById('form-incapacidad').reset();
        document.getElementById('modal-inc-titulo').textContent = 'Nueva Incapacidad';
        this.abrirModal('modal-incapacidad');
    }

    async editarIncapacidad(id) {
        this.incapacidadEditId = id;
        const i = await this.incapacidades.obtener(id);
        document.getElementById('inc-empleado').value     = i.empleado_id;
        document.getElementById('inc-inicio').value       = i.fecha_inicio;
        document.getElementById('inc-fin').value          = i.fecha_fin;
        document.getElementById('inc-tipo').value         = i.tipo;
        document.getElementById('inc-diagnostico').value  = i.diagnostico_general;
        document.getElementById('inc-entidad').value      = i.entidad_medica;
        document.getElementById('inc-observaciones').value = i.observaciones || '';
        document.getElementById('inc-estado').value       = i.estado;
        document.getElementById('modal-inc-titulo').textContent = 'Editar Incapacidad';
        this.abrirModal('modal-incapacidad');
    }

    async guardarIncapacidad() {
        const data = {
            empleado_id:         document.getElementById('inc-empleado').value,
            fecha_inicio:        document.getElementById('inc-inicio').value,
            fecha_fin:           document.getElementById('inc-fin').value,
            tipo:                document.getElementById('inc-tipo').value,
            diagnostico_general: document.getElementById('inc-diagnostico').value,
            entidad_medica:      document.getElementById('inc-entidad').value,
            observaciones:       document.getElementById('inc-observaciones').value,
            estado:              document.getElementById('inc-estado').value
        };

        let result;
        if (this.incapacidadEditId) {
            result = await this.incapacidades.modificar(this.incapacidadEditId, data);
        } else {
            result = await this.incapacidades.crear(data);
        }

        if (result.msg) {
            this.mostrarAlerta('alerta-incapacidades', result.msg, 'error');
        } else {
            this.cerrarModal('modal-incapacidad');
            this.mostrarAlerta('alerta-incapacidades', 'Incapacidad guardada correctamente', 'success');
            this.cargarIncapacidades();
        }
    }

    async cambiarEstadoIncapacidad(id, estadoActual) {
        const estados = ['registrada', 'en_revision', 'aprobada', 'rechazada', 'finalizada'];
        const siguiente = estados[(estados.indexOf(estadoActual) + 1) % estados.length];
        if (!confirm(`¿Cambiar estado a "${siguiente}"?`)) return;
        await this.incapacidades.cambiarEstado(id, siguiente);
        this.cargarIncapacidades();
    }

    async verSeguimiento(incapacidadId) {
        this.incapacidadSeleccionada = incapacidadId;
        try {
            const data = await this.seguimiento.porIncapacidad(incapacidadId);
            document.getElementById('tabla-seguimiento-modal').innerHTML = this.seguimiento.renderTabla(Array.isArray(data) ? data : []);
        } catch(e) {
            document.getElementById('tabla-seguimiento-modal').innerHTML = '<tr><td colspan="5" style="text-align:center">Sin seguimientos</td></tr>';
        }
        this.abrirModal('modal-seguimiento-ver');
    }

    // ─── SEGUIMIENTO ──────────────────────────────────────
    async cargarSeguimientos() {
        const data = await this.seguimiento.listar();
        document.getElementById('tabla-seguimiento').innerHTML = this.seguimiento.renderTabla(Array.isArray(data) ? data : []);
    }

    abrirModalNuevoSeguimiento() {
        this.seguimientoEditId = null;
        document.getElementById('form-seguimiento').reset();
        this.abrirModal('modal-seguimiento');
    }

    async guardarSeguimiento() {
        const data = {
            incapacidad_id:      document.getElementById('seg-incapacidad').value,
            fecha:               document.getElementById('seg-fecha').value,
            comentario:          document.getElementById('seg-comentario').value,
            estado:              document.getElementById('seg-estado').value,
            usuario_responsable: document.getElementById('seg-usuario').value
        };

        const result = await this.seguimiento.crear(data);

        if (result.msg) {
            this.mostrarAlerta('alerta-seguimiento', result.msg, 'error');
        } else {
            this.cerrarModal('modal-seguimiento');
            this.mostrarAlerta('alerta-seguimiento', 'Seguimiento registrado correctamente', 'success');
            this.cargarSeguimientos();
        }
    }

    // ─── UTILIDADES ───────────────────────────────────────
    abrirModal(id) {
        document.getElementById(id).classList.add('show');
    }

    cerrarModal(id) {
        document.getElementById(id).classList.remove('show');
    }

    mostrarAlerta(id, mensaje, tipo) {
        const alerta = document.getElementById(id);
        alerta.textContent = mensaje;
        alerta.className = `alert alert-${tipo} show`;
        setTimeout(() => alerta.classList.remove('show'), 4000);
    }

    // ─── TEMPLATES ────────────────────────────────────────
    templateLogin() {
        return `
        <div class="login-container">
            <div class="login-box">
                <h2>🏥 Sistema de Incapacidades</h2>
                <div id="login-alerta" class="alert alert-error"></div>
                <div class="form-group">
                    <label>Usuario o Correo</label>
                    <input type="text" id="usuario" placeholder="Ingrese usuario o correo">
                </div>
                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" id="contrasena" placeholder="Ingrese contraseña">
                </div>
                <button class="btn btn-primary" onclick="app.handleLogin()">Iniciar Sesión</button>
            </div>
        </div>`;
    }

    templateDashboard() {
        return `
        <div class="navbar">
            <h1> Sistema de Incapacidades</h1>
            <div class="navbar-user">
                <span>👤 ${this.auth.getNombre()}</span>
                <button class="btn-logout" onclick="app.handleLogout()">Cerrar Sesión</button>
            </div>
        </div>
        <div class="layout">
            <div class="sidebar">
                <div class="sidebar-item" id="nav-empleados" onclick="app.navegarA('empleados')">👥 Empleados</div>
                <div class="sidebar-item" id="nav-incapacidades" onclick="app.navegarA('incapacidades')">🏥 Incapacidades</div>
                <div class="sidebar-item" id="nav-seguimiento" onclick="app.navegarA('seguimiento')">📋 Seguimiento</div>
            </div>
            <div class="content">

                <!-- PÁGINA EMPLEADOS -->
                <div class="page" id="page-empleados">
                    <div class="page-header">
                        <h2>👥 Empleados</h2>
                        <button class="btn btn-success" onclick="app.abrirModalNuevoEmpleado()">+ Nuevo Empleado</button>
                    </div>
                    <div id="alerta-empleados" class="alert"></div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th><th>Nombre</th><th>Documento</th>
                                    <th>Cargo</th><th>Área</th><th>Fecha Ingreso</th>
                                    <th>Estado</th><th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-empleados"></tbody>
                        </table>
                    </div>
                </div>

                <!-- PÁGINA INCAPACIDADES -->
                <div class="page" id="page-incapacidades">
                    <div class="page-header">
                        <h2>🏥 Incapacidades</h2>
                        <button class="btn btn-success" onclick="app.abrirModalNuevaIncapacidad()">+ Nueva Incapacidad</button>
                    </div>
                    <div id="alerta-incapacidades" class="alert"></div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th><th>Empleado</th><th>Inicio</th><th>Fin</th>
                                    <th>Tipo</th><th>Días</th><th>Estado</th><th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-incapacidades"></tbody>
                        </table>
                    </div>
                </div>

                <!-- PÁGINA SEGUIMIENTO -->
                <div class="page" id="page-seguimiento">
                    <div class="page-header">
                        <h2>📋 Seguimiento</h2>
                        <button class="btn btn-success" onclick="app.abrirModalNuevoSeguimiento()">+ Nuevo Seguimiento</button>
                    </div>
                    <div id="alerta-seguimiento" class="alert"></div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th><th>Fecha</th><th>Comentario</th>
                                    <th>Estado</th><th>Responsable</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-seguimiento"></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <!-- MODAL EMPLEADO -->
        <div class="modal-overlay" id="modal-empleado">
            <div class="modal">
                <div class="modal-header">
                    <h3 id="modal-emp-titulo">Empleado</h3>
                    <button class="modal-close" onclick="app.cerrarModal('modal-empleado')">×</button>
                </div>
                <div class="modal-body">
                    <form id="form-empleado">
                        <div class="form-group"><label>Nombres</label><input type="text" id="emp-nombres"></div>
                        <div class="form-group"><label>Apellidos</label><input type="text" id="emp-apellidos"></div>
                        <div class="form-group"><label>Documento</label><input type="text" id="emp-documento"></div>
                        <div class="form-group"><label>Correo</label><input type="email" id="emp-correo"></div>
                        <div class="form-group"><label>Teléfono</label><input type="text" id="emp-telefono"></div>
                        <div class="form-group"><label>Cargo</label><input type="text" id="emp-cargo"></div>
                        <div class="form-group"><label>Área</label><input type="text" id="emp-area"></div>
                        <div class="form-group"><label>Fecha Ingreso</label><input type="date" id="emp-fecha"></div>
                        <div class="form-group"><label>Estado</label>
                            <select id="emp-estado">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn-cancel" onclick="app.cerrarModal('modal-empleado')">Cancelar</button>
                    <button class="btn btn-success" onclick="app.guardarEmpleado()">Guardar</button>
                </div>
            </div>
        </div>

        <!-- MODAL INCAPACIDAD -->
        <div class="modal-overlay" id="modal-incapacidad">
            <div class="modal">
                <div class="modal-header">
                    <h3 id="modal-inc-titulo">Incapacidad</h3>
                    <button class="modal-close" onclick="app.cerrarModal('modal-incapacidad')">×</button>
                </div>
                <div class="modal-body">
                    <form id="form-incapacidad">
                        <div class="form-group"><label>ID Empleado</label><input type="number" id="inc-empleado"></div>
                        <div class="form-group"><label>Fecha Inicio</label><input type="date" id="inc-inicio"></div>
                        <div class="form-group"><label>Fecha Fin</label><input type="date" id="inc-fin"></div>
                        <div class="form-group"><label>Tipo</label>
                            <select id="inc-tipo">
                                <option value="enfermedad_general">Enfermedad General</option>
                                <option value="accidente_laboral">Accidente Laboral</option>
                                <option value="licencia_medica">Licencia Médica</option>
                                <option value="incapacidad_temporal">Incapacidad Temporal</option>
                            </select>
                        </div>
                        <div class="form-group"><label>Diagnóstico</label><textarea id="inc-diagnostico" rows="2"></textarea></div>
                        <div class="form-group"><label>Entidad Médica</label><input type="text" id="inc-entidad"></div>
                        <div class="form-group"><label>Observaciones</label><textarea id="inc-observaciones" rows="2"></textarea></div>
                        <div class="form-group"><label>Estado</label>
                            <select id="inc-estado">
                                <option value="registrada">Registrada</option>
                                <option value="en_revision">En Revisión</option>
                                <option value="aprobada">Aprobada</option>
                                <option value="rechazada">Rechazada</option>
                                <option value="finalizada">Finalizada</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn-cancel" onclick="app.cerrarModal('modal-incapacidad')">Cancelar</button>
                    <button class="btn btn-success" onclick="app.guardarIncapacidad()">Guardar</button>
                </div>
            </div>
        </div>

        <!-- MODAL SEGUIMIENTO -->
        <div class="modal-overlay" id="modal-seguimiento">
            <div class="modal">
                <div class="modal-header">
                    <h3>Nuevo Seguimiento</h3>
                    <button class="modal-close" onclick="app.cerrarModal('modal-seguimiento')">×</button>
                </div>
                <div class="modal-body">
                    <form id="form-seguimiento">
                        <div class="form-group"><label>ID Incapacidad</label><input type="number" id="seg-incapacidad"></div>
                        <div class="form-group"><label>Fecha</label><input type="date" id="seg-fecha"></div>
                        <div class="form-group"><label>Comentario</label><textarea id="seg-comentario" rows="3"></textarea></div>
                        <div class="form-group"><label>Estado</label>
                            <select id="seg-estado">
                                <option value="registrada">Registrada</option>
                                <option value="en_revision">En Revisión</option>
                                <option value="aprobada">Aprobada</option>
                                <option value="rechazada">Rechazada</option>
                                <option value="finalizada">Finalizada</option>
                            </select>
                        </div>
                        <div class="form-group"><label>Usuario Responsable</label><input type="text" id="seg-usuario"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn-cancel" onclick="app.cerrarModal('modal-seguimiento')">Cancelar</button>
                    <button class="btn btn-success" onclick="app.guardarSeguimiento()">Guardar</button>
                </div>
            </div>
        </div>

        <!-- MODAL VER SEGUIMIENTO -->
        <div class="modal-overlay" id="modal-seguimiento-ver">
            <div class="modal" style="width:700px">
                <div class="modal-header">
                    <h3>Historial de Seguimiento</h3>
                    <button class="modal-close" onclick="app.cerrarModal('modal-seguimiento-ver')">×</button>
                </div>
                <div class="modal-body">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th><th>Fecha</th><th>Comentario</th>
                                <th>Estado</th><th>Responsable</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-seguimiento-modal"></tbody>
                    </table>
                </div>
            </div>
        </div>`;
    }
}

const app = new App();
app.init();
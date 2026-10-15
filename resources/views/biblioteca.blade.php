<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca</title>
    <style>
        :root {
            --bg: #0f172a;
            --card: #1e293b;
            --text: #f8fafc;
            --muted: #94a3b8;
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --border: #334155;
            --success: #10b981;
            --danger: #ef4444;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: system-ui, sans-serif; }
        body { background: var(--bg); color: var(--text); padding: 2rem 1rem; }
        .container { max-width: 1100px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        h1, h2 { font-weight: 700; margin-bottom: 1rem; }
        
        .card { background: var(--card); border: 1px solid var(--border); border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-value { font-size: 2rem; font-weight: bold; color: var(--primary); margin-top: 0.5rem; }

        .main-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; }
        @media (max-width: 768px) { .main-layout { grid-template-columns: 1fr; } }

        label { display: block; margin-bottom: 0.4rem; color: var(--muted); font-size: 0.85rem; }
        input, select, button { width: 100%; padding: 0.75rem; border-radius: 6px; border: 1px solid var(--border); background: #0f172a; color: var(--text); margin-bottom: 1rem; }
        button { background: var(--primary); font-weight: 600; cursor: pointer; border: none; transition: 0.2s; }
        button:hover { background: var(--primary-hover); }
        .btn-secondary { background: #334155; width: auto; padding: 0.5rem 1rem; margin-bottom: 0; }
        .btn-secondary:hover { background: #475569; }

        table { width: 100%; border-collapse: collapse; margin-top: 0.5rem; }
        th, td { text-align: left; padding: 0.75rem; border-bottom: 1px solid var(--border); font-size: 0.9rem; }
        th { color: var(--muted); }
        .badge { display: inline-block; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; }
        .badge-success { background: rgba(16, 185, 129, 0.2); color: var(--success); }
        .badge-danger { background: rgba(239, 68, 68, 0.2); color: var(--danger); }

        .alert { padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem; display: none; font-size: 0.9rem; }
        .alert-success { background: rgba(16, 185, 129, 0.2); border: 1px solid var(--success); color: var(--success); }
        .alert-danger { background: rgba(239, 68, 68, 0.2); border: 1px solid var(--danger); color: var(--danger); }
        
        .login-wrapper { max-width: 420px; margin: 4rem auto; }
        .hidden { display: none !important; }
    </style>
</head>
<body>
<div class="container">

    <div id="section-login" class="login-wrapper hidden">
        <div class="card">
            <h2>Iniciar Sesión API</h2>
            <div id="login-alert" class="alert"></div>
            <form id="form-login">
                <label>Correo Electrónico:</label>
                <input type="email" id="login-email" value="admin@biblioteca.test" required>
                
                <label>Contraseña:</label>
                <input type="password" id="login-password" value="password" required>
                
                <button type="submit">Ingresar</button>
            </form>
        </div>
    </div>

    <div id="section-app" class="hidden">
        <div class="header">
            <h1>Panel de Biblioteca</h1>
            <button class="btn-secondary" id="btn-logout">Cerrar Sesión</button>
        </div>

        <div class="stats-grid">
            <div class="card">
                <span style="color: var(--muted)">Total Libros</span>
                <div class="stat-value" id="stat-total-libros">{{ $stats['total_libros'] }}</div>
            </div>
            <div class="card">
                <span style="color: var(--muted)">Libros Disponibles</span>
                <div class="stat-value" id="stat-disponibles">{{ $stats['libros_disponibles'] }}</div>
            </div>
            <div class="card">
                <span style="color: var(--muted)">Préstamos Activos</span>
                <div class="stat-value">{{ $stats['prestamos_activos'] }}</div>
            </div>
            <div class="card">
                <span style="color: var(--muted)">Usuarios Activos</span>
                <div class="stat-value">{{ $stats['usuarios_activos'] }}</div>
            </div>
        </div>

        <div class="main-layout">
            <div class="card">
                <h2>Catálogo de Libros</h2>
                <input type="text" id="buscador" placeholder="Buscar libro por título en tiempo real...">

                <table>
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Año</th>
                            <th>Stock</th>
                            <th>Autores</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-libros">
                        <tr><td colspan="4" style="text-align: center; color: var(--muted);">Cargando libros...</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h2>Nuevo Préstamo</h2>
                <div id="prestamo-alert" class="alert"></div>

                <form id="form-prestamo">
                    <label>Usuario solicitante:</label>
                    <select id="usuario_id" required>
                        <option value="">-- Seleccionar Usuario --</option>
                        @foreach($usuarios as $u)
                            <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                        @endforeach
                    </select>

                    <label>Libro a prestar:</label>
                    <select id="libro_id" required>
                        <option value="">-- Seleccionar Libro --</option>
                        @foreach($libros as $l)
                            <option value="{{ $l->id }}">{{ $l->titulo }} (Stock: {{$l->stock_disponible }})</option>
                        @endforeach
                    </select>

                    <button type="submit" id="btn-submit-prestamo">Registrar Préstamo</button>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
    const sectionLogin = document.getElementById('section-login');
    const sectionApp = document.getElementById('section-app');
    const formLogin = document.getElementById('form-login');
    const btnLogout = document.getElementById('btn-logout');
    const loginAlert = document.getElementById('login-alert');
    const tablaLibros = document.getElementById('tabla-libros');
    const inputBuscador = document.getElementById('buscador');
    const formPrestamo = document.getElementById('form-prestamo');
    const prestamoAlert = document.getElementById('prestamo-alert');

    let debounceTimer = null;

    function verificarSesion() {
        const token = localStorage.getItem('api_token');
        if (token) {
            sectionLogin.classList.add('hidden');
            sectionApp.classList.remove('hidden');
            cargarLibros();
        } else {
            sectionLogin.classList.remove('hidden');
            sectionApp.classList.add('hidden');
        }
    }

    formLogin.addEventListener('submit', async (e) => {
        e.preventDefault();
        mostrarMensaje(loginAlert, '', false);

        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;

        try {
            const res = await fetch('/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email, password })
            });

            const data = await res.json();

            if (!res.ok || !data.success) {
                mostrarMensaje(loginAlert, data.message || 'Error en las credenciales.', true);
                return;
            }

            const token = data.data?.token || data.access_token || data.token;
            localStorage.setItem('api_token', token);

            verificarSesion();
        } catch (err) {
            mostrarMensaje(loginAlert, 'Error de conexión con el servidor.', true);
        }
    });

    btnLogout.addEventListener('click', async () => {
        const token = localStorage.getItem('api_token');
        try {
            await fetch('/api/logout', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });
        } catch (e) {
            console.error('Error al llamar logout en backend', e);
        } finally {
            localStorage.removeItem('api_token');
            verificarSesion();
        }
    });

    async function cargarLibros(query = '') {
        const token = localStorage.getItem('api_token');
        const url = query ? `/api/libros?titulo=${encodeURIComponent(query)}` : '/api/libros';

        try {
            const res = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (res.status === 401) {
                localStorage.removeItem('api_token');
                verificarSesion();
                return;
            }

            const json = await res.json();
            const items = json.data || [];

            if (items.length === 0) {
                tablaLibros.innerHTML = '<tr><td colspan="4" style="text-align: center; color: var(--muted);">No se encontraron libros.</td></tr>';
                return;
            }

            tablaLibros.innerHTML = items.map(libro => {
                const autores = libro.autores && libro.autores.length 
                    ? libro.autores.map(a => `${a.nombre} ${a.apellido}`).join(', ') 
                    : 'Sin autor';
                const stockBadge = libro.stock_disponible > 0 
                    ? `<span class="badge badge-success">${libro.stock_disponible} disp.</span>` 
                    : `<span class="badge badge-danger">Agotado</span>`;

                return `
                    <tr>
                        <td><strong>${libro.titulo}</strong></td>
                        <td>${libro.anio_publicacion ?? 'N/A'}</td>
                        <td>${stockBadge}</td>
                        <td style="color: var(--muted);">${autores}</td>
                    </tr>
                `;
            }).join('');
        } catch (err) {
            tablaLibros.innerHTML = '<tr><td colspan="4" style="text-align: center; color: var(--danger);">Error al cargar los libros.</td></tr>';
        }
    }

    inputBuscador.addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            cargarLibros(e.target.value);
        }, 300);
    });

    formPrestamo.addEventListener('submit', async (e) => {
        e.preventDefault();
        mostrarMensaje(prestamoAlert, '', false);

        const token = localStorage.getItem('api_token');
        const usuarioId = document.getElementById('usuario_id').value;
        const libroId = document.getElementById('libro_id').value;

        if (!usuarioId || !libroId) {
            mostrarMensaje(prestamoAlert, 'Debe seleccionar un usuario y un libro.', true);
            return;
        }

        try {
            const res = await fetch('/api/prestamos', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    usuario_id: parseInt(usuarioId),
                    libro_id: parseInt(libroId)
                })
            });

            const data = await res.json();

            if (!res.ok) {
                mostrarMensaje(prestamoAlert, data.message || 'Error al procesar el préstamo.', true);
            } else {
                mostrarMensaje(prestamoAlert, '¡Préstamo registrado exitosamente!', false);
                formPrestamo.reset();
                cargarLibros();
            }
        } catch (err) {
            mostrarMensaje(prestamoAlert, 'Error de conexión con el servidor.', true);
        }
    });

    function mostrarMensaje(el, msg, esError) {
        if (!msg) {
            el.style.display = 'none';
            return;
        }
        el.textContent = msg;
        el.className = `alert ${esError ? 'alert-danger' : 'alert-success'}`;
        el.style.display = 'block';
    }

    verificarSesion();
</script>
</body>
</html>
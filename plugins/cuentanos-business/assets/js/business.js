/**
 * CNMX Business - Frontend JS
 */

(function() {
  'use strict';

  const CNMXBiz = {
    init() {
      this.initLogin();
      this.initRegistro();
      this.initDashboard();
      this.initEditarNegocio();
      this.initLogros();
      this.initRecompensas();
    },

    async request(endpoint, options = {}) {
      const url = `${cnmxBizData.apiUrl}${endpoint}`;
      const config = {
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': cnmxBizData.nonce
        },
        ...options
      };

      try {
        const response = await fetch(url, config);
        const data = await response.json();
        
        if (!response.ok) {
          throw new Error(data.message || 'Error en la solicitud');
        }
        
        return data;
      } catch (error) {
        this.showToast('error', 'Error', error.message);
        throw error;
      }
    },

    initLogin() {
      const form = document.getElementById('cnmx-login-form');
      if (!form) return;

      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.textContent = 'Iniciando...';

        const formData = new FormData(form);
        
        try {
          const data = await this.request('/login', {
            method: 'POST',
            body: JSON.stringify({
              email: formData.get('email'),
              password: formData.get('password')
            })
          });

          this.showToast('success', '¡Bienvenido!', 'Redirigiendo...');
          
          setTimeout(() => {
            window.location.href = data.redirect;
          }, 1000);

        } catch (error) {
          btn.disabled = false;
          btn.textContent = 'Iniciar Sesión';
        }
      });
    },

    initRegistro() {
      const form = document.getElementById('cnmx-register-form');
      if (!form) return;

      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.textContent = 'Creando cuenta...';

        const formData = new FormData(form);
        
        try {
          const data = await this.request('/registro', {
            method: 'POST',
            body: JSON.stringify({
              email: formData.get('email'),
              password: formData.get('password'),
              nombre_negocio: formData.get('nombre_negocio'),
              categoria: formData.get('categoria'),
              direccion: formData.get('direccion'),
              ciudad: formData.get('ciudad'),
              telefono: formData.get('telefono'),
              whatsapp: formData.get('whatsapp'),
              sitio_web: formData.get('sitio_web'),
              descripcion: formData.get('descripcion')
            })
          });

          this.showToast('success', '¡Cuenta creada!', 'Tu negocio está pendiente de aprobación');
          
          setTimeout(() => {
            window.location.href = '/dashboard-empresa';
          }, 1500);

        } catch (error) {
          btn.disabled = false;
          btn.textContent = 'Crear mi cuenta';
        }
      });
    },

    async initDashboard() {
      const container = document.getElementById('cnmx-dashboard-app');
      if (!container) return;

      container.innerHTML = '<div class="cnmx-dashboard-loading"><div class="spinner"></div><p>Cargando dashboard...</p></div>';

      try {
        const data = await this.request('/dashboard');
        this.renderDashboard(container, data);
      } catch (error) {
        container.innerHTML = '<div class="cnmx-error"><p>Error al cargar el dashboard</p></div>';
      }
    },

    renderDashboard(container, data) {
      const { negocio, membresia, metricas, resenas_recientes } = data;

      container.innerHTML = `
        <!-- Preview Link -->
        <a href="${negocio.url}" class="cnmx-preview-link" target="_blank">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
            <polyline points="15 3 21 3 21 9"/>
            <line x1="10" y1="14" x2="21" y2="3"/>
          </svg>
          Ver mi negocio en el directorio
        </a>

        <!-- Membership Card -->
        <div class="cnmx-membership-card">
          <div class="cnmx-membership-header">
            <div class="cnmx-membership-plan">${membresia.plan === 'gratis' ? 'Plan Gratis' : 'Plan ' + membresia.plan}</div>
            <div class="cnmx-membership-status ${membresia.activa ? 'activa' : ''}">
              ${membresia.activa ? 'Activa' : 'Inactiva'}
            </div>
          </div>
          <div class="cnmx-membership-info">
            <div class="cnmx-membership-item">
              <div class="cnmx-membership-item-value">${membresia.dias_restantes}</div>
              <div class="cnmx-membership-item-label">Días restantes</div>
            </div>
            <div class="cnmx-membership-item">
              <div class="cnmx-membership-item-value">${metricas.vistas}</div>
              <div class="cnmx-membership-item-label">Vistas</div>
            </div>
            <div class="cnmx-membership-item">
              <div class="cnmx-membership-item-value">${metricas.resenas}</div>
              <div class="cnmx-membership-item-label">Reseñas</div>
            </div>
          </div>
          <div class="cnmx-membership-actions">
            <button class="btn btn-secondary" onclick="CNMXBiz.mostrarPlanes()">Cambiar Plan</button>
            <button class="btn" style="background: white; color: var(--cnmx-primary);" onclick="CNMXBiz.renovarMembresia()">Renovar</button>
          </div>
        </div>

        <!-- Stats Grid -->
        <div class="cnmx-stats-grid">
          <div class="cnmy-stat-card">
            <div class="cnmx-stat-icon visitas">👁️</div>
            <div class="cnmx-stat-value">${metricas.vistas}</div>
            <div class="cnmx-stat-label">Visitas totales</div>
          </div>
          <div class="cnmx-stat-card">
            <div class="cnmx-stat-icon resenas">⭐</div>
            <div class="cnmx-stat-value">${metricas.resenas}</div>
            <div class="cnmx-stat-label">Reseñas</div>
          </div>
          <div class="cnmx-stat-card">
            <div class="cnmx-stat-icon rating">📊</div>
            <div class="cnmx-stat-value">${metricas.rating}</div>
            <div class="cnmx-stat-label">Rating promedio</div>
          </div>
          <div class="cnmx-stat-card">
            <div class="cnmx-stat-icon favoritos">❤️</div>
            <div class="cnmx-stat-value">${metricas.favoritos}</div>
            <div class="cnmx-stat-label">Favoritos</div>
          </div>
        </div>

        <!-- Recent Reviews -->
        ${resenas_recientes && resenas_recientes.length > 0 ? `
          <div class="cnmx-reviews-section">
            <h3 class="cnmx-reviews-title">Reseñas Recientes</h3>
            ${resenas_recientes.map(resena => `
              <div class="cnmx-review-item">
                <div class="cnmx-review-header">
                  <div class="cnmx-review-user">
                    <div class="cnmx-review-avatar">${resena.display_name.charAt(0).toUpperCase()}</div>
                    <div>
                      <div class="cnmx-review-name">${resena.display_name}</div>
                      <div class="cnmx-review-date">${new Date(resena.fecha).toLocaleDateString('es-MX')}</div>
                    </div>
                  </div>
                  <div class="cnmx-review-stars">${'★'.repeat(resena.calificacion)}${'☆'.repeat(5-resena.calificacion)}</div>
                </div>
                <div class="cnmx-review-content">${resena.contenido}</div>
              </div>
            `).join('')}
          </div>
        ` : '<p style="text-align: center; color: var(--cnmx-text-muted); padding: 40px;">Aún no tienes reseñas</p>'}
      `;
    },

    async initEditarNegocio() {
      const container = document.getElementById('cnmx-edit-negocio-app');
      if (!container) return;

      container.innerHTML = '<div class="cnmx-dashboard-loading"><div class="spinner"></div><p>Cargando...</p></div>';

      try {
        const data = await this.request('/negocio');
        this.renderEditarNegocio(container, data);
      } catch (error) {
        container.innerHTML = '<div class="cnmx-error"><p>Error al cargar los datos</p></div>';
      }
    },

    renderEditarNegocio(container, data) {
      const negocio = data;

      container.innerHTML = `
        <form id="cnmx-edit-form" class="cnmx-edit-form">
          <div class="cnmx-form-grid">
            <div class="cnmx-form-group">
              <label>Nombre del negocio</label>
              <input type="text" name="nombre" value="${negocio.nombre || ''}" required>
            </div>
            <div class="cnmx-form-group">
              <label>Teléfono</label>
              <input type="tel" name="telefono" value="${negocio.telefono || ''}" placeholder="55 1234 5678">
            </div>
            <div class="cnmx-form-group">
              <label>WhatsApp</label>
              <input type="tel" name="whatsapp" value="${negocio.whatsapp || ''}" placeholder="52 55 1234 5678">
            </div>
            <div class="cnmx-form-group">
              <label>Sitio web</label>
              <input type="url" name="sitio_web" value="${negocio.sitio_web || ''}" placeholder="https://tunegocio.com">
            </div>
            <div class="cnmx-form-group full-width">
              <label>Descripción</label>
              <textarea name="descripcion" rows="5" placeholder="Cuéntanos sobre tu negocio...">${negocio.descripcion || ''}</textarea>
            </div>
          </div>
          <div class="cnmx-edit-actions">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="/dashboard-empresa" class="btn btn-secondary">Cancelar</a>
          </div>
        </form>
      `;

      const form = document.getElementById('cnmx-edit-form');
      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.textContent = 'Guardando...';

        const formData = new FormData(form);
        
        try {
          await this.request('/negocio', {
            method: 'POST',
            body: JSON.stringify(Object.fromEntries(formData))
          });

          this.showToast('success', '¡Guardado!', 'Los cambios se han aplicado');

        } catch (error) {
          btn.disabled = false;
          btn.textContent = 'Guardar Cambios';
        }
      });
    },

    async mostrarPlanes() {
      const data = await this.request('/membresia');
      console.log('Planes disponibles:', data);
      this.showToast('info', 'Planes', 'Consulta los planes disponibles en tu dashboard');
    },

    async renovarMembresia() {
      const plan = prompt('Ingresa el plan (basico, premium, enterprise):');
      if (!plan) return;

      try {
        await this.request('/membresia/renovar', {
          method: 'POST',
          body: JSON.stringify({ plan })
        });
        
        this.showToast('success', '¡Éxito!', 'Membresía actualizada');
        this.initDashboard();
      } catch (error) {
        console.error(error);
      }
    },

    showToast(type, title, message) {
      const container = document.querySelector('.cnmx-toast-container') || this.createToastContainer();
      
      const toast = document.createElement('div');
      toast.className = `cnmx-toast cnmx-toast-animate`;
      
      const icons = { success: '✓', error: '✕', info: 'ℹ' };
      
      toast.innerHTML = `
        <div class="cnmx-toast-icon ${type}">${icons[type] || 'ℹ'}</div>
        <div class="cnmx-toast-content">
          <div class="cnmx-toast-title">${title}</div>
          <div class="cnmx-toast-message">${message}</div>
        </div>
        <button class="cnmx-toast-close">✕</button>
      `;

      container.appendChild(toast);

      toast.querySelector('.cnmx-toast-close').addEventListener('click', () => toast.remove());
      setTimeout(() => toast.remove(), 4000);
    },

    createToastContainer() {
      const container = document.createElement('div');
      container.className = 'cnmx-toast-container';
      document.body.appendChild(container);
      return container;
    },

    async initLogros() {
      const container = document.getElementById('cnmx-logros-app');
      if (!container) return;

      container.innerHTML = '<div class="cnmx-dashboard-loading"><div class="spinner"></div><p>Cargando logros...</p></div>';

      try {
        const data = await this.request('/mis-logros');
        this.renderLogros(container, data.logros);
      } catch (error) {
        container.innerHTML = '<p>Error al cargar logros</p>';
      }
    },

    renderLogros(container, logros) {
      const obtenidos = logros.filter(l => l.obtenido);
      const disponibles = logros.filter(l => !l.obtenido);

      container.innerHTML = `
        <div class="cnmx-logros-section">
          <h3 class="cnmx-section-title">🏆 Mis Logros (${obtenidos.length})</h3>
          <div class="cnmx-logros-grid">
            ${obtenidos.map(logro => `
              <div class="cnmx-logro-card obtenido">
                <div class="cnmx-logro-icon">${logro.icono}</div>
                <div class="cnmx-logro-info">
                  <div class="cnmx-logro-nombre">${logro.titulo}</div>
                  <div class="cnmx-logro-megafonos">+${logro.megafonos} 🎤</div>
                </div>
              </div>
            `).join('')}
            ${disponibles.map(logro => `
              <div class="cnmx-logro-card">
                <div class="cnmx-logro-icon" style="opacity: 0.4">${logro.icono}</div>
                <div class="cnmx-logro-info">
                  <div class="cnmx-logro-nombre">${logro.titulo}</div>
                  <div class="cnmx-logro-megafonos">+${logro.megafonos} 🎤</div>
                </div>
              </div>
            `).join('')}
          </div>
        </div>
      `;
    },

    async initRecompensas() {
      const container = document.getElementById('cnmx-recompensas-app');
      if (!container) return;

      container.innerHTML = '<div class="cnmx-dashboard-loading"><div class="spinner"></div><p>Cargando...</p></div>';

      try {
        const [recompensasData, misData] = await Promise.all([
          this.request('/recompensas'),
          this.request('/mis-recompensas')
        ]);

        this.renderRecompensas(container, recompensasData.recompensas, misData);
      } catch (error) {
        container.innerHTML = '<p>Error al cargar recompensas</p>';
      }
    },

    renderRecompensas(container, recompensas, misDatos) {
      const { megafonos, canjeadas } = misData;

      container.innerHTML = `
        <div class="cnmx-recompensas-section">
          <div class="cnmx-recompensas-header">
            <h3 class="cnmx-section-title">🎁 Canjea tus Megáfonos</h3>
            <div class="cnmx-megafonos-display">
              <span class="cnmx-megafonos-num">${megafonos}</span>
              <span class="cnmx-megafonos-label">Megáfonos disponibles</span>
            </div>
          </div>
          
          <div class="cnmx-recompensas-grid">
            ${recompensas.map(r => {
              const puede = megafonos >= r.megafonos;
              return `
                <div class="cnmx-recompensa-card ${puede ? 'disponible' : 'bloqueada'}">
                  <div class="cnmx-recompensa-img">
                    ${r.imagen ? `<img src="${r.imagen}" alt="${r.titulo}">` : '🎁'}
                  </div>
                  <div class="cnmx-recompensa-info">
                    <div class="cnmx-recompensa-nombre">${r.titulo}</div>
                    <div class="cnmx-recompensa-precio">${r.megafonos} 🎤</div>
                    <button class="btn btn-primary cnmx-recompensa-btn" 
                      data-id="${r.id}" 
                      ${!puede ? 'disabled' : ''}
                      onclick="CNMXBiz.canjearRecompensa(${r.id})">
                      ${puede ? 'Canjear' : 'Necesitas más Megáfonos'}
                    </button>
                  </div>
                </div>
              `;
            }).join('')}
          </div>

          ${canjeadas.length > 0 ? `
            <div class="cnmx-canjeadas-section" style="margin-top: 40px;">
              <h4 class="cnmx-section-title">✅ Mis Recompensas Canjeadas</h4>
              <div class="cnmx-canjeadas-list">
                ${canjeadas.map(c => `
                  <div class="cnmx-canjeada-item">
                    <div class="cnmx-canjeada-info">
                      <strong>${c.recompensa_nombre}</strong>
                      <div class="cnmx-canjeada-codigo">Código: <code>${c.codigo}</code></div>
                    </div>
                    <div class="cnmx-canjeada-fecha">${new Date(c.fecha).toLocaleDateString('es-MX')}</div>
                  </div>
                `).join('')}
              </div>
            </div>
          ` : ''}
        </div>
      `;
    },

    async canjearRecompensa(recompensaId) {
      if (!confirm('¿Estás seguro de canjear esta recompensa?')) return;

      try {
        const result = await this.request('/recompensas/canjear', {
          method: 'POST',
          body: JSON.stringify({ recompensa_id: recompensaId })
        });

        this.showToast('success', '¡Canjeado!', `Tu código: ${result.codigo}`);
        
        setTimeout(() => {
          this.initRecompensas();
        }, 1500);
      } catch (error) {
        this.showToast('error', 'Error', error.message);
      }
    }
  };

  document.addEventListener('DOMContentLoaded', () => CNMXBiz.init());
  window.CNMXBiz = CNMXBiz;

})();
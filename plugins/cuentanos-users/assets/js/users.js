/* ============================================
   CNMX Users JavaScript
   ============================================ */

(function() {
    'use strict';

    const CNMXUsers = {
        data: window.cnmxUsersData || {},

        init() {
            this.initLoginForm();
            this.initRegisterForm();
            this.initProfileNavigation();
            this.loadProfileData();
        },

        async request(endpoint, options = {}) {
            const url = `${this.data.apiUrl}${endpoint}`;
            const defaults = {
                headers: {
                    'X-WP-Nonce': this.data.nonce
                }
            };
            
            const config = { ...defaults, ...options };
            
            try {
                const response = await fetch(url, config);
                const result = await response.json();
                
                if (!response.ok && !result.success) {
                    throw new Error(result.message || 'Error en la solicitud');
                }
                
                return result;
            } catch (error) {
                console.error('CNMX Users Error:', error);
                this.showToast('error', 'Error', error.message);
                throw error;
            }
        },

        initLoginForm() {
            const form = document.getElementById('cnmx-user-login-form');
            if (!form) return;

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const btn = form.querySelector('button[type="submit"]');
                const btnText = btn.querySelector('.btn-text');
                const btnLoading = btn.querySelector('.btn-loading');
                
                btn.disabled = true;
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline';
                
                const email = form.querySelector('[name="email"]').value;
                const password = form.querySelector('[name="password"]').value;
                
                try {
                    const result = await this.request('/auth/login', {
                        method: 'POST',
                        body: new URLSearchParams({ email, password })
                    });
                    
                    this.showToast('success', '¡Bienvenido!', 'Iniciando sesión...');
                    setTimeout(() => {
                        window.location.href = this.data.homeUrl + '/perfil';
                    }, 1000);
                } catch (error) {
                    this.showToast('error', 'Error', error.message);
                } finally {
                    btn.disabled = false;
                    btnText.style.display = 'inline';
                    btnLoading.style.display = 'none';
                }
            });
        },

        initRegisterForm() {
            const form = document.getElementById('cnmx-user-register-form');
            if (!form) return;

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const btn = form.querySelector('button[type="submit"]');
                const btnText = btn.querySelector('.btn-text');
                const btnLoading = btn.querySelector('.btn-loading');
                
                btn.disabled = true;
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline';
                
                const nombre = form.querySelector('[name="nombre"]').value;
                const email = form.querySelector('[name="email"]').value;
                const password = form.querySelector('[name="password"]').value;
                const cumpleanos = form.querySelector('[name="cumpleanos"]').value;
                
                try {
                    const result = await this.request('/auth/register', {
                        method: 'POST',
                        body: JSON.stringify({ nombre, email, password, cumpleanos })
                    });
                    
                    this.showToast('success', '¡Cuenta creada!', 'Recibiste 50 Megáfonos de bienvenida');
                    setTimeout(() => {
                        window.location.href = this.data.homeUrl + '/perfil';
                    }, 1500);
                } catch (error) {
                    this.showToast('error', 'Error', error.message);
                } finally {
                    btn.disabled = false;
                    btnText.style.display = 'inline';
                    btnLoading.style.display = 'none';
                }
            });
        },

        initProfileNavigation() {
            const navItems = document.querySelectorAll('.cnmx-profile-nav-item');
            const sections = document.querySelectorAll('.cnmx-profile-section');

            navItems.forEach(item => {
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    const target = item.dataset.section;

                    navItems.forEach(n => n.classList.remove('active'));
                    item.classList.add('active');

                    sections.forEach(s => s.classList.remove('active'));
                    const section = document.getElementById('section-' + target);
                    if (section) section.classList.add('active');
                });
            });
        },

        async loadProfileData() {
            if (!this.data.isLoggedIn) return;

            try {
                const profile = await this.request('/usuario/perfil');
                
                const megafonosEl = document.getElementById('cnmx-megafonos-count');
                const nivelBadgeEl = document.getElementById('cnmx-nivel-badge');
                const nivelProgressEl = document.getElementById('cnmx-nivel-progress');
                const statResenasEl = document.getElementById('cnmx-stat-resenas');
                const statFavoritosEl = document.getElementById('cnmx-stat-favoritos');
                
                if (megafonosEl) megafonosEl.textContent = profile.megafonos;
                if (nivelBadgeEl) nivelBadgeEl.textContent = this.capitalizeFirst(profile.nivel);
                
                const siguiente = profile.nivel === 'influencer' ? null : 
                    (profile.nivel === 'explorador' ? { nombre: 'Crítico', necesita: 500 - profile.megafonos } : 
                    { nombre: 'Influencer', necesita: 1000 - profile.megafonos });
                
                if (nivelProgressEl) {
                    nivelProgressEl.textContent = siguiente ? 
                        `${siguiente.necesita} para ${siguiente.nombre}` : '¡Eres el máximo nivel! 🎉';
                }
                
                if (statResenasEl) statResenasEl.textContent = profile.stats.resenas;
                if (statFavoritosEl) statFavoritosEl.textContent = profile.stats.favoritos;
                
                if (profile.cumpleanos_bonus) {
                    setTimeout(() => {
                        this.showToast('success', '🎂 ¡Feliz cumpleaños!', `Recibiste ${profile.cumpleanos_bonus.bonus} Megáfonos extra`);
                    }, 1000);
                }

                this.loadFavoritos();
                this.loadResenas();
                this.loadLogros();
                this.loadRecompensas();
                this.loadCanjeadas();
                this.loadNegocio();
            } catch (error) {
                console.error('Error loading profile:', error);
            }
        },

        async loadFavoritos() {
            const container = document.getElementById('cnmx-favoritos-list');
            if (!container) return;

            try {
                const result = await this.request('/favoritos');
                
                if (result.favoritos && result.favoritos.length > 0) {
                    container.innerHTML = result.favoritos.map(fav => `
                        <div class="cnmx-favorito-item">
                            <a href="${fav.post_url}" class="cnmx-favorito-link">
                                <span class="cnmx-favorito-nombre">${fav.post_title}</span>
                            </a>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = '<p class="cnmx-empty-state">Aún no tienes favoritos</p>';
                }
            } catch (error) {
                container.innerHTML = '<p class="cnmx-empty-state">Error al cargar favoritos</p>';
            }
        },

        async loadResenas() {
            const container = document.getElementById('cnmx-resenas-list');
            if (!container) return;

            container.innerHTML = '<p class="cnmx-empty-state">Cargando...</p>';
        },

        async loadLogros() {
            const container = document.getElementById('cnmx-logros-grid');
            if (!container) return;

            try {
                const result = await this.request('/usuario/logros');
                const logrosObtenidos = result.logros ? result.logros.filter(l => l.obtenido).length : 0;
                document.getElementById('cnmx-stat-logros').textContent = logrosObtenidos;
                
                if (result.logros && result.logros.length > 0) {
                    container.innerHTML = result.logros.map(logro => `
                        <div class="cnmx-logro-card ${logro.obtenido ? 'completed' : ''}">
                            <span class="cnmx-logro-icon">${logro.obtenido ? '✅' : '🔒'}</span>
                            <h4>${logro.post_title}</h4>
                            <p>${logro.post_content || 'Completa esta acción'}</p>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = '<p class="cnmx-empty-state">No hay logros disponibles</p>';
                }
            } catch (error) {
                container.innerHTML = '<p class="cnmx-empty-state">Error al cargar logros</p>';
            }
        },

        capitalizeFirst(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        },

        showToast(type, title, message) {
            if (!window.CNMX || !window.CNMX.showToast) {
                alert(title + ': ' + message);
                return;
            }
            window.CNMX.showToast(type, title, message);
        },

        async loadRecompensas() {
            const container = document.getElementById('cnmx-recompensas-grid');
            if (!container) return;

            try {
                const result = await this.request('/recompensas');
                
                if (result.recompensas && result.recompensas.length > 0) {
                    container.innerHTML = result.recompensas.map(rec => `
                        <div class="cnmx-recompensa-card ${rec.disponible ? '' : 'bloqueada'}">
                            <img src="${rec.imagen}" alt="${rec.titulo}" class="cnmx-recompensa-img">
                            <div class="cnmx-recompensa-content">
                                <h4>${rec.titulo}</h4>
                                <p>${rec.descripcion || 'Recompensa especial'}</p>
                                <div class="cnmx-recompensa-meta">
                                    <span class="cnmx-recompensa-cost">🎤 ${rec.megafonos} Megáfonos</span>
                                    ${rec.disponible 
                                        ? `<button class="btn btn-sm btn-primary cnmx-btn-canjear" data-id="${rec.id}">Canjear</button>`
                                        : `<span class="cnmx-recompensa-bloqueada">Necesitas más Megáfonos</span>`
                                    }
                                </div>
                            </div>
                        </div>
                    `).join('');

                    document.querySelectorAll('.cnmx-btn-canjear').forEach(btn => {
                        btn.addEventListener('click', () => this.canjearRecompensa(btn.dataset.id));
                    });
                } else {
                    container.innerHTML = '<p class="cnmx-empty-state">No hay recompensas disponibles</p>';
                }
            } catch (error) {
                container.innerHTML = '<p class="cnmx-empty-state">Error al cargar recompensas</p>';
            }
        },

        async canjearRecompensa(recompensaId) {
            try {
                const result = await this.request('/recompensas/canjear', {
                    method: 'POST',
                    body: JSON.stringify({ recompensa_id: recompensaId })
                });

                this.showToast('success', '¡Canje exitoso!', result.message || 'Canje realizado');
                document.getElementById('cnmx-megafonos-count').textContent = result.megafonos_restantes;
                this.loadRecompensas();
                this.loadCanjeadas();
            } catch (error) {
                this.showToast('error', 'Error', error.message);
            }
        },

        async loadCanjeadas() {
            const container = document.getElementById('cnmx-canjeados-list');
            if (!container) return;

            try {
                const result = await this.request('/recompensas/mis');
                
                if (result.canjes && result.canjes.length > 0) {
                    container.innerHTML = result.canjes.map(canje => `
                        <div class="cnmx-canjeado-item">
                            <div class="cnmx-canjeado-info">
                                <h4>${canje.recompensa}</h4>
                                <p><strong>Código:</strong> ${canje.codigo}</p>
                                <p>${canje.instrucciones || 'Presenta el código en el establecimiento'}</p>
                                <small>Canjeado el ${new Date(canje.fecha).toLocaleDateString()}</small>
                            </div>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = '<p class="cnmx-empty-state">Aún no has canjeado recompensas</p>';
                }
            } catch (error) {
                container.innerHTML = '<p class="cnmx-empty-state">Error al cargar canjes</p>';
            }
        },

        async loadNegocio() {
            const container = document.getElementById('cnmx-negocio-info');
            if (!container) return;

            try {
                const result = await this.request('/usuario/negocio');
                
                if (result.negocio) {
                    const negocio = result.negocio;
                    const statusClass = negocio.status === 'publish' ? 'status-aprobado' : 'status-pendiente';
                    const statusText = negocio.status === 'publish' ? '✅ Aprobado' : '⏳ Pendiente de aprobación';
                    
                    container.innerHTML = `
                        <div class="cnmx-negocio-card ${statusClass}">
                            <div class="cnmx-negocio-header">
                                <h3>${negocio.nombre}</h3>
                                <span class="cnmx-negocio-status">${statusText}</span>
                            </div>
                            <div class="cnmx-negocio-details">
                                ${negocio.telefono ? `<p><strong>📞</strong> ${negocio.telefono}</p>` : ''}
                                ${negocio.whatsapp ? `<p><strong>💬</strong> ${negocio.whatsapp}</p>` : ''}
                                ${negocio.email ? `<p><strong>✉️</strong> ${negocio.email}</p>` : ''}
                                ${negocio.direccion ? `<p><strong>📍</strong> ${negocio.direccion}${negocio.ciudad ? ', ' + negocio.ciudad : ''}</p>` : ''}
                                <p><strong>🏷️ Plan:</strong> ${negocio.plan || 'Gratis'}</p>
                            </div>
                            ${negocio.status === 'publish' 
                                ? `<a href="/mi-negocio" class="btn btn-primary">Editar mi negocio</a>`
                                : `<p class="cnmx-negocio-pending-msg">Te notificaremos por email cuando tu negocio sea aprobado.</p>`
                            }
                        </div>
                    `;
                } else {
                    container.innerHTML = `
                        <div class="cnmx-negocio-empty">
                            <p>¿Tienes un negocio?</p>
                            <a href="/registrar-negocio" class="btn btn-primary">Registrar mi negocio</a>
                        </div>
                    `;
                }
            } catch (error) {
                container.innerHTML = `
                    <div class="cnmx-negocio-empty">
                        <p>¿Tienes un negocio?</p>
                        <a href="/registrar-negocio" class="btn btn-primary">Registrar mi negocio</a>
                    </div>
                `;
            }
        }
    };

    document.addEventListener('DOMContentLoaded', () => CNMXUsers.init());
    window.CNMXUsers = CNMXUsers;

})();

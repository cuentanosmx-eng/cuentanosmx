/* ============================================
   CNMX Fotos JavaScript
   ============================================ */

(function() {
    'use strict';

    window.CNMXFotos = {
        init: function() {
            this.initUpload();
            this.initLightbox();
        },

        initUpload: function() {
            const zone = document.querySelector('.cnmx-foto-upload-zone');
            if (!zone) return;

            const input = zone.querySelector('input[type="file"]');
            
            zone.addEventListener('click', () => input.click());
            
            zone.addEventListener('dragover', (e) => {
                e.preventDefault();
                zone.classList.add('dragover');
            });
            
            zone.addEventListener('dragleave', () => {
                zone.classList.remove('dragover');
            });
            
            zone.addEventListener('drop', (e) => {
                e.preventDefault();
                zone.classList.remove('dragover');
                this.handleFiles(e.dataTransfer.files);
            });
            
            input.addEventListener('change', () => {
                if (input.files.length) {
                    this.handleFiles(input.files);
                }
            });
        },

        handleFiles: function(files) {
            const preview = document.querySelector('.cnmx-fotos-preview');
            if (!preview) return;

            Array.from(files).forEach(file => {
                if (!file.type.startsWith('image/')) return;
                
                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.className = 'cnmx-foto-item';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <div class="cnmx-foto-overlay">
                            <span class="cnmx-foto-desc">Subiendo...</span>
                        </div>
                    `;
                    preview.appendChild(div);
                    
                    this.uploadFile(file, div);
                };
                reader.readAsDataURL(file);
            });
        },

        uploadFile: function(file, previewEl) {
            const formData = new FormData();
            formData.append('foto', file);
            formData.append('negocio_id', window.cnmxBusinessData?.negocioId || 0);
            
            fetch('/wp-json/cnmx/v1/fotos/upload', {
                method: 'POST',
                headers: {
                    'X-WP-Nonce': cnmxFotosData?.nonce || ''
                },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    previewEl.innerHTML = `<img src="${data.foto.thumb}" alt="Foto">`;
                    previewEl.dataset.id = data.foto.id;
                    previewEl.classList.add('cnmx-foto-item');
                } else {
                    previewEl.remove();
                    console.error('Upload failed');
                }
            })
            .catch(err => {
                previewEl.remove();
                console.error('Upload error:', err);
            });
        },

        initLightbox: function() {
            const gallery = document.querySelector('.cnmx-fotos-gallery');
            if (!gallery) return;

            const items = gallery.querySelectorAll('.cnmx-foto-item img');
            let currentIndex = 0;
            const images = [];

            items.forEach((img, i) => {
                images.push(img.src.replace('-150x150', '').replace('-300x300', '').replace('-600x600', ''));
                img.closest('.cnmx-foto-item')?.addEventListener('click', () => {
                    currentIndex = i;
                    this.openLightbox(images[currentIndex], images.length, currentIndex + 1);
                });
            });
        },

        openLightbox: function(src, total, current) {
            let html = `
                <div class="cnmx-lightbox active" id="cnmx-lightbox">
                    <button class="cnmx-lightbox-close" onclick="CNMXFotos.closeLightbox()">×</button>
                    <div class="cnmx-lightbox-content">
                        <img src="${src}" alt="Foto">
                    </div>
                    <span class="cnmx-lightbox-counter">${current} / ${total}</span>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', html);
        },

        closeLightbox: function() {
            document.getElementById('cnmx-lightbox')?.remove();
        }
    };

    document.addEventListener('DOMContentLoaded', () => window.CNMXFotos.init());
})();

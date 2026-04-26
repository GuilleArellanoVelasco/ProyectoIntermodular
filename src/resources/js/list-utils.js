/**
 * Utilidades compartidas para listados con sorting y paginación infinita
 */

/**
 * Inicializa el dropdown de ordenamiento
 */
export function initSortDropdown() {
    const sortDropdown = document.querySelector('.sort-dropdown');
    const sortTrigger = sortDropdown?.querySelector('.sort-dropdown-trigger');
    const sortItems = sortDropdown?.querySelectorAll('.sort-dropdown-item');
    const dirToggle = document.querySelector('.sort-direction-toggle');

    if (sortTrigger) {
        sortTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            sortDropdown.classList.toggle('open');
        });
    }

    sortItems?.forEach(item => {
        item.addEventListener('click', function() {
            const value = this.dataset.value;
            updateUrlParam('order', value);
        });
    });

    if (dirToggle) {
        dirToggle.addEventListener('click', function() {
            const currentDir = this.dataset.dir;
            const newDir = currentDir === 'asc' ? 'desc' : 'asc';
            updateUrlParam('dir', newDir);
        });
    }

    // Cerrar dropdown al hacer clic fuera
    document.addEventListener('click', function() {
        sortDropdown?.classList.remove('open');
    });
}

/**
 * Inicializa la carga infinita (load more)
 * @param {string} containerId - ID del contenedor donde se añaden los elementos
 * @param {string} entityName - Nombre de la entidad para mensajes de error
 */
export function initLoadMore(containerId, entityName = 'elementos') {
    const loadMoreBtn = document.getElementById('load-more-btn');
    const loadMoreContainer = document.getElementById('load-more-container');
    const container = document.getElementById(containerId);

    if (!loadMoreBtn || !container) return;

    loadMoreBtn.addEventListener('click', async function() {
        const page = this.dataset.page;
        const url = new URL(window.location.href);
        url.searchParams.set('page', page);

        this.textContent = 'Cargando...';
        this.disabled = true;

        try {
            const response = await fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            // Añadir nuevos elementos
            container.insertAdjacentHTML('beforeend', data.html);

            // Actualizar botón
            if (data.hasMore) {
                this.dataset.page = data.nextPage;
                this.textContent = 'Ver más';
                this.disabled = false;
            } else {
                loadMoreContainer?.classList.add('hidden');
            }
        } catch (error) {
            console.error(`Error cargando más ${entityName}:`, error);
            this.textContent = 'Ver más';
            this.disabled = false;
        }
    });
}

/**
 * Actualiza un parámetro de la URL y recarga la página
 * @param {string} param - Nombre del parámetro
 * @param {string} value - Valor del parámetro
 */
function updateUrlParam(param, value) {
    const url = new URL(window.location.href);
    url.searchParams.set(param, value);
    window.location.href = url.toString();
}

/**
 * Inicializa todo: sorting y load more
 * @param {string} containerId - ID del contenedor de elementos
 * @param {string} entityName - Nombre de la entidad
 */
export function initListPage(containerId, entityName) {
    document.addEventListener('DOMContentLoaded', function() {
        initSortDropdown();
        initLoadMore(containerId, entityName);
    });
}

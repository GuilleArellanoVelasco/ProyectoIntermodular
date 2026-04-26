import './bootstrap';
import { initSortDropdown, initLoadMore, initListPage } from './list-utils';

// Exponer funciones globalmente para uso en vistas Blade
window.ListUtils = {
    initSortDropdown,
    initLoadMore,
    initListPage
};
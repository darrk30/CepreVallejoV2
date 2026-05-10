function buildEmbedUrl(url) {
    if (!url) return '';
    url = url.trim();

    // ── YouTube ──
    // Formatos: watch?v=, youtu.be/, /shorts/, /embed/
    const ytMatch = url.match(
        /(?:youtube\.com\/(?:watch\?(?:.*&)?v=|shorts\/|embed\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/
    );
    if (ytMatch) {
        // Preservar timestamp si existe (?t=XX o &t=XX)
        const tMatch = url.match(/[?&]t=(\d+)/);
        const start = tMatch ? `&start=${tMatch[1]}` : '';
        return `https://www.youtube-nocookie.com/embed/${ytMatch[1]}?rel=0&modestbranding=1${start}`;
    }

    // ── Vimeo ──
    const vimeoMatch = url.match(/vimeo\.com\/(\d+)/);
    if (vimeoMatch) {
        return `https://player.vimeo.com/video/${vimeoMatch[1]}?dnt=1`;
    }

    // ── URL directa de archivo u otro servicio (devolver tal cual) ──
    return url;
}

/* ══════════════════════════════════════════════════════════
   DROPDOWN HELPERS
══════════════════════════════════════════════════════════ */
function toggleDropdown(id, event) {
    event.stopPropagation();
    const target = document.getElementById(id);
    const isOpen = target.classList.contains('open');
    closeAllDropdowns();
    if (!isOpen) target.classList.add('open');
}

function closeAllDropdowns() {
    document.querySelectorAll('.dropdown-menu.open').forEach(m => m.classList.remove('open'));
}

// Cerrar al hacer click fuera
document.addEventListener('click', closeAllDropdowns);

/* ══════════════════════════════════════════════════════════
   AulaDnD — Drag & Drop para secciones y detalles
══════════════════════════════════════════════════════════ */
const AulaDnD = (function () {

    let secDragSrc = null;
    let detDragSrc = null;
    let secSortOn = false;
    const detSortOn = new Set();

    /* ── Notificación Filament v3 ── */
    function notify(title, status = 'success') {
        try {
            window.dispatchEvent(new CustomEvent('filament-notification', {
                detail: {
                    title,
                    status,
                    duration: 4000
                }
            }));
        } catch (e) { }
        try {
            Livewire.dispatch('filament.notifications.send', {
                notification: {
                    title,
                    status
                }
            });
        } catch (e) { }
    }

    /* ══ SECCIONES ══ */
    function bindSection(card) {
        if (card._secBound) return;
        card._secBound = true;

        card.addEventListener('dragstart', e => {
            if (!card._handleDown) {
                e.preventDefault();
                return;
            }
            secDragSrc = card;
            requestAnimationFrame(() => card.classList.add('dragging'));
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', card.dataset.id);
        });

        card.addEventListener('dragend', () => {
            card.classList.remove('dragging');
            card._handleDown = false;
            secDragSrc = null;
            clearDropClasses();
            refreshSectionBadges();
        });

        card.addEventListener('dragover', e => {
            if (!secSortOn || !secDragSrc || secDragSrc === card || detDragSrc) return;
            e.preventDefault();
            e.stopPropagation();
            clearCardDrop(card);
            const mid = card.getBoundingClientRect().top + card.getBoundingClientRect().height / 2;
            card.classList.add(e.clientY < mid ? 'drag-over-top' : 'drag-over-bottom');
        });

        card.addEventListener('dragleave', e => {
            if (!card.contains(e.relatedTarget)) clearCardDrop(card);
        });

        card.addEventListener('drop', e => {
            if (!secSortOn || !secDragSrc || secDragSrc === card || detDragSrc) return;
            e.preventDefault();
            e.stopPropagation();
            const container = document.getElementById('sectionsContainer');
            const mid = card.getBoundingClientRect().top + card.getBoundingClientRect().height / 2;
            container.insertBefore(secDragSrc, e.clientY < mid ? card : card.nextSibling);
            clearCardDrop(card);
            refreshSectionBadges();
        });
    }

    function refreshSectionBadges() {
        document.querySelectorAll('#sectionsContainer .sec-card').forEach((c, i) => {
            const b = document.getElementById('secBadge-' + c.dataset.id);
            if (b) b.textContent = i + 1;
        });
    }

    function commitSectionOrder() {
        const ids = [...document.querySelectorAll('#sectionsContainer .sec-card')]
            .map(c => parseInt(c.dataset.id));
        Livewire.dispatch('reorderSections', {
            ids
        });
        notify('Orden de secciones guardado');
    }

    window.toggleSectionSort = function () {
        secSortOn = !secSortOn;
        const btn = document.getElementById('sortSectionsBtn');
        const btnText = document.getElementById('sortSectionsBtnText');
        const confirm = document.getElementById('confirmSectionsBtn');
        const handles = document.querySelectorAll('.sec-handle-global');
        const cards = document.querySelectorAll('#sectionsContainer .sec-card');

        if (secSortOn) {
            btn.classList.add('active');
            btnText.textContent = 'Cancelar';
            confirm.style.display = 'inline-flex';
            handles.forEach(h => h.classList.add('visible'));
            cards.forEach(card => {
                const handle = card.querySelector('.sec-handle-global');
                if (!handle) return;
                if (!handle._mdBound) {
                    handle._mdBound = true;
                    handle.addEventListener('mousedown', () => {
                        card._handleDown = true;
                        card.setAttribute('draggable', 'true');
                    });
                    handle.addEventListener('mouseup', () => card.setAttribute('draggable',
                        'false'));
                }
                bindSection(card);
            });
        } else {
            secSortOn = false;
            btn.classList.remove('active');
            btnText.textContent = 'Ordenar secciones';
            confirm.style.display = 'none';
            handles.forEach(h => h.classList.remove('visible'));
            cards.forEach(c => {
                c.setAttribute('draggable', 'false');
                c._handleDown = false;
            });
        }
    };

    window.confirmSectionSort = function () {
        commitSectionOrder();
        secSortOn = false;
        const btn = document.getElementById('sortSectionsBtn');
        const btnText = document.getElementById('sortSectionsBtnText');
        const confirm = document.getElementById('confirmSectionsBtn');
        const handles = document.querySelectorAll('.sec-handle-global');
        const cards = document.querySelectorAll('#sectionsContainer .sec-card');
        btn.classList.remove('active');
        btnText.textContent = 'Ordenar secciones';
        confirm.style.display = 'none';
        handles.forEach(h => h.classList.remove('visible'));
        cards.forEach(c => {
            c.setAttribute('draggable', 'false');
            c._handleDown = false;
        });
    };

    /* ══ DETALLES ══ */
    function bindDetail(row, sectionId) {
        if (row._detBound) return;
        row._detBound = true;

        row.addEventListener('dragstart', e => {
            if (!row._handleDown) {
                e.preventDefault();
                return;
            }
            detDragSrc = row;
            requestAnimationFrame(() => row.classList.add('dragging'));
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', row.dataset.id);
            e.stopPropagation();
        });

        row.addEventListener('dragend', () => {
            row.classList.remove('dragging');
            row._handleDown = false;
            detDragSrc = null;
            const list = document.getElementById('detailsList-' + sectionId);
            list && list.querySelectorAll('.detail-row').forEach(r => r.classList.remove(
                'drag-over-top', 'drag-over-bottom'));
            refreshDetailNums(sectionId);
        });

        row.addEventListener('dragover', e => {
            if (!detDragSrc || detDragSrc === row) return;
            e.preventDefault();
            e.stopPropagation();
            row.classList.remove('drag-over-top', 'drag-over-bottom');
            const mid = row.getBoundingClientRect().top + row.getBoundingClientRect().height / 2;
            row.classList.add(e.clientY < mid ? 'drag-over-top' : 'drag-over-bottom');
        });

        row.addEventListener('dragleave', e => {
            if (!row.contains(e.relatedTarget)) row.classList.remove('drag-over-top',
                'drag-over-bottom');
        });

        row.addEventListener('drop', e => {
            if (!detDragSrc || detDragSrc === row) return;
            e.preventDefault();
            e.stopPropagation();
            const list = document.getElementById('detailsList-' + sectionId);
            const mid = row.getBoundingClientRect().top + row.getBoundingClientRect().height / 2;
            list.insertBefore(detDragSrc, e.clientY < mid ? row : row.nextSibling);
            row.classList.remove('drag-over-top', 'drag-over-bottom');
            refreshDetailNums(sectionId);
        });
    }

    function refreshDetailNums(sectionId) {
        const list = document.getElementById('detailsList-' + sectionId);
        if (!list) return;
        list.querySelectorAll('.detail-row').forEach((r, i) => {
            const n = r.querySelector('.detail-num');
            if (n) n.textContent = i + 1;
        });
    }

    function commitDetailOrder(sectionId) {
        const list = document.getElementById('detailsList-' + sectionId);
        if (!list) return;
        const ids = [...list.querySelectorAll('.detail-row')].map(r => parseInt(r.dataset.id));
        Livewire.dispatch('reorderDetails', {
            sectionId,
            ids
        });
        notify('Orden de temas guardado');
    }

    window.toggleDetailSort = function (sectionId) {
        const btn = document.getElementById('sortDetailsBtn-' + sectionId);
        const confirm = document.getElementById('confirmDetailsBtn-' + sectionId);
        const confirmMobile = document.getElementById('confirmDetailsMobile-' + sectionId);
        const mobileBtn = document.getElementById('sortDetailsMobile-' + sectionId);
        const mobileBtnText = document.getElementById('sortDetailsMobileText-' + sectionId);
        const bar = document.getElementById('sortBar-' + sectionId);
        const handles = document.querySelectorAll('.detail-handle-' + sectionId);
        const rows = document.querySelectorAll('#detailsList-' + sectionId + ' .detail-row');

        if (detSortOn.has(sectionId)) {
            // ── CANCELAR ──
            detSortOn.delete(sectionId);
            if (btn) {
                btn.classList.remove('active');
                btn.setAttribute('data-tip', 'Ordenar temas');
            }
            confirm && (confirm.style.display = 'none');
            if (confirmMobile) confirmMobile.style.display = 'none';
            if (mobileBtnText) mobileBtnText.textContent = 'Ordenar temas';
            if (mobileBtn) mobileBtn.classList.remove('item-cancel');
            bar && bar.classList.remove('visible');
            handles.forEach(h => h.classList.remove('visible'));
            rows.forEach(r => r.setAttribute('draggable', 'false'));
        } else {
            // ── ACTIVAR ──
            detSortOn.add(sectionId);
            if (btn) {
                btn.classList.add('active');
                btn.setAttribute('data-tip', 'Cancelar orden');
            }
            confirm && (confirm.style.display = 'inline-flex');
            if (confirmMobile) confirmMobile.style.display = 'flex';
            if (mobileBtnText) mobileBtnText.textContent = 'Cancelar ordenar';
            if (mobileBtn) mobileBtn.classList.add('item-cancel');
            bar && bar.classList.add('visible');
            handles.forEach(h => h.classList.add('visible'));
            rows.forEach(row => {
                const handle = row.querySelector('.detail-handle-' + sectionId);
                if (handle && !handle._mdBound) {
                    handle._mdBound = true;
                    // Mouse (desktop)
                    handle.addEventListener('mousedown', () => {
                        row._handleDown = true;
                        row.setAttribute('draggable', 'true');
                    });
                    handle.addEventListener('mouseup', () => row.setAttribute('draggable',
                        'false'));
                    // Touch (móvil)
                    bindTouchDetail(handle, row, sectionId);
                }
                bindDetail(row, sectionId);
            });
        }
    };

    window.confirmDetailSort = function (sectionId) {
        commitDetailOrder(sectionId);
        detSortOn.delete(sectionId);
        const btn = document.getElementById('sortDetailsBtn-' + sectionId);
        const confirm = document.getElementById('confirmDetailsBtn-' + sectionId);
        const confirmMobile = document.getElementById('confirmDetailsMobile-' + sectionId);
        const mobileBtnText = document.getElementById('sortDetailsMobileText-' + sectionId);
        const mobileBtn = document.getElementById('sortDetailsMobile-' + sectionId);
        const bar = document.getElementById('sortBar-' + sectionId);
        const handles = document.querySelectorAll('.detail-handle-' + sectionId);
        const rows = document.querySelectorAll('#detailsList-' + sectionId + ' .detail-row');
        if (btn) {
            btn.classList.remove('active');
            btn.setAttribute('data-tip', 'Ordenar temas');
        }
        if (mobileBtnText) mobileBtnText.textContent = 'Ordenar temas';
        if (mobileBtn) mobileBtn.classList.remove('item-cancel');
        confirm && (confirm.style.display = 'none');
        if (confirmMobile) confirmMobile.style.display = 'none';
        bar && bar.classList.remove('visible');
        handles.forEach(h => h.classList.remove('visible'));
        rows.forEach(r => r.setAttribute('draggable', 'false'));
    };

    /* ══ TOUCH DnD — MÓVIL ══════════════════════════════════
       Implementación con touchstart/touchmove/touchend para que
       el scroll no interfiera. Solo activo cuando el sort está on.
    ═══════════════════════════════════════════════════════════ */

    let touchGhost = null;
    let touchSrc = null;
    let touchType = null; // 'section' | 'detail'
    let touchSecId = null;

    function createGhost(text) {
        const g = document.createElement('div');
        g.className = 'touch-ghost';
        g.textContent = text;
        document.body.appendChild(g);
        return g;
    }

    function moveGhost(x, y) {
        if (!touchGhost) return;
        touchGhost.style.left = (x - touchGhost.offsetWidth / 2) + 'px';
        touchGhost.style.top = (y - 20) + 'px';
    }

    function removeGhost() {
        if (touchGhost) {
            touchGhost.remove();
            touchGhost = null;
        }
    }

    function getElementAtPoint(x, y, exclude) {
        // Ocultar ghost para poder hacer elementFromPoint limpio
        if (touchGhost) touchGhost.style.display = 'none';
        const el = document.elementFromPoint(x, y);
        if (touchGhost) touchGhost.style.display = '';
        return el;
    }

    function findAncestor(el, selector) {
        while (el) {
            if (el.matches && el.matches(selector)) return el;
            el = el.parentElement;
        }
        return null;
    }

    /* ── Touch para secciones ── */
    function bindTouchSection(handle, card) {
        if (handle._touchSecBound) return;
        handle._touchSecBound = true;

        handle.addEventListener('touchstart', e => {
            if (!secSortOn) return;
            e.preventDefault(); // ← bloquea scroll solo desde el handle
            touchSrc = card;
            touchType = 'section';
            card.classList.add('dragging');
            const titleEl = card.querySelector('.sec-title');
            touchGhost = createGhost(titleEl ? titleEl.textContent.trim() : 'Sección');
            const t = e.touches[0];
            moveGhost(t.clientX, t.clientY);
        }, {
            passive: false
        });

        handle.addEventListener('touchmove', e => {
            if (touchType !== 'section' || !touchSrc) return;
            e.preventDefault();
            const t = e.touches[0];
            moveGhost(t.clientX, t.clientY);

            // Indicador visual de posición
            document.querySelectorAll('#sectionsContainer .sec-card').forEach(c =>
                c.classList.remove('drag-over-top', 'drag-over-bottom'));

            const el = getElementAtPoint(t.clientX, t.clientY);
            const target = findAncestor(el, '.sec-card');
            if (target && target !== touchSrc) {
                const rect = target.getBoundingClientRect();
                const mid = rect.top + rect.height / 2;
                target.classList.add(t.clientY < mid ? 'drag-over-top' : 'drag-over-bottom');
            }
        }, {
            passive: false
        });

        handle.addEventListener('touchend', e => {
            if (touchType !== 'section' || !touchSrc) return;
            const t = e.changedTouches[0];
            removeGhost();
            touchSrc.classList.remove('dragging');

            const el = getElementAtPoint(t.clientX, t.clientY);
            const target = findAncestor(el, '.sec-card');
            if (target && target !== touchSrc) {
                const rect = target.getBoundingClientRect();
                const mid = rect.top + rect.height / 2;
                const container = document.getElementById('sectionsContainer');
                container.insertBefore(touchSrc, t.clientY < mid ? target : target.nextSibling);
                refreshSectionBadges();
            }

            document.querySelectorAll('#sectionsContainer .sec-card').forEach(c =>
                c.classList.remove('drag-over-top', 'drag-over-bottom'));

            touchSrc = null;
            touchType = null;
        });
    }

    /* ── Touch para detalles ── */
    function bindTouchDetail(handle, row, sectionId) {
        if (handle._touchDetBound) return;
        handle._touchDetBound = true;

        handle.addEventListener('touchstart', e => {
            if (!detSortOn.has(sectionId)) return;
            e.preventDefault();
            touchSrc = row;
            touchType = 'detail';
            touchSecId = sectionId;
            row.classList.add('dragging');
            const titleEl = row.querySelector('.detail-title');
            touchGhost = createGhost(titleEl ? titleEl.textContent.trim() : 'Tema');
            const t = e.touches[0];
            moveGhost(t.clientX, t.clientY);
        }, {
            passive: false
        });

        handle.addEventListener('touchmove', e => {
            if (touchType !== 'detail' || !touchSrc) return;
            e.preventDefault();
            const t = e.touches[0];
            moveGhost(t.clientX, t.clientY);

            const list = document.getElementById('detailsList-' + touchSecId);
            list && list.querySelectorAll('.detail-row').forEach(r =>
                r.classList.remove('drag-over-top', 'drag-over-bottom'));

            const el = getElementAtPoint(t.clientX, t.clientY);
            const target = findAncestor(el, '.detail-row');
            if (target && target !== touchSrc) {
                const rect = target.getBoundingClientRect();
                const mid = rect.top + rect.height / 2;
                target.classList.add(t.clientY < mid ? 'drag-over-top' : 'drag-over-bottom');
            }
        }, {
            passive: false
        });

        handle.addEventListener('touchend', e => {
            if (touchType !== 'detail' || !touchSrc) return;
            const t = e.changedTouches[0];
            removeGhost();
            touchSrc.classList.remove('dragging');

            const el = getElementAtPoint(t.clientX, t.clientY);
            const target = findAncestor(el, '.detail-row');
            if (target && target !== touchSrc) {
                const list = document.getElementById('detailsList-' + touchSecId);
                const rect = target.getBoundingClientRect();
                const mid = rect.top + rect.height / 2;
                list.insertBefore(touchSrc, t.clientY < mid ? target : target.nextSibling);
                refreshDetailNums(touchSecId);
            }

            const list = document.getElementById('detailsList-' + touchSecId);
            list && list.querySelectorAll('.detail-row').forEach(r =>
                r.classList.remove('drag-over-top', 'drag-over-bottom'));

            touchSrc = null;
            touchType = null;
            touchSecId = null;
        });
    }

    /* Enganchar touch en secciones al activar sort de secciones */
    const _origToggleSecSort = window.toggleSectionSort;
    window.toggleSectionSort = function () {
        _origToggleSecSort();
        if (secSortOn) {
            document.querySelectorAll('#sectionsContainer .sec-card').forEach(card => {
                const handle = card.querySelector('.sec-handle-global');
                if (handle) bindTouchSection(handle, card);
            });
        }
    };

    function clearDropClasses() {
        document.querySelectorAll('.sec-card, .detail-row').forEach(el =>
            el.classList.remove('drag-over-top', 'drag-over-bottom', 'dragging'));
    }

    function clearCardDrop(card) {
        card.classList.remove('drag-over-top', 'drag-over-bottom');
    }

    /* ── Reset tras re-render de Livewire ── */
    document.addEventListener('livewire:updated', () => {
        document.querySelectorAll('.sec-card').forEach(el => {
            delete el._secBound;
            delete el._handleDown;
            const h = el.querySelector('.sec-handle-global');
            if (h) delete h._mdBound;
        });
        document.querySelectorAll('.detail-row').forEach(el => {
            delete el._detBound;
            delete el._handleDown;
        });
        document.querySelectorAll('[class*="detail-handle-"]').forEach(h => delete h._mdBound);
    });

    document.addEventListener('DOMContentLoaded', () => {
        const c = document.getElementById('sectionsContainer');
        if (c) c.addEventListener('dragover', e => {
            if (secDragSrc) e.preventDefault();
        });
    });

})();
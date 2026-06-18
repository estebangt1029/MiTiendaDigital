const DB_NAME    = 'tienda-offline';
const DB_VERSION = 1;
const STORE_NAME = 'pending-sales';

// Abrir base de datos local
function openDB() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open(DB_NAME, DB_VERSION);
        req.onupgradeneeded = e => {
            const db = e.target.result;
            if (!db.objectStoreNames.contains(STORE_NAME)) {
                db.createObjectStore(STORE_NAME, { keyPath: 'id', autoIncrement: true });
            }
        };
        req.onsuccess = e => resolve(e.target.result);
        req.onerror   = e => reject(e.target.error);
    });
}

// Guardar venta pendiente localmente
async function savePendingSale(saleData) {
    const db    = await openDB();
    const tx    = db.transaction(STORE_NAME, 'readwrite');
    const store = tx.objectStore(STORE_NAME);
    saleData.savedAt = new Date().toISOString();
    store.add(saleData);
    return new Promise((res, rej) => {
        tx.oncomplete = res;
        tx.onerror    = rej;
    });
}

// Obtener ventas pendientes
async function getPendingSales() {
    const db    = await openDB();
    const tx    = db.transaction(STORE_NAME, 'readonly');
    const store = tx.objectStore(STORE_NAME);
    return new Promise((res, rej) => {
        const req = store.getAll();
        req.onsuccess = e => res(e.target.result);
        req.onerror   = e => rej(e.target.error);
    });
}

// Eliminar venta sincronizada
async function deletePendingSale(id) {
    const db    = await openDB();
    const tx    = db.transaction(STORE_NAME, 'readwrite');
    const store = tx.objectStore(STORE_NAME);
    store.delete(id);
    return new Promise((res, rej) => {
        tx.oncomplete = res;
        tx.onerror    = rej;
    });
}

// Sincronizar ventas pendientes cuando hay internet
async function syncPendingSales() {
    if (!navigator.onLine) return;

    const pending = await getPendingSales();
    if (!pending.length) return;

    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    for (const sale of pending) {
        try {
            const res = await fetch('/api/ventas/sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify(sale),
            });

            if (res.ok) {
                await deletePendingSale(sale.id);
                console.log('Venta sincronizada:', sale.id);
            }
        } catch (err) {
            console.log('Error sincronizando venta:', err);
        }
    }

    // Notificar al usuario
    const pending2 = await getPendingSales();
    if (!pending2.length) {
        showToast('✓ Todas las ventas sincronizadas', 'green');
    }
}

// Mostrar notificación
function showToast(msg, color = 'green') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 bg-${color}-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 text-sm`;
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Actualizar contador de pendientes en UI
async function updatePendingCount() {
    const pending = await getPendingSales();
    const el = document.getElementById('pending-count');
    if (!el) return;
    if (pending.length > 0) {
        el.textContent = pending.length;
        el.style.display = 'flex';
    } else {
        el.style.display = 'none';
    }
}

// Auto-sincronizar cuando vuelve el internet
window.addEventListener('online', () => {
    showToast('✓ Conexión restaurada — sincronizando...', 'green');
    setTimeout(syncPendingSales, 1000);
});

// Sincronizar al cargar si hay internet
window.addEventListener('load', () => {
    if (navigator.onLine) syncPendingSales();
    updatePendingCount();
});
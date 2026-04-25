/**
 * Pharmacare Storefront Core JavaScript
 * Centralized logic for Search, Cart, Auth, and AJAX navigation.
 */

// Immediate initialization for maximum reliability
(function() {
    console.log('Pharmacare JS Loading (v2.4)...');

    /**
     * Quick View Delegated Trigger
     */
    document.addEventListener('click', async (e) => {
        const qvBtn = e.target.closest('.quick-view-btn');
        if (!qvBtn) return;
        
        e.preventDefault();
        e.stopPropagation();
        
        const id = qvBtn.dataset.id;
        if (!id) return;

        console.log('QuickView Action Triggered for ID:', id);
        
        // 1. Zero-Latency Background: Instant UI update with available data
        const embeddedData = {
            id: qvBtn.dataset.id,
            name: qvBtn.dataset.name,
            category: qvBtn.dataset.category,
            price: qvBtn.dataset.price,
            description: qvBtn.dataset.description,
            unit: qvBtn.dataset.unit,
            stock: qvBtn.dataset.stock,
            image_url: qvBtn.dataset.image
        };
        
        // Show instantly if we have basic data
        if (embeddedData.name) {
            populateQuickView(embeddedData);
        }

        // 2. Freshness Sync: Always fetch latest details (especially stock)
        const originalHtml = qvBtn.innerHTML;
        if (!embeddedData.name) {
            qvBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            qvBtn.style.pointerEvents = 'none';
        }

        try {
            const url = `${window.PharmacareConfig.routes.quickView}/${id}?t=${Date.now()}`;
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();
            
            // Re-populate with official server data (updates stock, etc.)
            populateQuickView(data);
        } catch (err) {
            console.error('Quick View Freshness Error:', err);
            // If we didn't even have embedded data, show error
            if (!embeddedData.name && window.showToast) {
                window.showToast('error', 'Gagal memuat data obat.');
            }
        } finally {
            if (!embeddedData.name) {
                qvBtn.innerHTML = originalHtml;
                qvBtn.style.pointerEvents = 'auto';
            }
        }
    });

    /**
     * Shared Populator Logic - Now Event Driven for Alpine.js 3
     */
    function populateQuickView(data) {
        console.log('Dispatching open-quickview event...');
        window.dispatchEvent(new CustomEvent('open-quickview', { 
            detail: data 
        }));
    }


    // Global Search & Initialization
    document.addEventListener('DOMContentLoaded', () => {
        initSearch();
        console.log('Pharmacare Core Ready.');
    });
})();

/**
 * AJAX Search Autocomplete
 * Supports both desktop (#main-search) and mobile (#mobile-search)
 */
function initSearch() {
    setupSearchInput('main-search', 'search-results');
    setupSearchInput('mobile-search', 'mobile-search-results');
}

function setupSearchInput(inputId, resultsId) {
    const searchInput = document.getElementById(inputId);
    if (!searchInput) return;

    // Buat container hasil jika belum ada
    let searchResults = document.getElementById(resultsId);
    if (!searchResults) {
        searchResults = document.createElement('div');
        searchResults.id = resultsId;
        searchResults.style.cssText = `
            background: white;
            border-radius: 14px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            border: 1px solid #f0f0f0;
            max-height: 300px;
            overflow-y: auto;
            display: none;
            margin-top: 8px;
        `;
        searchInput.parentNode.insertBefore(searchResults, searchInput.nextSibling);
    }

    let searchTimeout = null;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`${window.PharmacareConfig.routes.search}?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) {
                        let html = '<div style="padding:6px;">';
                        data.forEach(item => {
                            const price = 'Rp ' + new Intl.NumberFormat('id-ID').format(item.price);
                            const cat   = item.category?.name || 'Obat';
                            const thumb = item.image_path
                                ? `<img src="${window.PharmacareConfig.assetBase}${item.image_path}" style="width:40px;height:40px;object-fit:contain;display:block;" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(item.name)}&background=random'">`
                                : '<span style="font-size:1.1rem;">💊</span>';

                            html += `
                            <a href="${window.PharmacareConfig.routes.itemDetail}/${item.id}"
                               style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:10px;text-decoration:none;color:inherit;transition:background 0.15s;cursor:pointer;"
                               onmouseover="this.style.background='#f0f7ff'" onmouseout="this.style.background='transparent'">
                                <div style="width:40px;height:40px;min-width:40px;background:#F5F7FA;border:1px solid #eee;border-radius:8px;display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;">
                                    ${thumb}
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:0.85rem;font-weight:700;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${item.name}</div>
                                    <div style="font-size:0.72rem;color:#94a3b8;margin-top:1px;">${cat}</div>
                                </div>
                                <div style="font-size:0.82rem;font-weight:800;color:#0076D6;flex-shrink:0;white-space:nowrap;">
                                    ${price}
                                </div>
                            </a>`;
                        });
                        html += '</div>';
                        searchResults.innerHTML = html;
                        searchResults.style.display = 'block';
                    } else {
                        searchResults.innerHTML = '<div style="padding:15px; text-align:center; color:#999; font-size:0.85rem;">Tidak menemukan produk.</div>';
                        searchResults.style.display = 'block';
                    }
                })
                .catch(err => {
                    console.error('Search error:', err);
                });
        }, 300);
    });

    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
}

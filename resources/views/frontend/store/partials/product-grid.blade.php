<div id="carousel-track-outer">
<div id="carousel-track" style="display: flex; gap: 20px; padding: 10px 5px 30px; will-change: transform;">
    @forelse($items as $item)
    <div class="product-card" style="min-width: 230px; max-width: 230px; background: white; border-radius: 20px; padding: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #F1F5F9; display: flex; flex-direction: column; position: relative; transition: box-shadow 0.3s ease, transform 0.3s ease; flex-shrink: 0;">
        @if($item->requires_prescription)
            <div style="position: absolute; top: -10px; left: -10px; background-color: #0076D6; border-radius: 12px; padding: 6px 10px; text-align: center; font-size: 0.55rem; font-weight: 800; line-height: 1.2; z-index: 15; box-shadow: 0 4px 10px rgba(0,0,0,0.15); transform: rotate(-5deg);">
                <div style="color: white;">CONTROLLED</div>
                <div style="color: #ff4d4d;">SUBSTANCE</div>
            </div>
        @endif
        
        <!-- Quick View Icon (Delegation Support) -->
        <button type="button" 
                data-id="{{ $item->id }}"
                data-name="{{ $item->name }}"
                data-category="{{ $item->category->name ?? 'Obat' }}"
                data-price="{{ $item->price }}"
                data-description="{{ $item->description ?? '' }}"
                data-unit="{{ $item->unit->name ?? 'Kemasan' }}"
                data-stock="{{ $item->stock }}"
                data-image="{{ $item->image_path ? asset($item->image_path) : '' }}"
                data-requires-prescription="{{ $item->requires_prescription ? '1' : '0' }}"
                style="position: absolute; top: 15px; right: 15px; width: 36px; height: 36px; background: white; border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary-blue); cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.1); opacity: 0; transition: all 0.2s; z-index: 10;" class="quick-view-btn">
            <i class="fas fa-eye"></i>
        </button>

        <a href="{{ route('store.show', $item->id) }}" style="text-decoration: none; color: inherit; display: block;">
            <div style="width: 100%; height: 160px; margin-bottom: 16px; background-color: #F8F9FB; border-radius: 16px; display: flex; align-items: center; justify-content: center; overflow: hidden; transition: 0.3s; position: relative;">
                @if($item->image_path)
                    <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}" width="160" height="160" style="height: 100%; width: 100%; object-fit: contain;" loading="lazy">
                @else
                    <span style="font-size: 3rem;">{{ $item->requires_prescription ? '⚕️' : '💊' }}</span>
                @endif
            </div>
            <div style="font-size: 1.05rem; font-weight: 700; margin-bottom: 6px; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $item->name }}</div>
            <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 15px; font-weight: 500;">{{ $item->unit->name ?? 'Kemasan' }}</div>
            <div style="font-size: 1.2rem; font-weight: 800; color: var(--primary-blue);">Rp {{ number_format($item->price, 0, ',', '.') }}</div>
        </a>
        <button type="button" @click.stop="window.dispatchEvent(new CustomEvent('add-to-cart', { detail: { id: {{ $item->id }}, qty: 1 } }))" style="position: absolute; bottom: 18px; right: 18px; width: 42px; height: 42px; background: var(--primary-blue); color: white; border: none; border-radius: 12px; font-size: 1.4rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,118,214,0.3); transition: all 0.2s;" onmouseover="this.style.background='#005BAA'; this.style.transform='scale(1.1)'" onmouseout="this.style.background='var(--primary-blue)'; this.style.transform='scale(1)'">+</button>
    </div>
    @empty
    <div style="text-align:center; padding: 60px; color: #999; background: white; border-radius: 166px; width: 100%;">Belum ada produk tersedia.</div>
    @endforelse
</div>
</div>

<style>
    #carousel-track-outer { overflow: hidden; position: relative; }
    #carousel-track::-webkit-scrollbar { display: none; }
</style>

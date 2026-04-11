@extends('layouts.frontend')

@section('title', 'Pharmacare - Beli Obat Mudah')

@section('content')
<div style="padding: 0 40px 60px;">

    <!-- Hero Banner -->
    <div style="padding-top: 20px; position: relative; margin-bottom: 0;">
        <div style="background: linear-gradient(135deg, #0076D6 0%, #004F8A 100%); color: white; border-radius: 24px; padding: 55px 60px; display: flex; justify-content: space-between; align-items: flex-end; box-shadow: 0 20px 50px rgba(0, 79, 138, 0.15); position: relative; min-height: 250px; overflow: visible;">
            <!-- Glow -->
            <div style="position: absolute; top: -10%; right: 5%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(255,255,255,0.07) 0%, transparent 70%); pointer-events: none; border-radius: 50%;"></div>

            <div style="position: relative; z-index: 2; max-width: 560px; padding-bottom: 5px;">
                <h2 style="font-size: 2.8rem; font-weight: 900; margin-bottom: 20px; line-height: 1.1; letter-spacing: -0.04em; text-shadow: 0 4px 12px rgba(0,0,0,0.1);">Layanan Farmasi Modern<br>Terintegrasi Teknologi AI</h2>
                <p style="font-size: 1.05rem; opacity: 0.9; margin-bottom: 35px; line-height: 1.7; font-weight: 400; max-width: 480px;">Meningkatkan akurasi pelayanan melalui asisten <strong>Apoteker Digital AI</strong>. Solusi kesehatan terpercaya yang hadir 24 jam untuk mendukung kesejahteraan Anda secara profesional.</p>
                
                <div style="display: flex; gap: 15px; align-items: center;">
                    <a href="#produk-pilihan" style="background-color: white; color: var(--primary-blue); padding: 15px 35px; border-radius: 14px; font-weight: 800; text-decoration: none; display: inline-block; box-shadow: 0 10px 25px rgba(0,0,0,0.1); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 15px 30px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 10px 25px rgba(0,0,0,0.1)'">Belanja Sekarang</a>
                    <a href="javascript:void(0)" onclick="toggleChat()" style="color: white; font-weight: 600; text-decoration: none; font-size: 0.95rem; display: flex; align-items: center; gap: 8px; opacity: 0.9;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.9'">
                        <i class="fas fa-comment-medical"></i> Tanya AI
                    </a>
                </div>
            </div>

            <div style="position: absolute; bottom: 0; right: 50px; z-index: 3; line-height: 0;">
                <img src="{{ asset('assets/images/branding/dokter.png') }}" alt="Doctor" style="height: 400px; width: auto; filter: drop-shadow(0 20px 40px rgba(0,0,0,0.15)); display: block;">
            </div>
        </div>
    </div>

    <!-- Product Carousel Section -->
    <div id="produk-pilihan" style="padding-top: 40px;">
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-size: 1.3rem; font-weight: 700; color: #1a1a1a;">Produk Farmasi Populer</h3>
            <div style="display: flex; align-items: center; gap: 12px;">
                <a href="#" style="color: var(--primary-blue); text-decoration: none; font-weight: 600; font-size: 0.9rem;">Lihat Semua</a>
                <button onclick="scrollCarousel(-1)" style="width: 36px; height: 36px; border-radius: 50%; border: 1.5px solid #E0E0E0; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1rem; color: #555; transition: all 0.2s;" onmouseover="this.style.background='var(--primary-blue)';this.style.color='white';this.style.borderColor='var(--primary-blue)'" onmouseout="this.style.background='white';this.style.color='#555';this.style.borderColor='#E0E0E0'">‹</button>
                <button onclick="scrollCarousel(1)" style="width: 36px; height: 36px; border-radius: 50%; border: 1.5px solid #E0E0E0; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1rem; color: #555; transition: all 0.2s;" onmouseover="this.style.background='var(--primary-blue)';this.style.color='white';this.style.borderColor='var(--primary-blue)'" onmouseout="this.style.background='white';this.style.color='#555';this.style.borderColor='#E0E0E0'">›</button>
            </div>
        </div>

        <!-- Track -->
        <div id="product-carousel" style="display: flex; gap: 18px; overflow-x: auto; scroll-behavior: smooth; scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; padding-bottom: 10px; scrollbar-width: none;">
            @forelse($items as $item)
            <div style="min-width: 210px; max-width: 210px; background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #F0F0F0; display: flex; flex-direction: column; scroll-snap-align: start; flex-shrink: 0; position: relative; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 25px rgba(0,0,0,0.06)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 15px rgba(0,0,0,0.02)'">
                <a href="{{ route('store.show', $item->id) }}" style="text-decoration: none; color: inherit;">
                    <div style="width: 100%; height: 140px; margin-bottom: 16px; background-color: #F8F9FB; border-radius: 12px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        @if($item->image_path)
                            <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}" style="height: 100%; width: 100%; object-fit: contain;">
                        @else
                            <span style="font-size: 2.5rem;">{{ $item->requires_prescription ? '⚕️' : '💊' }}</span>
                        @endif
                    </div>
                    <div style="font-size: 1rem; font-weight: 700; margin-bottom: 4px; color: #1a1a1a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $item->name }}</div>
                    <div style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 12px;">{{ $item->unit->name ?? 'Kemasan' }}</div>
                    <div style="font-size: 1.1rem; font-weight: 800; color: var(--primary-blue);">Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                </a>
                <form action="{{ route('cart.add', $item->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="qty" value="1">
                    <button type="submit" style="position: absolute; bottom: 18px; right: 18px; width: 36px; height: 36px; background: var(--primary-blue); color: white; border: none; border-radius: 10px; font-size: 1.3rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,118,214,0.3); transition: background 0.2s;" onmouseover="this.style.background='#005BAA'" onmouseout="this.style.background='var(--primary-blue)'">+</button>
                </form>
            </div>
            @empty
            <div style="text-align:center; padding: 60px; color: #999; background: white; border-radius: 16px; width: 100%;">Belum ada produk tersedia.</div>
            @endforelse
        </div>
    </div>

</div>

<style>
    #product-carousel::-webkit-scrollbar { display: none; }
</style>
<script>
    function scrollCarousel(direction) {
        const carousel = document.getElementById('product-carousel');
        carousel.scrollBy({ left: direction * (210 + 18) * 3, behavior: 'smooth' });
    }
</script>
@endsection


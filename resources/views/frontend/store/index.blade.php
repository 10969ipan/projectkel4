@extends('layouts.frontend')

@section('title', 'Pharmacare - Beli Obat Mudah')

@section('content')
<div style="padding: 0 40px 60px;">

    <!-- Hero Section: Premium AI Experience -->
    <div style="padding-top: 40px; margin-bottom: 40px;">
        <div style="background: linear-gradient(135deg, #0076D6 0%, #005FA3 100%); color: white; border-radius: 32px; padding: 60px 80px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 30px 60px rgba(0, 118, 214, 0.2); position: relative; overflow: hidden; min-height: 400px;">
            <!-- Decorative Elements -->
            <div style="position: absolute; top: -20%; right: -10%; width: 600px; height: 600px; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -10%; left: -5%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 60%); border-radius: 50%;"></div>
            
            <div style="position: relative; z-index: 10; max-width: 600px;">
                <h1 style="font-size: 3.5rem; font-weight: 900; margin-bottom: 20px; line-height: 1.05; letter-spacing: -0.05em;">Solusi Sehat,<br><span style="color: #93C5FD;">Cerdas & Terpercaya.</span></h1>
                <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 35px; line-height: 1.6; font-weight: 400; max-width: 500px;">Konsultasi kesehatan Anda dengan asisten <strong>Apoteker Digital bertenaga AI</strong> yang siap membantu kapan saja.</p>
                
                <div style="display: flex; gap: 20px; align-items: center;">
                    <a href="#katalog" style="background: white; color: var(--primary-blue); padding: 16px 36px; border-radius: 14px; font-weight: 800; text-decoration: none; font-size: 1.1rem; transition: 0.3s; box-shadow: 0 10px 25px rgba(0,0,0,0.1);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 15px 30px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.1)'">Jelajahi Produk</a>
                    <a href="javascript:void(0)" onclick="toggleChat()" style="color: white; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 10px; font-size: 1rem; opacity: 0.9;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.9'">
                        <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(5px);"><i class="fas fa-comment-dots"></i></div>
                        Chat Apoteker AI
                    </a>
                </div>
            </div>

            <div style="position: absolute; bottom: 0; right: 40px; z-index: 5; display: flex; align-items: flex-end;">
                <img src="{{ asset('assets/images/branding/dokter1.png') }}" style="height: 450px; width: auto; filter: drop-shadow(0 20px 50px rgba(0,0,0,0.2)); transform: scaleX(-1); margin-right: -80px; position: relative; z-index: 2;">
                <img src="{{ asset('assets/images/branding/dokter.png') }}" style="height: 420px; width: auto; filter: drop-shadow(0 20px 50px rgba(0,0,0,0.25)); position: relative; z-index: 1;">
            </div>
        </div>
    </div>


    <!-- Product Grid Section -->
    <div id="katalog" style="margin-bottom: 60px; scroll-margin-top: 120px; padding-top: 40px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px;">
            <div>
                <h2 style="font-size: 2rem; font-weight: 800; color: #1e293b; letter-spacing: -0.02em; margin-bottom: 5px;">Katalog Obat</h2>
                <p style="color: #64748b; font-size: 0.95rem;">Daftar obat-obatan terpercaya & bersertifikat</p>
            </div>
            <div style="display: flex; gap: 8px; align-items: center;">
                <button onclick="scrollGrid(-1)"
                    style="width:32px;height:32px;border-radius:50%;border:1px solid #e2e8f0;background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:0.75rem;transition:all 0.2s;outline:none;"
                    onmouseover="this.style.borderColor='#0076D6';this.style.color='#0076D6';this.style.background='#f0f7ff'"
                    onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#94a3b8';this.style.background='transparent'">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button onclick="scrollGrid(1)"
                    style="width:32px;height:32px;border-radius:50%;border:1px solid #e2e8f0;background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:0.75rem;transition:all 0.2s;outline:none;"
                    onmouseover="this.style.borderColor='#0076D6';this.style.color='#0076D6';this.style.background='#f0f7ff'"
                    onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#94a3b8';this.style.background='transparent'">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <!-- AJAX Wrapper for Grid -->
        <div id="item-grid-wrapper" style="transition: opacity 0.3s ease;">
            @include('frontend.store.partials.product-grid')
        </div>

        <style>
            #carousel-track-outer {
                overflow: hidden;
                position: relative;
            }
            #carousel-track {
                display: flex;
                gap: 20px;
                padding: 10px 5px 30px;
                will-change: transform;
                transition: none;
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
            #carousel-track::-webkit-scrollbar { display: none; }
        </style>

        <script>
        (function() {
            let isSliding = false;

            function initInfiniteCarousel() {
                const outer = document.getElementById('carousel-track-outer');
                const track = document.getElementById('carousel-track');
                if (!outer || !track) return;

                const cards = Array.from(track.children);
                if (cards.length === 0) return;

                // Clone cards for seamless looping (prepend + append)
                const clonesBefore = cards.map(c => c.cloneNode(true));
                const clonesAfter  = cards.map(c => c.cloneNode(true));
                clonesBefore.forEach(c => { c.setAttribute('aria-hidden','true'); track.prepend(c); });
                clonesAfter.forEach(c  => { c.setAttribute('aria-hidden','true'); track.append(c); });

                // Calculate width of one set
                const gapPx = 20;
                function getSetWidth() {
                    const card = track.querySelector('.product-card');
                    if (!card) return 0;
                    return cards.length * (card.offsetWidth + gapPx);
                }

                // Start scroll at the "real" cards (after the clones before)
                let currentOffset = 0;
                function jumpToReal() {
                    const w = getSetWidth();
                    currentOffset = w;
                    track.style.transition = 'none';
                    track.style.transform = `translateX(-${currentOffset}px)`;
                }
                jumpToReal();

                function slide(direction) {
                    if (isSliding) return;
                    isSliding = true;

                    const card = track.querySelector('.product-card');
                    if (!card) { isSliding = false; return; }
                    const cardWidth = card.offsetWidth + gapPx;

                    currentOffset += direction * cardWidth;

                    track.style.transition = 'transform 0.45s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                    track.style.transform = `translateX(-${currentOffset}px)`;

                    // After animation: silently jump if we hit the clone boundary
                    setTimeout(() => {
                        const setWidth = getSetWidth();
                        if (currentOffset >= setWidth * 2) {
                            currentOffset -= setWidth;
                            track.style.transition = 'none';
                            track.style.transform = `translateX(-${currentOffset}px)`;
                        } else if (currentOffset <= 0) {
                            currentOffset += setWidth;
                            track.style.transition = 'none';
                            track.style.transform = `translateX(-${currentOffset}px)`;
                        }
                        // Force repaint before releasing lock
                        requestAnimationFrame(() => { isSliding = false; });
                    }, 460);
                }

                window.scrollGrid = (dir) => slide(dir);

                // --- Mouse Wheel Support ---
                outer.addEventListener('wheel', (e) => {
                    if (Math.abs(e.deltaX) > Math.abs(e.deltaY)) {
                        e.preventDefault();
                        if (!isSliding) slide(e.deltaX > 0 ? 1 : -1);
                    } else if (e.shiftKey && Math.abs(e.deltaY) > 0) {
                        e.preventDefault();
                        if (!isSliding) slide(e.deltaY > 0 ? 1 : -1);
                    }
                }, { passive: false });

                // Recalculate on resize
                window.addEventListener('resize', () => {
                    jumpToReal();
                });
            }

            // Init after DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initInfiniteCarousel);
            } else {
                initInfiniteCarousel();
            }
        })();
        </script>
    </div>

    <!-- Customer Reviews Section: High Trust & Premium Social Proof -->
    <div style="margin-top: 100px; padding: 60px 0; border-top: 1px solid #f1f5f9;">
        <h2 style="font-size: 2.2rem; font-weight: 800; color: #1e293b; letter-spacing: -0.03em; margin-bottom: 50px;">Kata Mereka tentang <span style="color: var(--primary-blue);">Pharmacare</span></h2>
        
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px;">
            <!-- Review 1 -->
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div style="width: 72px; height: 72px; border-radius: 50%; overflow: hidden; background: #f1f5f9; border: 2px solid white; box-shadow: 0 10px 20px rgba(0,0,0,0.05);">
                    <img src="{{ asset('assets/images/reviews/user1.png') }}" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div>
                    <p style="font-style: italic; font-family: 'Georgia', serif; font-size: 1.15rem; color: #475569; line-height: 1.6; margin-bottom: 15px;">"Sangat membantu.. malam-malam butuh obat, tidak perlu keluar rumah. Layanan cepat dan terpercaya."</p>
                    <div style="font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1.5px;">Sainem Wiyono</div>
                </div>
            </div>

            <!-- Review 2 -->
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div style="width: 72px; height: 72px; border-radius: 50%; overflow: hidden; background: #f1f5f9; border: 2px solid white; box-shadow: 0 10px 20px rgba(0,0,0,0.05);">
                    <img src="{{ asset('assets/images/reviews/user2.png') }}" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div>
                    <p style="font-style: italic; font-family: 'Georgia', serif; font-size: 1.15rem; color: #475569; line-height: 1.6; margin-bottom: 15px;">"Sangat Helpful!!! Terima kasih yaa, sangat menghemat waktu dan respon dokternya juga baik. Rekep obatnya juga manjur sekali!"</p>
                    <div style="font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1.5px;">Lintang Anindhitya Indraswari</div>
                </div>
            </div>

            <!-- Review 2 -->
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div style="width: 72px; height: 72px; border-radius: 50%; overflow: hidden; background: #f1f5f9; border: 2px solid white; box-shadow: 0 10px 20px rgba(0,0,0,0.05);">
                    <img src="{{ asset('assets/images/reviews/user3.png') }}" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div>
                    <p style="font-style: italic; font-family: 'Georgia', serif; font-size: 1.15rem; color: #475569; line-height: 1.6; margin-bottom: 15px;">"Menggunakan Pharmacare untuk layanan kesehatan keluarga sangat memuaskan. Proses cepat, refund mudah & transparan."</p>
                    <div style="font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1.5px;">Ahkbar Felayati</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


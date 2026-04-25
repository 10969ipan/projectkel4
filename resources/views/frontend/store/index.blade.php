@extends('layouts.frontend')

@section('title', 'Pharmacare - Beli Obat Mudah')

@section('content')
<div class="store-hero-wrapper" style="padding: 0 40px 60px;">

    <!-- Hero Section: Premium AI Experience -->
    <div style="padding-top: 40px; margin-bottom: 40px;">
        <div class="store-hero-card" style="background: linear-gradient(135deg, #0076D6 0%, #005FA3 100%); color: white; border-radius: 32px; padding: 60px 80px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 30px 60px rgba(0, 118, 214, 0.2); position: relative; overflow: hidden; min-height: 400px;">
            <!-- Decorative Elements -->
            <div style="position: absolute; top: -20%; right: -10%; width: 600px; height: 600px; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -10%; left: -5%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 60%); border-radius: 50%;"></div>
            
            <div style="position: relative; z-index: 10; max-width: 600px;">
                <h1 style="font-size: 3.5rem; font-weight: 900; margin-bottom: 20px; line-height: 1.05; letter-spacing: -0.05em;">Solusi Sehat,<br><span style="color: #93C5FD;">Cerdas & Terpercaya.</span></h1>
                <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 35px; line-height: 1.6; font-weight: 400; max-width: 500px;">Konsultasi kesehatan Anda dengan asisten <strong>SIMA</strong> yang siap membantu kapan saja.</p>
                
                <div class="store-hero-buttons" style="display: flex; gap: 20px; align-items: center;">
                    <a href="#katalog" style="background: white; color: var(--primary-blue); padding: 16px 36px; border-radius: 14px; font-weight: 800; text-decoration: none; font-size: 1.1rem; transition: 0.3s; box-shadow: 0 10px 25px rgba(0,0,0,0.1);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 15px 30px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.1)'">Jelajahi Produk</a>
                    <a href="javascript:void(0)" onclick="toggleChat()" style="color: white; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 10px; font-size: 1rem; opacity: 0.9;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.9'">
                        <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(5px);"><i class="fas fa-comment-dots"></i></div>
                        Chat SIMA
                    </a>
                </div>
            </div>

            <div class="store-hero-doctors" style="position: absolute; bottom: 0; right: 40px; z-index: 5; display: flex; align-items: flex-end;">
                <img src="{{ asset('assets/images/branding/dokter1-opt.png') }}" loading="lazy" style="height: 450px; width: auto; filter: drop-shadow(0 20px 50px rgba(0,0,0,0.2)); transform: scaleX(-1); margin-right: -80px; position: relative; z-index: 2;">
                <img src="{{ asset('assets/images/branding/dokter-opt.png') }}" loading="lazy" style="height: 420px; width: auto; filter: drop-shadow(0 20px 50px rgba(0,0,0,0.25)); position: relative; z-index: 1;">
            </div>
        </div>
    </div>


    <!-- Product Grid Section -->
    <div id="katalog" class="catalog-section" style="margin-bottom: 60px; scroll-margin-top: 120px; padding-top: 40px;">
        <div class="catalog-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px;">
            <div>
                <h2 style="font-size: 2.2rem; font-weight: 800; color: #1e293b; letter-spacing: -0.03em; margin-bottom: 5px;"><span style="color: var(--primary-blue);">Katalog</span> Obat</h2>
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

                // Threshold: If cards don't even fill the view, don't clone
                if (cards.length <= 5) {
                    track.style.justifyContent = 'center';
                    track.style.transform = 'none';
                    return; 
                }

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

    <!-- Wellness Highlights Section: Immersive & Educational -->
    @if(isset($wellnessArticles) && count($wellnessArticles) > 0)
    <div style="margin-top: 80px; margin-bottom: 20px;">
        <h2 style="font-size: 2.2rem; font-weight: 800; color: #1e293b; letter-spacing: -0.03em; margin-bottom: 40px;"><span style="color: var(--primary-blue);">Insight</span> Kesehatan</h2>
        
        <div x-data="{ 
            active: 0, 
            items: {{ $wellnessArticles->count() }},
            init() {
                setInterval(() => {
                    this.active = (this.active + 1) % this.items;
                }, 7000); // 7 Seconds for better readability
            }
        }" x-init="init()" class="wellness-highlights" style="position: relative; overflow: hidden; border-radius: 30px; box-shadow: 0 25px 50px rgba(0,0,0,0.12); height: 480px; background: #000;">
            
            @foreach($wellnessArticles as $index => $article)
                <script>window._wellnessArticles = window._wellnessArticles || []; window._wellnessArticles[{{ $index }}] = {!! \Illuminate\Support\Js::from($article) !!};</script>
                <div x-show="active === {{ $index }}" 
                     x-transition:enter="transition cubic-bezier(0.16, 1, 0.3, 1) duration-1200"
                     x-transition:enter-start="opacity-0 transform translate-x-1/3"
                     x-transition:enter-end="opacity-100 transform translate-x-0"
                     x-transition:leave="transition cubic-bezier(0.16, 1, 0.3, 1) duration-1200"
                     x-transition:leave-start="opacity-100 transform translate-x-0"
                     x-transition:leave-end="opacity-0 transform -translate-x-1/3"
                     class="slide-item" 
                     style="position: absolute; inset: 0; background-image: linear-gradient(to right, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.4) 50%, transparent 100%), url('{{ asset($article->image_path) }}'); background-size: cover; background-position: center; display: flex; flex-direction: column; justify-content: center; padding: 50px 80px; height: 100%;">
                
                <div style="max-width: 580px; color: white; text-shadow: 0 2px 10px rgba(0,0,0,0.3);">
                    <h3 style="font-size: 2.8rem; font-weight: 800; margin-top: 0; margin-bottom: 20px; line-height: 1.15; letter-spacing: -0.03em;">{{ $article->title }}</h3>
                    <p style="font-size: 1.2rem; opacity: 0.9; line-height: 1.7; margin-bottom: 35px; font-weight: 500;">{{ Str::limit($article->content, 140) }}</p>
                    <a href="javascript:void(0)" onclick="window.openArticleModal(window._wellnessArticles[{{ $index }}])"
                       style="display: inline-flex; align-items: center; gap: 12px; background: #ffffff; color: #0f172a; text-decoration: none; font-weight: 800; font-size: 1rem; padding: 16px 32px; border-radius: 18px; box-shadow: 0 15px 30px rgba(0,0,0,0.25); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); text-shadow: none; cursor: pointer; z-index: 50; position: relative;"
                       onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 20px 40px rgba(0,0,0,0.3)'"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 15px 30px rgba(0,0,0,0.25)'">
                        Baca Selengkapnya <i class="fas fa-arrow-right" style="font-size: 0.85rem; color: var(--primary-blue);"></i>
                    </a>
                </div>
            </div>
            @endforeach


            <!-- Navigation Indicators -->
            <div class="wellness-nav-indicators" style="position: absolute; bottom: 40px; right: 60px; display: flex; gap: 12px; z-index: 10;">
                @foreach($wellnessArticles as $index => $article)
                <button @click="active = {{ $index }}" 
                        :style="active === {{ $index }} ? 'width: 40px; background: white;' : 'width: 12px; background: rgba(255,255,255,0.3);'" 
                        style="height: 6px; border-radius: 3px; cursor: pointer; border: none; transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);"></button>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if($reviews->count() > 0)
    <div style="margin-top: 80px; padding: 60px 0; border-top: 1px solid #f1f5f9;">
        <h2 style="font-size: 2.2rem; font-weight: 800; color: #1e293b; letter-spacing: -0.03em; margin-bottom: 50px;"><span style="color: var(--primary-blue);">Kata Mereka</span> tentang Pharmacare</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px;">
            @foreach($reviews as $review)
            <div style="display: flex; flex-direction: column; gap: 20px; background: white; padding: 30px; border-radius: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); transition: transform 0.3s ease; border: 1px solid #f8fafc;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="width: 54px; height: 54px; border-radius: 50%; overflow: hidden; background: #e0f2fe; display: flex; align-items: center; justify-content:center; font-weight: 800; color: var(--primary-blue); font-size: 1.2rem;">
                        {{ strtoupper(substr($review->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight: 800; color: #1e293b; font-size: 0.95rem;">{{ $review->user->name }}</div>
                        <div style="display: flex; gap: 2px; margin-top: 4px;">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star" style="color: {{ $i <= $review->rating ? '#f59e0b' : '#e2e8f0' }}; font-size: 0.75rem;"></i>
                            @endfor
                        </div>
                    </div>
                </div>
                <div>
                    <p style="font-style: italic; font-family: 'Georgia', serif; font-size: 1.1rem; color: #475569; line-height: 1.6;">"{{ $review->comment }}"</p>
                    <div style="margin-top: 15px; font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">Pesanan #{{ substr($review->order->order_number, -8) }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection


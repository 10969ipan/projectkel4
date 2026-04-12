@if ($paginator->hasPages())
    <div style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-top: 30px; margin-bottom: 20px;">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 12px; background: #f8fafc; color: #cbd5e1; font-size: 0.9rem; cursor: not-allowed; border: 1px solid #f1f5f9;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 12px; background: white; color: var(--primary-blue); font-size: 0.9rem; text-decoration: none; border: 1px solid #e2e8f0; transition: all 0.3s; box-shadow: 0 4px 6px rgba(0,0,0,0.02);"
               onmouseover="this.style.borderColor='var(--primary-blue)'; this.style.color='var(--primary-blue)'; this.style.transform='translateY(-2px)';"
               onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='var(--primary-blue)'; this.style.transform='translateY(0)';">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </a>
        @endif

        {{-- Pagination Elements --}}
        <div style="display: flex; gap: 8px;">
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; color: #94a3b8; font-weight: 700;">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 12px; background: var(--primary-blue); color: white; font-weight: 800; font-size: 0.95rem; box-shadow: 0 8px 15px rgba(0,118,214,0.25);">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 12px; background: white; color: #475569; font-weight: 700; font-size: 0.95rem; text-decoration: none; border: 1px solid #e2e8f0; transition: all 0.3s;"
                           onmouseover="this.style.borderColor='var(--primary-blue)'; this.style.color='var(--primary-blue)'; this.style.transform='translateY(-2px)';"
                           onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#475569'; this.style.transform='translateY(0)';">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 12px; background: white; color: var(--primary-blue); font-size: 0.9rem; text-decoration: none; border: 1px solid #e2e8f0; transition: all 0.3s; box-shadow: 0 4px 6px rgba(0,0,0,0.02);"
               onmouseover="this.style.borderColor='var(--primary-blue)'; this.style.color='var(--primary-blue)'; this.style.transform='translateY(-2px)';"
               onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='var(--primary-blue)'; this.style.transform='translateY(0)';">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </a>
        @else
            <span style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 12px; background: #f8fafc; color: #cbd5e1; font-size: 0.9rem; cursor: not-allowed; border: 1px solid #f1f5f9;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </span>
        @endif

    </div>
@endif

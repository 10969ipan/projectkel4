@extends('layouts.frontend')

@section('title', 'Dompet Saya')

@section('content')
<div class="container" style="padding-top: 40px; padding-bottom: 80px;">
    <div style="max-width: 800px; margin: 0 auto;">
        
        <!-- Header -->
        <div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-end;">
            <div>
                <h1 style="font-size: 2rem; font-weight: 800; color: #1e293b; margin-bottom: 8px;">Dompet Saya</h1>
                <p style="color: #64748b;">Kelola saldo dan riwayat transaksi otomatis Anda.</p>
            </div>
            <a href="{{ route('account.dashboard') }}" style="color: var(--primary-blue); text-decoration: none; font-weight: 700; font-size: 0.9rem;">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            
            <!-- Balance Card -->
            <div style="background: linear-gradient(135deg, var(--primary-blue), #005BAA); border-radius: 24px; padding: 35px; color: white; box-shadow: 0 20px 40px rgba(0,118,214,0.25); position: relative; overflow: hidden;">
                <div style="position: absolute; top: -20px; right: -20px; font-size: 10rem; opacity: 0.1;"><i class="fas fa-wallet"></i></div>
                
                <div style="font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; opacity: 0.9;">Total Saldo Tersedia</div>
                <div style="font-size: 2.5rem; font-weight: 800; margin-bottom: 30px;">Rp {{ number_format($user->wallet_balance, 0, ',', '.') }}</div>
                
                <form action="{{ url('/account/wallet/topup') }}" method="POST">
                    @csrf
                    <input type="hidden" name="amount" value="100000">
                    <button type="submit" style="background: white; color: var(--primary-blue); border: none; padding: 14px 28px; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.3s; width: 100%; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                         Isi Saldo (Simulasi Rp 100rb)
                    </button>
                </form>
            </div>

            <!-- Promotion / Info Card -->
            <div style="background: white; border-radius: 24px; padding: 30px; border: 1px solid #f1f5f9; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: #1e293b; margin-bottom: 12px;">Keuntungan Langganan</h3>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <li style="margin-bottom: 10px; display: flex; align-items: start; gap: 10px; font-size: 0.9rem; color: #475569;">
                            <i class="fas fa-check-circle" style="color: #10b981; margin-top: 3px;"></i>
                            <span>Otomatis didiskon 10% setiap pengiriman.</span>
                        </li>
                        <li style="margin-bottom: 10px; display: flex; align-items: start; gap: 10px; font-size: 0.9rem; color: #475569;">
                            <i class="fas fa-check-circle" style="color: #10b981; margin-top: 3px;"></i>
                            <span>Tidak perlu checkout manual tiap bulan.</span>
                        </li>
                    </ul>
                </div>
                <div style="background: #ECFDF5; padding: 15px; border-radius: 12px; border: 1px solid #D1FAE5; font-size: 0.85rem; color: #065F46;">
                    <strong>Tips:</strong> Pastikan saldo cukup sebelum tanggal pengiriman agar obat Anda tidak terhenti.
                </div>
            </div>

        </div>

        <!-- Transaction History -->
        <div style="margin-top: 50px;">
            <h3 style="font-size: 1.25rem; font-weight: 800; color: #1e293b; margin-bottom: 20px;">Riwayat Transaksi Dompet</h3>
            
            <div style="background: white; border-radius: 20px; border: 1px solid #f1f5f9; overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid #f1f5f9;">
                            <th style="padding: 15px 20px; text-align: left; font-size: 0.85rem; color: #64748b;">Tanggal</th>
                            <th style="padding: 15px 20px; text-align: left; font-size: 0.85rem; color: #64748b;">Deskripsi</th>
                            <th style="padding: 15px 20px; text-align: right; font-size: 0.85rem; color: #64748b;">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $trx)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 15px 20px; font-size: 0.9rem; color: #475569;">
                                {{ $trx->created_at->format('d M Y, H:i') }}
                            </td>
                            <td style="padding: 15px 20px;">
                                <div style="font-weight: 700; color: #1e293b; font-size: 0.9rem;">{{ $trx->description }}</div>
                                <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase;">{{ $trx->type }}</div>
                            </td>
                            <td style="padding: 15px 20px; text-align: right; font-weight: 800; color: {{ $trx->amount > 0 ? '#10b981' : '#ef4444' }}; font-size: 1rem;">
                                {{ $trx->amount > 0 ? '+' : '' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="padding: 40px; text-align: center; color: #94a3b8; font-style: italic;">
                                Belum ada riwayat transaksi saldo.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection

<div class="glass-card">
    @if($faturalar->count() > 0)
        @if(isset($totals))
            {{-- İSTATİSTİK KUTULARI --}}
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-icon purple"><i class="fas fa-file-invoice"></i></div>
                    <div>
                        <div class="stat-val">{{ number_format($totals->total_fatura, 0, ',', '.') }}</div>
                        <div class="stat-lbl">Toplam Fatura</div>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon blue"><i class="fas fa-bolt"></i></div>
                    <div>
                        <div class="stat-val">{{ number_format($totals->total_tuketim, 2, ',', '.') }}</div>
                        <div class="stat-lbl">Toplam Tüketim (kWh)</div>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon green"><i class="fas fa-lira-sign"></i></div>
                    <div>
                        <div class="stat-val">{{ number_format($totals->total_tutar, 2, ',', '.') }}</div>
                        <div class="stat-lbl">Toplam Tutar (₺)</div>
                    </div>
                </div>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="section-title mb-0"><i class="fas fa-exclamation-triangle"></i> Tespit Edilen Anomaliler</h5>
            <span style="font-size: 0.8rem; font-weight: 700; color: #94a3b8;">{{ $faturalar->total() }} kayıt</span>
        </div>

        <div class="tbl-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Bölge</th>
                        <th>Abone No</th>
                        <th>Fatura No</th>
                        <th style="text-align: right;">Tüketim (kWh)</th>
                        <th>Anomali Detayı</th>
                        <th style="text-align: right;">Tutar</th>
                        <th style="text-align: right;">İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($faturalar as $fatura)
                        @php
                            $anomaliler = $fatura->payload['_tuketim_anomalileri'] ?? [];
                            $bolgeAdi = $bolgeMap[(string)$fatura->ilce_kodu] ?? ($fatura->abone->BOLGE_ADI ?? $fatura->ilce);
                        @endphp
                        <tr>
                            <td><span style="font-weight: 700; color: #2563eb;">{{ $bolgeAdi }}</span></td>
                            <td><code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-weight: 700; color: #1e293b;">{{ $fatura->abone_tesis_no ?? $fatura->tesisat_no }}</code></td>
                            <td style="font-weight: 600;">{{ $fatura->fatura_no }}</td>
                            <td style="text-align: right; font-weight: 800; color: #0f172a;">{{ number_format((float)($fatura->fatura_edilecek_toplam_tuketim_kwh ?? 0), 2, ',', '.') }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($anomaliler as $ano)
                                        @php 
                                            $kod = is_array($ano) ? ($ano['kod'] ?? '') : $ano; 
                                            $mesaj = is_array($ano) ? ($ano['mesaj'] ?? '') : $ano;
                                        @endphp
                                        @if($kod == 'negatif_tuketim') <span class="badge" style="background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;font-size:0.7rem;font-weight:700;"><i class="fas fa-arrow-down"></i> Negatif Endeks</span>
                                        @elseif($kod == 'anormal_tuketim') <span class="badge" style="background:#f5f3ff;color:#6d28d9;border:1px solid #ddd6fe;font-size:0.7rem;font-weight:700;"><i class="fas fa-chart-line"></i> Anormal Sarfiyat</span>
                                        @elseif($kod == 'sifir_tuketim') <span class="badge" style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;font-size:0.7rem;font-weight:700;"><i class="fas fa-power-off"></i> 0 Sarfiyat</span>
                                        @elseif($kod == 'reaktif_ceza') <span class="badge" style="background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;font-size:0.7rem;font-weight:700;"><i class="fas fa-bolt"></i> Reaktif Ceza</span>
                                        @elseif($kod == 'cakisan_donem') <span class="badge" style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;font-size:0.7rem;font-weight:700;"><i class="fas fa-copy"></i> Çakışan Dönem</span>
                                        @else <span class="badge" style="background:#fdf4ff;color:#701a75;border:1px solid #f5d0fe;font-size:0.7rem;font-weight:700;">{{ $mesaj }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                            <td style="text-align: right; font-weight: 800; color: #059669;">₺{{ number_format((float)($fatura->tutar_toplam ?? 0), 2, ',', '.') }}</td>
                            <td style="text-align: right;">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn-pro btn-outline-pro" style="padding: 6px 10px;" onclick="showDetail({{ $fatura->id }})" title="Detay Görüntüle"><i class="fas fa-eye"></i></button>
                                    <button class="btn-pro btn-outline-pro text-danger" style="padding: 6px 10px;" onclick="openItirazModal({{ $fatura->id }})" title="İtiraz Et"><i class="fas fa-hand-paper"></i></button>
                                    <button class="btn-pro btn-outline-pro text-success" style="padding: 6px 10px; white-space: nowrap;" onclick="openAnomaliKaydetModal({{ $fatura->id }})" title="Hatayı Kaydet"><i class="fas fa-save"></i> Kaydet</button>
                                    <button class="btn-pro btn-outline-pro text-muted" style="padding: 6px 10px; white-space: nowrap;" onclick="ignoreAnomali({{ $fatura->id }})" title="Hatayı Görmezden Gel"><i class="fas fa-ban"></i> Görmezden Gel</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($faturalar->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $faturalar->links('pagination::bootstrap-4') }}
            </div>
        @endif
    @else
        <div class="text-center py-5">
            <div style="width: 80px; height: 80px; background: #f8fafc; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 2rem; color: #cbd5e1;">
                <i class="fas fa-search-minus"></i>
            </div>
            <h4 style="font-weight: 800; color: #1e293b; margin-bottom: 10px;">Anomali Bulunamadı</h4>
            <p style="color: #64748b; max-width: 400px; margin: 0 auto;">Seçilen kriterlere göre herhangi bir anomali tespit edilmedi.</p>
        </div>
    @endif
</div>

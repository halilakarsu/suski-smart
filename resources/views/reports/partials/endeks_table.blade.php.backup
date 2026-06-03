@if($results->count() > 0)
    {{-- İSTATİSTİK KARTLARI --}}
    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-icon purple"><i class="fas fa-file-invoice"></i></div>
            <div>
                <div class="stat-val">{{ number_format($results->total(), 0, ',', '.') }}</div>
                <div class="stat-lbl">Toplam Kayıt Sayısı</div>
            </div>
        </div>
        <div class="stat-box">
            <div class="stat-icon blue"><i class="fas fa-bolt"></i></div>
            <div>
                <div class="stat-val">{{ number_format($totalKWH, 0, ',', '.') }}</div>
                <div class="stat-lbl">Toplam Tüketim (kWh)</div>
            </div>
        </div>
        <div class="stat-box">
            <div class="stat-icon green"><i class="fas fa-lira-sign"></i></div>
            <div>
                <div class="stat-val">{{ number_format($totalAmount, 2, ',', '.') }}</div>
                <div class="stat-lbl">Toplam Tutar (₺)</div>
            </div>
        </div>
    </div>

    <div class="tbl-wrap">
        <table class="tbl">
            <thead>
                <tr onclick="$(this).next().toggleClass('d-none')">
                    <th style="width:40px;">#</th>
                    <th>Dönem</th>
                    <th>Tesisat No</th>
                    <th style="text-align:right;">İlk Endeks</th>
                    <th style="text-align:right;">Son Endeks</th>
                    <th style="text-align:right;">Fark</th>
                    <th style="text-align:right;">Tüketim</th>
                    <th style="text-align:right;">Tutar</th>
                    <th style="text-align:center;">Durum</th>
                    <th style="text-align:center;">İşlem</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $i => $row)
                @php
                    $carpan = (float)($row->carpan ?: 1);
                    $trafo = (float)($row->trafo_kaybi ?: 0);
                    $ek = (float)($row->ek_tuketim ?: 0);
                    
                    $t1Ilk = (float)str_replace(',', '.', $row->t1_ilk_endeks);
                    $t1Son = (float)str_replace(',', '.', $row->t1_son_endeks);
                    $t2Ilk = (float)str_replace(',', '.', $row->t2_ilk_endeks);
                    $t2Son = (float)str_replace(',', '.', $row->t2_son_endeks);
                    $t3Ilk = (float)str_replace(',', '.', $row->t3_ilk_endeks);
                    $t3Son = (float)str_replace(',', '.', $row->t3_son_endeks);
                    
                    $t1Fark = $t1Son - $t1Ilk; $t1Gercek = ($t1Fark * $carpan); $t1Gelen = (float)$row->t1_tuketim;
                    $t2Fark = $t2Son - $t2Ilk; $t2Gercek = ($t2Fark * $carpan); $t2Gelen = (float)$row->t2_tuketim;
                    $t3Fark = $t3Son - $t3Ilk; $t3Gercek = ($t3Fark * $carpan); $t3Gelen = (float)$row->t3_tuketim;

                    $hasTariff = ($t1Ilk + $t2Ilk + $t3Ilk) > 0;
                    $t0Ilk = $hasTariff ? ($t1Ilk + $t2Ilk + $t3Ilk) : (float)str_replace(',', '.', $row->t0_ilk_endeks);
                    $t0Son = $hasTariff ? ($t1Son + $t2Son + $t3Son) : (float)str_replace(',', '.', $row->t0_son_endeks);
                    
                    $t0Fark = $t0Son - $t0Ilk;
                    $t0Gelen = (float)($row->t1_tuketim + $row->t2_tuketim + $row->t3_tuketim);
                    $t0Gercek = ($t0Fark * $carpan);

                    // Trafo kaybı detayı (payload'dan)
                    $_pld = $row->payload;
                    if (is_string($_pld)) { try { $_pld = json_decode($_pld, true); } catch (\Exception $e) { $_pld = []; } }
                    $_pld = is_array($_pld) ? $_pld : [];
                    $_pldLower = array_change_key_case($_pld, CASE_LOWER);
                    $t1Tk = (float)str_replace(',', '.', (is_array($_pldLower['t1_tk_kwh'] ?? null) ? 0 : ($_pldLower['t1_tk_kwh'] ?? 0)));
                    $t2Tk = (float)str_replace(',', '.', (is_array($_pldLower['t2_tk_kwh'] ?? null) ? 0 : ($_pldLower['t2_tk_kwh'] ?? 0)));
                    $t3Tk = (float)str_replace(',', '.', (is_array($_pldLower['t3_tk_kwh'] ?? null) ? 0 : ($_pldLower['t3_tk_kwh'] ?? 0)));
                    $toplamTk = $t1Tk + $t2Tk + $t3Tk;

                    // Birim fiyat: DB'de 0 ise payload'daki tüm BIRIM FIYAT varyantlarını tara
                    // (alt çizgili/boşluklu, büyük/küçük harf fark etmez, _2/_3 suffixli de dahil)
                    // Sıfırdan büyük ilk değeri al — Excel'de 3 sütun aynı adla gelirse boş olmayanı seç
                    $_birimFiyat = (float)($row->birim_fiyat ?? 0);
                    if ($_birimFiyat <= 0 && is_array($_pldLower) && count($_pldLower) > 0) {
                        foreach ($_pldLower as $_pk => $_pv) {
                            // 'birim_fiyat', 'birim fiyat', 'birim_fiyat_2', 'birim fiyat_2' vb. eşleştir
                            $_pkNorm = str_replace(' ', '_', $_pk);
                            if (!preg_match('/^birim_fiyat(_\d+)?$/', $_pkNorm)) continue;
                            if (is_array($_pv)) continue;
                            $_pvStr = trim((string)$_pv);
                            if ($_pvStr === '' || $_pvStr === '0' || $_pvStr === '0.0' || $_pvStr === '0.00') continue;
                            $_candidate = (float)str_replace(',', '.', $_pvStr);
                            if ($_candidate > 0) { $_birimFiyat = $_candidate; break; }
                        }
                    }

                    $analizFunc = function($ilk, $son, $fark, $gelen, $gercek, $isT0, $t1, $t2, $t3) {
                        $h = [];
                        if ($son < $ilk) $h[] = "Son < İlk";
                        if ($fark == 0 || $gelen <= 0) $h[] = "Tüketim Yok";
                        if (abs($gelen - $gercek) > 10 && $fark != 0) $h[] = "Fark uyuşmuyor";
                        if ($isT0 && ($t1 + $t2 + $t3) > 0 && abs($gelen - ($t1+$t2+$t3)) > 5) $h[] = "T0 != T1+T2+T3";
                        
                        if (count($h) > 0) return ['d' => 'HATALI', 'a' => implode(' | ', $h), 'c' => '#ef4444', 'bg' => '#fee2e2'];
                        return ['d' => 'UYUMLU', 'a' => 'Sorun Yok', 'c' => '#10b981', 'bg' => '#dcfce7'];
                    };

                    $d0 = $analizFunc($t0Ilk, $t0Son, $t0Fark, $t0Gelen, $t0Gercek, true, $t1Gelen, $t2Gelen, $t3Gelen);
                    $hasError = ($d0['d'] == 'HATALI');
                    $genelDurum = $hasError ? '<span class="status-badge error"><i class="fas fa-exclamation-triangle"></i> DİKKAT</span>' : '<span class="status-badge success"><i class="fas fa-check-circle"></i> TAMAM</span>';
                @endphp
                <tr>
                    <td>{{ $results->firstItem() + $i }}</td>
                    <td><span class="badge-donem">{{ $row->donem }}</span></td>
                    <td><span class="badge-tesisat">{{ $row->abone_tesis_no ?? $row->tesisat_no }}</span></td>
                    <td style="text-align:right;">{{ number_format($t0Ilk, 2, ',', '.') }}</td>
                    <td style="text-align:right;">{{ number_format($t0Son, 2, ',', '.') }}</td>
                    <td style="text-align:right; font-weight:700; color:#2563eb;">{{ number_format($t0Fark, 2, ',', '.') }}</td>
                    <td style="text-align:right; font-weight:700;">{{ number_format($t0Gelen, 0, ',', '.') }} <small>kWh</small></td>
                    <td style="text-align:right; font-weight:700; color:#0f172a;">₺ {{ number_format((float)$row->tutar_toplam, 2, ',', '.') }}</td>
                    <td style="text-align:center;">{!! $genelDurum !!}</td>
                    <td style="text-align:center;">
                        <button type="button" class="btn-incele"><i class="fas fa-chevron-down"></i></button>
                    </td>
                </tr>
                <tr class="d-none" style="background:#f8fafc;">
                    <td colspan="10" style="padding:20px;">
                        <div class="detay-panel" style="border-radius:16px;box-shadow:0 4px 12px rgba(0,0,0,0.04);">
                            <div class="detay-header" style="background:linear-gradient(135deg,#1e293b,#334155);padding:14px 24px;font-weight:700;color:#fff;border-radius:16px 16px 0 0;display:flex;justify-content:space-between;align-items:center;letter-spacing:0.02em;">
                                <span><i class="fas fa-microscope" style="color:#60a5fa;margin-right:10px;"></i> DETAYLI ENDEKS ANALİZİ</span>
                                <span style="font-size:0.7rem;color:#94a3b8;font-weight:600;background:rgba(255,255,255,0.08);padding:4px 12px;border-radius:20px;">Çarpan: x{{$carpan}} | Trafo: {{$trafo}} kWh | Ek: {{$ek}} kWh</span>
                            </div>

                            <div style="padding:20px 24px;background:#fff;border-bottom:1px solid #e2e8f0;">
                                <table style="width:100%;border-collapse:collapse;font-size:0.82rem;">
                                    <thead>
                                        <tr>
                                            <th colspan="6" style="padding:8px 10px;background:#f1f5f9;font-size:0.6rem;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:0.08em;text-align:left;border-bottom:2px solid #cbd5e1;">TEMEL BİLGİLER</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;color:#64748b;font-weight:600;width:12%;">Fatura No</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;font-weight:700;color:#0f172a;width:20%;">{{ $row->fatura_no }}</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;color:#64748b;font-weight:600;width:12%;">Tesisat No</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;font-weight:700;color:#0f172a;width:20%;">{{ $row->abone_tesis_no ?? $row->tesisat_no }}</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;color:#64748b;font-weight:600;width:12%;">Sayaç Seri No</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;font-weight:700;color:#0f172a;width:20%;">{{ $row->sayac_seri_no ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;color:#64748b;font-weight:600;">Dönem</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;font-weight:700;color:#2563eb;">{{ $row->donem }}</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;color:#64748b;font-weight:600;">Tarife</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;font-weight:700;color:#0f172a;">{{ $row->tarife_2 ?: $row->tarife ?? '—' }}</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;color:#64748b;font-weight:600;">Bağlantı Grubu</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;font-weight:700;color:#0f172a;">{{ $row->baglanti_grubu ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;color:#64748b;font-weight:600;">İlk Okuma</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;font-weight:700;color:#0f172a;">{{ $row->ilk_okuma ? \Carbon\Carbon::parse($row->ilk_okuma)->format('d.m.Y') : '—' }}</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;color:#64748b;font-weight:600;">Son Okuma</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;font-weight:700;color:#0f172a;">{{ $row->son_okuma ? \Carbon\Carbon::parse($row->son_okuma)->format('d.m.Y') : '—' }}</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;color:#64748b;font-weight:600;">Adres</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;font-weight:600;color:#0f172a;word-break:break-word;">{{ $row->adres ?? '—' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div style="padding:20px 24px;background:#fff;border-bottom:1px solid #e2e8f0;">
                                <table style="width:100%;border-collapse:collapse;font-size:0.82rem;">
                                    <thead>
                                        <tr>
                                            <th colspan="8" style="padding:8px 10px;background:#f1f5f9;font-size:0.6rem;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:0.08em;text-align:left;border-bottom:2px solid #cbd5e1;">ENDEKS DEĞERLERİ</th>
                                        </tr>
                                        <tr>
                                            <th style="padding:8px 10px;font-size:0.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;border-bottom:1px solid #e2e8f0;">Tarife</th>
                                            <th style="padding:8px 10px;font-size:0.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;border-bottom:1px solid #e2e8f0;text-align:right;">İlk Endeks</th>
                                            <th style="padding:8px 10px;font-size:0.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;border-bottom:1px solid #e2e8f0;text-align:right;">Son Endeks</th>
                                            <th style="padding:8px 10px;font-size:0.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;border-bottom:1px solid #e2e8f0;text-align:right;">Fark</th>
                                            <th style="padding:8px 10px;font-size:0.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;border-bottom:1px solid #e2e8f0;text-align:right;">Tüketim</th>
                                            <th style="padding:8px 10px;font-size:0.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;border-bottom:1px solid #e2e8f0;text-align:right;">Çarpanlı</th>
                                            <th style="padding:8px 10px;font-size:0.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;border-bottom:1px solid #e2e8f0;text-align:center;">Durum</th>
                                            <th style="padding:8px 10px;font-size:0.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;border-bottom:1px solid #e2e8f0;text-align:right;">Detay</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $t1Gercek = $t1Fark * $carpan;
                                            $t2Gercek = $t2Fark * $carpan;
                                            $t3Gercek = $t3Fark * $carpan;
                                            $d1 = $analizFunc($t1Ilk, $t1Son, $t1Fark, $t1Gelen, $t1Gercek, false, 0, 0, 0);
                                            $d2 = $analizFunc($t2Ilk, $t2Son, $t2Fark, $t2Gelen, $t2Gercek, false, 0, 0, 0);
                                            $d3 = $analizFunc($t3Ilk, $t3Son, $t3Fark, $t3Gelen, $t3Gercek, false, 0, 0, 0);
                                            $riFark = (float)($row->ri_fark_endeks ?? 0);
                                            $rcFark = (float)($row->rc_fark_endeks ?? 0);
                                        @endphp
                                        <tr>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;font-weight:800;color:#1e293b;">T0</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;font-weight:600;">{{ number_format($t0Ilk,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;font-weight:600;">{{ number_format($t0Son,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;font-weight:700;color:#2563eb;">{{ number_format($t0Fark,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;font-weight:600;">{{ number_format($t0Gelen,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;font-weight:600;">{{ number_format($t0Gercek,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:center;"><span style="font-size:0.7rem;font-weight:700;color:{{ $d0['c'] }};background:{{ $d0['bg'] }};padding:2px 10px;border-radius:4px;">{{ $d0['d'] }}</span></td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;font-size:0.7rem;color:#64748b;">{{ $d0['a'] }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;font-weight:700;color:#1e293b;">T1</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;">{{ number_format($t1Ilk,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;">{{ number_format($t1Son,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;font-weight:600;color:#2563eb;">{{ number_format($t1Fark,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;">{{ number_format($t1Gelen,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;">{{ number_format($t1Gercek,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:center;">—</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;font-size:0.7rem;color:#94a3b8;"></td>
                                        </tr>
                                        <tr>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;font-weight:700;color:#1e293b;">T2</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;">{{ number_format($t2Ilk,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;">{{ number_format($t2Son,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;font-weight:600;color:#2563eb;">{{ number_format($t2Fark,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;">{{ number_format($t2Gelen,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;">{{ number_format($t2Gercek,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:center;">—</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;font-size:0.7rem;color:#94a3b8;"></td>
                                        </tr>
                                        <tr>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;font-weight:700;color:#1e293b;">T3</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;">{{ number_format($t3Ilk,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;">{{ number_format($t3Son,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;font-weight:600;color:#2563eb;">{{ number_format($t3Fark,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;">{{ number_format($t3Gelen,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;">{{ number_format($t3Gercek,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:center;">—</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;font-size:0.7rem;color:#94a3b8;"></td>
                                        </tr>
                                        @php
                                            $riIlk = (float)str_replace(',', '.', $row->ri_ilk_endeks ?? 0);
                                            $riSon = (float)str_replace(',', '.', $row->ri_son_endeks ?? 0);
                                            $riFarkVal = $riSon - $riIlk;
                                            $rcIlk = (float)str_replace(',', '.', $row->rc_ilk_endeks ?? 0);
                                            $rcSon = (float)str_replace(',', '.', $row->rc_son_endeks ?? 0);
                                            $rcFarkVal = $rcSon - $rcIlk;
                                            $riHasData = $riIlk > 0 || $riSon > 0 || $riFark > 0;
                                            $rcHasData = $rcIlk > 0 || $rcSon > 0 || $rcFark > 0;
                                        @endphp
                                        @if($riHasData)
                                        <tr>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;font-weight:700;color:#dc2626;">Rİ</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;">{{ number_format($riIlk,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;">{{ number_format($riSon,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;font-weight:700;color:#dc2626;">{{ number_format($riFarkVal,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;color:#94a3b8;">—</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;color:#94a3b8;">—</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:center;"><span style="font-size:0.7rem;font-weight:700;color:#dc2626;background:#fef2f2;padding:2px 10px;border-radius:4px;">REAKTİF</span></td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;font-size:0.7rem;color:#64748b;">Endüktif</td>
                                        </tr>
                                        @endif
                                        @if($rcHasData)
                                        <tr>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;font-weight:700;color:#dc2626;">RC</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;">{{ number_format($rcIlk,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;">{{ number_format($rcSon,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;font-weight:700;color:#dc2626;">{{ number_format($rcFarkVal,2,',','.') }}</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;color:#94a3b8;">—</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;color:#94a3b8;">—</td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:center;"><span style="font-size:0.7rem;font-weight:700;color:#dc2626;background:#fef2f2;padding:2px 10px;border-radius:4px;">REAKTİF</span></td>
                                            <td style="padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:right;font-size:0.7rem;color:#64748b;">Kapasitif</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <div style="padding:20px 24px;background:#fff;">
                                <table style="width:100%;border-collapse:collapse;font-size:0.82rem;">
                                    <thead>
                                        <tr>
                                            <th colspan="6" style="padding:8px 10px;background:#f1f5f9;font-size:0.6rem;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:0.08em;text-align:left;border-bottom:2px solid #cbd5e1;">TÜKETİM & FİNANS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;color:#64748b;font-weight:600;">Trafo Kaybı</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;font-weight:800;color:#92400e;">{{ number_format($toplamTk,2,',','.') }} kWh</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;color:#64748b;font-weight:600;">Ek Tüketim</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;font-weight:700;color:#0f172a;">{{ number_format($ek,2,',','.') }} kWh</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;color:#64748b;font-weight:600;background:#f0fdf4;">Toplam Tüketim</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #f1f5f9;font-weight:800;color:#059669;background:#f0fdf4;">{{ number_format((float)$row->fatura_edilecek_toplam_tuketim_kwh,2,',','.') }} kWh</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:8px 10px;border-bottom:1px solid #e2e8f0;color:#64748b;font-weight:600;">Birim Fiyat</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #e2e8f0;font-weight:700;color:#0f172a;">{{ $_birimFiyat > 0 ? number_format($_birimFiyat,4,',','.').' ₺' : '—' }}</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #e2e8f0;color:#64748b;font-weight:600;">Dağ. Birim Fiyat</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #e2e8f0;font-weight:700;color:#0f172a;">{{ $row->dagitim_birim_fiyat ? number_format((float)$row->dagitim_birim_fiyat,4,',','.').' ₺' : '—' }}</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #e2e8f0;color:#64748b;font-weight:600;">KDV</td>
                                            <td style="padding:8px 10px;border-bottom:1px solid #e2e8f0;font-weight:700;color:#0f172a;">{{ $row->kdv ? '₺ '.number_format((float)$row->kdv,2,',','.') : '—' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:8px 10px;color:#64748b;font-weight:600;">Fatura Tutarı</td>
                                            <td style="padding:8px 10px;font-weight:800;color:#059669;">₺ {{ number_format((float)$row->tutar_toplam,2,',','.') }}</td>
                                            <td style="padding:8px 10px;color:#64748b;font-weight:600;background:#f0fdf4;">Genel Toplam</td>
                                            <td colspan="3" style="padding:8px 10px;font-weight:900;color:#059669;font-size:1rem;background:#f0fdf4;">₺ {{ number_format((float)$row->genel_toplam,2,',','.') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div style="padding:12px 24px;background:#f8fafc;border-top:2px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;border-radius:0 0 16px 16px;">
                                <div style="display:flex;align-items:center;gap:12px;">
                                    <span style="font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;">T0 Analiz:</span>
                                    <span style="font-size:0.8rem;font-weight:700;color:{{ $d0['c'] }};background:{{ $d0['bg'] }};padding:3px 12px;border-radius:6px;">{{ $d0['d'] }}</span>
                                    @if($d0['a'] != 'Sorun Yok')
                                        <span style="font-size:0.72rem;color:#64748b;font-weight:600;background:#fff;padding:3px 10px;border-radius:4px;border:1px solid #e2e8f0;">{{ $d0['a'] }}</span>
                                    @endif
                                </div>
                                <div style="display:flex;align-items:center;gap:12px;">
                                    @if($riFark > 0 || $rcFark > 0)
                                        <span style="font-size:0.7rem;font-weight:800;color:#dc2626;background:#fef2f2;padding:3px 12px;border-radius:6px;"><i class="fas fa-bolt"></i> Reaktif Ceza</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" style="text-align:right; font-weight:800;">GENEL TOPLAM:</td>
                    <td style="text-align:right; font-weight:800;">{{ number_format($totalKWH, 2, ',', '.') }} kWh</td>
                    <td style="text-align:right; font-weight:800; color:#059669;">₺ {{ number_format($totalAmount, 2, ',', '.') }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="pagination-wrap mt-4">
        {!! $results->appends(request()->except('page'))->links('pagination::bootstrap-4') !!}
    </div>

@else
    <div style="text-align:center;padding:40px;color:#64748b;">
        <i class="fas fa-inbox fa-3x" style="color:#cbd5e1;margin-bottom:15px;display:block;"></i>
        Kayıt bulunamadı.
    </div>
@endif

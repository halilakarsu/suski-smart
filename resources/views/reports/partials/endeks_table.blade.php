@if($results->count() > 0 || (isset($tabCounts) && array_sum($tabCounts) > 0))
    {{-- TAB UI --}}
    @if(isset($tabCounts))
        <div class="glass-card" style="padding: 0; margin-bottom: 20px; overflow: hidden;">
            <div
                style="display: flex; overflow-x: auto; gap: 0; scrollbar-width: none; -ms-overflow-style: none; border-bottom: 2px solid #e2e8f0;">
                @php
                    $tabs = [
                        'sifir_sayac' => ['label' => 'Sıfır Tüketim', 'icon' => 'fa-tachometer-alt', 'color' => '#7c3aed'],
                        'dusuk' => ['label' => 'Düşük Tüketim', 'icon' => 'fa-arrow-down', 'color' => '#0369a1'],
                        'astronomik' => ['label' => 'Yüksek Tüketim', 'icon' => 'fa-arrow-up', 'color' => '#c2410c'],
                        'tarife_degisen' => ['label' => 'Tarife Değişimi', 'icon' => 'fa-tags', 'color' => '#0f766e'],
                        'carpan_degisimi' => ['label' => 'Çarpan Değişimi', 'icon' => 'fa-times-circle', 'color' => '#b45309'],
                        'birim_fiyat_degisimi' => ['label' => 'Fiyat Değişimi', 'icon' => 'fa-lira-sign', 'color' => '#6d28d9'],
                        'negatif_endeks' => ['label' => 'Negatif Endeks', 'icon' => 'fa-minus-circle', 'color' => '#dc2626'],
                        'tutarsiz_endeks' => ['label' => 'Tutarsız Endeks', 'icon' => 'fa-exclamation-triangle', 'color' => '#ea580c'],
                    ];
                    $active = $activeTab ?? 'sifir_sayac';
                    $totalAll = array_sum($tabCounts);
                @endphp
                @foreach($tabs as $key => $tab)
                    @php
                        $count = $tabCounts[$key] ?? 0;
                        $isActive = $key === $active;
                        $color = $tab['color'];
                    @endphp
                    <button class="endeks-tab-btn" data-tab="{{ $key }}" style="
                                flex-shrink: 0;
                                display: inline-flex; align-items: center; gap: 7px;
                                padding: 16px 20px;
                                background: {{ $isActive ? '#fff' : 'transparent' }};
                                color: {{ $isActive ? $color : '#64748b' }};
                                font-weight: {{ $isActive ? '800' : '600' }};
                                font-size: 0.82rem;
                                border: none;
                                border-bottom: 3px solid {{ $isActive ? $color : 'transparent' }};
                                cursor: pointer;
                                transition: all 0.2s;
                                white-space: nowrap;
                                position: relative;
                            ">
                        <i class="fas {{ $tab['icon'] }}" style="font-size:0.75rem; opacity:{{ $isActive ? '1' : '0.6' }};"></i>
                        {{ $tab['label'] }}
                        <span style="
                                display: inline-flex; align-items: center; justify-content: center;
                                min-width: 22px; height: 20px; padding: 0 6px;
                                background: {{ $isActive ? $color : '#e2e8f0' }};
                                color: {{ $isActive ? '#fff' : '#64748b' }};
                                border-radius: 20px;
                                font-size: 0.7rem; font-weight: 800;
                                transition: all 0.2s;
                            ">{{ $count }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    @if($results->count() > 0)

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
                            $carpan = (float) ($row->carpan ?: 1);
                            $trafo = (float) ($row->trafo_kaybi ?: 0);
                            $ek = (float) ($row->ek_tuketim ?: 0);

                            $t1Ilk = (float) str_replace(',', '.', $row->t1_ilk_endeks);
                            $t1Son = (float) str_replace(',', '.', $row->t1_son_endeks);
                            $t2Ilk = (float) str_replace(',', '.', $row->t2_ilk_endeks);
                            $t2Son = (float) str_replace(',', '.', $row->t2_son_endeks);
                            $t3Ilk = (float) str_replace(',', '.', $row->t3_ilk_endeks);
                            $t3Son = (float) str_replace(',', '.', $row->t3_son_endeks);

                            $t1Fark = $t1Son - $t1Ilk;
                            $t1Gercek = ($t1Fark * $carpan);
                            $t1Gelen = (float) $row->t1_tuketim;
                            $t2Fark = $t2Son - $t2Ilk;
                            $t2Gercek = ($t2Fark * $carpan);
                            $t2Gelen = (float) $row->t2_tuketim;
                            $t3Fark = $t3Son - $t3Ilk;
                            $t3Gercek = ($t3Fark * $carpan);
                            $t3Gelen = (float) $row->t3_tuketim;

                            $hasTariff = ($t1Ilk + $t2Ilk + $t3Ilk) > 0;
                            $t0Ilk = $hasTariff ? ($t1Ilk + $t2Ilk + $t3Ilk) : (float) str_replace(',', '.', $row->t0_ilk_endeks);
                            $t0Son = $hasTariff ? ($t1Son + $t2Son + $t3Son) : (float) str_replace(',', '.', $row->t0_son_endeks);

                            $t0Fark = $t0Son - $t0Ilk;
                            $t0Gelen = (float) ($row->t1_tuketim + $row->t2_tuketim + $row->t3_tuketim);
                            $t0Gercek = ($t0Fark * $carpan);

                            // Trafo kaybı detayı (payload'dan)
                            $_pld = $row->payload;
                            if (is_string($_pld)) {
                                try {
                                    $_pld = json_decode($_pld, true);
                                } catch (\Exception $e) {
                                    $_pld = [];
                                }
                            }
                            $_pld = is_array($_pld) ? $_pld : [];
                            $_pldLower = array_change_key_case($_pld, CASE_LOWER);
                            $t1Tk = (float) str_replace(',', '.', (is_array($_pldLower['t1_tk_kwh'] ?? null) ? 0 : ($_pldLower['t1_tk_kwh'] ?? 0)));
                            $t2Tk = (float) str_replace(',', '.', (is_array($_pldLower['t2_tk_kwh'] ?? null) ? 0 : ($_pldLower['t2_tk_kwh'] ?? 0)));
                            $t3Tk = (float) str_replace(',', '.', (is_array($_pldLower['t3_tk_kwh'] ?? null) ? 0 : ($_pldLower['t3_tk_kwh'] ?? 0)));
                            $toplamTk = $t1Tk + $t2Tk + $t3Tk;

                            $_birimFiyat = (float) ($row->birim_fiyat ?? 0);
                            if ($_birimFiyat <= 0 && is_array($_pldLower) && count($_pldLower) > 0) {
                                foreach ($_pldLower as $_pk => $_pv) {
                                    // 'birim_fiyat', 'birim fiyat', 'birim_fiyat_2', 'birim fiyat_2' vb. eşleştir
                                    $_pkNorm = str_replace(' ', '_', $_pk);
                                    if (!preg_match('/^birim_fiyat(_\d+)?$/', $_pkNorm))
                                        continue;
                                    if (is_array($_pv))
                                        continue;
                                    $_pvStr = trim((string) $_pv);
                                    if ($_pvStr === '' || $_pvStr === '0' || $_pvStr === '0.0' || $_pvStr === '0.00')
                                        continue;
                                    $_candidate = (float) str_replace(',', '.', $_pvStr);
                                    if ($_candidate > 0) {
                                        $_birimFiyat = $_candidate;
                                        break;
                                    }
                                }
                            }

                            $analizFunc = function ($ilk, $son, $fark, $gelen, $gercek, $isT0, $t1, $t2, $t3) use ($row) {
                                $h = [];
                                if ($son < $ilk)
                                    $h[] = "Son < İlk";
                                if ($fark == 0 || $gelen <= 0)
                                    $h[] = "Tüketim Yok";
                                if (abs($gelen - $gercek) > 10 && $fark != 0)
                                    $h[] = "Fark uyuşmuyor";
                                if ($isT0 && ($t1 + $t2 + $t3) > 0 && abs($gelen - ($t1 + $t2 + $t3)) > 5)
                                    $h[] = "T0 != T1+T2+T3";

                                $cat = $row->anomaly_category ?? 'normal';
                                if ($cat === 'carpan_degisimi') $h[] = "Çarpan Değişimi";
                                if ($cat === 'tarife_degisen') $h[] = "Tarife Değişimi";
                                if ($cat === 'birim_fiyat_degisimi') $h[] = "Fiyat Değişimi";
                                if ($cat === 'astronomik') $h[] = "Anormal Yüksek";
                                if ($cat === 'dusuk') $h[] = "Anormal Düşük";

                                if (count($h) > 0)
                                    return ['d' => 'HATALI', 'a' => implode(' | ', $h), 'c' => '#ef4444', 'bg' => '#fee2e2'];
                                return ['d' => 'UYUMLU', 'a' => 'Sorun Yok', 'c' => '#10b981', 'bg' => '#dcfce7'];
                            };

                            $d0 = $analizFunc($t0Ilk, $t0Son, $t0Fark, $t0Gelen, $t0Gercek, true, $t1Gelen, $t2Gelen, $t3Gelen);
                            $hasError = ($d0['d'] == 'HATALI');
                            $genelDurum = $hasError ? '<span class="status-badge error"><i class="fas fa-exclamation-triangle"></i> DİKKAT</span>' : '<span class="status-badge success"><i class="fas fa-check-circle"></i> TAMAM</span>';


                            $detayliMesajlar = [];
                            if ($t0Son < $t0Ilk) {
                                $detayliMesajlar[] = "Son endeks (" . number_format($t0Son, 2, ',', '.') . "), ilk endeksten (" . number_format($t0Ilk, 2, ',', '.') . ") düşük. Sayaç geri sarmış, sıfırlanmış veya ilk-son endeks kolonları ters girilmiş olabilir. Pano/sayaç arızası kontrol edilmelidir.";
                            }
                            if ($t0Fark == 0 || $t0Gelen <= 0) {
                                $detayliMesajlar[] = "Endeks farkı (" . number_format($t0Fark, 2, ',', '.') . ") veya tüketim değeri (" . number_format($t0Gelen, 2, ',', '.') . " kWh) sıfır. Sayaç okunmamış, sayaç/pano arızalı olabilir veya okuma verisi eksik aktarılmış olabilir.";
                            }
                            if (abs($t0Gelen - $t0Gercek) > 10 && $t0Fark != 0) {
                                $detayliMesajlar[] = "Hesaplanan tüketim (" . number_format($t0Gercek, 2, ',', '.') . " kWh = fark " . number_format($t0Fark, 2, ',', '.') . " × çarpan " . $carpan . ") ile faturadaki tüketim (" . number_format($t0Gelen, 2, ',', '.') . " kWh) arasında " . number_format(abs($t0Gelen - $t0Gercek), 2, ',', '.') . " kWh fark var. Çarpan, endeks veya tüketim alanlarından biri hatalı olabilir.";
                            }
                            if (($t1Gelen + $t2Gelen + $t3Gelen) > 0 && abs($t0Gelen - ($t1Gelen + $t2Gelen + $t3Gelen)) > 5) {
                                $detayliMesajlar[] = "T0 tüketimi (" . number_format($t0Gelen, 2, ',', '.') . " kWh), T1+T2+T3 toplamına (" . number_format($t1Gelen + $t2Gelen + $t3Gelen, 2, ',', '.') . " kWh) eşit değil. Aradaki fark: " . number_format(abs($t0Gelen - ($t1Gelen + $t2Gelen + $t3Gelen)), 2, ',', '.') . " kWh. Tarife bazında okuma/aktarma hatası olabilir.";
                            }
                            $cat = $row->anomaly_category ?? 'normal';
                            if ($cat === 'carpan_degisimi') {
                                $detayliMesajlar[] = "Geçmiş dönem ile kıyaslandığında çarpan değerinde değişiklik tespit edildi. Bu durum tüketim hesaplamasında köklü bir farka yol açmış olabilir.";
                            }
                            if ($cat === 'tarife_degisen') {
                                $detayliMesajlar[] = "Abone tarife grubunda (veya alt tarifesinde) geçmiş döneme göre bir farklılık tespit edildi. Değişiklik doğru uygulanmış mı teyit edilmelidir.";
                            }
                            if ($cat === 'birim_fiyat_degisimi') {
                                $detayliMesajlar[] = "Birim fiyat geçmiş döneme kıyasla önemli bir oranda değişiklik gösterdi. Mevzuat bazlı bir değişiklik yoksa hesaplama/faturalandırma aşaması incelenmelidir.";
                            }
                            if ($cat === 'astronomik') {
                                $detayliMesajlar[] = "Tüketim (veya tutar) geçmiş ortalamalara kıyasla beklenenden çok daha yüksek (%150+ artış) geldi. Hatalı endeks okuması olabilir.";
                            }
                            if ($cat === 'dusuk') {
                                $detayliMesajlar[] = "Tüketim (veya tutar) geçmiş ortalamalara kıyasla anormal seviyede düşük (%80+ düşüş) geldi. Sayaç arızası veya bozuk okuma olabilir.";
                            }
                        @endphp
                        <tr>
                            <td>{{ $results->firstItem() + $i }}</td>
                            <td><span class="badge-donem">{{ $row->donem }}</span></td>
                            <td><span class="badge-tesisat">{{ $row->abone_tesis_no ?? $row->tesisat_no }}</span></td>
                            <td style="text-align:right;">{{ number_format($t0Ilk, 2, ',', '.') }}</td>
                            <td style="text-align:right;">{{ number_format($t0Son, 2, ',', '.') }}</td>
                            <td style="text-align:right; font-weight:700; color:#2563eb;">{{ number_format($t0Fark, 2, ',', '.') }}
                            </td>
                            <td style="text-align:right; font-weight:700;">{{ number_format($t0Gelen, 0, ',', '.') }}
                                <small>kWh</small></td>
                            <td style="text-align:right; font-weight:700; color:#0f172a;">₺
                                {{ number_format((float) $row->tutar_toplam, 2, ',', '.') }}</td>
                            <td style="text-align:center;">{!! $genelDurum !!}</td>
                            <td style="text-align:center;">
                                <button type="button" class="btn-incele endeks-detail-btn"><i
                                        class="fas fa-chevron-down"></i></button>
                            </td>
                        </tr>
                        @php
                            $riIlkJ = (float) str_replace(',', '.', $row->ri_ilk_endeks ?? 0);
                            $riSonJ = (float) str_replace(',', '.', $row->ri_son_endeks ?? 0);
                            $rcIlkJ = (float) str_replace(',', '.', $row->rc_ilk_endeks ?? 0);
                            $rcSonJ = (float) str_replace(',', '.', $row->rc_son_endeks ?? 0);
                            $riFarkJ = (float) ($row->ri_fark_endeks ?? 0);
                            $rcFarkJ = (float) ($row->rc_fark_endeks ?? 0);
                            $t1GercekJ = $t1Fark * $carpan;
                            $t2GercekJ = $t2Fark * $carpan;
                            $t3GercekJ = $t3Fark * $carpan;
                            $detayJson = json_encode([
                                'fatura_no' => $row->fatura_no,
                                'donem' => $row->donem,
                                'tesisat' => $row->abone_tesis_no ?? $row->tesisat_no,
                                'sayac' => $row->sayac_seri_no ?? '—',
                                'tarife' => $row->tarife_2 ?: $row->tarife ?? '—',
                                'baglanti' => $row->baglanti_grubu ?? '—',
                                'adres' => $row->adres ?? '—',
                                'ilk_okuma' => $row->ilk_okuma ? \Carbon\Carbon::parse($row->ilk_okuma)->format('d.m.Y') : '—',
                                'son_okuma' => $row->son_okuma ? \Carbon\Carbon::parse($row->son_okuma)->format('d.m.Y') : '—',
                                'carpan' => $carpan,
                                'tarifeler' => [
                                    ['ad' => 'T1', 'ilk' => $t1Ilk, 'son' => $t1Son, 'fark' => $t1Fark, 'gelen' => $t1Gelen, 'gercek' => $t1GercekJ, 'ana' => false],
                                    ['ad' => 'T2', 'ilk' => $t2Ilk, 'son' => $t2Son, 'fark' => $t2Fark, 'gelen' => $t2Gelen, 'gercek' => $t2GercekJ, 'ana' => false],
                                    ['ad' => 'T3', 'ilk' => $t3Ilk, 'son' => $t3Son, 'fark' => $t3Fark, 'gelen' => $t3Gelen, 'gercek' => $t3GercekJ, 'ana' => false],
                                    ['ad' => 'T0', 'ilk' => $t0Ilk, 'son' => $t0Son, 'fark' => $t0Fark, 'gelen' => $t0Gelen, 'gercek' => $t0Gercek, 'ana' => true],
                                ],
                                'reaktif' => [
                                    'ri' => ['ilk' => $riIlkJ, 'son' => $riSonJ, 'fark' => round($riSonJ - $riIlkJ, 3), 'aktif' => ($riIlkJ > 0 || $riSonJ > 0 || $riFarkJ > 0), 'tip' => 'Endüktif'],
                                    'rc' => ['ilk' => $rcIlkJ, 'son' => $rcSonJ, 'fark' => round($rcSonJ - $rcIlkJ, 3), 'aktif' => ($rcIlkJ > 0 || $rcSonJ > 0 || $rcFarkJ > 0), 'tip' => 'Kapasitif'],
                                ],
                                'finans' => [
                                    'trafo' => $toplamTk,
                                    'ek' => $ek,
                                    'toplam' => (float) ($row->fatura_edilecek_toplam_tuketim_kwh ?? 0),
                                    'birim' => $_birimFiyat,
                                    'dagitim' => (float) ($row->dagitim_birim_fiyat ?? 0),
                                    'kdv' => (float) ($row->kdv ?? 0),
                                    'tutar' => (float) $row->tutar_toplam,
                                    'genel' => (float) $row->genel_toplam,
                                ],
                                'analiz' => [
                                    'durum' => $d0['d'],
                                    'mesaj' => $d0['a'],
                                    'renk' => $d0['c'],
                                    'bg' => $d0['bg'],
                                    'ri_var' => ((float) ($row->reaktif_tl ?? 0) > 0),
                                    'detaylar' => $detayliMesajlar,
                                ],
                            ], JSON_UNESCAPED_UNICODE);
                        @endphp
                        <tr class="d-none">
                            <td colspan="10" style="padding:0;">
                                <div class="detay-panel" data-json="{{ $detayJson }}"></div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" style="text-align:right; font-weight:800;">GENEL TOPLAM:</td>
                        <td style="text-align:right; font-weight:800;">{{ number_format($totalKWH, 2, ',', '.') }} kWh</td>
                        <td style="text-align:right; font-weight:800; color:#059669;">₺
                            {{ number_format($totalAmount, 2, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="pagination-wrap mt-4">
            {!! $results->links('pagination::bootstrap-4') !!}
        </div>
    @else
        <div class="no-results" style="margin-top: 20px;">
            <i class="fas fa-search" style="font-size:3rem; color:#cbd5e1; margin-bottom:15px; display:block;"></i>
            Seçtiğiniz sekmeye ait kayıt bulunamadı. Lütfen başka bir sekme seçin.
        </div>
    @endif
@else
    <div class="no-results">
        <i class="fas fa-search" style="font-size:3rem; color:#cbd5e1; margin-bottom:15px; display:block;"></i>
        Bu kriterlere uygun kayıt bulunamadı. Lütfen filtrelerinizi değiştirip tekrar deneyin.
    </div>
@endif
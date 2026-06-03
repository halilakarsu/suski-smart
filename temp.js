<script>
let Toast = null;
if (typeof window.Swal !== 'undefined') {
  Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
} else {
  // Graceful fallback dummy
  Toast = { fire: (args) => { alert(args.title); return Promise.resolve(); } };
  window.Swal = { fire: (args) => { alert(args.title || args); return Promise.resolve({isConfirmed:true}); } };
}
const csrf = () => document.querySelector('meta[name="csrf-token"]').content;
document.getElementById('salAll').addEventListener('change', function(){
  document.querySelectorAll('.rc').forEach(c => c.checked = this.checked);
});
document.getElementById('salBtn').addEventListener('click', () => {
  document.querySelectorAll('.rc').forEach(c => c.checked = true);
});

const apvBtn = document.getElementById('apvBtn');
if (apvBtn) {
  apvBtn.addEventListener('click', () => {
    const ids = [...document.querySelectorAll('.rc:checked')].map(c => c.value);
    if (!ids.length) { Swal.fire('Uyarı', 'Lütfen en az bir kayıt seçin.', 'warning'); return; }
    Swal.fire({
      title: 'Toplu Onay',
      text: `${ids.length} adet faturayı toplu olarak onaylamak istediğinize emin misiniz?`,
      icon: 'question', showCancelButton: true, confirmButtonColor: '#3a9e40', cancelButtonColor: '#d33',
      confirmButtonText: 'Evet, Onayla', cancelButtonText: 'İptal'
    }).then((res) => { if (res.isConfirmed) approveItems(ids); });
  });
}

const pendBtn = document.getElementById('pendBtn');
if (pendBtn) {
  pendBtn.addEventListener('click', () => {
    const ids = [...document.querySelectorAll('.rc:checked')].map(c => c.value);
    if (!ids.length) { Swal.fire('Uyarı', 'Lütfen en az bir kayıt seçin.', 'warning'); return; }
    Swal.fire({
      title: 'Beklemeye Al',
      text: `${ids.length} adet faturayı tekrar beklemeye almak istediğinize emin misiniz?`,
      icon: 'warning', showCancelButton: true, confirmButtonColor: '#f59e0b', cancelButtonColor: '#d33',
      confirmButtonText: 'Evet, Beklemeye Al', cancelButtonText: 'İptal'
    }).then((res) => { if (res.isConfirmed) pendItems(ids); });
  });
}

function toggleStatus(id) {
  const item = rData[id];
  fetch('/import/staging/'+id+'/toggle-kontrol',{method:'PATCH',headers:{'X-CSRF-TOKEN':csrf(),'Content-Type':'application/json'}})
    .then(r=>r.json()).then(d=>{
       if(d.success) {
         if (d.kontrol_edildi) {
            Swal.fire({
              title: 'Onaylandı!',
              html: `<b>${item.dagitim || 'Mevcut'}</b> bölgesi, <br><b>${item.fatura_no || 'Belirtilmemiş'}</b> numaralı fatura başarıyla onaylandı.`,
              icon: 'success', confirmButtonText: 'Tamam'
            }).then(() => location.reload());
         } else {
            Swal.fire({
              title: 'Beklemeye Alındı!',
              text: 'Fatura tekrar bekleyenler listesine alındı.',
              icon: 'info', confirmButtonText: 'Tamam'
            }).then(() => location.reload());
         }
       }
    });
}

function approveItems(ids) {
  fetch('/import/staging/approve-multiple',{method:'POST',headers:{'X-CSRF-TOKEN':csrf(),'Content-Type':'application/json'},body:JSON.stringify({ids})})
    .then(r=>r.json()).then(d=>{
      if(d.success) { Toast.fire({ icon: 'success', title: 'Veriler başarıyla onaylandı.' }).then(()=>location.reload()); }
    });
}

function pendItems(ids) {
  fetch('/import/staging/pend-multiple',{method:'POST',headers:{'X-CSRF-TOKEN':csrf(),'Content-Type':'application/json'},body:JSON.stringify({ids})})
    .then(r=>r.json()).then(d=>{
      if(d.success) { Toast.fire({ icon: 'info', title: 'Veriler beklemeye alındı.' }).then(()=>location.reload()); }
    });
}

function deleteItem(id) {
  if(confirm('İtiraz edilen bu kayıt havuzdan kalıcı olarak silinecek, ancak orijinal Excel/Ham verilerinde korunacaktır. Emin misiniz?')) {
    fetch('/import/staging/'+id, {
      method:'DELETE',
      headers:{'X-CSRF-TOKEN':csrf(),'Content-Type':'application/json'}
    }).then(r=>r.json()).then(d=>{if(d.success)location.reload();});
  }
}

let cItirazId = null;
function openItirazModal(id) {
  cItirazId = id;
  const d = rData[id];
  document.getElementById('itirazNedeni').value = (d && d.payload && d.payload.itiraz_nedeni) ? d.payload.itiraz_nedeni : '';
  document.getElementById('itirazMdl').classList.add('show');
}
function closeItirazModal() {
  document.getElementById('itirazMdl').classList.remove('show');
  cItirazId = null;
}
function submitItiraz() {
  const reason = document.getElementById('itirazNedeni').value.trim();
  if(!reason) { alert('Lütfen itiraz nedeni girin.'); return; }
  
  fetch('/import/staging/'+cItirazId+'/itiraz',{
    method:'POST',
    headers:{'X-CSRF-TOKEN':csrf(),'Content-Type':'application/json'},
    body:JSON.stringify({ itiraz_nedeni: reason })
  }).then(r=>r.json()).then(d=>{if(d.success)location.reload();});
}

function cancelItiraz(id) {
  if(confirm('İtiraz geri alınacak ve kayıt bekleyenler arasına dönecektir. Emin misiniz?')) {
    fetch('/import/staging/'+id+'/itiraz-iptal', {
      method:'POST',
      headers:{'X-CSRF-TOKEN':csrf(),'Content-Type':'application/json'}
    }).then(r=>r.json()).then(d=>{if(d.success)location.reload();});
  }
}

// Detail Modal
const rData = {!! $stagingler->keyBy('id')->toJson() !!};
function showDetail(id) {
  const d = rData[id];
  if(!d) return;
  const ignore = ['id','hamveri_id','import_log_id','current_row_hash','payload','created_at','updated_at','sira_no','raw_data','import_log'];
  let h = '';
  // Explicitly prepend sayac_seri_no to be prominent
  let prominentKeys = ['fatura_no', 'tesisat_no', 'sayac_seri_no', 'dagitim', 'adres'];
  const ft = document.getElementById('detFt');
  ft.style.display = 'none';

  if (d.payload && d.payload.itiraz_durumu && d.payload.itiraz_nedeni) {
    h += `
      <div class="dg-item" style="background:#fef2f2;border-bottom:1px solid rgba(239,68,68,.2);flex-direction:column;align-items:flex-start;gap:.5rem;">
        <div style="width:100%;display:flex;justify-content:space-between;align-items:center;">
           <div class="dg-lbl" style="color:#b91c1c;width:auto;"><i class="fas fa-exclamation-triangle"></i> İtiraz Nedeni:</div>
           <button class="bp" style="background:#fff;border:1px solid rgba(239,68,68,.3);color:#b91c1c;padding:4px 10px;font-size:0.7rem;" onclick="openItirazModal(${id})"><i class="fas fa-edit"></i> Düzenle</button>
        </div>
        <div class="dg-val" style="color:#991b1b;width:100%;text-align:left;">${d.payload.itiraz_nedeni}</div>
      </div>`;
    ft.innerHTML = `
      <button class="bp bda" style="background:#90aab8;border:none;color:#fff;" onclick="cancelItiraz(${id})"><i class="fas fa-undo"></i> İtirazı İptal Et</button>
      <button class="bp bda" style="background:#b91c1c;border:none;color:#fff;" onclick="deleteItem(${id})"><i class="fas fa-trash"></i> Havuzdan Sil</button>
    `;
    ft.style.display = 'flex';
  }
  
  prominentKeys.forEach(key => {
    if(d[key] !== undefined) {
      let niceKey = key.replace(/_/g, ' ').toUpperCase();
      let val = d[key] ? d[key] : '-';
      h += `<div class="dg-item" style="background:#eaf2f8;"><div class="dg-lbl">${niceKey}</div><div class="dg-val">${val}</div></div>`;
    }
  });

  for(let key in d) {
    if(ignore.includes(key) || prominentKeys.includes(key)) continue;
    let val = d[key];
    
    // boolean check
    if(val === true) val = 'Evet';
    if(val === false) val = 'Hayır';
    if(val === null || val === '') val = '-';

    let niceKey = key.replace(/_/g, ' ').toUpperCase();
    // format decimals
    if(!isNaN(val) && val !== '-' && val.toString().indexOf('.') !== -1 && !key.includes('endeks')) {
       val = parseFloat(val).toLocaleString('tr-TR', { maximumFractionDigits: 2 });
    }
    
    h += `<div class="dg-item"><div class="dg-lbl">${niceKey}</div><div class="dg-val">${val}</div></div>`;
  }
  document.getElementById('detGrid').innerHTML = h;
  document.getElementById('detMdl').classList.add('show');
}
</script>
@endpush

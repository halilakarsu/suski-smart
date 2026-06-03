ALTER TABLE aboneler_eski
ADD COLUMN bolge_adi varchar(255) DEFAULT NULL,
ADD COLUMN sr varchar(255) DEFAULT NULL,
ADD COLUMN fatura_no varchar(255) DEFAULT NULL,
ADD COLUMN ft varchar(255) DEFAULT NULL,
ADD COLUMN tesisat varchar(255) DEFAULT NULL,
ADD COLUMN a_d_r_e_s varchar(255) DEFAULT NULL,
ADD COLUMN vdkod varchar(255) DEFAULT NULL,
ADD COLUMN vergino varchar(255) DEFAULT NULL,
ADD COLUMN hesap_kodu varchar(255) DEFAULT NULL,
ADD COLUMN fn varchar(255) DEFAULT NULL,
ADD COLUMN blg varchar(255) DEFAULT NULL,
ADD COLUMN f_bolge_kodu varchar(255) DEFAULT NULL,
ADD COLUMN okuyucu_kodu varchar(255) DEFAULT NULL,
ADD COLUMN f_f varchar(255) DEFAULT NULL,
ADD COLUMN alt_isletme_adi varchar(255) DEFAULT NULL,
ADD COLUMN abone_grup_adi varchar(255) DEFAULT NULL,
ADD COLUMN sayac_no varchar(255) DEFAULT NULL,
ADD COLUMN karne varchar(255) DEFAULT NULL,
ADD COLUMN created_at timestamp NULL DEFAULT NULL,
ADD COLUMN updated_at timestamp NULL DEFAULT NULL;

UPDATE aboneler_eski e
INNER JOIN aboneler_yeni y 
ON e.ABONE_TESIS_NO = y.tesisat AND e.SAYAC_SERI_NO = y.sayac_no
SET 
    e.bolge_adi = y.bolge_adi,
    e.sr = y.sr,
    e.fatura_no = y.fatura_no,
    e.ft = y.ft,
    e.a_d_r_e_s = y.a_d_r_e_s,
    e.vdkod = y.vdkod,
    e.vergino = y.vergino,
    e.hesap_kodu = y.hesap_kodu,
    e.fn = y.fn,
    e.blg = y.blg,
    e.f_bolge_kodu = y.f_bolge_kodu,
    e.okuyucu_kodu = y.okuyucu_kodu,
    e.f_f = y.f_f,
    e.alt_isletme_adi = y.alt_isletme_adi,
    e.abone_grup_adi = y.abone_grup_adi,
    e.karne = y.karne,
    e.created_at = y.created_at,
    e.updated_at = y.updated_at;

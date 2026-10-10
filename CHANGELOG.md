# Changelog

## 1.1.1 — Güvenlik güncellemesi

### Güvenlik
- Ödeme callback'i artık siparişe bağlı, **tek kullanımlık** doğrulama kaydı olmadan kabul edilmiyor. Kayıt yoksa hiçbir hash geçerli sayılmaz (önceden beklenen değer herkesin hesaplayabildiği `sha256("T")` sabitine düşüyordu).
- Hash karşılaştırması sabit zamanlı `hash_equals()` ile yapılıyor.
- Sipariş yalnızca ödeme Moka tarafında sunucu-sunucuya doğrulandıktan sonra tamamlanıyor: `PaymentStatus = 2` **ve** `TrxStatus = 1` (iptal ve ön provizyon durumlarında da `TrxStatus = 1` döner) ve tutar/para birimi eşleşmelidir.

### Düzeltmeler
- **Çapraz site callback çalışmıyordu:** banka dönüşündeki POST'ta oturum çerezi gönderilmediği için callback düşüyordu. Doğrulama kaydı veritabanına (`oc_moka_pending`) taşındı ve benzersiz işlem koduyla bulunuyor.
- İşlem kodu (`OtherTrxCode`) her denemede benzersiz üretiliyor; Moka bu alanı bayi bazında kalıcı olarak benzersiz ister. Aynı sipariş için birden fazla deneme yan yana durabiliyor.
- `oc_moka_transaction` tablosu kayıt yolunda da oluşturuluyor; yükseltilen kurulumlarda callback tablo hatasıyla kesiliyor ve sipariş hiç ödenmiş olmuyordu.
- Şema değişikliği için **otomatik göç** eklendi: mevcut `oc_moka_pending` kayıtları korunarak dönüştürülür (manuel işlem gerekmez).
- `moka-php` istemcisine bağlantı (10 sn) ve toplam (60 sn) zaman aşımı eklendi; Moka yanıt vermezse istek sonsuza kadar asılı kalıyordu.

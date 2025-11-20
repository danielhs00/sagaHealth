import joblib
import numpy as np
from flask import Flask, request, jsonify
import os 
import sys 
import time 
# Modul Google telah dihapus total.

# --- KONFIGURASI STATIS ---
# Menghapus API Key dan Gemini Client

# Muat Model Bundle dan Scaler 
try:
    # Asumsi file model berada di direktori yang sama
    MODEL_BUNDLE = joblib.load('multi_disease_svm_bundle.pkl')
    MODEL_SCALER = MODEL_BUNDLE['scaler']
    MODELS = MODEL_BUNDLE['models']
    DISEASES = MODEL_BUNDLE['diseases']
    print("Multi-Model SVM dan Scaler berhasil dimuat.")
except FileNotFoundError:
    print("❌ ERROR Fatal: Pastikan file multi_disease_svm_bundle.pkl ada dan di lokasi yang benar.")
    sys.exit(1)

# FUNGSI INTERPRETASI RISIKO
def interpretasi_risiko(probabilitas):
    # Ambang batas lebih sensitif (Sama seperti sebelumnya)
    if probabilitas >= 0.55:
        return "TINGGI", "merah"
    elif probabilitas >= 0.15:
        return "SEDANG", "kuning"
    else:
        return "RENDAH", "hijau"

app = Flask(__name__)

DISEASE_MAP_REVERSE = {
    'risiko_hipertensi': 'Hipertensi',
    'risiko_dm': 'Diabetes Melitus (DM)',
    'risiko_jantung': 'Penyakit Jantung',
    'risiko_stroke': 'Stroke',
    'risiko_asma': 'Asma/PPOK',
    'risiko_kanker': 'Kanker'
}

# ==========================================
# ROUTE 1: PREDIKSI RISIKO SVM (TETAP SAMA)
# ==========================================
@app.route('/predict_multirisk', methods=['POST'])
def predict_multirisk():
    data = request.get_json(force=True)
    
    features_order = [
        'imt', 'riwayat_hipertensi', 'konsumsi_obat', 'tekanan_sistolik', 
        'tekanan_diastolik', 'kebiasaan_begadang', 'jumlah_keluhan', 
        'riwayat_penyakit_pribadi', 'riwayat_penyakit_keluarga', 'jumlah_gejala'
    ]

    try:
        features = [data[f] for f in features_order]
    except KeyError:
        return jsonify({"status": "error", "message": "Input fitur tidak lengkap."}), 400

    input_data = np.array([features])
    input_scaled = MODEL_SCALER.transform(input_data)
    
    results = {}
    for disease_key in DISEASES:
        model = MODELS[disease_key]
        prob_risiko_tinggi = model.predict_proba(input_data)[0, 1] # Menggunakan input_data karena scaling dilakukan di Python
        kategori, warna = interpretasi_risiko(prob_risiko_tinggi)
        
        results[disease_key] = {
            "kategori": kategori,
            "probabilitas": round(prob_risiko_tinggi * 100, 2),
            "warna": warna
        }
        
    return jsonify({"status": "success", "results": results})


# ==========================================
# GENERATOR TEKS KUSTOM (PENGGANTI AI)
# ==========================================
def generate_custom_recommendation(user_input, risk_results):
    
    imt_val = user_input.get('imt', 0)
    sistolik = user_input.get('tekanan_sistolik', 120)
    diastolik = user_input.get('tekanan_diastolik', 80)
    begadang = user_input.get('kebiasaan_begadang', 0)
    riwayat_pribadi = user_input.get('riwayat_penyakit_pribadi', 0)
    
    high_risk_list = []
    tinggi_count = 0
    sedang_count = 0
    
    for key, result in risk_results.items():
        disease_name = DISEASE_MAP_REVERSE.get(key, key)
        if result['kategori'] == 'TINGGI':
            tinggi_count += 1
            high_risk_list.append((disease_name, result['probabilitas'], result['warna']))
        elif result['kategori'] == 'SEDANG':
            sedang_count += 1
            high_risk_list.append((disease_name, result['probabilitas'], result['warna']))

    # --- BAGIAN 1: RINGKASAN STATUS & PERHATIAN UTAMA ---
    if tinggi_count > 0:
        summary = "🚨 Status: Risiko Klinis TINGGI. Anda memiliki {} risiko tinggi terdeteksi. Tindakan medis segera diperlukan.".format(tinggi_count)
    elif sedang_count > 0:
        summary = "⚠️ Status: Risiko Menengah/SEDANG. Anda memiliki {} risiko yang perlu diwaspadai. Perubahan gaya hidup mendesak.".format(sedang_count)
    else:
        summary = "✅ Status: RENDAH. Kesehatan Anda terpelihara dengan baik. Pertahankan!"

    # --- BAGIAN 2: REKOMENDASI PRIORITAS (HOLISTIK) ---
    prioritas = ""
    if tinggi_count > 0:
        prioritas = (
            "📌 KONSULTASI DOKTER SPESIALIS: Segera kunjungi dokter untuk validasi. \n"
            "📌 KONTROL VITAL: Fokus pada penurunan tekanan darah dan IMT Anda. \n"
            "📌 HINDARI RESIKO JANTUNG: Batasi garam, gula, dan lemak jenuh secara agresif."
        )
    elif sedang_count > 0:
        prioritas = (
            "📌 MONITORING BERKALA: Pantau tekanan darah (Saat ini: {}/{}) dan IMT (Saat ini: {:.2f}) mingguan. \n".format(sistolik, diastolik, imt_val) +
            "📌 PERBAIKI TIDUR: Kurangi begadang ({}) untuk menyeimbangkan hormon. \n".format("Pemicu utama" if begadang == 1 else "Risiko kecil") +
            "📌 FOKUS DIET SEIMBANG: Batasi garam dan gula untuk mencegah eskalasi risiko DM dan Hipertensi."
        )
    else:
        prioritas = (
            "📌 JAGA KONSISTENSI: Pertahankan pola makan sehat dan aktivitas fisik teratur. \n"
            "📌 SCREENING TAHUNAN: Jadwalkan Medical Check-up lengkap sekali setahun. \n"
            "📌 AKTIVITAS FISIK: Coba variasi olahraga baru."
        )

    # --- BAGIAN 3: LANGKAH SPESIFIK PER PENYAKIT ---
    spesifik = ""
    if len(high_risk_list) > 0:
        spesifik = "--- Langkah Spesifik per Risiko ---\n"
        for name, prob, warna in high_risk_list:
            saran = ""
            if name == 'Hipertensi':
                saran = "Ukur TD 2x sehari. Mulai diet rendah garam (<1 sdt/hari)."
            elif name == 'Diabetes Melitus (DM)':
                saran = "Kurangi asupan karbohidrat sederhana dan gula. Lakukan Gula Darah Puasa (GDP)."
            elif name == 'Penyakit Jantung':
                saran = "Jaga berat badan dan kontrol stress. Cek profil lipid (kolesterol)."
            elif name == 'Stroke':
                saran = "Waspadai gejala FAST. Jaga tekanan darah agar tetap normal."
            elif name == 'Asma/PPOK':
                saran = "Hindari pemicu seperti asap rokok atau debu. Konsultasi untuk inhaler jika keluhan berulang."
            elif name == 'Kanker':
                saran = "Tingkatkan serat. Rutin skrining mandiri."
            
            spesifik += "➤ {}: {} (Probabilitas: {:.1f}%)\n".format(name, saran, prob)
    else:
        spesifik = "Tidak ada risiko di kategori Sedang atau Tinggi. Fokus pada pencegahan umum."


    return "{}\n\n{}\n\n{}".format(summary, prioritas, spesifik)


# ==========================================
# ROUTE 2: GENERATE REKOMENDASI CUSTOM
# ==========================================
@app.route('/generate_recommendation', methods=['POST'])
def generate_recommendation():
    data = request.get_json(force=True)
    
    user_input = data['user_input']
    risk_results = data['risk_results']
    
    try:
        # Panggil generator teks kustom
        summary_text = generate_custom_recommendation(user_input, risk_results)
        
        # Kembalikan hasil
        return jsonify({"status": "success", "recommendation": summary_text})
        
    except Exception as e:
        # Tangani error jika terjadi masalah dalam logika kustom
        print(f"❌ Error saat Generate Rekomendasi Kustom: {e}")
        summary_text = f"Error saat membuat rekomendasi kustom: {e}"
        return jsonify({"status": "success", "recommendation": summary_text})


if __name__ == '__main__':
    print("🚀 Microservice SagaHealth (API) sedang berjalan di Port 5001...")
    app.run(host='0.0.0.0', port=5001, debug=False, use_reloader=False)
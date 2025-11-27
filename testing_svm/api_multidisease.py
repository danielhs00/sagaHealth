import joblib
import numpy as np
from flask import Flask, request, jsonify
import requests
import os 
import sys 
import time 

# --- KONFIGURASI API LLM (PHi-3/Ollama) ---
# PERHATIAN: Ganti URL ini jika Anda menggunakan endpoint selain Ollama standar 
# (Misalnya Azure/HuggingFace Endpoint. Jika menggunakan Azure, Anda mungkin perlu API Key).
PHI3_API_URL = "http://localhost:11434/api/generate" 
PHI3_MODEL_NAME = "sagabot-phi3" # Menggunakan model kustom yang baru Anda buat (sagabot-phi3)
API_HEADERS = {'Content-Type': 'application/json'}
# Jika API Key dibutuhkan oleh endpoint Anda, masukkan di headers atau payload
# PHI3_API_KEY = "YOUR_PHI3_SECRET_KEY" 


# --- KONFIGURASI SVM STATIS ---
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
# ROUTE 1: PREDIKSI RISIKO SVM
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
        prob_risiko_tinggi = model.predict_proba(input_scaled)[0, 1] 
        kategori, warna = interpretasi_risiko(prob_risiko_tinggi)
        
        results[disease_key] = {
            "kategori": kategori,
            "probabilitas": round(prob_risiko_tinggi * 100, 2),
            "warna": warna
        }
        
    return jsonify({"status": "success", "results": results})


# ==========================================
# FUNGSI PROMPT GENERATOR UNTUK LLM
# ==========================================
def format_prompt(user_input, risk_results):
    # Mengumpulkan semua data yang akan dimasukkan ke LLM
    input_str = "\n[DATA INPUT MENTAH USER]\n"
    input_str += f"- IMT (BMI): {user_input.get('imt'):.2f}\n"
    input_str += f"- Tekanan Sistolik/Diastolik: {user_input.get('tekanan_sistolik')}/{user_input.get('tekanan_diastolik')}\n"
    input_str += f"- Riwayat Penyakit Pribadi: {user_input.get('riwayat_penyakit_pribadi')} kasus\n"
    input_str += f"- Kebiasaan Begadang: {'Ya' if user_input.get('kebiasaan_begadang') == 1 else 'Tidak'}\n"

    risk_str = "\n[HASIL ANALISIS RISIKO SVM]\n"
    for key, result in risk_results.items():
        disease_name = DISEASE_MAP_REVERSE.get(key, key)
        risk_str += f"- {disease_name}: Kategori {result['kategori']} (Probabilitas: {result['probabilitas']}%) \n"
        
    prompt = (
        "PERINGATAN SISTEM: WAJIB gunakan Bahasa Indonesia baku yang formal dan mudah dipahami. Dilarang menggunakan Bahasa Melayu, slang, atau istilah yang tidak baku.\n\n"
        "Anda adalah SagaBot, seorang asisten kesehatan AI dari SagaHealth. "
        "Tugas Anda adalah menganalisis data input dan hasil risiko penyakit yang diberikan. "
        "Berikan rekomendasi kesehatan yang sangat personal, holistik (menyeluruh), dan aman untuk semua kondisi. "
        "Output harus berupa teks profesional, mudah dipahami, dalam bahasa Indonesia baku, dan dibagi menjadi 3 bagian:\n\n"
        "1. Ringkasan Status & Perhatian Utama: Tunjukkan risiko tertinggi yang paling perlu diwaspadai, berdasarkan analisis IMT dan Tekanan Darah.\n"
        "2. Rekomendasi Prioritas (Aman & Holistik): Berikan 3-5 poin saran gaya hidup terintegrasi yang aman untuk semua kondisi berisiko. Gunakan kata kerja aktif (misalnya, Lakukan, Kurangi, Hindari).\n"
        "3. Langkah Spesifik per Penyakit: Berikan 1 saran spesifik untuk setiap penyakit yang berkategori TINGGI atau SEDANG. Jika semua RENDAH, sampaikan pesan pencegahan.\n\n"
        "Harap tampilkan output tanpa markdown, dan hindari menyimpulkan TINGGI atau SEDANG secara kolektif (sajikan per poin risiko.\n\n"
        f"{input_str}\n{risk_str}\n\n"
        "OUTPUT ANDA:\n"
    )
    return prompt


# ==========================================
# ROUTE 2: GENERATE REKOMENDASI DARI PHI-3 (DENGAN RETRY)
# ==========================================
@app.route('/generate_recommendation', methods=['POST'])
def generate_recommendation():
    data = request.get_json(force=True)
    user_input = data['user_input']
    risk_results = data['risk_results']
    
    prompt = format_prompt(user_input, risk_results)
    MAX_RETRIES = 3
    
    for attempt in range(MAX_RETRIES):
        try:
            # Payload untuk API Phi-3 (Ollama / Standar JSON)
            payload = {
                "model": PHI3_MODEL_NAME,
                "prompt": prompt,
                "stream": False,
                "options": {
                    "temperature": 0.5
                }
            }
            
            # Panggilan API menggunakan requests
            response = requests.post(PHI3_API_URL, json=payload, headers=API_HEADERS, timeout=60)
            response.raise_for_status() # Raise HTTPError for bad responses (4xx or 5xx)
            
            # Asumsi Ollama/Endpoint: Hasil dikembalikan sebagai JSON
            result_json = response.json()
            
            # Struktur respons Ollama yang berhasil
            if 'response' in result_json:
                summary_text = result_json['response']
            elif 'content' in result_json: # Struktur API LLM lain (misal Azure)
                summary_text = result_json['content']
            else:
                summary_text = f"Error: Struktur respons LLM tidak dikenali. Cek logs API. (Keys: {list(result_json.keys())})"
                
            return jsonify({"status": "success", "recommendation": summary_text})
            
        except requests.exceptions.RequestException as e:
            error_msg = f"Koneksi/Permintaan ke Phi-3 Gagal: {e}"
            
            if attempt < MAX_RETRIES - 1:
                print(f"⚠️ Percobaan {attempt + 1} gagal ({error_msg}). Mencoba lagi dalam 5 detik...")
                time.sleep(5)
                continue
            else:
                print(f"❌ Semua percobaan gagal. Melewati LLM.")
                # Fallback rekomendasi jika LLM tidak dapat dijangkau
                summary_text = (
                    "⚠️ Layanan LLM (Phi-3) tidak tersedia. Sebagai rekomendasi darurat, "
                    "silakan ikuti saran umum berdasarkan risiko tertinggi yang terdeteksi:\n\n"
                    "1. Konsultasi Klinis: Segera kunjungi dokter untuk memvalidasi semua risiko TINGGI yang terdeteksi oleh model SVM.\n"
                    "2. Gaya Hidup: Prioritaskan diet seimbang, kurangi garam/gula, dan hindari begadang."
                )
                return jsonify({"status": "success", "recommendation": summary_text})
        
        except Exception as e:
            print(f"❌ Error Tak Terduga saat Pemrosesan: {e}")
            return jsonify({"status": "success", "recommendation": f"Error Pemrosesan Data: {e}"})


if __name__ == '__main__':
    print(f"🚀 Microservice SagaHealth (API) sedang berjalan di Port 5001. Menghubungi LLM di {PHI3_API_URL}...")
    app.run(host='0.0.0.0', port=5001, debug=False, use_reloader=False)
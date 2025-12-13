import joblib
import numpy as np
import pandas as pd
from flask import Flask, request, jsonify
from flask_cors import CORS
import requests
import sys

app = Flask(__name__)
CORS(app) # Wajib agar AJAX dari browser bisa masuk

# Konfigurasi LLM (Ollama)
PHI3_URL = "http://localhost:11434/api/generate"

# --- 1. LOAD MODEL & FEATURE NAMES ---
try:
    BUNDLE = joblib.load('multi_disease_svm_bundle_v2.pkl')
    MODELS = BUNDLE['models']
    # Ambil nama fitur urut sesuai saat training
    FEATURE_NAMES = BUNDLE.get('features', [
        'imt', 'riwayat_hipertensi', 'konsumsi_obat', 'sistolik', 
        'diastolik', 'begadang', 'keluhan', 'riwayat_pribadi', 
        'riwayat_keluarga', 'gejala'
    ])
    print(f"✅ Sistem Siap. Model V2 dimuat ({len(MODELS)} penyakit).")
except FileNotFoundError:
    print("❌ ERROR FATAL: File 'multi_disease_svm_bundle_v2.pkl' tidak ditemukan.")
    print("   Jalankan 'python multi_disease_engine_v2.py' dulu!")
    sys.exit(1)

# --- 2. ENDPOINT PREDIKSI SKOR (CEPAT) ---
@app.route('/predict_score_only', methods=['POST'])
def predict_score():
    data = request.get_json(force=True)
    try:
        # Susun data sesuai urutan training
        raw_feats = [
            data.get('imt', 0), 
            data.get('riwayat_hipertensi', 0), 
            data.get('konsumsi_obat', 0),
            data.get('sistolik', 120), 
            data.get('diastolik', 80), 
            data.get('kebiasaan_begadang', 0),
            data.get('jumlah_keluhan', 0), 
            data.get('riwayat_penyakit_pribadi', 0),
            data.get('riwayat_penyakit_keluarga', 0), 
            data.get('jumlah_gejala', 0)
        ]
        
        # Bungkus ke DataFrame agar tidak ada warning Scikit-Learn
        input_data = pd.DataFrame([raw_feats], columns=FEATURE_NAMES)
        
        results = {}
        for name, model in MODELS.items():
            prob = model.predict_proba(input_data)[0, 1]
            
            # Kategori
            if prob > 0.65: cat = "TINGGI"
            elif prob > 0.35: cat = "SEDANG"
            else: cat = "RENDAH"
                
            results[name] = {"prob": round(prob*100, 1), "cat": cat}
            
        return jsonify({"status": "success", "data": results})
        
    except Exception as e:
        print(f"Error Prediksi: {e}")
        return jsonify({"status": "error", "msg": str(e)}), 500

# --- 3. ENDPOINT AI ADVICE (CERDAS & TERKONTROL) ---
@app.route('/ask_ai_advice', methods=['POST'])
def ask_ai():
    data = request.get_json(force=True)
    
    # Ambil Data
    risks = data.get('risks', {})
    features = data.get('features', {})
    
    # Format Data Fisik
    sistolik = features.get('sistolik', '-')
    diastolik = features.get('diastolik', '-')
    imt = features.get('imt', 0)
    
    # Label status berat badan untuk bantu AI
    if imt > 30: status_imt = "Obesitas"
    elif imt > 25: status_imt = "Overweight (Gemuk)"
    elif imt < 18.5: status_imt = "Underweight (Kurus)"
    else: status_imt = "Ideal"

    fisik_str = (
        f"- Tekanan Darah: {sistolik}/{diastolik} mmHg\n"
        f"- IMT: {imt:.1f} ({status_imt})\n"
        f"- Keluhan Fisik: {features.get('jumlah_keluhan', 0)} item\n"
        f"- Gejala Spesifik: {features.get('jumlah_gejala', 0)} gejala"
    )
    
    # Filter hanya risiko yg perlu perhatian
    risk_list = []
    for k, v in risks.items():
        cat = v.get('cat', 'RENDAH')
        if cat in ['TINGGI', 'SEDANG']:
            clean_name = k.replace('risiko_', '').upper()
            risk_list.append(f"{clean_name} ({cat})")
            
    risk_summary = ", ".join(risk_list)
    if not risk_summary: risk_summary = "Semua Risiko Rendah (Sehat)."

    # --- PROMPT DOKTER (ANTI HALUSINASI) ---
    prompt = (
        f"Anda adalah dr. Saga, asisten medis AI yang profesional, logis, dan ramah. "
        f"Tugas: Berikan konsultasi singkat untuk pasien ini:\n\n"
        f"[DATA KLINIS]\n{fisik_str}\n"
        f"[HASIL AI]\n{risk_summary}\n\n"
        f"[INSTRUKSI WAJIB]\n"
        f"1. Fokus pada solusi medis & gaya hidup nyata (Diet, Olahraga, Cek Dokter).\n"
        f"2. JANGAN bahas topik non-medis (seperti judi, politik, dll).\n"
        f"3. Jika Tensi > 140 atau IMT > 25, berikan teguran halus tapi tegas.\n"
        f"4. Format Output: Satu paragraf pembuka pendek, diikuti 3 POIN saran praktis.\n"
        f"5. Gunakan Bahasa Indonesia yang baik.\n\n"
        f"Saran dr. Saga:"
    )

    try:
        # Temperature 0.3 = AI lebih fokus, tidak ngarang
        payload = {
            "model": "sagabot-phi3", 
            "prompt": prompt, 
            "stream": False, 
            "options": {"num_ctx": 1024, "temperature": 0.3} 
        }
        
        resp = requests.post(PHI3_URL, json=payload, timeout=300)
        
        if resp.status_code == 200:
            ai_text = resp.json().get('response', 'Gagal generate saran.')
            # Bersihkan format markdown jika ada
            ai_text = ai_text.replace('**', '').replace('###', '')
            return jsonify({"status": "success", "advice": ai_text})
        else:
            return jsonify({"status": "error", "advice": f"Error System: {resp.status_code}"})
            
    except Exception as e:
        print(f"Error AI: {e}")
        return jsonify({"status": "error", "advice": "Maaf, Dokter AI sedang menangani pasien lain. Silakan coba sesaat lagi."})

if __name__ == '__main__':
    print("🚀 SagaHealth AI Engine V2.1 Berjalan di Port 5001...")
    app.run(host='0.0.0.0', port=5001, debug=False)
import joblib
import numpy as np
import pandas as pd
from flask import Flask, request, jsonify
from flask_cors import CORS
import requests
import sys
import time
import csv
import os
from datetime import datetime

# ============================================================
# 1. KONFIGURASI SISTEM & LOGGING
# ============================================================
app = Flask(__name__)
CORS(app)

PHI3_API_URL = "http://localhost:11434/api/generate"
PHI3_MODEL_NAME = "sagabot-phi3"
MODEL_PATH = 'multi_disease_svm_bundle_v2.pkl'

# Nama file untuk menyimpan data respon time (Bisa dibuka di Excel)
LOG_FILE = 'sagahealth_performance_log.csv'

def write_log(model_name, duration, status):
    """
    Fungsi untuk menyimpan waktu respon ke file CSV secara otomatis.
    """
    file_exists = os.path.isfile(LOG_FILE)
    
    try:
        with open(LOG_FILE, mode='a', newline='') as file:
            writer = csv.writer(file)
            
            # Buat Header jika file baru dibuat
            if not file_exists:
                writer.writerow(['Timestamp', 'Model_Type', 'Duration_Seconds', 'Status'])
            
            # Tulis Data
            timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            writer.writerow([timestamp, model_name, f"{duration:.4f}", status])
            print(f"📝 [LOG] Data tercatat: {model_name} | {duration:.4f}s | {status}")
            
    except Exception as e:
        print(f"⚠️ Gagal menulis log: {e}")

# ============================================================
# 2. LOAD MODEL (MEMORY RESIDENT)
# ============================================================
print("⏳ System Init: Memuat Model SVM ke RAM...")
try:
    BUNDLE = joblib.load(MODEL_PATH)
    MODELS = BUNDLE['models']
    FEATURE_NAMES = BUNDLE.get('features', [
        'imt', 'riwayat_hipertensi', 'konsumsi_obat', 'sistolik', 
        'diastolik', 'begadang', 'keluhan', 'riwayat_pribadi', 
        'riwayat_keluarga', 'gejala'
    ])
    print(f"✅ FAST ENGINE SIAP: {len(MODELS)} Model dimuat.")
except FileNotFoundError:
    print(f"❌ ERROR: File '{MODEL_PATH}' tidak ditemukan.")
    sys.exit(1)

# ============================================================
# 3. ENDPOINT PREDIKSI SKOR (SVM)
# ============================================================
@app.route('/predict_score_only', methods=['POST'])
def predict_score():
    data = request.get_json(force=True)
    
    # --- MULAI STOPWATCH SVM ---
    start_time = time.time()
    
    try:
        raw_feats = [
            data.get('imt', 0), data.get('riwayat_hipertensi', 0), data.get('konsumsi_obat', 0),
            data.get('sistolik', 120), data.get('diastolik', 80), data.get('kebiasaan_begadang', 0),
            data.get('jumlah_keluhan', 0), data.get('riwayat_penyakit_pribadi', 0),
            data.get('riwayat_penyakit_keluarga', 0), data.get('jumlah_gejala', 0)
        ]
        
        input_data = pd.DataFrame([raw_feats], columns=FEATURE_NAMES)
        results = {}
        
        for name, model in MODELS.items():
            prob = model.predict_proba(input_data)[0, 1]
            if prob > 0.65: cat = "TINGGI"
            elif prob > 0.35: cat = "SEDANG"
            else: cat = "RENDAH"
            results[name] = {"prob": round(prob*100, 1), "cat": cat}
            
        # --- STOP STOPWATCH SVM ---
        durasi = time.time() - start_time
        
        # Simpan ke CSV
        write_log("SVM_Prediction", durasi, "Success")
        
        return jsonify({"status": "success", "data": results})
        
    except Exception as e:
        write_log("SVM_Prediction", 0, f"Error: {str(e)}")
        return jsonify({"status": "error", "msg": str(e)}), 500

# ============================================================
# 4. ENDPOINT AI ADVICE (LLM - PHI3)
# ============================================================
@app.route('/ask_ai_advice', methods=['POST'])
def ask_ai():
    data = request.get_json(force=True)
    
    # Persiapan Data (Tidak dihitung dalam waktu respon AI murni)
    risks = data.get('risks', {})
    features = data.get('features', {})
    
    sistolik = features.get('sistolik', '-')
    diastolik = features.get('diastolik', '-')
    imt = features.get('imt', 0)
    
    if imt > 30: status_imt = "Obesitas"
    elif imt > 25: status_imt = "Overweight"
    elif imt < 18.5: status_imt = "Underweight"
    else: status_imt = "Normal"

    fisik_str = (
        f"- Tensi: {sistolik}/{diastolik} mmHg\n"
        f"- IMT: {imt:.1f} ({status_imt})\n"
        f"- Keluhan: {features.get('jumlah_keluhan', 0)}\n"
        f"- Gejala: {features.get('jumlah_gejala', 0)}"
    )
    
    risk_list = []
    for k, v in risks.items():
        cat = v.get('cat', 'RENDAH')
        if cat in ['TINGGI', 'SEDANG']:
            clean_name = k.replace('risiko_', '').upper()
            risk_list.append(f"{clean_name} ({cat})")
    
    risk_summary = ", ".join(risk_list) if risk_list else "Risiko Rendah (Sehat)"

    prompt = (
        f"Anda adalah dr. Saga, dokter AI SagaHealth. "
        f"Data Pasien:\n{fisik_str}\n"
        f"Analisis Risiko:\n{risk_summary}\n\n"
        f"TUGAS: Berikan 3 saran medis praktis Bahasa Indonesia. "
        f"Jika Tensi > 140 atau IMT > 25, berikan teguran halus. "
        f"Maksimal 150 kata."
    )

    MAX_RETRIES = 3
    
    # --- MULAI PROSES AI ---
    total_start_time = time.time() 

    for attempt in range(MAX_RETRIES):
        try:
            print(f"🤖 [AI] Percobaan generate ke-{attempt+1}...")
            
            # --- START STOPWATCH PER REQUEST ---
            req_start = time.time()
            
            payload = {
                "model": PHI3_MODEL_NAME,
                "prompt": prompt,
                "stream": False,
                "options": {
                    "num_ctx": 1024,
                    "num_predict": 250,
                    "temperature": 0.3,
                    "num_thread": 4
                }
            }
            
            resp = requests.post(PHI3_API_URL, json=payload, timeout=300)
            
            # --- STOP STOPWATCH PER REQUEST ---
            req_duration = time.time() - req_start
            
            if resp.status_code == 200:
                ai_text = resp.json().get('response', 'Gagal generate.')
                ai_text = ai_text.replace('**', '').replace('###', '')
                
                # Simpan Log Sukses
                write_log("LLM_Phi3_Generation", req_duration, "Success")
                
                return jsonify({"status": "success", "advice": ai_text})
            
            else:
                # Simpan Log Gagal (HTTP Error)
                write_log("LLM_Phi3_Generation", req_duration, f"HTTP_{resp.status_code}")
                print(f"⚠️ [AI] Gagal status {resp.status_code}. Retrying...")
        
        except Exception as e:
            # Simpan Log Gagal (Koneksi/Timeout)
            write_log("LLM_Phi3_Generation", 300, "Timeout/Connection_Error")
            print(f"⚠️ [AI] Error koneksi: {e}. Retrying...")
            time.sleep(2)

    # Jika gagal total
    total_duration = time.time() - total_start_time
    write_log("LLM_Phi3_Generation", total_duration, "Failed_All_Attempts")
    
    return jsonify({
        "status": "success", 
        "advice": "Maaf, dr. Saga sedang menangani banyak pasien. Saran: Konsultasi ke dokter segera."
    })

if __name__ == '__main__':
    print("🚀 SAGAHEALTH ENGINE (With Performance Logging) Berjalan...")
    print(f"📝 Data akan dicatat otomatis ke file: {LOG_FILE}")
    app.run(host='0.0.0.0', port=5001, debug=False)
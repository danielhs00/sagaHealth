import joblib
import numpy as np
import pandas as pd
import time
from sklearn.metrics import accuracy_score, classification_report, confusion_matrix

# ============================================================
# KONFIGURASI
# ============================================================
MODEL_PATH = 'multi_disease_svm_bundle_v2.pkl' # Pastikan path sesuai
N_TEST_SAMPLES = 2000 # Jumlah data untuk pengujian

print("="*60)
print("📊 EVALUASI MODEL HYBRID AI - SAGAHEALTH (SVM MULTI-CLASS)")
print("="*60)

# 1. LOAD MODEL
print(f"📂 Memuat model dari '{MODEL_PATH}'...")
try:
    # PERBAIKAN DI SINI: Load langsung ke variabel models
    models = joblib.load(MODEL_PATH) 
    print("✅ Model berhasil dimuat!")
except Exception as e:
    print(f"❌ Gagal memuat model: {e}")
    exit()

# 2. GENERATE DATA TEST (Harus sama logikanya dengan Training Engine)
print(f"⏳ Men-generate {N_TEST_SAMPLES} data pengujian (13 Fitur)...")
np.random.seed(999) # Seed beda agar data beda dengan training

# Generate Fitur (Logic disamakan dengan Engine v2 agar valid)
usia = np.random.randint(20, 90, N_TEST_SAMPLES)
imt = np.random.normal(24, 4, N_TEST_SAMPLES) + (usia * 0.05)
sistolik = np.random.normal(120, 15, N_TEST_SAMPLES) + (usia * 0.3)
diastolik = np.random.normal(80, 10, N_TEST_SAMPLES) + (usia * 0.1)

merokok = np.random.choice([0, 1, 2, 3], N_TEST_SAMPLES, p=[0.55, 0.25, 0.1, 0.1])
olahraga = np.random.choice([0, 1, 2, 3], N_TEST_SAMPLES, p=[0.4, 0.35, 0.2, 0.05])
mental_score = np.random.randint(0, 7, N_TEST_SAMPLES)

# Probabilitas sakit
prob_sakit = (merokok * 0.1) + ((3-olahraga) * 0.1) + (usia/200)
prob_sakit = np.clip(prob_sakit, 0.1, 0.9)

X_test = pd.DataFrame({
    'imt': imt,
    'riwayat_hipertensi': np.random.binomial(1, np.clip(prob_sakit, 0, 1), N_TEST_SAMPLES),
    'konsumsi_obat': np.random.choice([0, 1, 2], N_TEST_SAMPLES, p=[0.6, 0.3, 0.1]),
    'sistolik': sistolik,
    'diastolik': diastolik,
    'begadang': np.random.choice([0, 1], N_TEST_SAMPLES),
    'keluhan': np.random.choice([0, 1, 2, 3, 4], N_TEST_SAMPLES),
    'riwayat_pribadi': np.random.choice([0, 1, 2, 3], N_TEST_SAMPLES),
    'riwayat_keluarga': np.random.choice([0, 1, 2], N_TEST_SAMPLES),
    'gejala': np.random.choice([0, 1, 2, 3, 4], N_TEST_SAMPLES),
    'merokok': merokok,
    'olahraga': olahraga,
    'mental_score': mental_score
})

# Re-create Labels (Ground Truth) untuk perbandingan
def sigmoid(x): return 1 / (1 + np.exp(-x))

# Logika Target (Harus persis sama dengan training engine)
score_hip = (sistolik - 140)/10 + (X_test['riwayat_hipertensi']*2) - (olahraga*0.5) + (usia/50)
y_hip = (sigmoid(score_hip) > 0.65).astype(int)

score_dm = (imt - 27)/3 + (X_test['riwayat_keluarga']*1.5) - (olahraga*0.8) + (usia/60)
y_dm = (sigmoid(score_dm) > 0.7).astype(int)

score_jantung = (merokok*1.2) + (y_hip*2) + (X_test['keluhan']*0.5) + (X_test['begadang']*0.5) + (usia/40) - 4
y_jantung = (sigmoid(score_jantung) > 0.6).astype(int)

score_stroke = (sistolik - 160)/10 + (merokok*0.8) + (X_test['riwayat_pribadi']*0.5) + (y_hip*3)
y_stroke = (sigmoid(score_stroke) > 0.75).astype(int)

y_asma = ((X_test['gejala'] >= 3) | ((merokok >= 1) & (X_test['gejala'] >= 2))).astype(int)

score_kanker = (merokok*1.5) + (X_test['riwayat_pribadi']*1.0) + (X_test['riwayat_keluarga']*0.8) + (usia/100) - 3
y_kanker = (sigmoid(score_kanker) > 0.7).astype(int)

Y_true = pd.DataFrame({
    'risiko_hipertensi': y_hip, 'risiko_dm': y_dm, 'risiko_jantung': y_jantung,
    'risiko_stroke': y_stroke, 'risiko_asma': y_asma, 'risiko_kanker': y_kanker
})

# 3. PROSES PENGUJIAN
print("\n🚀 Memulai Prediksi...")
avg_acc = 0

for disease_name, model in models.items():
    print(f"\n🔬 PENGUJIAN: {disease_name.upper().replace('RISIKO_', '')}")
    print("-" * 40)
    
    start_time = time.time()
    y_pred = model.predict(X_test)
    duration = time.time() - start_time
    
    acc = accuracy_score(Y_true[disease_name], y_pred)
    avg_acc += acc
    
    print(f"   Akurasi Test: {acc:.2%} (Waktu: {duration:.4f}s)")
    print("   Confusion Matrix:")
    print(confusion_matrix(Y_true[disease_name], y_pred))
    print("\n   Detailed Report:")
    print(classification_report(Y_true[disease_name], y_pred, target_names=['Sehat', 'Berisiko']))

print("="*60)
print(f"🏆 RATA-RATA AKURASI SYSTEM: {(avg_acc/6):.2%}")
print("="*60)
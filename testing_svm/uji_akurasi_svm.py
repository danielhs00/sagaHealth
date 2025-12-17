import joblib
import pandas as pd
import numpy as np
from sklearn.metrics import classification_report, accuracy_score, confusion_matrix
import matplotlib.pyplot as plt
import seaborn as sns

# ==========================================
# KONFIGURASI
# ==========================================
MODEL_PATH = 'multi_disease_svm_bundle_v2.pkl'
N_TEST = 1000 # Jumlah data uji
np.random.seed(123) # Seed tetap agar hasil konsisten

print("="*60)
print("📊 EVALUASI MODEL HYBRID AI - SAGAHEALTH (SVM MULTI-CLASS)")
print("="*60)

# 1. LOAD MODEL
try:
    BUNDLE = joblib.load(MODEL_PATH)
    models = BUNDLE['models']
    # Ambil nama fitur asli dari file model agar tidak error
    feature_names = BUNDLE.get('features', [
        'imt', 'riwayat_hipertensi', 'konsumsi_obat', 'sistolik', 
        'diastolik', 'begadang', 'keluhan', 'riwayat_pribadi', 
        'riwayat_keluarga', 'gejala'
    ])
    print("✅ Model Bundle berhasil dimuat.")
except FileNotFoundError:
    print(f"❌ File {MODEL_PATH} tidak ditemukan. Lakukan training dulu.")
    exit()

# 2. GENERATE DATA UJI (SINTETIK)
# Data ini dibuat mirip dengan training tapi baru (Unseen Data)
print(f"⏳ Membuat {N_TEST} data pasien simulasi untuk pengujian...")

X_test = pd.DataFrame({
    'imt': np.random.normal(24, 5, N_TEST),
    'sistolik': np.random.normal(125, 20, N_TEST),
    'diastolik': np.random.normal(80, 12, N_TEST),
    'riwayat_hipertensi': np.random.choice([0, 1], N_TEST, p=[0.7, 0.3]),
    'konsumsi_obat': np.random.choice([0, 1, 2], N_TEST),
    'begadang': np.random.choice([0, 1], N_TEST),
    'keluhan': np.random.randint(0, 5, N_TEST),
    'riwayat_pribadi': np.random.randint(0, 6, N_TEST),
    'riwayat_keluarga': np.random.randint(0, 4, N_TEST),
    'gejala': np.random.randint(0, 5, N_TEST)
})

# Pastikan urutan kolom sesuai dengan training
X_test = X_test[feature_names]

# 3. BUAT KUNCI JAWABAN (GROUND TRUTH)
# Kita gunakan logika medis dasar untuk menentukan siapa yang 'seharusnya' sakit
# agar kita bisa menilai apakah AI menebak dengan benar.

y_true = {}

# Logika Hipertensi (Tensi tinggi / Riwayat)
y_true['risiko_hipertensi'] = ((X_test['sistolik'] > 140) | (X_test['diastolik'] > 90) | (X_test['riwayat_hipertensi'] == 1)).astype(int)

# Logika Diabetes (IMT tinggi + Keluarga)
y_true['risiko_dm'] = ((X_test['imt'] > 27) & (X_test['riwayat_keluarga'] >= 1)).astype(int)

# Logika Jantung (Komorbiditas Hipertensi + Begadang)
y_true['risiko_jantung'] = ((y_true['risiko_hipertensi'] == 1) & (X_test['begadang'] == 1)).astype(int)

# Logika Stroke (Tensi Ekstrem)
y_true['risiko_stroke'] = (X_test['sistolik'] > 160).astype(int)

# Logika Asma (Banyak Gejala)
y_true['risiko_asma'] = (X_test['gejala'] >= 3).astype(int)

# Logika Kanker (Riwayat Genetik Kuat)
y_true['risiko_kanker'] = (X_test['riwayat_keluarga'] >= 2).astype(int)


# 4. LOOP EVALUASI SEMUA PENYAKIT
summary_acc = {}

for disease_name, model in models.items():
    print(f"\n🔬 ANALISIS: {disease_name.upper().replace('_', ' ')}")
    print("-" * 40)
    
    # Prediksi AI
    y_pred = model.predict(X_test)
    y_target = y_true[disease_name]
    
    # Hitung Metrik
    acc = accuracy_score(y_target, y_pred)
    summary_acc[disease_name] = acc
    
    print(f"   Akurasi: {acc*100:.2f}%")
    print("\n   Laporan Klasifikasi:")
    print(classification_report(y_target, y_pred, target_names=['Sehat', 'Berisiko']))
    
    # Tampilkan Confusion Matrix Angka
    cm = confusion_matrix(y_target, y_pred)
    print(f"   Confusion Matrix: TP={cm[1][1]}, TN={cm[0][0]}, FP={cm[0][1]}, FN={cm[1][0]}")

print("\n" + "="*60)
print("🏆 REKAPITULASI AKURASI MODEL")
print("="*60)
rata_rata = np.mean(list(summary_acc.values()))
for k, v in summary_acc.items():
    print(f"📌 {k:<20} : {v*100:.2f}%")
print("-" * 30)
print(f"🌟 RATA-RATA SISTEM   : {rata_rata*100:.2f}%")
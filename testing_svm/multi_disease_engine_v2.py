import numpy as np
import pandas as pd
from sklearn.svm import SVC
from sklearn.preprocessing import StandardScaler
from sklearn.pipeline import make_pipeline
import joblib

# Konfigurasi
N_SAMPLES = 5000
np.random.seed(42)

print("⏳ Sedang men-generate dataset cerdas (Probabilistik)...")

# 1. Fitur Dasar (Distribusi Normal Medis)
X = pd.DataFrame({
    'imt': np.random.normal(24, 5, N_SAMPLES),      # Mean 24, SD 5
    'sistolik': np.random.normal(125, 20, N_SAMPLES),
    'diastolik': np.random.normal(80, 12, N_SAMPLES),
    'gula_darah': np.random.normal(140, 40, N_SAMPLES), # Fitur implisit utk simulasi
    'usia': np.random.randint(20, 80, N_SAMPLES)
})

# Fitur Kategori (0/1) & Ordinal
X['riwayat_hipertensi'] = np.random.choice([0, 1], N_SAMPLES, p=[0.7, 0.3])
X['konsumsi_obat'] = np.random.choice([0, 1, 2], N_SAMPLES)
X['begadang'] = np.random.choice([0, 1], N_SAMPLES)
X['keluhan'] = np.random.randint(0, 5, N_SAMPLES)
X['riwayat_pribadi'] = np.random.randint(0, 6, N_SAMPLES)
X['riwayat_keluarga'] = np.random.randint(0, 4, N_SAMPLES)
X['gejala'] = np.random.randint(0, 5, N_SAMPLES)

# Rapikan urutan kolom agar sesuai API nanti
feature_cols = ['imt', 'riwayat_hipertensi', 'konsumsi_obat', 'sistolik', 
                'diastolik', 'begadang', 'keluhan', 'riwayat_pribadi', 
                'riwayat_keluarga', 'gejala']
X_final = X[feature_cols]

# 2. Logika Label Probabilistik (Lebih Realistis untuk SVM)
# Kita buat "Skor Risiko" tersembunyi, lalu tambah noise random, baru di-threshold
# Ini memaksa SVM mencari hyperplane terbaik, bukan sekedar if-else.

def logistic(x): return 1 / (1 + np.exp(-x))

# HIPERTENSI
score_hip = 0.1 * (X['sistolik'] - 120) + 0.2 * (X['diastolik'] - 80) + 2 * X['riwayat_hipertensi']
prob_hip = logistic(score_hip / 10) # Normalisasi
y_hip = (prob_hip > np.random.rand(N_SAMPLES)).astype(int) # Stochastic threshold

# DIABETES (Asumsi korelasi dengan IMT & Keluarga)
score_dm = 0.3 * (X['imt'] - 25) + 1.5 * X['riwayat_keluarga']
prob_dm = logistic(score_dm / 5)
y_dm = (prob_dm > np.random.rand(N_SAMPLES)).astype(int)

# JANTUNG (Kombinasi Hip + Begadang + Keluhan)
score_jantung = 3 * y_hip + 1.5 * X['begadang'] + 0.5 * X['keluhan']
prob_jantung = logistic(score_jantung - 2)
y_jantung = (prob_jantung > np.random.rand(N_SAMPLES)).astype(int)

# STROKE (Hipertensi Ekstrem)
score_stroke = 0.1 * (X['sistolik'] - 150) + 4 * y_hip
prob_stroke = logistic(score_stroke / 5)
y_stroke = (prob_stroke > np.random.rand(N_SAMPLES)).astype(int)

# ASMA & KANKER (Sederhana)
y_asma = ((X['gejala'] >= 3) & (np.random.rand(N_SAMPLES) > 0.3)).astype(int)
y_kanker = ((X['riwayat_pribadi'] >= 4) | (X['riwayat_keluarga'] == 3)).astype(int)

targets = {
    'risiko_hipertensi': y_hip, 'risiko_dm': y_dm,
    'risiko_jantung': y_jantung, 'risiko_stroke': y_stroke,
    'risiko_asma': y_asma, 'risiko_kanker': y_kanker
}

# 3. Training & Saving
models = {}
print("🚀 Melatih 6 Model SVM...")
for name, y in targets.items():
    # Pipeline: Scaler otomatis di dalam model -> lebih aman
    clf = make_pipeline(StandardScaler(), SVC(kernel='rbf', probability=True, C=1.5))
    clf.fit(X_final, y)
    models[name] = clf

joblib.dump({'models': models, 'features': feature_cols}, 'multi_disease_svm_bundle_v2.pkl')
print("✅ Model Bundle V2 Tersimpan! SVM siap digunakan.")
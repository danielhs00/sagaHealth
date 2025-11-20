import numpy as np
import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.svm import SVC
from sklearn.preprocessing import StandardScaler
import joblib

# Definisi 6 Target Penyakit
TARGET_DISEASES = [
    'risiko_hipertensi', 'risiko_dm', 'risiko_jantung', 
    'risiko_stroke', 'risiko_asma', 'risiko_kanker'
]
FITUR_NAMES = [
    'imt', 'riwayat_hipertensi', 'konsumsi_obat', 'tekanan_sistolik', 
    'tekanan_diastolik', 'kebiasaan_begadang', 'jumlah_keluhan', 
    'riwayat_penyakit_pribadi', 'riwayat_penyakit_keluarga', 'jumlah_gejala'
]

jumlah_sampel = 5000 

print("="*70)
print("MEMBUAT DATASET SINTETIK & MELATIH MULTI-MODEL SVM")
print("="*70)

np.random.seed(42)

# ==========================================
# 1. GENERASI DATA FITUR INPUT (10 Kolom) - PERBAIKAN DITERAPKAN
# ==========================================

# Membuat fitur input dengan distribusi statistik yang realistis
df = pd.DataFrame({
    # 1. IMT
    'imt': np.random.normal(loc=24, scale=4, size=jumlah_sampel),
    
    # 2. Riwayat Hipertensi (0 atau 1) - MENGGUNAKAN choice
    'riwayat_hipertensi': np.random.choice([0, 1], size=jumlah_sampel, p=[0.7, 0.3]),
    
    # 3. Konsumsi Obat (0, 1, atau 2) - MENGGUNAKAN choice
    'konsumsi_obat': np.random.choice([0, 1, 2], size=jumlah_sampel, p=[0.7, 0.2, 0.1]),
    
    # 4 & 5. Tekanan Darah
    'tekanan_sistolik': np.random.normal(loc=125, scale=15, size=jumlah_sampel),
    'tekanan_diastolik': np.random.normal(loc=80, scale=8, size=jumlah_sampel),
    
    # 6. Kebiasaan Begadang (0 atau 1) - MENGGUNAKAN choice
    'kebiasaan_begadang': np.random.choice([0, 1], size=jumlah_sampel, p=[0.6, 0.4]),
    
    # 7. Jumlah Keluhan (0-4, menggunakan randint yang benar)
    'jumlah_keluhan': np.random.randint(0, 5, size=jumlah_sampel),
    
    # 8. Riwayat Penyakit Pribadi (0-5)
    'riwayat_penyakit_pribadi': np.random.randint(0, 6, size=jumlah_sampel),
    
    # 9. Riwayat Penyakit Keluarga (0-3)
    'riwayat_penyakit_keluarga': np.random.randint(0, 4, size=jumlah_sampel),
    
    # 10. Jumlah Gejala (0-4)
    'jumlah_gejala': np.random.randint(0, 5, size=jumlah_sampel),
})

# ==========================================
# 2. GENERASI LABEL OUTPUT (6 Kolom)
# ==========================================

labels = {}
# Risiko 1: Hipertensi
labels['risiko_hipertensi'] = ((df['tekanan_sistolik'] > 135) | (df['tekanan_diastolik'] > 85) | (df['riwayat_hipertensi'] == 1)).astype(int)
# Risiko 2: Diabetes Melitus (DM)
labels['risiko_dm'] = ((df['imt'] > 28) | (df['riwayat_penyakit_keluarga'] >= 2)).astype(int)
# Risiko 3: Penyakit Jantung
labels['risiko_jantung'] = ((labels['risiko_hipertensi'] == 1) | (df['kebiasaan_begadang'] == 1) & (df['jumlah_keluhan'] > 3)).astype(int)
# Risiko 4: Stroke
labels['risiko_stroke'] = ((labels['risiko_hipertensi'] == 1) & (df['tekanan_sistolik'] > 160)).astype(int)
# Risiko 5: Asma/PPOK
labels['risiko_asma'] = ((df['jumlah_gejala'] >= 3) & (df['riwayat_penyakit_pribadi'] > 0)).astype(int)
# Risiko 6: Kanker
labels['risiko_kanker'] = ((df['riwayat_penyakit_pribadi'] >= 4) | (df['riwayat_penyakit_keluarga'] == 3)).astype(int)

# Gabungkan fitur dan label menjadi satu DataFrame
df_labels = pd.DataFrame(labels)
df_full = pd.concat([df, df_labels], axis=1)

# Simpan dataset (opsional, untuk inspeksi)
df_full.to_csv('multi_disease_engine.csv', index=False)
print(f"✓ Dataset {len(df_full)} sampel berhasil dibuat dan disimpan.")

# ==========================================
# 3. PELATIHAN DAN PENYIMPANAN MODEL
# ==========================================

X = df_full[FITUR_NAMES].values
Y = df_full[TARGET_DISEASES].values

# Scaling
scaler = StandardScaler()
X_scaled = scaler.fit_transform(X)

# Training 6 Model SVM (satu per penyakit)
print("Memulai pelatihan 6 model SVM...")
models = {}
for i, disease in enumerate(TARGET_DISEASES):
    # Menggunakan SVC dengan probabilitas untuk hasil yang lebih detail
    svc = SVC(kernel='rbf', C=1.0, gamma='scale', probability=True, random_state=42)
    svc.fit(X_scaled, Y[:, i])
    models[disease] = svc
    print(f"  - Model {disease} selesai dilatih.")

# Menyimpan Scaler dan semua Model dalam satu bundle
model_bundle = {
    'scaler': scaler,
    'models': models,
    'diseases': TARGET_DISEASES
}
joblib.dump(model_bundle, 'multi_disease_svm_bundle.pkl')
print("\n✓ Model bundle (6 model & scaler) berhasil disimpan: 'multi_disease_svm_bundle.pkl'")
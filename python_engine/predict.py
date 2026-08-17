import sys
import json
import re
import os
import joblib
from catboost import CatBoostClassifier
from Sastrawi.Stemmer.StemmerFactory import StemmerFactory
from Sastrawi.StopWordRemover.StopWordRemoverFactory import StopWordRemoverFactory

# Inisialisasi NLP Sastrawi
stemmer = StemmerFactory().create_stemmer()
stopword_remover = StopWordRemoverFactory().create_stop_word_remover()

def clean_text(text):
    text = text.lower()
    text = re.sub(r'http\S+|www\S+|https\S+', '', text, flags=re.MULTILINE)
    text = re.sub(r'#\w+|@\w+|\d+|[^\w\s]', ' ', text)
    text = re.sub(r'\s+', ' ', text).strip()
    text = stopword_remover.remove(text)
    return stemmer.stem(text)

def main():
    # Membaca teks masukan dari STDIN yang dikirim oleh Laravel
    input_text = sys.stdin.read().strip()

    if not input_text:
        print(json.dumps({"error": "Teks masukan kosong"}))
        return

    # Lokasi path file model & vectorizer
    base_dir = os.path.dirname(os.path.abspath(__file__))
    model_path = os.path.join(base_dir, 'model_catboost_herbal.cbm')
    tfidf_path = os.path.join(base_dir, 'tfidf_vectorizer.pkl')

    try:
        model = CatBoostClassifier()
        model.load_model(model_path)
        tfidf = joblib.load(tfidf_path)

        # Preprocessing & Vektorisasi
        cleaned = clean_text(input_text)
        vector = tfidf.transform([cleaned]).toarray()

        pred_label = int(model.predict(vector)[0])
        probabilities = model.predict_proba(vector)[0]
        confidence = float(probabilities[pred_label]) * 100

        result = {
            "label": pred_label,
            "status": "Hoaks" if pred_label == 1 else "Valid",
            "confidence": round(confidence, 2),
            "analysis": "Teks mengandung klaim bombastis, sumber tidak jelas, dan bahasa emosional yang sering ditemukan pada berita palsu pengobatan herbal." if pred_label == 1 else "Teks memiliki struktur berita resmi, menggunakan referensi medis umum, dan sentimen netral."
        }
        print(json.dumps(result))
    except Exception as e:
        print(json.dumps({"error": str(e)}))

if __name__ == "__main__":
    main()
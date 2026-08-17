import json
import re
import os
import joblib
from http.server import HTTPServer, BaseHTTPRequestHandler
from catboost import CatBoostClassifier
from Sastrawi.Stemmer.StemmerFactory import StemmerFactory
from Sastrawi.StopWordRemover.StopWordRemoverFactory import StopWordRemoverFactory

# Load Sastrawi NLP
print("Loading Sastrawi NLP...")
stemmer = StemmerFactory().create_stemmer()
stopword_remover = StopWordRemoverFactory().create_stop_word_remover()

def clean_text(text):
    text = text.lower()
    text = re.sub(r'http\S+|www\S+|https\S+', '', text, flags=re.MULTILINE)
    text = re.sub(r'#\w+|@\w+|\d+|[^\w\s]', ' ', text)
    text = re.sub(r'\s+', ' ', text).strip()
    text = stopword_remover.remove(text)
    return stemmer.stem(text)

# Load Model & Vectorizer once into memory at startup
base_dir = os.path.dirname(os.path.abspath(__file__))
model_path = os.path.join(base_dir, 'model_catboost_herbal.cbm')
tfidf_path = os.path.join(base_dir, 'tfidf_vectorizer.pkl')

print("Loading CatBoost Model & TF-IDF Vectorizer...")
model = CatBoostClassifier()
model.load_model(model_path)
tfidf = joblib.load(tfidf_path)
print("Model loaded successfully!")

class PredictionHandler(BaseHTTPRequestHandler):
    def do_POST(self):
        content_length = int(self.headers.get('Content-Length', 0))
        post_data = self.rfile.read(content_length)
        
        try:
            data = json.loads(post_data.decode('utf-8'))
            input_text = data.get('text', '').strip()
            
            if not input_text:
                response = {"error": "Teks masukan kosong"}
            else:
                cleaned = clean_text(input_text)
                vector = tfidf.transform([cleaned]).toarray()

                pred_label = int(model.predict(vector)[0])
                probabilities = model.predict_proba(vector)[0]
                confidence = float(probabilities[pred_label]) * 100

                response = {
                    "label": pred_label,
                    "status": "Hoaks" if pred_label == 1 else "Valid",
                    "confidence": round(confidence, 2),
                    "analysis": "Teks mengandung klaim bombastis, sumber tidak jelas, dan bahasa emosional yang sering ditemukan pada berita palsu pengobatan herbal." if pred_label == 1 else "Teks memiliki struktur berita resmi, menggunakan referensi medis umum, dan sentimen netral."
                }

            self.send_response(200)
            self.send_header('Content-Type', 'application/json')
            self.send_header('Access-Control-Allow-Origin', '*')
            self.end_headers()
            self.wfile.write(json.dumps(response).encode('utf-8'))
        except Exception as e:
            self.send_response(500)
            self.send_header('Content-Type', 'application/json')
            self.end_headers()
            self.wfile.write(json.dumps({"error": str(e)}).encode('utf-8'))

    def do_OPTIONS(self):
        self.send_response(200)
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Allow-Methods', 'POST, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', 'Content-Type')
        self.end_headers()

def run_server(port=5000):
    server_address = ('0.0.0.0', port)
    httpd = HTTPServer(server_address, PredictionHandler)
    print(f"CatBoost Prediction API running on http://0.0.0.0:{port} ...")
    httpd.serve_forever()

if __name__ == '__main__':
    run_server(5000)

from flask import Flask, render_template, request, jsonify
from flask_cors import CORS
from datetime import datetime
import numpy as np
from sklearn.preprocessing import StandardScaler
from tensorflow.keras.models import model_from_json
import requests
import json
import joblib

app = Flask(__name__, template_folder="./template")
CORS(app, resources={r"/*": {"origins": "*"}})

data = []
file_path = "linear_regression_model.pkl"
model = joblib.load(file_path)


@app.route('/')
def home():
    return render_template('index.html')


#FOR WEBSITE INDEX
@app.route('/send', methods=['POST'])
def send():
    if request.method == 'POST':
        SP02 = float(request.form.get("Sp02"))
        HeartRate = float(request.form.get("Heart_Rate"))
        Motion = float(request.form.get("Motion"))
        Humidity_Ambient = float(request.form.get("Humidity"))
        Temperature_Ambient = float(request.form.get("Temperature"))
        EDA = float(request.form.get("EDA"))
        #Sp02, HeartRate, Motion, Humidity, Temperature, EDA
        input = [[SP02,HeartRate,Motion,Humidity_Ambient,Temperature_Ambient,EDA]]
        prediction = model.predict(input).tolist()
        return str(prediction)
#FOR PHONE 
@app.route('/appsend', methods=['POST'])
def appsend():
    if request.method == 'POST':
        appInput = request.json
        
        # Print or log the JSON payload
        print("Received JSON payload:", appInput)
        
        required_fields = ['SP02', 'HeartRate', 'Motion', 
                           'Humidity', 'Temperature', 'EDA']
        for field in required_fields:
            if field not in appInput:
                return f"Error: '{field}' is missing in the request data", 400
            
        try:
            # Convert data to floats
            #Age = float(appInput.get('Age'))
            #Height = float(appInput.get('Height'))
            #Weight = float(appInput.get('Weight'))
            #Height_meters = Height / 100
            #BMI = Weight / (Height_meters * Height_meters)

            Age = 25
            Height = 170
            Weight = 62
            Height_meters = Height / 100
            BMI = Weight / (Height_meters * Height_meters)

            SP02 = float(appInput.get('SP02'))
            HeartRate = float(appInput.get('HeartRate'))
            Motion = float(appInput.get('Motion'))
            Humidity_Ambient = float(appInput.get('Humidity'))
            Temperature_Ambient = float(appInput.get('Temperature'))
            EDA = float(appInput.get('EDA'))
            
            # Perform further processing with the data
            input = [[SP02,HeartRate,Motion,Humidity_Ambient,Temperature_Ambient,EDA]]
            prediction = model.predict(input)[0].tolist()
            data.append(prediction)
            payload = {
                "Age":Age,
                "Height":Height,
                "Weight":Weight,
                "BMI":BMI,
                "SP02": SP02,
                "HeartRate": HeartRate,
                "Motion":Motion,
                "Humidity_Ambient": Humidity_Ambient,
                "Temperature_Ambient": Temperature_Ambient,
                "EDA": EDA,
                "Glucose": prediction
            }
            # Send data to the PHP script
            php_script_url = "http://172.20.10.9/linear/store_data.php"  # Update with your actual URL
            response = requests.post(php_script_url, data=json.dumps(payload), headers={'Content-Type': 'application/json'})

            if response.status_code == 200:
                return str(prediction)
            else:
                return "Error sending data to PHP script"
                # Return response or perform additional operations
        except ValueError:
            return "Error: One or more fields contain invalid data", 400

        
        

@app.route('/predict', methods=['GET'])
def predict():
    try:
        # Extract data from the URL parameters
        SP02 = float(request.args.get("SP02"))
        HeartRate = float(request.args.get("HeartRate"))
        Motion = float(request.args.get("Motion"))
        Humidity_Ambient = float(request.args.get("Humidity"))
        Temperature_Ambient = float(request.args.get("Temperature"))
        EDA = float(request.args.get("EDA"))
        input = [[SP02,HeartRate,Motion,Humidity_Ambient,Temperature_Ambient,EDA]]
        prediction = model.predict(input)[0].tolist()
        
        data.append(prediction)
        return str(prediction)
    except Exception as e:
        return jsonify(result={"Error": str(e)})

@app.route('/receive', methods=['GET'])
def receive():
    if data:
        latest_prediction = data[-1]
        return jsonify(result={"Success": latest_prediction})
    else:
        return jsonify(result={"Error": "No predictions available"})

@app.route('/getData', methods=['GET'])
def getData():
    getData_url = "http://172.20.10.9/linear/getData.php"
    response = requests.get(getData_url)
    data = response.json()
    return jsonify(data)

if __name__ == '__main__':
    app.run(host="0.0.0.0")

from flask import Flask,request,render_template
import pickle
application=Flask(__name__)
app=application

## import ridge regressor and standard scaler pickle
ridge_model=pickle.load(open('models/ridge.pkl','rb'))
standard_scaler=pickle.load(open('models/scaler.pkl','rb'))
@app.route('/predictdata',methods=['GET','POST'])
def predict_datapoint():
    if request.method=="POST":
        try:
            Temperature=float(request.form.get('Temperature'))
            RH=float(request.form.get('RH'))
            Ws=float(request.form.get('Ws'))
            Rain=float(request.form.get('Rain'))
            FFMC=float(request.form.get('FFMC'))
            DMC=float(request.form.get('DMC'))
            ISI=float(request.form.get('ISI'))
            Classes=float(request.form.get('Classes'))
            Region = 1  # Default value for Region
            new_data_scaled=standard_scaler.transform([[Temperature,RH,Ws,Rain,FFMC,DMC,ISI,Classes,Region]])
            result=ridge_model.predict(new_data_scaled)
            return render_template('home.html',results=round(result[0], 2))
        except (ValueError, TypeError):
            error_message = "Please enter valid numeric values for all fields."
            return render_template('home.html', error=error_message)
        except Exception:
            error_message = "An error occurred during prediction. Please try again."
            return render_template('home.html', error=error_message)
    else:
        return render_template('home.html')


@app.route('/')
def index():
    return render_template('index.html')

if __name__=="__main__":
    app.run(host="0.0.0.0",port=5001,debug=True)